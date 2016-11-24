<?php

namespace HHIT\Doctrine\Illuminate;

use HHIT\Doctrine\App\ORM\SamplePersistenceUnit;
use HHIT\Doctrine\Fixtures\Contracts\ORMFixtureHandler;
use HHIT\Doctrine\Illuminate\Console\TestingDoctrineKernel;
use HHIT\Doctrine\Illuminate\DBAL\Providers\DBALProvider;
use HHIT\Doctrine\Illuminate\Fixtures\Providers\FixturesProvider;
use HHIT\Doctrine\Illuminate\Migrations\Providers\MigrationsProvider;
use HHIT\Doctrine\Illuminate\ORM\Providers\ORMProvider;
use HHIT\Doctrine\Migrations\Contracts\MigrationsHandler;
use HHIT\Illuminate\Testing\Application\TestingApplicationBuilder;

abstract class AbstractAppTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @before
     */
    public function before()
    {
        $builder = new TestingApplicationBuilder();
        $builder->getConfiguration()
            ->addProvider(DBALProvider::class)
            ->addProvider(ORMProvider::class)
            ->addProvider(MigrationsProvider::class)
            ->addProvider(FixturesProvider::class)
            ->set('dbal', $this->getDbalConfiguration())
            ->set('migrations', $this->getMigrationsConfiguration())
            ->set('orm', $this->getOrmConfiguration());

        if($this->isProductionEnv()) {
            $items = $builder->getConfiguration()->getItems('app');
            $items['env'] = 'production';
            $builder->getConfiguration()->set('app', $items);
        }

        $this->app = $builder->build(TestingDoctrineKernel::class);
        $this->app->bootstrap();

        $migrationsHandler = $this->app->make(MigrationsHandler::class);
        $migrationsHandler->migrateToLatest();

        $fixtureHandler = $this->app->make(ORMFixtureHandler::class);
        $fixtureHandler->load(__DIR__ . '/../../fixtures');
    }

    protected function isProductionEnv()
    {
        return false;
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
