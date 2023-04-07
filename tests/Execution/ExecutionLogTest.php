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
        $result = $this->createMock(Result::class);
        $result
            ->expects(self::once())
            ->method('fetchAllAssociative')
            ->willReturn([
                ['id' => 1, 'pid' => 12, 'tstamp' => 1234, 'origin' => 'tl_someTable', 'originId' => 4, 'simulated' => ''],
                ['id' => 2, 'pid' => 12, 'tstamp' => 2345, 'origin' => 'tl_otherTable', 'originId' => 7, 'simulated' => ''],
            ])
        ;

        $connection = $this->createMock(Connection::class);
        $connection
            ->expects(self::once())
            ->method('executeQuery')
            ->with('SELECT * FROM tl_eblick_trigger_log WHERE pid=?', [12])
            ->willReturn($result)
        ;

        $log = new ExecutionLog($connection);

        $expected = [
            4 => ['id' => 1, 'pid' => 12, 'tstamp' => 1234, 'origin' => 'tl_someTable', 'originId' => 4, 'simulated' => ''],
            7 => ['id' => 2, 'pid' => 12, 'tstamp' => 2345, 'origin' => 'tl_otherTable', 'originId' => 7, 'simulated' => ''],
        ];

        self::assertEquals($expected, $log->getLog(12));
    }

    public function testGetLogWithOrigin(): void
    {
        $result = $this->createMock(Result::class);
        $result
            ->expects(self::once())
            ->method('fetchAllAssociative')
            ->willReturn([
                ['id' => 1, 'pid' => 12, 'tstamp' => 1234, 'origin' => 'tl_someTable', 'originId' => 4, 'simulated' => ''],
                ['id' => 2, 'pid' => 12, 'tstamp' => 2345, 'origin' => 'tl_someTable', 'originId' => 7, 'simulated' => ''],
            ])
        ;

        $connection = $this->createMock(Connection::class);
        $connection
            ->expects(self::once())
            ->method('executeQuery')
            ->with('SELECT * FROM tl_eblick_trigger_log WHERE pid=? AND origin =?', [12, 'tl_someTable'])
            ->willReturn($result)
        ;

        $log = new ExecutionLog($connection);

        $expected = [
            4 => ['id' => 1, 'pid' => 12, 'tstamp' => 1234, 'origin' => 'tl_someTable', 'originId' => 4, 'simulated' => ''],
            7 => ['id' => 2, 'pid' => 12, 'tstamp' => 2345, 'origin' => 'tl_someTable', 'originId' => 7, 'simulated' => ''],
        ];

        self::assertEquals($expected, $log->getLog(12, 'tl_someTable'));
    }

    public function testAddLogWithoutOriginFails(): void
    {
        $log = new ExecutionLog($this->createMock(Connection::class));

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
