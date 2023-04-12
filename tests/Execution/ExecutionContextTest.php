<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\Test\Execution;

use EBlick\ContaoTrigger\Execution\ExecutionContext;
use EBlick\ContaoTrigger\Execution\ExecutionLog;
use PHPUnit\Framework\TestCase;

class ExecutionContextTest extends TestCase
{
    public function testInstantiationFailsWithoutId(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ExecutionContext(
            new \stdClass(),
            1234,
            $this->createMock(ExecutionLog::class)
        );
    }

    public function testInstantiationWithId(): void
    {
        $parameters = new \stdClass();
        $parameters->id = 12;

        $obj = new ExecutionContext(
            $parameters,
            1234,
            $this->createMock(ExecutionLog::class)
        );
        self::assertInstanceOf(ExecutionContext::class, $obj);
    }

    public function testGetParametersAndStartTime(): void
    {
        $parameters = new \stdClass();
        $parameters->id = 12;
        $parameters->someValue = 'meow';

        $context = new ExecutionContext(
            $parameters,
            12345,
            $this->createMock(ExecutionLog::class)
        );

        self::assertEquals($context->getParameters()->id, 12);
        self::assertEquals($context->getParameters()->someValue, 'meow');
        self::assertEquals($context->getStartTime(), 12345);
    }

    public function testGetLog(): void
    {
        $parameters = new \stdClass();
        $parameters->id = 5;

        $data = [2 => [['id' => 4, 'pid' => 5, 'tstamp' => 1234, 'origin' => 'tl_someTable']]];
        $log = $this->createMock(ExecutionLog::class);
        $log
            ->expects(self::once())
            ->method('getLog')
            ->with(5, 'tl_someTable')
            ->willReturn($data)
        ;

        $context = new ExecutionContext($parameters, 15000, $log);
        self::assertEquals($data, $context->getLog('tl_someTable'));
    }

    public function testAddLog(): void
    {
        $parameters = new \stdClass();
        $parameters->id = 5;

        $log = $this->createMock(ExecutionLog::class);
        $log
            ->expects(self::once())
            ->method('addLog')
            ->with(5, 61, 'tl_someTable', false)
        ;

        $context = new ExecutionContext($parameters, 15000, $log);
        $context->addLog(61, 'tl_someTable', false);
    }
}
