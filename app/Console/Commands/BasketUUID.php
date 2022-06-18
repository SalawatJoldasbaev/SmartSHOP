<?php

namespace App\Console\Commands;

use App\Models\Basket;
use App\Models\QrCode;
use Illuminate\Console\Command;

class BasketUUID extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'basket:uuid';

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
        $qrcodes = QrCode::where('type', 'basket')->get();
        foreach ($qrcodes as $code) {
            $basket = Basket::find($code->additional['basket_id']);
            $basket->update([
                'uuid'=> $code->uuid
            ]);
        }
    }
}
