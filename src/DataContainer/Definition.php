<?php

declare(strict_types=1);

/*
 * @copyright LUMAS Consulting
 * @license   LGPL-3.0+
 * @link      https://github.com/lumas-consulting/contao-trigger
 */

namespace EBlick\ContaoTrigger\DataContainer;

class Definition
{
    public function __construct(
        public array $fields = [],
        public string $palette = '',
        public array $selectors = [],
        public array $subPalettes = [],
    ) {
    }
}
