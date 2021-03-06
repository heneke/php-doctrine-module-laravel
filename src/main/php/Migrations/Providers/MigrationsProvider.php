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

namespace HHIT\Doctrine\Illuminate\Migrations\Providers;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\OutputWriter;
use Doctrine\ORM\EntityManagerInterface;
use HHIT\Doctrine\Illuminate\Migrations\IlluminateMigrationsConfigurationSource;
use HHIT\Doctrine\Migrations\Contracts\MigrationsConfigurationFactory;
use HHIT\Doctrine\Migrations\Contracts\MigrationsConfigurationSource;
use HHIT\Doctrine\Migrations\Contracts\MigrationsHandler;
use HHIT\Doctrine\Migrations\MigrationsDefaultConfigurationFactory;
use HHIT\Doctrine\Migrations\MigrationsDefaultHandler;
use HHIT\Doctrine\ORM\Contracts\EntityManagerConfigurationSource;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationsProvider extends ServiceProvider
{
    protected $defer = true;

    public function provides()
    {
        return [
            MigrationsConfigurationSource::class,
            MigrationsConfigurationFactory::class,
            Configuration::class,
            MigrationsHandler::class,
        ];
    }

    public function register()
    {
        $this->registerConfigurationSource();
        $this->registerConfigurationFactory();
        $this->registerMigrationsHandler();
    }

    protected function registerConfigurationSource()
    {
        $this->app->singleton(MigrationsConfigurationSource::class, IlluminateMigrationsConfigurationSource::class);
    }

    protected function registerConfigurationFactory()
    {
        $this->app->singleton(MigrationsConfigurationFactory::class, function () {
            return new MigrationsDefaultConfigurationFactory(
                $this->app->make(MigrationsConfigurationSource::class),
                $this->app->make(EntityManagerConfigurationSource::class),
                $this->app->make(EntityManagerInterface::class)
            );
        });
        $this->app->singleton(Configuration::class, function (Application $app, $args) {
            $configuration = $this->app->make(MigrationsConfigurationFactory::class)->createConfiguration();

            $outputInterface = null;
            if ($args != null && is_array($args)) {
                foreach ($args as $arg) {
                    if ($arg instanceof OutputInterface) {
                        $outputInterface = $arg;
                    }
                }
            }
            if ($outputInterface != null) {
                $configuration->setOutputWriter(new OutputWriter(function ($message) use ($outputInterface) {
                    $outputInterface->writeln($message);
                }));
            }
            return $configuration;
        });
    }

    protected function registerMigrationsHandler()
    {
        $this->app->singleton(MigrationsHandler::class, function () {
            return new MigrationsDefaultHandler($this->app->make(Configuration::class));
        });
    }
}
