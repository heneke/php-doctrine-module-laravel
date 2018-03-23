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
