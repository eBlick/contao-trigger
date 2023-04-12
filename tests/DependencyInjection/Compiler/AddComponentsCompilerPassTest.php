<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\Test\DependencyInjection\Compiler;

use EBlick\ContaoTrigger\Component\ComponentManager;
use EBlick\ContaoTrigger\DependencyInjection\Compiler\AddComponentsCompilerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AddComponentsCompilerPassTest extends TestCase
{
    public function testProcess(): void
    {
        $componentManager = $this->createMock(Definition::class);
        $componentManager
            ->expects(self::exactly(2))
            ->method('addMethodCall')
            ->withConsecutive(
                ['addCondition', [new Reference('testTag1'), 'testAlias1']],
                ['addAction', [new Reference('testTag2'), 'testAlias2']]
            )
        ;

        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects(self::once())
            ->method('getDefinition')
            ->with('eblick_contao_trigger.component.component_manager')
            ->willReturn($componentManager)
        ;

        $container
            ->expects(self::exactly(2))
            ->method('findTaggedServiceIds')
            ->withConsecutive(['eblick_contao_trigger.condition'], ['eblick_contao_trigger.action'])
            ->willReturnOnConsecutiveCalls(
                ['testTag1' => [['alias' => 'testAlias1']]],
                ['testTag2' => [['alias' => 'testAlias2']]]
            )
        ;

        $compilerPass = new AddComponentsCompilerPass();
        $compilerPass->process($container);

        self::assertTrue(method_exists(ComponentManager::class, 'addCondition'));
        self::assertTrue(method_exists(ComponentManager::class, 'addAction'));
    }
}
