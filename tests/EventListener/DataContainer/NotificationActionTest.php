<?php

declare(strict_types=1);

/*
 * @copyright LUMAS Consulting
 * @license   LGPL-3.0+
 * @link      https://github.com/lumas-consulting/contao-trigger
 */

namespace EBlick\ContaoTrigger\Test\EventListener\DataContainer;

use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use EBlick\ContaoTrigger\Component\ComponentManager;
use EBlick\ContaoTrigger\Component\Condition\ConditionInterface;
use EBlick\ContaoTrigger\EventListener\DataContainer\NotificationAction;
use EBlick\ContaoTrigger\NotificationCenter\TriggerNotificationType;
use PHPUnit\Framework\TestCase;
use Terminal42\NotificationCenterBundle\NotificationCenter;

class NotificationActionTest extends TestCase
{
    public function testOnGetTokenList(): void
    {
        $GLOBALS['TL_LANG']['tl_eblick_trigger']['action_notification_tokens'] = '';

        $result = $this->createMock(Result::class);
        $result
            ->expects($this->once())
            ->method('fetchOne')
            ->willReturn('testCondition')
        ;

        $connection = $this->createMock(Connection::class);
        $connection
            ->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT condition_type FROM tl_eblick_trigger WHERE id = ?', [9])
            ->willReturn($result)
        ;

        $condition = $this->createMock(ConditionInterface::class);
        $condition
            ->expects($this->once())
            ->method('getDataPrototype')
            ->with(9)
            ->willReturn(['testColumn1' => null, 'testColumn2' => null])
        ;

        $componentManager = $this->createMock(ComponentManager::class);
        $componentManager
            ->expects($this->once())
            ->method('getCondition')
            ->with('testCondition')
            ->willReturn($condition)
        ;

        $action = new NotificationAction($componentManager, $connection, $this->createStub(NotificationCenter::class));

        $dc = $this->createStub(DataContainer::class);
        $dc
            ->method('__get')
            ->with('id')
            ->willReturn(9)
        ;

        $this->assertStringContainsString(
            '##trigger_id##, ##trigger_title##, ##trigger_startTime##, ##data_testColumn1##, ##data_testColumn2##',
            $action->onGetTokenList($dc),
        );
    }

    public function testGetNotificationChoices(): void
    {
        $notificationCenter = $this->createStub(NotificationCenter::class);
        $notificationCenter
            ->method('getNotificationsForNotificationType')
            ->with(TriggerNotificationType::NAME)
            ->willReturn([42 => 'notification'])
        ;

        $action = new NotificationAction(
            $this->createStub(ComponentManager::class),
            $this->createStub(Connection::class),
            $notificationCenter,
        );

        $this->assertSame([42 => 'notification'], $action->getNotificationChoices());
    }
}
