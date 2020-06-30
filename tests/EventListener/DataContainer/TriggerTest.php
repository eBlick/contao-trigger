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

namespace EBlick\ContaoTrigger\Test\EventListener\DataContainer;

use Doctrine\DBAL\Connection;
use EBlick\ContaoTrigger\Component\ComponentManager;
use EBlick\ContaoTrigger\EventListener\DataContainer\Trigger;
use EBlick\ContaoTrigger\EventListener\TriggerListener;
use PHPUnit\Framework\TestCase;

class TriggerTest extends TestCase
{
    public function testInstantiation(): void
    {
        $obj = new Trigger(
            $this->createMock(ComponentManager::class),
            $this->createMock(Connection::class),
            $this->createMock(TriggerListener::class)
        );

        $this->assertInstanceOf(Trigger::class, $obj);
    }
}
