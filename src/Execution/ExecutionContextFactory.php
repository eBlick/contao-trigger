<?php

declare(strict_types=1);

/*
 * @copyright LUMAS Consulting
 * @license   LGPL-3.0+
 * @link      https://github.com/lumas-consulting/contao-trigger
 */

namespace EBlick\ContaoTrigger\Execution;

class ExecutionContextFactory
{
    public function __construct(private readonly ExecutionLog $executionLog)
    {
    }

    public function createExecutionContext(\stdClass $triggerParameters, int $startTime): ExecutionContext
    {
        return new ExecutionContext($triggerParameters, $startTime, $this->executionLog);
    }
}
