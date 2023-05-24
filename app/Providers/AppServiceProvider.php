<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
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
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // force polymorphic types
        Relation::enforceMorphMap([
            'source' => 'App\Models\Source',
            'category' => 'App\Models\Category',
            'author' => 'App\Models\Author',
        ]);
    }
}
