<?php

namespace HHIT\Doctrine\Illuminate\DBAL;

use HHIT\Doctrine\Common\ArrayConfigurationSource;
use HHIT\Doctrine\DBAL\Contracts\DBALConfigurationSource;
use HHIT\Doctrine\DBAL\DBALArrayConfiguration;
use Illuminate\Config\Repository;

class IlluminateDBALConfigurationSource extends ArrayConfigurationSource implements DBALConfigurationSource
{
    use DBALArrayConfiguration;

    public function __construct(Repository $repository)
    {
        parent::__construct($repository->get('dbal'));
    }
}
