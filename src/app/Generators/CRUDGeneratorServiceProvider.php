<?php namespace App\Generators;

use Illuminate\Support\ServiceProvider;

class CRUDGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->registerCRUDControllerGenerator();
    }

    private function registerCRUDControllerGenerator()
    {
        $this->app->singleton('command.galaz.crudcontoller', function ($app) {
            return $app['App\Generators\Commands\CRUDControllerMakeCommand'];
        });

        $this->commands('command.galaz.crudcontoller');
    }
}
