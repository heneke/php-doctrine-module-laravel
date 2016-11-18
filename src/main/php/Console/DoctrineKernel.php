<?php

namespace HHIT\Doctrine\Illuminate\Console;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use HHIT\Doctrine\Console\DoctrineClient;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel;

class DoctrineKernel extends Kernel
{

    protected $commands = [];

    public function __construct(Application $app, Dispatcher $events)
    {
        parent::__construct($app, $events);
        foreach (DoctrineClient::getCommandClasses() as $commandClass) {
            $this->addCommand($commandClass);
        }
    }

    protected function addCommand($commandClass)
    {
        $this->commands[] = $commandClass;
    }

    protected function getArtisan()
    {
        $artisan = parent::getArtisan();
        foreach (DoctrineClient::createHelperSet($this->app->make(EntityManagerInterface::class), $this->app->make(Configuration::class)) as $key => $helper) {
            $artisan->getHelperSet()->set($helper, $key);
        }
        return $artisan;
    }
}
