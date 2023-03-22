<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\Execution;

use Doctrine\DBAL\Connection;

class ExecutionLog
{
    public function __construct(private Connection $connection)
    {
    }

    public function getLog(int $triggerId, string $origin = null): array
    {
        $query = 'SELECT originId, l.* FROM tl_eblick_trigger_log l WHERE pid=?';
        $params = [$triggerId];

        if (null !== $origin) {
            $query .= ' AND origin =?';
            $params[] = $origin;
        }

        return $this->connection
            ->executeQuery($query, $params)
            ->fetchAllAssociative()
        ;
    }

    public function addLog(int $triggerId, int $originId, string $origin, bool $simulated): void
    {
        if (!$origin) {
            throw new \InvalidArgumentException(sprintf('Origin can\'t be empty in trigger %s!', $triggerId));
        }

        $this->connection
            ->executeQuery(
                'INSERT INTO tl_eblick_trigger_log SET pid=?, tstamp=?, originId=?, origin=?, simulated=?',
                [$triggerId, time(), $originId, $origin, $simulated]
            )
        ;
    }
}
