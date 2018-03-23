<?php

namespace HHIT\Doctrine\Illuminate\DBAL\Logging;

use Doctrine\DBAL\Logging\SQLLogger;
use Psr\Log\LoggerInterface;

class IlluminateSQLLogger implements SQLLogger
{
    /**
     * @var Log
     */
    private $log;

    /**
     * @var string
     */
    private $level;

    public function __construct(LoggerInterface $log, $level = 'debug')
    {
        $this->log = $log;
        $this->level = $level;
    }

    public function startQuery($sql, array $params = null, array $types = null)
    {
        call_user_func([$this->log, $this->level], ["DBAL query started: {$sql}", ['params' => $params, 'types' => $types]]);
    }

    public function stopQuery()
    {
    }
}
