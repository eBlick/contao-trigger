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

namespace EBlick\ContaoTrigger\EventListener\DataContainer;

class ExecutionLog
{
    /**
     * @param array $row
     *
     * @return string
     */
    public function onGenerateLabel(array $row): string
    {
        return sprintf(
            '%s &nbsp;(\'%s\' . \'%s\')',
            \Date::parse(\Config::get('datimFormat'),$row['tstamp']),
            $row['origin'],
            $row['originId']
        );
    }
}