<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

use Contao\ArrayUtil;

// Backend modules
ArrayUtil::arrayInsert(
    $GLOBALS['BE_MOD'],
    1,
    [
        'automation' => [
            'eblick_trigger' => [
                'tables' => ['tl_eblick_trigger', 'tl_eblick_trigger_log'],
                'execute' => ['eblick_contao_trigger.listener.datacontainer.trigger', 'onExecute'],
                'simulate' => ['eblick_contao_trigger.listener.datacontainer.trigger', 'onSimulate'],
                'reset' => ['eblick_contao_trigger.listener.datacontainer.trigger', 'onReset'],
            ],
        ],
    ]
);

// Generic Notification Center action
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['eblick_trigger'] = [
    // Type
    'eblick_notification_action' => [
        'recipients' => ['admin_email', 'data_*'],
        'email_subject' => ['data_*', 'trigger_id', 'trigger_title', 'trigger_startTime', 'admin_email'],
        'email_text' => ['data_*', 'trigger_id', 'trigger_title', 'trigger_startTime', 'admin_email'],
        'email_html' => ['data_*', 'trigger_id', 'trigger_title', 'trigger_startTime', 'admin_email'],
        'email_sender_name' => ['data_*', 'admin_email'],
        'email_sender_address' => ['data_*', 'admin_email'],
        'email_recipient_cc' => ['data_*', 'admin_email'],
        'email_recipient_bcc' => ['data_*', 'admin_email'],
        'email_replyTo' => ['data_*', 'admin_email'],
    ],
];
