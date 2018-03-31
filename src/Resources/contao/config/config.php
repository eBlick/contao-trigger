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

// Backend modules
array_insert(
    $GLOBALS['BE_MOD'],
    1,
    array
    (
        'automation' => array
        (
            'eblick_trigger' => [
                'tables'  => ['tl_eblick_trigger', 'tl_eblick_trigger_log'],
                'execute' => ['eblick_contao_trigger.listener.datacontainer.trigger', 'onExecute'],
                'simulate'   => ['eblick_contao_trigger.listener.datacontainer.trigger', 'onSimulate'],
                'reset'   => ['eblick_contao_trigger.listener.datacontainer.trigger', 'onReset']
            ]
        )
    )
);

if ('BE' === TL_MODE) {
    $GLOBALS['TL_CSS'][] = 'bundles/eblickcontaotrigger/css/backend.css';
}


// Import component definitions into trigger dca
$GLOBALS['TL_HOOKS']['loadDataContainer'][] =
    [
        'eblick_contao_trigger.listener.datacontainer.trigger',
        'onImportDefinitions'
    ];

// Add insert tag helpers
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] =
    [
        'eblick_contao_trigger.listener.util.insert_tags',
        'onReplaceInsertTags'
    ];

// Cron job with fallback for the periodic command scheduler
$GLOBALS['TL_CRON']['minutely'][] = ['eblick_contao_trigger.listener.trigger', 'onExecute'];


// Generic Notification Center action
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['eblick_trigger'] = array
(
    // Type
    'eblick_notification_action' => array
    (
        'recipients'           => ['admin_email', 'data_*'],
        'email_subject'        => ['data_*', 'trigger_id', 'trigger_title', 'trigger_startTime', 'admin_email'],
        'email_text'           => ['data_*', 'trigger_id', 'trigger_title', 'trigger_startTime', 'admin_email'],
        'email_html'           => ['data_*', 'trigger_id', 'trigger_title', 'trigger_startTime', 'admin_email'],
        'email_sender_name'    => ['data_*', 'admin_email'],
        'email_sender_address' => ['data_*', 'admin_email'],
        'email_recipient_cc'   => ['data_*', 'admin_email'],
        'email_recipient_bcc'  => ['data_*', 'admin_email'],
        'email_replyTo'        => ['data_*', 'admin_email']
    )
);
