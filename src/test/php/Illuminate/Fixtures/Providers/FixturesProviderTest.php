<?php

namespace HHIT\Doctrine\Illuminate\Fixtures\Providers;

use HHIT\Doctrine\Illuminate\AbstractAppTest;

class FixturesProviderTest extends AbstractAppTest
{

    /**
     * @test
     */
    public function provider()
    {
        $provider = $this->app->getProvider(FixturesProvider::class);
        $this->assertNotNull($provider, FixturesProvider::class . ' not found');
        foreach ($provider->provides() as $abstract) {
            $this->assertTrue($this->app->bound($abstract), "{$abstract} is not bound");
            $concrete = $this->app->make($abstract);
            $this->assertNotNull($concrete, "Could not create {$abstract}");
            $this->assertTrue($concrete instanceof $abstract, "Object created is not an instance of {$abstract}");
        }
    }
}