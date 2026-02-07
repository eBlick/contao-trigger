<?php

declare(strict_types=1);

/*
 * @copyright LUMAS Consulting
 * @license   LGPL-3.0+
 * @link      https://github.com/lumas-consulting/contao-trigger
 */

namespace EBlick\ContaoTrigger\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AddComponentsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $componentManager = $container->getDefinition('eblick_contao_trigger.component.component_manager');

        $this->addToManager($container, $componentManager, 'eblick_contao_trigger.condition', 'addCondition');
        $this->addToManager($container, $componentManager, 'eblick_contao_trigger.action', 'addAction');
    }

    private function addToManager(ContainerBuilder $container, Definition $componentManager, string $tagName, string $method): void
    {
        foreach ($container->findTaggedServiceIds($tagName) as $id => $tags) {
            foreach ($tags as $attributes) {
                $componentManager->addMethodCall($method, [new Reference($id), $attributes['alias']]);
            }
        }
    }
}
