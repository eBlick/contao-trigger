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

namespace EBlick\ContaoTrigger\Util;

use Contao\Date;

class InsertTags
{
    /**
     * Insert tag 'parse_date', use like:
     *  {{parse_date[::timestamp[::format]]}}
     *
     * Examples:
     *
     *  {{parse_date}}
     *   --> parses current date/time in global date/time format
     *
     *  {{parse_date::1522531234}}
     *   --> parses given timestamp in global date/time format
     *
     *  {{parse_date::1522531234::d.m.Y H:i}}
     *   --> parses given timestamp in given date/time format
     *
     * @param string $tag
     *
     * @return bool|string
     */
    public function onReplaceInsertTags(string $tag)
    {
        $parts = explode('::', $tag);

        if ('parse_date' !== $parts[0]) {
            return false;
        }

        $count = \count($parts);
        return Date::parse(
            $count > 2 ? $parts[2] : \Config::get('datimFormat'),
            $count > 1 ? $parts[1] : time()
        );
    }
}