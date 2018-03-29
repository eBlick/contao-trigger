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

namespace EBlick\ContaoTrigger\Component\Action;

use EBlick\ContaoTrigger\Execution\ExecutionContext;
use EBlick\ContaoTrigger\Execution\ExecutionException;

interface ActionInterface
{
    /**
     * Trigger action execution. Return true if action was executed and the attempt should be saved to the log, false
     * else. Throw an ExecutionException if execution is in a misconfigured state.
     *
     * @param ExecutionContext $context
     * @param array            $data
     *
     * @return bool
     * @throws ExecutionException
     */
    public function fire(ExecutionContext $context, array $data): bool;
}