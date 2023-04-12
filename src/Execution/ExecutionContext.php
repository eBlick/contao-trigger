<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\Execution;

class ExecutionContext
{
    public function __construct(private \stdClass $triggerParameters, private int $startTime, private ExecutionLog $executionLog)
    {
        if (!property_exists($triggerParameters, 'id')) {
            throw new \InvalidArgumentException('Trigger id missing in parameter set.');
        }
    }

    /**
     * Returns a parameter object with all available config parameters of the current trigger.
     */
    public function getParameters(): \stdClass
    {
        return $this->triggerParameters;
    }

    /**
     * Returns the start time of execution as a timestamp.
     */
    public function getStartTime(): int
    {
        return $this->startTime;
    }

    /**
     * Returns an array of log entries associated with this trigger with keys being the origin ids and values a
     * parameter object of all columns.
     */
    public function getLog(string $origin = 'tl_eblick_trigger'): array
    {
        return $this->executionLog->getLog((int) $this->triggerParameters->id, $origin);
    }

    /**
     * Returns an array of log entries associated with this trigger with keys being the origin ids and values a
     * parameter object of all columns.
     */
    public function getAllLogs(): array
    {
        return $this->executionLog->getLog((int) $this->triggerParameters->id);
    }

    /**
     * Adds a new log entry for this trigger.
     */
    public function addLog(int $originId, string $origin = 'tl_eblick_trigger', bool $simulated = false): void
    {
        $this->executionLog->addLog((int) $this->triggerParameters->id, $originId, $origin, $simulated);
    }
}
