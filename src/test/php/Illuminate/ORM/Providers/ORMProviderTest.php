<?php

namespace HHIT\Doctrine\Illuminate\ORM\Providers;

use Doctrine\ORM\EntityManagerInterface;
use HHIT\Doctrine\App\ORM\Entity\SampleEntity;
use HHIT\Doctrine\Illuminate\AbstractAppTest;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;

class ORMProviderTest extends AbstractAppTest
{
    /**
     * @test
     */
    public function provider()
    {
        $provider = $this->app->getProvider(ORMProvider::class);
        $this->assertNotNull($provider, ORMProvider::class . ' not found');
        foreach ($provider->provides() as $abstract) {
            $this->assertTrue($this->app->bound($abstract), "{$abstract} is not bound");
            $concrete = $this->app->make($abstract);
            $this->assertNotNull($concrete, "Could not create {$abstract}");
            $this->assertTrue($concrete instanceof $abstract, "Object created is not an instance of {$abstract}");
        }
    }

    /**
     * @after
     */
    public function after()
    {
        \Mockery::close();
    }

    /**
     * @test
     */
    public function bindEntities()
    {
        // ensure the provider is booted
        $em = $this->app->make(EntityManagerInterface::class);
        $sampleEntity = $em->find(SampleEntity::class, 1);
        $this->assertNotNull($sampleEntity);

        /**
         * @var $router Router
         */
        $router = $this->app->make(Router::class);
        $this->assertNotNull($router);

        $route = \Mockery::mock(Route::class);
        $route->shouldReceive('parameters')->andReturn(['SampleEntity' => 1])->once();
        $route->shouldReceive('setParameter')->withArgs(['SampleEntity', $sampleEntity])->once();

        $router->substituteBindings($route);
    }
}
