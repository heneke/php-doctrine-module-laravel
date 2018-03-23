<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2018 Hendrik Heneke
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

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
