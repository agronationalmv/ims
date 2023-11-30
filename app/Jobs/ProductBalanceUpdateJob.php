<?php

namespace App\Jobs;

use App\Models\Contracts\HasInventory;
use App\Services\ProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProductBalanceUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected HasInventory $model)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ProductService $productService): void
    {
        foreach($this->model->items()->with('product')->get() as $itemLine){
            $productService->updateProductBalance($itemLine->product);
        }
    }
}
