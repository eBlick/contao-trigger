<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\EventListener\DataContainer;

use Contao\Config;
use Contao\Date;

class ExecutionLog
{
    public function onGenerateLabel(array $row): string
    {
        $simulated = $row['simulated'] ?
            sprintf(
                ' &nbsp;<span class="trigger-simulated">[%s]</span>',
                $GLOBALS['TL_LANG']['tl_eblick_trigger_log']['simulated'][0]
            ) : '';

        return sprintf(
            '%s &nbsp;(\'%s\' . \'%s\')%s',
            Date::parse(Config::get('datimFormat'), $row['tstamp']),
            $row['origin'],
            $row['originId'],
            $simulated
        );
    }
}
