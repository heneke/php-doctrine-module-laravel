<?php

namespace HHIT\Doctrine\Illuminate\ORM;

use HHIT\Doctrine\Common\ArrayConfigurationSource;
use HHIT\Doctrine\ORM\Contracts\EntityManagerConfigurationSource;
use HHIT\Doctrine\ORM\EntityManagerArrayConfiguration;
use Illuminate\Config\Repository;

class IlluminateEntityManagerConfigurationSource extends ArrayConfigurationSource implements EntityManagerConfigurationSource
{
    use EntityManagerArrayConfiguration;

    public function __construct(Repository $repository)
    {
        parent::__construct($repository->get('orm'));
    }
}
