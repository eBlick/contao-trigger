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

namespace EBlick\ContaoTrigger\Test\EventListener;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use EBlick\ContaoTrigger\Component\ComponentManager;
use EBlick\ContaoTrigger\EventListener\TriggerListener;
use EBlick\ContaoTrigger\Execution\ExecutionContextFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class TriggerListenerTest extends TestCase
{
    public function testInstantiation(): void
    {
        $obj = new TriggerListener(
            $this->createMock(ComponentManager::class),
            $this->createMock(Connection::class),
            $this->createMock(LoggerInterface::class),
            $this->createMock(ExecutionContextFactory::class),
            $this->createMock(RequestStack::class)
        );

        $this->assertInstanceOf(TriggerListener::class, $obj);
    }

    public function testOnExecute(): void
    {
        $statement = $this->createMock(Statement::class);
        $statement->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_OBJ)
            ->willReturn([]);

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT * FROM tl_eblick_trigger WHERE enabled = 1 && error IS NULL')
            ->willReturn($statement);

        $listener = new TriggerListener(
            $this->createMock(ComponentManager::class),
            $connection,
            $this->createMock(LoggerInterface::class),
            $this->createMock(ExecutionContextFactory::class),
            $this->createMock(RequestStack::class)
        );

        define('TL_MODE', 'FE');
        $listener->onExecute();

        // todo test actual execution
    }
}
