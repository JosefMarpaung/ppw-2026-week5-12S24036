<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
     * Mem-bootstrap layanan aplikasi, listener query basis data, dan styling paginasi.
     */
    public function boot(): void
    {
        // 1. Mengaktifkan styling paginasi Bootstrap 5 secara global
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // 2. Listener SQL Audit sesuai Modul Lab 4: Memantau dan mencatat seluruh eksekusi query ke log
        \Illuminate\Support\Facades\DB::listen(function ($query) {
            // Mencatat durasi eksekusi dalam milidetik (ms) dan teks query SQL ke file storage/logs/laravel.log
            \Illuminate\Support\Facades\Log::info(sprintf(
                "[SQL AUDIT] (%.2f ms) %s", $query->time, $query->sql
            ));
        });
    }
}
