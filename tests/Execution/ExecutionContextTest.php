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

use EBlick\ContaoTrigger\Execution\ExecutionContext;
use EBlick\ContaoTrigger\Execution\ExecutionLog;

class ExecutionContextTest extends \PHPUnit_Framework_TestCase
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
        $parameters     = new \stdClass;
        $parameters->id = 12;

        $obj = new ExecutionContext(
            $parameters,
            1234,
            $this->createMock(ExecutionLog::class)
        );
        $this->assertInstanceOf(ExecutionContext::class, $obj);
    }


    public function testGetParametersAndStartTime(): void
    {
        $parameters            = new \stdClass;
        $parameters->id        = 12;
        $parameters->someValue = 'meow';

        $context = new ExecutionContext(
            $parameters,
            12345,
            $this->createMock(ExecutionLog::class)
        );

        $this->assertEquals($context->getParameters()->id, 12);
        $this->assertEquals($context->getParameters()->someValue, 'meow');
        $this->assertEquals($context->getStartTime(), 12345);
    }


    public function testGetLog(): void
    {
        $parameters     = new \stdClass;
        $parameters->id = 5;

        $data = [2 => [['id' => 4, 'pid' => 5, 'tstamp' => 1234, 'origin' => 'tl_someTable']]];
        $log  = $this->createMock(ExecutionLog::class);
        $log->expects($this->once())
            ->method('getLog')
            ->with(5, 'tl_someTable')
            ->willReturn($data);

        $context = new ExecutionContext($parameters, 15000, $log);
        $this->assertEquals($data, $context->getLog('tl_someTable'));
    }

    public function testAddLog(): void
    {
        $parameters     = new \stdClass;
        $parameters->id = 5;

        $log = $this->createMock(ExecutionLog::class);
        $log->expects($this->once())
            ->method('addLog')
            ->with(5, 61, 'tl_someTable', false);

        $context = new ExecutionContext($parameters, 15000, $log);
        $context->addLog(61, 'tl_someTable', false);
    }
}
