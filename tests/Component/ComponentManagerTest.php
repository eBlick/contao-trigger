<?php

declare(strict_types=1);

/*
 * @copyright LUMAS Consulting
 * @license   LGPL-3.0+
 * @link      https://github.com/lumas-consulting/contao-trigger
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

        $condition = $this->createStub(ConditionInterface::class);
        $manager->addCondition($condition, 'testCondition');

        $this->assertSame($condition, $manager->getCondition('testCondition'));
    }

    public function testAddAndGetAction(): void
    {
        $manager = new ComponentManager();

        $action = $this->createStub(ActionInterface::class);
        $manager->addAction($action, 'testAction');

        $this->assertSame($action, $manager->getAction('testAction'));
    }

    public function testGetConditionNames(): void
    {
        $manager = new ComponentManager();

        $condition1 = $this->createStub(ConditionInterface::class);
        $condition2 = $this->createStub(ConditionInterface::class);
        $manager->addCondition($condition1, 'testCondition1');
        $manager->addCondition($condition2, 'testCondition2');

        $this->assertSame(['testCondition1', 'testCondition2'], $manager->getConditionNames());
    }

    public function testGetActionNames(): void
    {
        $manager = new ComponentManager();

        $action1 = $this->createStub(ActionInterface::class);
        $action2 = $this->createStub(ActionInterface::class);
        $manager->addAction($action1, 'testAction1');
        $manager->addAction($action2, 'testAction2');

        $this->assertSame(['testAction1', 'testAction2'], $manager->getActionNames());
    }
}
