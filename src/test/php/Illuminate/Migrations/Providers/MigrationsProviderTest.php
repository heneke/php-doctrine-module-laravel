<?php

namespace HHIT\Doctrine\Illuminate\Migrations\Providers;

use HHIT\Doctrine\Illuminate\AbstractAppTest;

class MigrationsProviderTest extends AbstractAppTest
{

    /**
     * @test
     */
    public function provider()
    {
        $provider = $this->app->getProvider(MigrationsProvider::class);
        $this->assertNotNull($provider, MigrationsProvider::class . ' not found');
        foreach ($provider->provides() as $abstract) {
            $this->assertTrue($this->app->bound($abstract), "{$abstract} is not bound");
            $concrete = $this->app->make($abstract);
            $this->assertNotNull($concrete, "Could not create {$abstract}");
            $this->assertTrue($concrete instanceof $abstract, "Object created is not an instance of {$abstract}");
        }
    }
}