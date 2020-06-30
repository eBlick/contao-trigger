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

use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use EBlick\ContaoTrigger\Component\ComponentManager;
use EBlick\ContaoTrigger\Component\Condition\ConditionInterface;
use EBlick\ContaoTrigger\EventListener\DataContainer\NotificationAction;
use PHPUnit\Framework\TestCase;

class NotificationActionTest extends TestCase
{
    public function testInstantiation(): void
    {
        $obj = new NotificationAction(
            $this->createMock(ComponentManager::class),
            $this->createMock(Connection::class)
        );

        $this->assertInstanceOf(NotificationAction::class, $obj);
    }


    public function testOnGetTokenList(): void
    {
        $GLOBALS['TL_LANG']['tl_eblick_trigger']['action_notification_tokens'] = '';

        $statement = $this->createMock(Statement::class);
        $statement->expects($this->once())
            ->method('fetch')
            ->with(\PDO::FETCH_COLUMN)
            ->willReturn('testCondition');

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT condition_type FROM tl_eblick_trigger WHERE id = ?', [9])
            ->willReturn($statement);

        $condition =  $this->createMock(ConditionInterface::class);
        $condition->expects($this->once())
            ->method('getDataPrototype')
            ->with(9)
            ->willReturn(['testColumn1' => null, 'testColumn2' => null]);

        $componentManager = $this->createMock(ComponentManager::class);
        $componentManager->expects($this->once())
            ->method('getCondition')
            ->with('testCondition')
            ->willReturn($condition);

        $action = new NotificationAction($componentManager, $connection);

        $dc     = $this->createMock(DataContainer::class);
        $dc->method('__get')->with('id')->willReturn(9);

        $result = $action->onGetTokenList($dc);
        $part   = '##trigger_id##, ##trigger_title##, ##trigger_startTime##, ##data_testColumn1##, ##data_testColumn2##';

        $this->assertStringContainsString($part, $result);
    }

    public function testGetNotificationChoices(): void
    {
        $data = [4 => 'firstTitle', 63 => 'secondTitle'];

        $statement = $this->createMock(Statement::class);
        $statement->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_KEY_PAIR)
            ->willReturn($data);

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('executeQuery')
            ->with("SELECT id, title FROM tl_nc_notification WHERE type='eblick_notification_action' ORDER BY title")
            ->willReturn($statement);

        $action = new NotificationAction(
            $this->createMock(ComponentManager::class),
            $connection
        );

        $this->assertEquals($data, $action->getNotificationChoices());

    }
}
