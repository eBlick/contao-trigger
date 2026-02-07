<?php

declare(strict_types=1);

/*
 * @copyright LUMAS Consulting
 * @license   LGPL-3.0+
 * @link      https://github.com/lumas-consulting/contao-trigger
 */

namespace EBlick\ContaoTrigger\Test;

use EBlick\ContaoTrigger\DependencyInjection\Compiler\AddComponentsCompilerPass;
use EBlick\ContaoTrigger\EBlickContaoTriggerBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EBlickContaoTriggerBundleTest extends TestCase
{
    public function testBuild(): void
    {
        $containerBuilder = $this->createMock(ContainerBuilder::class);
        $containerBuilder
            ->expects($this->once())
            ->method('addCompilerPass')
            ->with(new AddComponentsCompilerPass())
        ;

        $bundle = new EBlickContaoTriggerBundle();
        $bundle->build($containerBuilder);
    }
}
