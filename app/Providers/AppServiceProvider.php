<?php

namespace App\Providers;

use Illuminate\Support\Number;
use App\Services\SessionCartService;
use App\Contract\CartServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CartServiceInterface::class, SessionCartService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Number::useCurrency('IDR');
    }
}
