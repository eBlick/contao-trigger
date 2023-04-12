<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\Component\Condition;

use EBlick\ContaoTrigger\Execution\ExecutionContext;
use EBlick\ContaoTrigger\Execution\ExecutionException;

interface ConditionInterface
{
    /**
     * Evaluate condition and trigger action(s) if condition is met. Throw an ExecutionException if execution is in a
     * misconfigured state. The fireCallback closure must be used according to the following signature:.
     *
     * fireCallback(array $data = [], int $originId = 0, string $origin = 'tl_eblick_trigger')
     *
     * @param \Closure(array, int, string):void $fireCallback
     *
     * @throws ExecutionException
     */
    public function evaluate(ExecutionContext $context, \Closure $fireCallback): void;

    /**
     * Return a flat array with the same keys that will be transmitted in the data array during execution.
     *
     * e.g. ['myValue' => null, 'myOtherValue' => null]
     */
    public function getDataPrototype(int $triggerId): array;
}
