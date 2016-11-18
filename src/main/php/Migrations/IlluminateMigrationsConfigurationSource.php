<?php

namespace HHIT\Doctrine\Illuminate\Migrations;

use HHIT\Doctrine\Common\ArrayConfigurationSource;
use HHIT\Doctrine\Migrations\Contracts\MigrationsConfigurationSource;
use HHIT\Doctrine\Migrations\MigrationsArrayConfiguration;
use Illuminate\Config\Repository;

class IlluminateMigrationsConfigurationSource extends ArrayConfigurationSource implements MigrationsConfigurationSource
{

    use MigrationsArrayConfiguration;

    public function __construct(Repository $repository)
    {
        parent::__construct($repository->get('migrations'));
    }
}