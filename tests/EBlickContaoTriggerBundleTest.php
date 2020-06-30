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

namespace EBlick\ContaoTrigger\Test;

use EBlick\ContaoTrigger\DependencyInjection\Compiler\AddComponentsCompilerPass;
use EBlick\ContaoTrigger\EBlickContaoTriggerBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EBlickContaoTriggerBundleTest extends TestCase
{
    public function testInstantiation(): void
    {
        $obj = new EBlickContaoTriggerBundle();
        $this->assertInstanceOf(EBlickContaoTriggerBundle::class, $obj);
    }

    public function testBuild(): void
    {
        $containerBuilder = $this->createMock(ContainerBuilder::class);
        $containerBuilder->expects($this->once())
            ->method('addCompilerPass')
            ->with(new AddComponentsCompilerPass());

        $bundle = new EBlickContaoTriggerBundle();
        $bundle->build($containerBuilder);
    }
}
