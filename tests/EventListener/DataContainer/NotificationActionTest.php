<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\Test\EventListener\DataContainer;

use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use EBlick\ContaoTrigger\Component\ComponentManager;
use EBlick\ContaoTrigger\Component\Condition\ConditionInterface;
use EBlick\ContaoTrigger\EventListener\DataContainer\NotificationAction;
use PHPUnit\Framework\TestCase;

class NotificationActionTest extends TestCase
{
    public function testOnGetTokenList(): void
    {
        $GLOBALS['TL_LANG']['tl_eblick_trigger']['action_notification_tokens'] = '';

        $result = $this->createMock(Result::class);
        $result
            ->expects(self::once())
            ->method('fetchOne')
            ->willReturn('testCondition')
        ;

        $connection = $this->createMock(Connection::class);
        $connection
            ->expects(self::once())
            ->method('executeQuery')
            ->with('SELECT condition_type FROM tl_eblick_trigger WHERE id = ?', [9])
            ->willReturn($result)
        ;

        $condition = $this->createMock(ConditionInterface::class);
        $condition
            ->expects(self::once())
            ->method('getDataPrototype')
            ->with(9)
            ->willReturn(['testColumn1' => null, 'testColumn2' => null])
        ;

        $componentManager = $this->createMock(ComponentManager::class);
        $componentManager
            ->expects(self::once())
            ->method('getCondition')
            ->with('testCondition')
            ->willReturn($condition)
        ;

        $action = new NotificationAction($componentManager, $connection);

        $dc = $this->createMock(DataContainer::class);
        $dc
            ->method('__get')
            ->with('id')
            ->willReturn(9)
        ;

        self::assertStringContainsString(
            '##trigger_id##, ##trigger_title##, ##trigger_startTime##, ##data_testColumn1##, ##data_testColumn2##',
            $action->onGetTokenList($dc)
        );
    }

    public function testGetNotificationChoices(): void
    {
        $data = [4 => 'firstTitle', 63 => 'secondTitle'];

        $result = $this->createMock(Result::class);
        $result
            ->expects(self::once())
            ->method('fetchAllKeyValue')
            ->willReturn($data)
        ;

        $connection = $this->createMock(Connection::class);
        $connection
            ->expects(self::once())
            ->method('executeQuery')
            ->with("SELECT id, title FROM tl_nc_notification WHERE type='eblick_notification_action' ORDER BY title")
            ->willReturn($result)
        ;

        $action = new NotificationAction(
            $this->createMock(ComponentManager::class),
            $connection
        );

        self::assertEquals($data, $action->getNotificationChoices());
    }
}
