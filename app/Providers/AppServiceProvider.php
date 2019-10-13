<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
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
        if(env('APP_DEBUG')) {
            DB::listen(function($query) {
                $bindings_str = [];
                foreach ($query->bindings as $binding) {
                    if ($binding instanceof \DateTime) {
                        $bindings_str[] = $binding->format(DATE_RFC2822);
                    } else {
                        $bindings_str[] = $binding;
                    }
                }
                File::append(
                    storage_path('/logs/query.log'),
                    $query->sql . ' [' . implode(', ', $bindings_str) . ']' . PHP_EOL
               );
            });
        }
    }
}
