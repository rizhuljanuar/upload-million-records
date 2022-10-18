<?php

namespace App\Jobs;

use App\Models\Sales;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessSales implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $salesData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($salesData)
    {
        $this->salesData = $salesData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->salesData as $salesData) {
            $sales = new Sales();
            $sales->region = $salesData['Region'];
            $sales->country = $salesData['Country'];
            $sales->item_type = $salesData['Item Type'];
            $sales->sales_channel = $salesData['Sales Channel'];
            $sales->save();
        }
    }
}
