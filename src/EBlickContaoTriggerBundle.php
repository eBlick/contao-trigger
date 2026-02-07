<?php

declare(strict_types=1);

/*
 * @copyright LUMAS Consulting
 * @license   LGPL-3.0+
 * @link      https://github.com/lumas-consulting/contao-trigger
 */

namespace EBlick\ContaoTrigger;

use EBlick\ContaoTrigger\DependencyInjection\Compiler\AddComponentsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EBlickContaoTriggerBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(
            new AddComponentsCompilerPass(),
        );
    }
}
