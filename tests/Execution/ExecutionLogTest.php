<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\Test\Execution;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use EBlick\ContaoTrigger\Execution\ExecutionLog;
use PHPUnit\Framework\TestCase;

class ExecutionLogTest extends TestCase
{
    public function testGetLogWithoutOrigin(): void
    {
        $data = [2 => [['id' => 4, 'pid' => 5, 'tstamp' => 1234, 'origin' => 'tl_someTable']]];

        $result = $this->createMock(Result::class);
        $result
            ->expects(self::once())
            ->method('fetchAllAssociative')
            ->willReturn($data)
        ;

        $connection = $this->createMock(Connection::class);
        $connection
            ->expects(self::once())
            ->method('executeQuery')
            ->with('SELECT originId, l.* FROM tl_eblick_trigger_log l WHERE pid=?', [12])
            ->willReturn($result)
        ;

        $log = new ExecutionLog($connection);
        self::assertEquals($data, $log->getLog(12));
    }

    public function testGetLogWithOrigin(): void
    {
        $data = [2 => [['id' => 4, 'pid' => 5, 'tstamp' => 1234, 'origin' => 'tl_someTable']]];

        $result = $this->createMock(Result::class);
        $result
            ->expects(self::once())
            ->method('fetchAllAssociative')
            ->willReturn($data)
        ;

        $connection = $this->createMock(Connection::class);
        $connection
            ->expects(self::once())
            ->method('executeQuery')
            ->with('SELECT originId, l.* FROM tl_eblick_trigger_log l WHERE pid=? AND origin =?', [12, 'tl_someTable'])
            ->willReturn($result)
        ;

        $log = new ExecutionLog($connection);
        self::assertEquals($data, $log->getLog(12, 'tl_someTable'));
    }

    public function testAddLogWithoutOriginFails(): void
    {
        $log = new ExecutionLog($connection = $this->createMock(Connection::class));

        $this->expectException(\InvalidArgumentException::class);
        $log->addLog(12, 5, '', false);
    }

    public function testAddLogWithOrigin(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection
            ->expects(self::once())
            ->method('executeQuery')
            ->with(
                'INSERT INTO tl_eblick_trigger_log SET pid=?, tstamp=?, originId=?, origin=?, simulated=?',
                self::anything()
            )
        ;

        $log = new ExecutionLog($connection);
        $log->addLog(12, 5, 'tl_someTable', false);
    }
}
