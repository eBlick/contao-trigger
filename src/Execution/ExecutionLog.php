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

use Doctrine\DBAL\Connection;

class ExecutionLog
{
    /** @var Connection */
    private $database;

    /**
     * ExecutionLog constructor.
     *
     * @param Connection $database
     */
    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    /**
     * @param int    $triggerId
     * @param string $origin
     *
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLog(int $triggerId, string $origin = null): array
    {
        $query  = 'SELECT originId, l.* FROM tl_eblick_trigger_log l WHERE pid=?';
        $params = [$triggerId];

        if (null !== $origin) {
            $query    .= ' AND origin =?';
            $params[] = $origin;
        }

        return $this->database
            ->executeQuery($query, $params)
            ->fetchAll(\PDO::FETCH_GROUP | \PDO::FETCH_OBJ);
    }

    /**
     * @param int    $triggerId
     * @param int    $originId
     * @param string $origin
     *
     * @throws \InvalidArgumentException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function addLog(int $triggerId, int $originId, string $origin): void
    {
        if (!$origin) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Origin can\'t be empty in trigger %s!',
                    $triggerId
                )
            );
        }

        $this->database
            ->executeQuery(
                'INSERT INTO tl_eblick_trigger_log SET pid=?, tstamp=?, originId=?, origin=?',
                [$triggerId, time(), $originId, $origin]
            );
    }
}