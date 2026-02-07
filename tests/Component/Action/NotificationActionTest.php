<?php

declare(strict_types=1);

/*
 * @copyright LUMAS Consulting
 * @license   LGPL-3.0+
 * @link      https://github.com/lumas-consulting/contao-trigger
 */

namespace EBlick\ContaoTrigger\Test\Component\Action;

use Contao\TestCase\ContaoTestCase;
use EBlick\ContaoTrigger\Component\Action\NotificationAction;
use EBlick\ContaoTrigger\Execution\ExecutionContext;
use EBlick\ContaoTrigger\Execution\ExecutionLog;
use Terminal42\NotificationCenterBundle\NotificationCenter;

class NotificationActionTest extends ContaoTestCase
{
    public function testFireWithoutData(): void
    {
        $parameters = new \stdClass();

        $parameters->id = 6;
        $parameters->title = 'testTrigger1';
        $parameters->act_notification_entity = 24;

        $context = new ExecutionContext(
            $parameters,
            159800,
            $this->createStub(ExecutionLog::class),
        );

        $data = [];

        $preparedData = [
            'trigger_id' => 6,
            'trigger_title' => 'testTrigger1',
            'trigger_startTime' => 159800,
        ];

        $action = $this->getAction($preparedData, 24);
        $this->assertTrue($action->fire($context, $data));
    }

    public function testFireWithCustomData(): void
    {
        $parameters = new \stdClass();

        $parameters->id = 11;
        $parameters->title = 'testTrigger2';
        $parameters->act_notification_entity = 24;

        $context = new ExecutionContext(
            $parameters,
            123456,
            $this->createStub(ExecutionLog::class),
        );

        $data = [
            'some_value' => 4,
            'other_value' => 'yes',
        ];

        $preparedData = [
            'trigger_id' => 11,
            'trigger_title' => 'testTrigger2',
            'trigger_startTime' => 123456,
            'data_some_value' => 4,
            'data_other_value' => 'yes',
        ];

        $action = $this->getAction($preparedData, 24);
        $this->assertTrue($action->fire($context, $data));
    }

    public function testGetDataContainerDefinition(): void
    {
        $obj = new NotificationAction($this->createStub(NotificationCenter::class));

        $definition = $obj->getDataContainerDefinition();

        $this->assertCount(0, $definition->selectors);
        $this->assertCount(0, $definition->subPalettes);
        $this->assertCount(2, $definition->fields);
        $this->assertSame('act_notification_entity,act_notification_tokenList', $definition->palette);
    }

    private function getAction(array $tokens, int $notificationId): NotificationAction
    {
        $notificationCenter = $this->createMock(NotificationCenter::class);
        $notificationCenter
            ->expects($this->once())
            ->method('sendNotification')
            ->with($notificationId, $tokens)
        ;

        return new NotificationAction($notificationCenter);
    }
}
