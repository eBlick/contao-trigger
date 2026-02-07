<?php

declare(strict_types=1);

/*
 * @copyright LUMAS Consulting
 * @license   LGPL-3.0+
 * @link      https://github.com/lumas-consulting/contao-trigger
 */

namespace EBlick\ContaoTrigger\Test\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use EBlick\ContaoTrigger\ContaoManager\Plugin;
use EBlick\ContaoTrigger\EBlickContaoTriggerBundle;
use PHPUnit\Framework\TestCase;

class PluginTest extends TestCase
{
    public function testGetBundles(): void
    {
        $plugin = new Plugin();
        $bundles = $plugin->getBundles($this->createMock(ParserInterface::class));

        /** @var BundleConfig $config */
        $config = $bundles[0];

        $this->assertCount(1, $bundles);
        $this->assertInstanceOf(BundleConfig::class, $config);
        $this->assertSame(EBlickContaoTriggerBundle::class, $config->getName());
        $this->assertSame([ContaoCoreBundle::class, 'notification-center'], $config->getLoadAfter());
    }
}
