<?php

namespace HHIT\Doctrine\Illuminate\ORM\Providers;

use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use HHIT\Doctrine\App\ORM\Entity\SampleEntity;
use HHIT\Doctrine\Illuminate\AbstractAppTest;

class ORMProviderProductionTest extends AbstractAppTest
{

    protected function isProductionEnv()
    {
        return true;
    }

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
}
