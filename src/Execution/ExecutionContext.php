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

class ExecutionContext
{
    /** @var \stdClass */
    private $triggerParameters;

    /** @var int */
    private $startTime;

    /** @var ExecutionLog */
    private $executionLog;

    /**
     * ExecutionContext constructor.
     *
     * @param \stdClass    $triggerParameters
     * @param int          $startTime
     * @param ExecutionLog $executionLog
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(\stdClass $triggerParameters, int $startTime, ExecutionLog $executionLog)
    {
        $this->triggerParameters = $triggerParameters;
        $this->startTime         = $startTime;
        $this->executionLog      = $executionLog;

        if (!property_exists($triggerParameters, 'id')) {
            throw new \InvalidArgumentException('Trigger id missing in parameter set.');
        }
    }

    /**
     * Returns a parameter object with all available config parameters of the current trigger.
     *
     * @return \stdClass
     */
    public function getParameters(): \stdClass
    {
        return $this->triggerParameters;
    }

    /**
     * Returns the start time of execution as a timestamp.
     *
     * @return int
     */
    public function getStartTime(): int
    {
        return $this->startTime;
    }

    /**
     * Returns an array of log entries associated with this trigger with keys being the origin ids and values a
     * parameter object of all columns.
     *
     * @param string $origin
     *
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLog(string $origin = 'tl_eblick_trigger'): array
    {
        return $this->executionLog->getLog((int) $this->triggerParameters->id, $origin);
    }

    /**
     * Returns an array of log entries associated with this trigger with keys being the origin ids and values a
     * parameter object of all columns.
     *
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllLogs(): array
    {
        return $this->executionLog->getLog((int) $this->triggerParameters->id, null);
    }

    /**
     * Adds a new log entry for this trigger..
     *
     * @param int    $originId
     * @param string $origin
     * @param bool   $simulated
     *
     * @throws \InvalidArgumentException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function addLog(int $originId, string $origin = 'tl_eblick_trigger', bool $simulated = false): void
    {
        $this->executionLog->addLog((int) $this->triggerParameters->id, $originId, $origin, $simulated);
    }

}