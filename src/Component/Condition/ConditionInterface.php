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

namespace EBlick\ContaoTrigger\Component\Condition;

use Closure;
use EBlick\ContaoTrigger\Execution\ExecutionContext;
use EBlick\ContaoTrigger\Execution\ExecutionException;

interface ConditionInterface
{
    /**
     * Evaluate condition and trigger action(s) if condition is met. Throw an ExecutionException if execution is in a
     * misconfigured state. The fireCallback closure must be used according to the following signature:
     *
     * fireCallback(array $data = [], int $originId = 0, string $origin = 'tl_eblick_trigger')
     *
     * @param ExecutionContext $context
     * @param Closure          $fireCallback
     *
     * @throws ExecutionException
     */
    public function evaluate(ExecutionContext $context, Closure $fireCallback): void;


    /**
     * Return a flat array with the same keys that will be transmitted in the data array during execution.
     *
     * e.g. ['myValue' => null, 'myOtherValue' => null]
     *
     * @param int $triggerId
     *
     * @return array
     */
    public function getDataPrototype(int $triggerId): array;
}