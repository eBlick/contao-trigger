<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AddComponentsCompilerPass implements CompilerPassInterface
{
    private ContainerBuilder $container;
    private Definition $componentManager;

    public function process(ContainerBuilder $container): void
    {
        $this->container = $container;
        $this->componentManager = $container->getDefinition('eblick_contao_trigger.component.component_manager');

        $this->addToManager('eblick_contao_trigger.condition', 'addCondition');
        $this->addToManager('eblick_contao_trigger.action', 'addAction');
    }

    private function addToManager(string $tagName, string $method): void
    {
        foreach ($this->container->findTaggedServiceIds($tagName) as $id => $tags) {
            foreach ($tags as $attributes) {
                $this->componentManager->addMethodCall($method, [new Reference($id), $attributes['alias']]);
            }
        }
    }
}
