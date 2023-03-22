<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\Execution;

class ExecutionContextFactory
{
    public function __construct(private ExecutionLog $executionLog)
    {
    }

    public function createExecutionContext(\stdClass $triggerParameters, int $startTime): ExecutionContext
    {
        return new ExecutionContext($triggerParameters, $startTime, $this->executionLog);
    }
}
