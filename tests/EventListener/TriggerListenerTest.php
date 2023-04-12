<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\Test\EventListener;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use EBlick\ContaoTrigger\Component\ComponentManager;
use EBlick\ContaoTrigger\EventListener\TriggerListener;
use EBlick\ContaoTrigger\Execution\ExecutionContextFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class TriggerListenerTest extends TestCase
{
    public function testOnExecute(): void
    {
        $result = $this->createMock(Result::class);
        $result
            ->expects(self::once())
            ->method('fetchAllAssociative')
            ->willReturn([])
        ;

        $connection = $this->createMock(Connection::class);
        $connection
            ->expects(self::once())
            ->method('executeQuery')
            ->with('SELECT * FROM tl_eblick_trigger WHERE enabled = 1 && error IS NULL')
            ->willReturn($result)
        ;

        $listener = new TriggerListener(
            $this->createMock(ComponentManager::class),
            $connection,
            $this->createMock(LoggerInterface::class),
            $this->createMock(ExecutionContextFactory::class),
            $this->createMock(RequestStack::class)
        );

        \define('TL_MODE', 'FE');
        $listener->onExecute();
    }
}
