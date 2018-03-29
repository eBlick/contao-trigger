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

namespace EBlick\ContaoTrigger\Component;

use EBlick\ContaoTrigger\Component\Action\ActionInterface;
use EBlick\ContaoTrigger\Component\Condition\ConditionInterface;

class ComponentManager
{
    /** @var array */
    private $conditions = [];

    /** @var array */
    private $actions = [];

    /**
     * Register a condition.
     *
     * @param ConditionInterface $condition
     * @param string             $alias
     */
    public function addCondition(ConditionInterface $condition, string $alias): void
    {
        $this->conditions[$alias] = $condition;
    }

    /**
     * Register an action.
     *
     * @param ActionInterface $action
     * @param string          $name
     */
    public function addAction(ActionInterface $action, string $name): void
    {
        $this->actions[$name] = $action;
    }

    /**
     * Returns an array of all registered condition names.
     *
     * @return array
     */
    public function getConditionNames(): array
    {
        return \array_keys($this->conditions);
    }

    /**
     * Get a certain condition.
     *
     * @param $name
     *
     * @return ConditionInterface|null
     */
    public function getCondition($name): ?ConditionInterface
    {
        if (!\array_key_exists($name, $this->conditions)) {
            return null;
        }

        return $this->conditions[$name];
    }

    /**
     * Returns an array of all registered action names.
     *
     * @return array
     */
    public function getActionNames(): array
    {
        return \array_keys($this->actions);
    }

    /**
     * Get a certain action.
     *
     * @param $name
     *
     * @return ActionInterface|null
     */
    public function getAction($name): ?ActionInterface
    {
        if (!\array_key_exists($name, $this->actions)) {
            return null;
        }

        return $this->actions[$name];
    }
}