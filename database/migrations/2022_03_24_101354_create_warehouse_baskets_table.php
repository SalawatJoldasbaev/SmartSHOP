<?php

use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_baskets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class);
            $table->foreignIdFor(Branch::class, 'to_branch_id')->nullable();
            $table->foreignIdFor(Employee::class);
            $table->date('date');
            $table->enum('type', ['defect', 'return', 'gift', 'branch to branch', ' factory to main warehouse']);
            $table->enum('status', ['given', 'taken']);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_baskets');
    }
};
