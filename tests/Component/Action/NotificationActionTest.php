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

namespace EBlick\ContaoTrigger\Test\Component\Action;

use Contao\TestCase\ContaoTestCase;
use EBlick\ContaoTrigger\Component\Action\NotificationAction;
use EBlick\ContaoTrigger\DataContainer\Definition;
use EBlick\ContaoTrigger\Execution\ExecutionContext;

use EBlick\ContaoTrigger\Execution\ExecutionLog;
use NotificationCenter\Model\Notification;


class NotificationActionTest extends ContaoTestCase
{
    public function testInstantiation(): void
    {
        $obj = new NotificationAction();
        $this->assertInstanceOf(NotificationAction::class, $obj);
    }

    private function getMockedAction($preparedData, $notificationId): NotificationAction
    {
        $notification = $this->mockAdapter(['send']);
        $notification
            ->expects($this->once())
            ->method('send')
            ->with($preparedData)
            ->willReturn(true);

        $notificationAdapter = $this->mockAdapter(['findByPk']);
        $notificationAdapter
            ->expects($this->once())
            ->method('findByPk')
            ->with($notificationId)
            ->willReturn($notification);

        $framework = $this->mockContaoFramework([Notification::class => $notificationAdapter]);

        $action = new NotificationAction();
        $action->setFramework($framework);

        return $action;
    }

    public function testFireWithoutData(): void
    {
        $parameters = new \stdClass();

        $parameters->id                      = 6;
        $parameters->title                   = 'testTrigger1';
        $parameters->act_notification_entity = 24;

        $context = new ExecutionContext(
            $parameters,
            159800,
            $this->createMock(ExecutionLog::class)
        );

        $data = [];

        $preparedData = [
            'trigger_id'            => 6,
            'trigger_title'         => 'testTrigger1',
            'trigger_startTime' => 159800,
        ];

        $action = $this->getMockedAction($preparedData, 24);
        $this->assertTrue($action->fire($context, $data));
    }

    public function testFireWithCustomData(): void
    {
        $parameters = new \stdClass();

        $parameters->id                      = 11;
        $parameters->title                   = 'testTrigger2';
        $parameters->act_notification_entity = 24;

        $context = new ExecutionContext(
            $parameters,
            123456,
            $this->createMock(ExecutionLog::class)
        );

        $data = [
            'some_value'  => 4,
            'other_value' => 'yes'
        ];

        $preparedData = [
            'trigger_id'        => 11,
            'trigger_title'     => 'testTrigger2',
            'trigger_startTime' => 123456,
            'data_some_value'   => 4,
            'data_other_value'  => 'yes'
        ];

        $action = $this->getMockedAction($preparedData, 24);
        $this->assertTrue($action->fire($context, $data));
    }

    public function testGetDataContainerDefinition(): void
    {
        $obj = new NotificationAction();

        $definition = $obj->getDataContainerDefinition();
        $this->assertInstanceOf(Definition::class, $definition);

        $this->assertCount(0, $definition->selectors);
        $this->assertCount(0, $definition->subPalettes);
        $this->assertCount(2, $definition->fields);
        $this->assertSame('act_notification_entity,act_notification_tokenList', $definition->palette);
    }
}
