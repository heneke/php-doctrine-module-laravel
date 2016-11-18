<?php

namespace HHIT\Doctrine\Illuminate\ORM\Providers;

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
        $path = storage_path('doctrine/cache');
        if (file_exists($path)) {
            rmdir($path);
        }
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
