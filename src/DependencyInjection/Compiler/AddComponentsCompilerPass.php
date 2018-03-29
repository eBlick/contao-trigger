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

namespace EBlick\ContaoTrigger\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AddComponentsCompilerPass implements CompilerPassInterface
{
    /** @var ContainerBuilder */
    private $container;

    /** @var Definition */
    private $componentManager;

    /**
     * @param ContainerBuilder $container
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function process(ContainerBuilder $container): void
    {
        $this->container        = $container;
        $this->componentManager = $container->getDefinition('eblick_contao_trigger.component.component_manager');

        $this->addToManager('eblick_contao_trigger.condition', 'addCondition');
        $this->addToManager('eblick_contao_trigger.action', 'addAction');
    }

    /**
     * @param string $tagName
     * @param string $method
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    private function addToManager(string $tagName, string $method): void
    {
        foreach ($this->container->findTaggedServiceIds($tagName) as $id => $tags) {
            /** @var array $tags */
            foreach ($tags as $attributes) {
                $this->componentManager->addMethodCall($method, [new Reference($id), $attributes['alias']]);
            }
        }
    }
}