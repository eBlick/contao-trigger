<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\DataContainer;

class Definition
{
    public function __construct(public array $fields = [], public string $palette = '', public array $selectors = [], public array $subPalettes = [])
    {
    }
}
