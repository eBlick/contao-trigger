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

namespace EBlick\ContaoTrigger\Component\Action;

use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\Model;
use EBlick\ContaoTrigger\DataContainer\DataContainerComponentInterface;
use EBlick\ContaoTrigger\DataContainer\Definition;
use EBlick\ContaoTrigger\Execution\ExecutionContext;
use EBlick\ContaoTrigger\Execution\ExecutionException;
use NotificationCenter\Model\Notification;

class NotificationAction implements ActionInterface, DataContainerComponentInterface, FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function fire(ExecutionContext $context, array $rawData): bool
    {
        $this->framework->initialize();

        /** @var Model $notificationModel */
        $notificationModel = $this->framework->getAdapter(Notification::class);

        if (!class_exists(Notification::class)) {
            throw new ExecutionException(
                'Notification Center not found! This extension is needed in order to run this trigger.'
            );
        }

        $trigger = $context->getParameters();

        /** @noinspection StaticInvocationViaThisInspection */
        $objNotification = $notificationModel->findByPk($trigger->act_notification_entity);

        if (null !== $objNotification) {
            $data = array_merge(
                [
                    'trigger_id'        => $trigger->id,
                    'trigger_title'     => $trigger->title,
                    'trigger_startTime' => $context->getStartTime()
                ],
                $this->prepareData($rawData)
            );
            /** @var Notification $objNotification */
            $objNotification->send($data);

            return true;
        }

        return false;
    }

    /**
     * Prefix keys with 'data_'
     *
     * @param array $rawData
     *
     * @return array
     */
    private function prepareData(array $rawData): array
    {
        $data = [];
        foreach ($rawData as $k => $v) {
            $data['data_' . $k] = $v;
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataContainerDefinition(): Definition
    {
        $palette = 'act_notification_entity,act_notification_tokenList';

        $fields = [
            'act_notification_entity'    =>
                [
                    'label'            => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['action_notification_entity'],
                    'exclude'          => true,
                    'inputType'        => 'select',
                    'options_callback' => [
                        'eblick_contao_trigger.listener.datacontainer.notification_action',
                        'getNotificationChoices'
                    ],
                    'eval'             => [
                        'mandatory'          => true,
                        'includeBlankOption' => true,
                        'chosen'             => true,
                        'tl_class'           => 'w50'
                    ],
                    'sql'              => "int(10) unsigned NOT NULL default '0'"
                ],
            'act_notification_tokenList' => array
            (
                'exclude'              => true,
                'input_field_callback' => [
                    'eblick_contao_trigger.listener.datacontainer.notification_action',
                    'onGetTokenList'
                ]
            ),
        ];

        return new Definition($fields, $palette);
    }
}