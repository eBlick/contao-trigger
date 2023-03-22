<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\DataContainer;

interface DataContainerComponentInterface
{
    /**
     * Return a datacontainer definition that can get merged into the main datacontainer.
     */
    public function getDataContainerDefinition(): Definition;
}
