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

$GLOBALS['TL_LANG']['tl_eblick_trigger']['execute'] = 'Manually refresh';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['show'][1] = 'Show details of trigger ID%s.';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['new'] = ['Add a new trigger.'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['edit'][1] = 'Edit trigger ID%s.';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['log'][1] = 'View log of trigger ID%s.';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['reset'][1] = 'Reset trigger ID%s.';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['resetConfirm'] = 'This will empty the log and execute all actions again if conditions are met!\nAre you sure?';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['delete'][1] = 'Delete trigger ID%s.';

$GLOBALS['TL_LANG']['tl_eblick_trigger']['meta_legend'] = 'Meta';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['title'] = ['Title', 'Specify a name or short description what the trigger does.'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['error'] = ['There was an error on execution!', 'Attention: This error will get dismissed when saving the record. Until then the execution of the trigger is disabled.'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['condition_legend'] = 'Condition';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['condition_type'] = ['Condition Type'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['action_legend'] = 'Action';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['action_type'] = ['Action Type'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['system_legend'] = 'System Settings';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['enabled'] = ['Enable this trigger', 'Start execution as soon as your ensured all settings are correct.'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['lastRun'] = ['Last Run'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['lastDuration'] = ['Last Duration [ms]'];

// table condition
$GLOBALS['TL_LANG']['tl_eblick_trigger']['condition']['table'] = ['Table Records'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_src'] = ['Source Table', 'Actions are fired at most once for each of the table\'s rows.'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timed'] = ['Add time constraint'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timeColumn'] = ['Time Field', 'Select the database column that holds the time value.'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timeOffset'] = ['Offset', 'Offset like 5 or -30.'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timeOffsetUnit'] = ['Unit'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timeOffsetUnit']['MINUTE'] = ['minutes'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timeOffsetUnit']['HOUR'] = ['hours'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timeOffsetUnit']['DAY'] = ['days'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_overwriteExecutionTime'] = ['Overwrite execution time'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_executionTime'] = ['Time', 'Value will replace the calculated offset\'s time.'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_expression'] = ['Custom Expression', 'Optional condition in <a href="https://symfony.com/doc/current/components/expression_language/syntax.html" style="color: #148ace;" target="_blank">Symfony Expression Language</a>. The database columns are available as variables, e.g.: \'sum >= 4 and published\''];

// notification action
$GLOBALS['TL_LANG']['tl_eblick_trigger']['action']['notification'] = ['Notification'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['action_notification_entity'] = ['Notification', 'Choose a trigger notification from the notification center.'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['action_notification_tokens'] = 'Available tokens';

