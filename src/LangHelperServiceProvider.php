<?php

namespace Apih\LangHelper;

use Apih\LangHelper\Commands\Json\DuplicatesCommand;
use Apih\LangHelper\Commands\Json\SortCommand;
use Apih\LangHelper\Commands\MissingCommand;
use Illuminate\Support\ServiceProvider;

class LangHelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Register the commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                MissingCommand::class,
                DuplicatesCommand::class,
                SortCommand::class,
            ]);
        }
    }
}
