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

namespace HHIT\Doctrine\Illuminate;

use HHIT\Doctrine\App\ORM\SamplePersistenceUnit;
use HHIT\Doctrine\Fixtures\Contracts\ORMFixtureHandler;
use HHIT\Doctrine\Migrations\Contracts\MigrationsHandler;
use Orchestra\Testbench\TestCase;

abstract class AbstractAppTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $migrationsHandler = $this->app->make(MigrationsHandler::class);
        $migrationsHandler->migrateToLatest();

        $fixtureHandler = $this->app->make(ORMFixtureHandler::class);
        $fixtureHandler->load(__DIR__ . '/../../fixtures');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    /*
    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Console\Kernel', 'HHIT\Doctrine\Illuminate\Console\DoctrineKernel');
    }
       */
    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'HHIT\Doctrine\Illuminate\DBAL\Providers\DBALProvider',
            'HHIT\Doctrine\Illuminate\Fixtures\Providers\FixturesProvider',
            'HHIT\Doctrine\Illuminate\Migrations\Providers\MigrationsProvider',
            'HHIT\Doctrine\Illuminate\ORM\Providers\ORMProvider'
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('dbal', $this->getDbalConfiguration());
        $app['config']->set('migrations', $this->getMigrationsConfiguration());
        $app['config']->set('orm', $this->getOrmConfiguration());
    }

    private function getDbalConfiguration()
    {
        return [
            'connection' => [
                'url' => 'sqlite:///:memory:'
            ]
        ];
    }

    private function getMigrationsConfiguration()
    {
        return [
            'column' => 'version',
            'table' => 'doctrine_migrations_version',
            'namespace' => 'Some\Name\Space',
            'directory' => __DIR__ . '/../../migrations',
            'platform_dependent' => true
        ];
    }

    private function getOrmConfiguration()
    {
        return [
            'driver' => 'annotation',
            'units' => [
                SamplePersistenceUnit::class
            ]
        ];
    }
}
