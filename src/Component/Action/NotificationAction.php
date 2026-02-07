<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\Component\Action;

use EBlick\ContaoTrigger\DataContainer\DataContainerComponentInterface;
use EBlick\ContaoTrigger\DataContainer\Definition;
use EBlick\ContaoTrigger\Execution\ExecutionContext;
use EBlick\ContaoTrigger\Execution\ExecutionException;
use Terminal42\NotificationCenterBundle\NotificationCenter;

class NotificationAction implements ActionInterface, DataContainerComponentInterface
{
    public function __construct(private readonly NotificationCenter|null $notificationCenter)
    {
    }

    public function fire(ExecutionContext $context, array $data): bool
    {
        if (!$this->notificationCenter) {
            throw new ExecutionException('Notification Center not found! This extension is needed in order to run this trigger.');
        }

        $trigger = $context->getParameters();

        if (null !== ($id = $trigger->act_notification_entity)) {
            $tokens = [
                'trigger_id' => $trigger->id,
                'trigger_title' => $trigger->title,
                'trigger_startTime' => $context->getStartTime(),
                ...$this->prepareData($data),
            ];

            $this->notificationCenter->sendNotification($id, $tokens);

            return true;
        }

        return false;
    }

    public function getDataContainerDefinition(): Definition
    {
        $palette = 'act_notification_entity,act_notification_tokenList';

        $fields = [
            'act_notification_entity' => [
                'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['action_notification_entity'],
                'exclude' => true,
                'inputType' => 'select',
                'options_callback' => [
                    'eblick_contao_trigger.listener.datacontainer.notification_action',
                    'getNotificationChoices',
                ],
                'eval' => [
                    'mandatory' => true,
                    'includeBlankOption' => true,
                    'chosen' => true,
                    'tl_class' => 'w50',
                ],
                'sql' => "int(10) unsigned NOT NULL default '0'",
            ],
            'act_notification_tokenList' => [
                'exclude' => true,
                'input_field_callback' => [
                    'eblick_contao_trigger.listener.datacontainer.notification_action',
                    'onGetTokenList',
                ],
            ],
        ];

        return new Definition($fields, $palette);
    }

    /**
     * Prefix keys with 'data_'.
     */
    private function prepareData(array $rawData): array
    {
        $data = [];

        foreach ($rawData as $k => $v) {
            $data['data_'.$k] = $v;
        }

        return $data;
    }
}
