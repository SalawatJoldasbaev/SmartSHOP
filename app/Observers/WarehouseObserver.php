<?php

namespace App\Observers;

use App\Models\Warehouse;

class WarehouseObserver
{
    /**
     * Handle the Warehouse "created" event.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return void
     */
    public function created(Warehouse $warehouse)
    {

    }

    /**
     * Handle the Warehouse "updated" event.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return void
     */
    public function updated(Warehouse $warehouse)
    {
        //
    }

    /**
     * Handle the Warehouse "deleted" event.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return void
     */
    public function deleted(Warehouse $warehouse)
    {
        //
    }

    /**
     * Handle the Warehouse "restored" event.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return void
     */
    public function restored(Warehouse $warehouse)
    {
        //
    }

    /**
     * Handle the Warehouse "force deleted" event.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return void
     */
    public function forceDeleted(Warehouse $warehouse)
    {
        //
    }
}
