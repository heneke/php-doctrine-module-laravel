<?php

namespace HHIT\Doctrine\Illuminate\Console;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use HHIT\Doctrine\Console\DoctrineClient;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class DoctrineKernel extends Kernel
{

    /**
     * @var OutputInterface
     */
    private $output;

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

    public function handle($input, $output = null)
    {
        if ($output == null) {
            $this->output = new ConsoleOutput();
        } else {
            $this->output = $output;
        }
        return parent::handle($input, $output);
    }

    protected function getArtisan()
    {
        $artisan = parent::getArtisan();
        foreach (DoctrineClient::createHelperSet($this->app->make(EntityManagerInterface::class), $this->app->make(Configuration::class, [$this->output])) as $key => $helper) {
            $artisan->getHelperSet()->set($helper, $key);
        }
        return $artisan;
    }
}
