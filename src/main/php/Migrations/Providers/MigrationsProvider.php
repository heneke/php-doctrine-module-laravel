<?php

namespace HHIT\Doctrine\Illuminate\Migrations\Providers;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use HHIT\Doctrine\Illuminate\Migrations\IlluminateMigrationsConfigurationSource;
use HHIT\Doctrine\Migrations\Contracts\MigrationsConfigurationFactory;
use HHIT\Doctrine\Migrations\Contracts\MigrationsConfigurationSource;
use HHIT\Doctrine\Migrations\Contracts\MigrationsHandler;
use HHIT\Doctrine\Migrations\MigrationsDefaultConfigurationFactory;
use HHIT\Doctrine\Migrations\MigrationsDefaultHandler;
use HHIT\Doctrine\ORM\Contracts\EntityManagerConfigurationSource;
use Illuminate\Support\ServiceProvider;

class MigrationsProvider extends ServiceProvider
{

    protected $defer = true;

    public function provides()
    {
        return [
            MigrationsConfigurationSource::class,
            MigrationsConfigurationFactory::class,
            Configuration::class,
            MigrationsHandler::class,
        ];
    }

    public function register()
    {
        $this->registerConfigurationSource();
        $this->registerConfigurationFactory();
        $this->registerMigrationsHandler();
    }

    protected function registerConfigurationSource()
    {
        $this->app->singleton(MigrationsConfigurationSource::class, IlluminateMigrationsConfigurationSource::class);
    }

    protected function registerConfigurationFactory()
    {
        $this->app->singleton(MigrationsConfigurationFactory::class, function () {
            return new MigrationsDefaultConfigurationFactory(
                $this->app->make(MigrationsConfigurationSource::class),
                $this->app->make(EntityManagerConfigurationSource::class),
                $this->app->make(EntityManagerInterface::class)
            );
        });
        $this->app->singleton(Configuration::class, function () {
            return $this->app->make(MigrationsConfigurationFactory::class)->createConfiguration();
        });
    }

    protected function registerMigrationsHandler()
    {
        $this->app->singleton(MigrationsHandler::class, function () {
            return new MigrationsDefaultHandler($this->app->make(Configuration::class));
        });
    }
}
