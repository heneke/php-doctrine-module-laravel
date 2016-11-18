<?php

namespace HHIT\Doctrine\App\ORM;

use HHIT\Doctrine\ORM\AbstractPersistenceUnit;

class SamplePersistenceUnit extends AbstractPersistenceUnit
{
    public function __construct()
    {
        parent::__construct(__DIR__.'/Entity');
    }
}
