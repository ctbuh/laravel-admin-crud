<?php

namespace ctbuh\Admin;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CrudServiceProvider extends ServiceProvider
{
    public function register()
    {
        // load helpers
        require_once(__DIR__ . '/helpers.php');
    }

    public function boot()
    {
        Route::macro('crud', function ($name, $controller) {
            Route::post("{$name}/ajaxSortable", "{$controller}@ajaxSortable");
            Route::post("{$name}/restore", "{$controller}@restore");
            Route::get("{$name}/trashed", "{$controller}@indexTrashed");
            Route::resource($name, $controller);
        });

        $this->publishes([
            __DIR__ . '/../config/crzud.php' => config_path('crud.php'),
        ], 'nova-config');

        // use the vendor configuration file as fallback
        $this->mergeConfigFrom(
            __DIR__ . '/config/crud.php',
            'crud'
        );

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'crud');
    }
}
