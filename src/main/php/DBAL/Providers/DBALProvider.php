<?php

namespace HHIT\Doctrine\Illuminate\DBAL\Providers;

use Doctrine\Common\Cache\Cache;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use HHIT\Doctrine\DBAL\Contracts\DBALConfigurationFactory;
use HHIT\Doctrine\DBAL\Contracts\DBALConfigurationSource;
use HHIT\Doctrine\DBAL\Contracts\DBALConnectionFactory;
use HHIT\Doctrine\DBAL\DBALDefaultConfigurationFactory;
use HHIT\Doctrine\DBAL\DBALDefaultConnectionFactory;
use HHIT\Doctrine\Illuminate\DBAL\IlluminateDBALConfigurationSource;
use Illuminate\Support\ServiceProvider;

class DBALProvider extends ServiceProvider
{
    protected $defer = true;

    public function provides()
    {
        return [
            DBALConfigurationSource::class,
            DBALConfigurationFactory::class,
            Configuration::class,
            DBALConnectionFactory::class,
            Connection::class,
            AbstractSchemaManager::class,
        ];
    }

    public function register()
    {
        $this->registerConfigurationSource();
        $this->registerConfigurationFactory();
        $this->registerConnectionFactory();
        $this->registerConnection();
        $this->registerSchemaManager();
    }

    protected function registerConfigurationSource()
    {
        $this->app->singleton(DBALConfigurationSource::class, IlluminateDBALConfigurationSource::class);
    }

    protected function registerConfigurationFactory()
    {
        $this->app->singleton(DBALConfigurationFactory::class, DBALDefaultConfigurationFactory::class);
        $this->app->singleton(Configuration::class, function () {
            return $this->app->make(DBALConfigurationFactory::class)->createConfiguration();
        });
    }

    protected function registerConnectionFactory()
    {
        $this->app->singleton(DBALConnectionFactory::class, function () {
            return new DBALDefaultConnectionFactory(
                $this->app->make(DBALConfigurationFactory::class),
                $this->createResultCache(),
                $this->createSQLLogger(),
                $this->createEventManager()
            );
        });
    }

    protected function registerConnection()
    {
        $this->app->singleton(Connection::class, function () {
            return $this->app->make(DBALConnectionFactory::class)->createConnection();
        });
    }

    protected function registerSchemaManager()
    {
        $this->app->singleton(AbstractSchemaManager::class, function () {
            return $this->app->make(Connection::class)->getSchemaManager();
        });
    }

    /**
     * Creates the result cache if any.
     *
     * @return Cache|null
     */
    protected function createResultCache()
    {
        return null;
    }

    /**
     * Creates the sql logger of any.
     *
     * @return SQLLogger|null
     */
    protected function createSQLLogger()
    {
        return null;
    }

    /**
     * Returns the event manager if any.
     */
    protected function createEventManager()
    {
        return null;
    }
}
