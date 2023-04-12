<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\Test\Component;

use EBlick\ContaoTrigger\Component\Action\ActionInterface;
use EBlick\ContaoTrigger\Component\ComponentManager;
use EBlick\ContaoTrigger\Component\Condition\ConditionInterface;
use PHPUnit\Framework\TestCase;

class ComponentManagerTest extends TestCase
{
    public function testAddAndGetCondition(): void
    {
        $manager = new ComponentManager();

        $condition = $this->createMock(ConditionInterface::class);
        $manager->addCondition($condition, 'testCondition');

        self::assertEquals($condition, $manager->getCondition('testCondition'));
    }

    public function testAddAndGetAction(): void
    {
        $manager = new ComponentManager();

        $action = $this->createMock(ActionInterface::class);
        $manager->addAction($action, 'testAction');

        self::assertEquals($action, $manager->getAction('testAction'));
    }

    public function testGetConditionNames(): void
    {
        $manager = new ComponentManager();

        $condition1 = $this->createMock(ConditionInterface::class);
        $condition2 = $this->createMock(ConditionInterface::class);
        $manager->addCondition($condition1, 'testCondition1');
        $manager->addCondition($condition2, 'testCondition2');

        self::assertEquals(['testCondition1', 'testCondition2'], $manager->getConditionNames());
    }

    public function testGetActionNames(): void
    {
        $manager = new ComponentManager();

        $action1 = $this->createMock(ActionInterface::class);
        $action2 = $this->createMock(ActionInterface::class);
        $manager->addAction($action1, 'testAction1');
        $manager->addAction($action2, 'testAction2');

        self::assertEquals(['testAction1', 'testAction2'], $manager->getActionNames());
    }
}
