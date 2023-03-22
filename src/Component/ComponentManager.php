<?php

declare(strict_types=1);

namespace EBlick\ContaoTrigger\Component;

use EBlick\ContaoTrigger\Component\Action\ActionInterface;
use EBlick\ContaoTrigger\Component\Condition\ConditionInterface;

class ComponentManager
{
    /**
     * @var array<string, ConditionInterface>
     */
    private array $conditions = [];

    /**
     * @var array<string, ActionInterface>
     */
    private array $actions = [];

    /**
     * Register a condition.
     */
    public function addCondition(ConditionInterface $condition, string $alias): void
    {
        $this->conditions[$alias] = $condition;
    }

    /**
     * Register an action.
     */
    public function addAction(ActionInterface $action, string $name): void
    {
        $this->actions[$name] = $action;
    }

    /**
     * Returns an array of all registered condition names.
     *
     * @return list<string>
     */
    public function getConditionNames(): array
    {
        return array_keys($this->conditions);
    }

    /**
     * Get a certain condition.
     */
    public function getCondition(string $name): ConditionInterface|null
    {
        return $this->conditions[$name] ?? null;
    }

    /**
     * Returns an array of all registered action names.
     *
     * @return list<string>
     */
    public function getActionNames(): array
    {
        return array_keys($this->actions);
    }

    /**
     * Get a certain action.
     */
    public function getAction(string $name): ActionInterface|null
    {
        return $this->actions[$name] ?? null;
    }
}
