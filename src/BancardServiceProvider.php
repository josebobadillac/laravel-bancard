<?php 

namespace Mancoide\Bancard;

use Illuminate\Support\ServiceProvider;
use Mancoide\Bancard\Trait\PublishesMigrations;

class BancardServiceProvider extends ServiceProvider
{
    use PublishesMigrations;
    public function boot()
    {
        $this->registerMigrations(__DIR__ . '/database/migrations');
        $this->publishes([
            __DIR__.'/config/bancard.php' => config_path('bancard.php'),
        ], 'bancard-configs');

    }

    public function register()
    {
        //
    }
}