<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessSales;
use App\Models\JobBatch;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        return view('upload');
    }

    /**
     * @return View
     */
    public function progress(): View
    {
        return view('progress');
    }

    /**
     * @param Request $request
     *
     * @return void
     */
    public function upload(Request $request)
    {
        try {
            if ($request->has('csvFile')) {
                $fileName = $request->csvFile->getClientOriginalName();
                $fileWithPath = public_path('uploads') . '/' . $fileName;

                if (!file_exists($fileWithPath)) {
                    $request->csvFile->move(public_path('uploads'), $fileName);
                }

                $header = null;
                $dataFromCsv = array();
                $records = array_map('str_getcsv', file($fileWithPath));

                // loop
                foreach ($records as $record) {
                    if (!$header) {
                        $header = $record;
                    } else {
                        $dataFromCsv[] = $record;
                    }
                }

                // breaking data for example 10k to 1k/300 each.
                $dataFromCsv = array_chunk($dataFromCsv, 300);
                $batch = Bus::batch([])->dispatch();

                // Looping through each 1000/300 sales.
                foreach ($dataFromCsv as $index => $dataCsv) {
                    // looping through each sales data.
                    foreach ($dataCsv as $data) {
                        $salesData[$index][] = array_combine($header, $data);
                    }

                    $batch->add(new ProcessSales($salesData[$index]));
                    // ProcessSales::dispatch($salesData[$index]);
                }

                // We update session id every time we process new batch
                session()->put('lastBatchId', $batch->id);

                return redirect('/progress?id=' . $batch->id);
            }
        } catch (Exception $e) {
            Log::error($e);
            dd($e);
        }
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function csvStoreProcess(Request $request)
    {
        try {
            $batchId = $request->id ?? session()->get('lastBatchId');
            if (JobBatch::where('id', $batchId)->count()) {
                $response = JobBatch::where('id', $batchId)->first();
                return response()->json($response);
            }
        } catch (Exception $e) {
            Log::error($e);
            dd($e);
        }
    }
}
