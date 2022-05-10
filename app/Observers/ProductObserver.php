<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\QrCode;
use Str;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function created(Product $product)
    {
        $uuid = Str::uuid();
        $check = QrCode::where('uuid', $uuid)->first();
        while ($check) {
            $uuid = Str::uuid();
            $check = QrCode::where('uuid', $uuid)->first();
        }

        $code = QrCode::create([
            'uuid' => $uuid,
            'type' => 'product',
            'additional' => [
                'product_id' => $product->id
            ]
        ]);
    }

    /**
     * Handle the Product "updated" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function updated(Product $product)
    {
        //
    }

    /**
     * Handle the Product "deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function deleted(Product $product)
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function restored(Product $product)
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function forceDeleted(Product $product)
    {
        //
    }
}
