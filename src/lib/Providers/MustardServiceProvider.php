<?php

/*

This file is part of Mustard.

Mustard is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Mustard is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Mustard.  If not, see <http://www.gnu.org/licenses/>.

*/

namespace Hamjoint\Mustard\Providers;

use Illuminate\Support\ServiceProvider;

class MustardServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Include routes
        if (!$this->app->routesAreCached()) {
            require __DIR__.'/../../includes/routes.php';
        }

        // Load views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'mustard');

        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'mustard');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations'),
        ], 'migrations');

        // Merge config
        $this->mergeConfigFrom(__DIR__.'/../../includes/config.php', 'mustard');

        // Include validators
        require __DIR__.'/../../includes/validators.php';
    }

    /**
     * Register any services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register('Tablelegs\Providers\TablelegsServiceProvider');
    }
}
