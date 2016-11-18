<?php

namespace HHIT\Doctrine\Illuminate\Console;

use HHIT\Illuminate\Testing\Application\Bootstrap\WithTestingConfiguration;
use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Foundation\Bootstrap\ConfigureLogging;
use Illuminate\Foundation\Bootstrap\DetectEnvironment;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Foundation\Bootstrap\RegisterFacades;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Illuminate\Foundation\Bootstrap\SetRequestForConsole;

class TestingDoctrineKernel extends DoctrineKernel
{

    protected $bootstrappers = [
        DetectEnvironment::class,
        WithTestingConfiguration::class,
        ConfigureLogging::class,
        HandleExceptions::class,
        RegisterFacades::class,
        SetRequestForConsole::class,
        RegisterProviders::class,
        BootProviders::class,
    ];


}