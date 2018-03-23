<?php

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
