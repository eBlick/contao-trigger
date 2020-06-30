<?php

declare(strict_types=1);

/*
 * Trigger Framework Bundle for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2018, eBlick Medienberatung
 * @license    LGPL-3.0+
 * @link       https://github.com/eBlick/contao-trigger
 *
 * @author     Moritz Vondano
 */

namespace EBlick\ContaoTrigger\Test\Execution;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use EBlick\ContaoTrigger\Execution\ExecutionLog;
use PHPUnit\Framework\TestCase;

class ExecutionLogTest extends TestCase
{
    public function testInstantiation(): void
    {
        $obj = new ExecutionLog($this->createMock(Connection::class));
        $this->assertInstanceOf(ExecutionLog::class, $obj);
    }

    public function testGetLogWithoutOrigin(): void
    {
        $data = [2 => [['id' => 4, 'pid' => 5, 'tstamp' => 1234, 'origin' => 'tl_someTable']]];

        $statement = $this->createMock(Statement::class);
        $statement->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_GROUP | \PDO::FETCH_OBJ)
            ->willReturn($data);

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT originId, l.* FROM tl_eblick_trigger_log l WHERE pid=?', [12])
            ->willReturn($statement);

        $log = new ExecutionLog($connection);
        $this->assertEquals($data, $log->getLog(12));
    }

    public function testGetLogWithOrigin(): void
    {
        $data = [2 => [['id' => 4, 'pid' => 5, 'tstamp' => 1234, 'origin' => 'tl_someTable']]];

        $statement = $this->createMock(Statement::class);
        $statement->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_GROUP | \PDO::FETCH_OBJ)
            ->willReturn($data);

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT originId, l.* FROM tl_eblick_trigger_log l WHERE pid=? AND origin =?', [12, 'tl_someTable'])
            ->willReturn($statement);

        $log = new ExecutionLog($connection);
        $this->assertEquals($data, $log->getLog(12, 'tl_someTable'));
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
        $connection->expects($this->once())
            ->method('executeQuery')
            ->with(
                'INSERT INTO tl_eblick_trigger_log SET pid=?, tstamp=?, originId=?, origin=?, simulated=?',
                $this->anything()
            );

        $log = new ExecutionLog($connection);
        $log->addLog(12, 5, 'tl_someTable', false);
    }
}
