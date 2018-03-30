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

$GLOBALS['TL_LANG']['tl_eblick_trigger']['execute'] = 'Manuelle Ausführung';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['show'][1] = 'Details von Trigger ID%s anzeigen.';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['new'] = ['Neuen Trigger hinzufügen.'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['edit'][1] = 'Trigger ID%s bearbeiten.';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['log'][1] = 'Trigger-Log für ID%s anzeigen.';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['reset'][1] = 'Trigger-Log für Trigger ID%s zurücksetzen.';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['resetConfirm'] = 'Dies wird den Log zurücksetzen und alle Aktionen erneut auslösen, wenn die Bedingungen erfüllt sind!\nSind Sie sicher?';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['simulate'][1] = 'Abarbeitung von Trigger ID%s simulieren (ohne Aktionen auszuführen).';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['simulateConfirm'] = 'Dies wird simulierte Log-Einträge für alle derzeit zutreffenden Bedingungen erzeugen.\nSind Sie sicher?';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['delete'][1] = 'Trigger ID%s löschen.';

$GLOBALS['TL_LANG']['tl_eblick_trigger']['meta_legend'] = 'Metadaten';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['title'] = ['Titel', 'Geben Sie einen Namen oder eine kurze Beschreibung für den Trigger ein'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['error'] = ['Bei der Ausführung ist ein Fehler aufgetreten!', 'Achtung: Diese Meldung wird entfernt, sobald Sie den Datensatz speichern. Bis dahin ist die Ausführung des Triggers deaktiviert.'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['condition_legend'] = 'Bedingung';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['condition_type'] = ['Bedingung-Typ'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['action_legend'] = 'Aktion';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['action_type'] = ['Aktions-Typ'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['system_legend'] = 'System';
$GLOBALS['TL_LANG']['tl_eblick_trigger']['enabled'] = ['Trigger aktivieren', 'Starten Sie die Abarbeitung, wenn Sie sicher sind, dass alle Einstellung korrekt sind.'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['lastRun'] = ['Letzte Ausführung'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['lastDuration'] = ['Letzte Ausführungsdauer [ms]'];

// table condition
$GLOBALS['TL_LANG']['tl_eblick_trigger']['condition']['table'] = ['Tabellen-Einträge'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_src'] = ['Quelltabelle', 'Für jede Zeile werden die folgenden Bedingungen geprüft und ggf. einmalig die Aktion ausgelöst.'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timed'] = ['Zeitbedingung hinzufügen'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timeColumn'] = ['Überprüftes Zeitfeld', 'Wählen Sie die Spalte, die die Zeitinformation enhält. Die Bedingung ist erfüllt, sobald der eingestellte Zeitversatz zum Zeitfeld eingetreten ist.'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timeOffset'] = ['Zeitversatz', 'zuvor/danach, z.B. 5 oder -30.'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timeOffsetUnit'] = ['Einheit'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timeOffsetUnit']['MINUTE'] = ['Minuten'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timeOffsetUnit']['HOUR'] = ['Stunden'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timeOffsetUnit']['DAY'] = ['Tage'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_overwriteExecutionTime'] = ['Ausführungs-Uhrzeit überschreiben'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_executionTime'] = ['Uhrzeit', 'z.B. 07:00, wenn die Auslösung unabhängig von der Uhrzeit des Zeitfeldes immer morgens geschehen soll.'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_expression'] = ['Custom Expression', 'Optionale Bedingung in <a href="https://symfony.com/doc/current/components/expression_language/syntax.html" style="color: #148ace;" target="_blank">Symfony Expression Language</a>. Die Tabellen-Spalten sind als Variablen verfügbar, z.B.: \'sum >= 4 and published\''];

// time condition
$GLOBALS['TL_LANG']['tl_eblick_trigger']['condition']['time'] = ['Zeitpunkt'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_time_executionTime'] = ['Datum/Uhrzeit', 'Aktion wird ausgelöst, sobald der Zeitpunkt eingetreten ist.'];

// notification action
$GLOBALS['TL_LANG']['tl_eblick_trigger']['action']['notification'] = ['Benachrichtigung via Notification Center'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['action_notification_entity'] = ['Benachrichtigung', 'Wählen Sie eine zuvor angelegte Benachrichtigung des Typs `Trigger Benachrichtigung` aus dem Notification Center.'];
$GLOBALS['TL_LANG']['tl_eblick_trigger']['action_notification_tokens'] = 'Verfügbare Simple-Tokens';