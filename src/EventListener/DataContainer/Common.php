<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\EventListener\DataContainer;

class Common
{
    public function addBackendCss(): void
    {
        $GLOBALS['TL_CSS'][] = 'bundles/eblickcontaotrigger/css/backend.css';
    }
}
