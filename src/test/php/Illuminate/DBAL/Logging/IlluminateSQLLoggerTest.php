<?php

namespace HHIT\Doctrine\Illuminate\DBAL\Logging;

use Psr\Log\LoggerInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class IlluminateSQLLoggerTest extends TestCase
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
        $this->log = \Mockery::mock(LoggerInterface::class);
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
        $this->assertNotNull($this->sqlLogger);
        $this->log->shouldReceive('debug')->once();
        $this->sqlLogger->startQuery('TEST');
        $this->sqlLogger->stopQuery();
    }
}
