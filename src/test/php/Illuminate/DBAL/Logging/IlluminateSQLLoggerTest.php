<?php

namespace HHIT\Doctrine\Illuminate\DBAL\Logging;

use Illuminate\Contracts\Logging\Log;
use Mockery\MockInterface;

class IlluminateSQLLoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MockInterface
     */
    private $log;

    /**
     * @var IlluminateSQLLogger
     */
    private $sqlLogger;

    /**
     * @before
     */
    public function before()
    {
        $this->log = \Mockery::mock(Log::class);
        $this->sqlLogger = new IlluminateSQLLogger($this->log);
    }

    /**
     * @after
     */
    public function after()
    {
        \Mockery::close();
    }

    /**
     * @test
     */
    public function log()
    {
        $this->log->shouldReceive('debug')->once();
        $this->sqlLogger->startQuery('TEST');
        $this->sqlLogger->stopQuery();
    }
}
