<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-04
 */

namespace fk\utility\Database\Console\Migrations;

use Illuminate\Support\ServiceProvider;

class MigrateMakeServiceProvider extends ServiceProvider
{

    protected $defer = true;

    public function register()
    {
        $commands = [];
        $this->app->singleton($commands[] = 'command.migrate.create', function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['migration.creator'];

            $composer = $app['composer'];
            return new MigrateMakeCommand($creator, $composer);
        });

        $this->commands($commands);
    }
}