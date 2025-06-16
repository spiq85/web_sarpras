<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\DetailPeminjaman;
use App\Observers\DetailPeminjamanObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DetailPeminjaman::observe(DetailPeminjamanObserver::class);
    }
}
