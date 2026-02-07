<?php

declare(strict_types=1);

/*
 * @copyright LUMAS Consulting
 * @license   LGPL-3.0+
 * @link      https://github.com/lumas-consulting/contao-trigger
 */

use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_eblick_trigger'] =
    [
        // Config
        'config' => [
            'dataContainer' => DC_Table::class,
            'ctable' => ['tl_eblick_trigger_log'],
            'switchToEdit' => true,
            'enableVersioning' => true,
            'sql' => [
                'keys' => [
                    'id' => 'primary',
                    'enabled' => 'index',
                    'condition_type' => 'index',
                    'action_type' => 'index',
                ],
            ],
        ],

        // List
        'list' => [
            'sorting' => [
                'mode' => 2,
                'fields' => ['title'],
                'flag' => 1,
                'panelLayout' => 'sort,search,limit',
            ],
            'label' => [
                'fields' => ['title'],
            ],
            'global_operations' => [
                'execute' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['execute'],
                    'href' => 'key=execute',
                    'class' => 'header_icon',
                    'icon' => 'sync.svg',
                    'primary' => true,
                ],
            ],
            'operations' => [
                'show',
                'edit',
                'delete',
                '-',
                'log' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['log'],
                    'href' => 'table=tl_eblick_trigger_log',
                    'icon' => 'bundles/eblickcontaotrigger/img/log.svg',
                    'primary' => true,
                ],
                '-',
                'simulate' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['simulate'],
                    'href' => 'key=simulate',
                    'icon' => 'bundles/eblickcontaotrigger/img/simulate.svg',
                    'button_callback' => [
                        'eblick_contao_trigger.listener.datacontainer.trigger',
                        'onShowSimulateButton',
                    ],
                    'attributes' => 'data-action="contao--scroll-offset#store" onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['tl_eblick_trigger']['simulateConfirm'] ?? null).'\'))return false"',
                ],
                'reset' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['reset'],
                    'href' => 'key=reset',
                    'icon' => 'bundles/eblickcontaotrigger/img/reset.svg',
                    'attributes' => 'data-action="contao--scroll-offset#store" onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['tl_eblick_trigger']['resetConfirm'] ?? null).'\'))return false"',
                    'primary' => true,
                ],
            ],
        ],

        // Select
        'select' => [
            'buttons_callback' => [],
        ],

        // Edit
        'edit' => [
            'buttons_callback' => [],
        ],

        // Palettes
        'palettes' => [
            '__selector__' => ['condition_type', 'action_type'],
            'default' => '{meta_legend},error,title;'.
                              '{condition_legend},condition_type;'.
                              '{action_legend},action_type;'.
                              '{system_legend},enabled;',
        ],

        // Subpalettes
        'subpalettes' => [
            'condition_type_table' => 'condition_table_srcField',
        ],

        // Fields
        'fields' => [
            'id' => [
                'sql' => 'int(10) unsigned NOT NULL auto_increment',
            ],
            'tstamp' => [
                'sql' => "int(10) unsigned NOT NULL default '0'",
            ],
            'title' => [
                'exclude' => true,
                'inputType' => 'text',
                'eval' => [
                    'mandatory' => true,
                    'maxlength' => 255,
                ],
                'sql' => "varchar(255) NOT NULL default ''",
            ],
            'error' => [
                'exclude' => true,
                'sql' => 'TEXT NULL default NULL',
            ],
            'enabled' => [
                'exclude' => true,
                'default' => false,
                'inputType' => 'checkbox',
                'eval' => [
                    'isBoolean' => true,
                ],
                'sql' => "char(1) NOT NULL default '0'",
            ],
            'condition_type' => [
                'exclude' => true,
                'inputType' => 'select',
                'reference' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['condition'],
                'eval' => [
                    'mandatory' => true,
                    'includeBlankOption' => true,
                    'submitOnChange' => true,
                    'tl_class' => 'w50',
                ],
                'sql' => "varchar(64) NOT NULL default ''",
            ],
            'action_type' => [
                'exclude' => true,
                'inputType' => 'select',
                'reference' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['action'],
                'eval' => [
                    'mandatory' => true,
                    'includeBlankOption' => true,
                    'submitOnChange' => true,
                    'tl_class' => 'w50',
                ],
                'sql' => "varchar(64) NOT NULL default ''",
            ],
            'lastRun' => [
                'eval' => ['rgxp' => 'datim'],
                'exclude' => true,
                'sql' => "int(10) unsigned NOT NULL default '0'",
            ],
            'lastDuration' => [
                'sql' => "int(10) unsigned NOT NULL default '0'",
                'exclude' => true,
            ],
        ],
    ];
