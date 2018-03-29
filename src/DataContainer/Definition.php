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

namespace EBlick\ContaoTrigger\DataContainer;

class Definition
{
    /** @var array $fields */
    public $fields;

    /** @var string $palette */
    public $palette;

    /** @var array $selectors */
    public $selectors;

    /** @var array $subPalettes */
    public $subPalettes;

    /**
     * Definition constructor.
     *
     * @param array  $fields
     * @param string $palette
     * @param array  $selectors
     * @param array  $subPalettes
     */
    public function __construct(array $fields = [], string $palette = '', array $selectors = [], array $subPalettes = [])
    {
        $this->fields      = $fields;
        $this->palette     = $palette;
        $this->selectors   = $selectors;
        $this->subPalettes = $subPalettes;
    }
}