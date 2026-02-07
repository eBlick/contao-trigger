<?php

declare(strict_types=1);

/*
 * @copyright LUMAS Consulting
 * @license   LGPL-3.0+
 * @link      https://github.com/lumas-consulting/contao-trigger
 */

namespace EBlick\ContaoTrigger\Test\Execution;

use EBlick\ContaoTrigger\Execution\ExecutionContext;
use EBlick\ContaoTrigger\Execution\ExecutionContextFactory;
use EBlick\ContaoTrigger\Execution\ExecutionLog;
use PHPUnit\Framework\TestCase;

class ExecutionContextFactoryTest extends TestCase
{
    public function testCreateExecutionContext(): void
    {
        $factory = new ExecutionContextFactory($this->createStub(ExecutionLog::class));
        $parameters = new \stdClass();
        $parameters->id = 4;

        $context = $factory->createExecutionContext($parameters, 1000);

        $this->assertInstanceOf(ExecutionContext::class, $context);
    }
}
