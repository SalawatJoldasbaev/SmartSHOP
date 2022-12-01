<?php

use App\Models\ConsumptionCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumption_categories', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->timestamps();
            $table->softDeletes();
        });

        $categories = [
            [
                'name' => [
                    'uz' => 'Boshqa',
                    'en' => 'Other',
                    'ru' => 'Другой',
                    'qr' => 'Basqa',
                ],
            ],
            [
                'name' => [
                    'uz' => 'Ma\'muriy',
                    'en' => 'Administrative',
                    'ru' => 'Административные',
                    'qr' => 'Administrativ',
                ],
            ],
            [
                'name' => [
                    'uz' => 'Ijara',
                    'en' => 'Rent',
                    'ru' => 'Аренда',
                    'qr' => 'Ijara',
                ],
            ],
            [
                'name' => [
                    'uz' => 'Ish haqi',
                    'en' => 'Salary',
                    'ru' => 'Зарплата',
                    'qr' => 'Is haqi',
                ],
            ],
            [
                'name' => [
                    'uz' => 'Investitsiyalar',
                    'en' => 'Investments',
                    'ru' => 'Инвестиции',
                    'qr' => 'Investitsiyalar',
                ],
            ],
            [
                'name' => [
                    'uz' => 'Ofis   ',
                    'en' => 'Office',
                    'ru' => 'Офис',
                    'qr' => 'Ofis',
                ],
            ],
            [
                'name' => [
                    'uz' => 'Soliqlar',
                    'en' => 'Taxes',
                    'ru' => 'Налоги',
                    'qr' => 'Saliqlar',
                ],
            ],
            [
                'name' => [
                    'uz' => 'Maishiy',
                    'en' => 'Household',
                    'ru' => 'Домашнее хозяйство',
                    'qr' => 'Uy xojalig\'i',
                ],
            ],
        ];
        foreach ($categories as $category) {
            ConsumptionCategory::create([
                'name' => $category['name'],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('consumption_categories');
    }
};
