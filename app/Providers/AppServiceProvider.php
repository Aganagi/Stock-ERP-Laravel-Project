<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        Schema::defaultStringLength(191);

        $this->app->afterResolving('Illuminate\Database\Eloquent\Model', function ($model) {
            if (!$model->photo) {
                $model->photo = 'no-photo.jpg';
                $model->save();
            }
        });
    }
}
