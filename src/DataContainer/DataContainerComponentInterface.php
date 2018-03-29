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

interface DataContainerComponentInterface
{
    /**
     * Return a datacontainer definition that can get merged into the main datacontainer.
     *
     * @return Definition
     */
    public function getDataContainerDefinition(): Definition;
}