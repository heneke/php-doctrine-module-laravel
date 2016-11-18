<?php

namespace HHIT\Doctrine\Illuminate\ORM\Providers;

use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use HHIT\Doctrine\DBAL\Contracts\DBALConnectionFactory;
use HHIT\Doctrine\Illuminate\ORM\IlluminateEntityManagerConfigurationSource;
use HHIT\Doctrine\ORM\Contracts\EntityManagerConfigurationFactory;
use HHIT\Doctrine\ORM\Contracts\EntityManagerConfigurationSource;
use HHIT\Doctrine\ORM\Contracts\EntityManagerFactory;
use HHIT\Doctrine\ORM\EntityManagerDefaultConfigurationFactory;
use HHIT\Doctrine\ORM\EntityManagerDefaultFactory;
use Illuminate\Support\ServiceProvider;

class ORMProvider extends ServiceProvider
{
    protected $defer = true;

    public function provides()
    {
        return [
            EntityManagerConfigurationSource::class,
            EntityManagerConfigurationFactory::class,
            Configuration::class,
            EntityManagerFactory::class,
            EntityManagerInterface::class,
        ];
    }

    public function register()
    {
        $this->registerConfigurationSource();
        $this->registerConfigurationFactory();
        $this->registerEntityManagerFactory();
    }

    protected function registerConfigurationSource()
    {
        $this->app->singleton(EntityManagerConfigurationSource::class, IlluminateEntityManagerConfigurationSource::class);
    }

    protected function registerConfigurationFactory()
    {
        $this->app->singleton(EntityManagerConfigurationFactory::class, function () {
            return new EntityManagerDefaultConfigurationFactory(
                $this->app->make(EntityManagerConfigurationSource::class),
                $this->createCache()
            );
        });
        $this->app->singleton(Configuration::class, function () {
            return $this->app->make(EntityManagerConfigurationFactory::class)->createConfiguration();
        });
    }

    protected function registerEntityManagerFactory()
    {
        $this->app->singleton(EntityManagerFactory::class, function () {
            return new EntityManagerDefaultFactory(
                $this->app->make(EntityManagerConfigurationFactory::class),
                $this->app->make(DBALConnectionFactory::class),
                $this->createEventManager()
            );
        });
        $this->app->singleton(EntityManagerInterface::class, function () {
            return $this->app->make(EntityManagerFactory::class)->createEntityManager();
        });
    }

    protected function createCache()
    {
        if ($this->app->environment('production')) {
            $path = storage_path('doctrine/cache');
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            return new FilesystemCache(realpath($path));
        }

        return null;
    }

    protected function createEventManager()
    {
        return null;
    }
}
