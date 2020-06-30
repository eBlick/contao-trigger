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
use EBlick\ContaoTrigger\Execution\ExecutionContextFactory;
use EBlick\ContaoTrigger\Execution\ExecutionLog;
use PHPUnit\Framework\TestCase;

class ExecutionContextFactoryTest extends TestCase
{
    public function testInstantiation(): void
    {
        $obj = new ExecutionContextFactory($this->createMock(ExecutionLog::class));
        $this->assertInstanceOf(ExecutionContextFactory::class, $obj);
    }

    public function testCreateExecutionContext(): void
    {
        $factory = new ExecutionContextFactory($this->createMock(ExecutionLog::class));
        $parameters = new \stdClass;
        $parameters->id = 4;

        $context = $factory->createExecutionContext($parameters, 1000);

        $this->assertInstanceOf(ExecutionContext::class, $context);
    }
}
