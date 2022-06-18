<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\QrCode;
use Illuminate\Console\Command;

class ProductUUID extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:uuid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $qrcodes = QrCode::where('type', 'product')->get();
        foreach ($qrcodes as $code) {
            $product = Product::where('id', $code->additional['product_id'])->withTrashed()->first();
            $product->update([
                'uuid'=> $code->uuid
            ]);
        }
    }
}
