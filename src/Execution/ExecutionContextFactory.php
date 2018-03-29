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

namespace EBlick\ContaoTrigger\Execution;


class ExecutionContextFactory
{
    /** @var ExecutionLog */
     private $executionLog;

    /**
     * ExecutionContextFactory constructor.
     *
     * @param ExecutionLog $executionLog
     */
    public function __construct(ExecutionLog $executionLog)
    {
        $this->executionLog = $executionLog;
    }

    /**
     * @param $triggerParameters
     * @param $startTime
     *
     * @return ExecutionContext
     * @throws \InvalidArgumentException
     */
    public function createExecutionContext($triggerParameters, $startTime): ExecutionContext
    {
        return new ExecutionContext($triggerParameters, $startTime, $this->executionLog);
    }
}