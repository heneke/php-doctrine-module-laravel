<?php

namespace HHIT\Doctrine\Illuminate\Fixtures\Providers;

use Doctrine\ORM\EntityManagerInterface;
use HHIT\Doctrine\Fixtures\Contracts\ORMFixtureHandler;
use HHIT\Doctrine\Fixtures\ORMDefaultFixtureHandler;
use Illuminate\Support\ServiceProvider;

class FixturesProvider extends ServiceProvider
{

    protected $defer = true;

    public function provides()
    {
        return [
            ORMFixtureHandler::class,
        ];
    }

    public function register()
    {
        $this->app->singleton(ORMFixtureHandler::class, function () {
            return new ORMDefaultFixtureHandler(
                $this->app->make(EntityManagerInterface::class)
            );
        });
    }
}
