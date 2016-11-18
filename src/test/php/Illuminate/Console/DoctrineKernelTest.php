<?php

namespace HHIT\Doctrine\Illuminate\Console;

use HHIT\Doctrine\Illuminate\AbstractAppTest;
use Illuminate\Contracts\Console\Kernel;

class DoctrineKernelTest extends AbstractAppTest
{

    /**
     * @test
     */
    public function artisan()
    {
        /**
         * @var $kernel DoctrineKernel
         */
        $kernel = $this->app->make(Kernel::class);
        $this->assertNotNull($kernel);
        $this->assertInstanceOf(DoctrineKernel::class, $kernel);
        $this->assertTrue(count($kernel->all()) > 0);
    }
}