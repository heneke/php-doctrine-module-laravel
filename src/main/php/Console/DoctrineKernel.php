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
