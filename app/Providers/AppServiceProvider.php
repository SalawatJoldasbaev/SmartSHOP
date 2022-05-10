<?php

namespace App\Providers;

use App\Models\Basket;
use App\Models\Employee;
use App\Models\Product;
use App\Models\User;
use App\Observers\BasketObserver;
use App\Observers\ProductObserver;
use App\Policies\EmployeePolicy;
use App\Policies\ProductPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Product::class => ProductPolicy::class,
        Employee::class => EmployeePolicy::class,
    ];
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Product::observe(ProductObserver::class);
        Basket::observe(BasketObserver::class);
    }
}
