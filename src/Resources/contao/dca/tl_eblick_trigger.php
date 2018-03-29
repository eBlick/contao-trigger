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

$GLOBALS['TL_DCA']['tl_eblick_trigger'] =
    [
        // Config
        'config'      =>
            [
                'dataContainer'     => 'Table',
                'ctable'            => ['tl_eblick_trigger_log'],
                'switchToEdit'      => true,
                'enableVersioning'  => true,
                'sql'               =>
                    [
                        'keys' =>
                            [
                                'id'             => 'primary',
                                'enabled'        => 'index',
                                'condition_type' => 'index',
                                'action_type'    => 'index'
                            ]
                    ],
                'onsubmit_callback' => [
                    [
                        'eblick_contao_trigger.listener.datacontainer.trigger',
                        'onResetError'
                    ]
                ],
            ],

        // List
        'list'        =>
            [
                'sorting'           =>
                    [
                        'mode'        => 2,
                        'fields'      => ['title'],
                        'flag'        => 1,
                        'panelLayout' => 'sort,search,limit'
                    ],
                'label'             =>
                    [
                        'fields'         => ['title'],
                        'label_callback' => [
                            'eblick_contao_trigger.listener.datacontainer.trigger',
                            'onGenerateLabel'
                        ]
                    ],
                'global_operations' => [
                    'execute' => [
                        'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['execute'],
                        'href'  => 'key=execute',
                        'class' => 'header_icon',
                        'icon'  => 'sync.svg',
                    ]
                ],
                'operations'        =>
                    [
                        'show'   =>
                            [
                                'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['show'],
                                'href'  => 'act=show',
                                'icon'  => 'show.svg'
                            ],
                        'edit'   =>
                            [
                                'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['edit'],
                                'href'  => 'act=edit',
                                'icon'  => 'edit.svg'
                            ],
                        'log'    =>
                            [
                                'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['log'],
                                'href'  => 'table=tl_eblick_trigger_log',
                                'icon'  => 'bundles/eblickcontaotrigger/img/log.svg'
                            ],
                        'reset'  =>
                            [
                                'label'      => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['reset'],
                                'href'       => 'key=reset',
                                'icon'       => 'bundles/eblickcontaotrigger/img/reset.svg',
                                'attributes' => 'onclick="if(!confirm(\''
                                                . $GLOBALS['TL_LANG']['tl_eblick_trigger']['resetConfirm']
                                                . '\'))return false;Backend.getScrollOffset()"'
                            ],
                        'delete' =>
                            [
                                'label'      => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['delete'],
                                'href'       => 'act=delete',
                                'icon'       => 'delete.svg',
                                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                                                . '\'))return false;Backend.getScrollOffset()"'
                            ]
                    ]
            ],

        // Select
        'select'      =>
            [
                'buttons_callback' => []
            ],

        // Edit
        'edit'        =>
            [
                'buttons_callback' => []
            ],

        // Palettes
        'palettes'    =>
            [
                '__selector__' => ['condition_type', 'action_type'],
                'default'      => '{meta_legend},error,title;' .
                                  '{condition_legend},condition_type;' .
                                  '{action_legend},action_type;' .
                                  '{system_legend},enabled;'
            ],

        // Subpalettes
        'subpalettes' =>
            [
                'condition_type_table' => 'condition_table_srcField',
            ],

        // Fields
        'fields'      =>
            [
                'id'             =>
                    [
                        'sql' => 'int(10) unsigned NOT NULL auto_increment'
                    ],
                'tstamp'         =>
                    [
                        'sql' => "int(10) unsigned NOT NULL default '0'"
                    ],
                'title'          =>
                    [
                        'label'     => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['title'],
                        'exclude'   => true,
                        'inputType' => 'text',
                        'eval'      => [
                            'mandatory' => true,
                            'maxlength' => 255
                        ],
                        'sql'       => "varchar(255) NOT NULL default ''",
                    ],
                'error'          =>
                    [
                        'exclude'              => true,
                        'input_field_callback' => [
                            'eblick_contao_trigger.listener.datacontainer.trigger',
                            'onGetError'
                        ],
                        'sql'                  => 'TEXT NULL default NULL'
                    ],
                'enabled'        =>
                    [
                        'label'     => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['enabled'],
                        'exclude'   => true,
                        'default'   => false,
                        'inputType' => 'checkbox',
                        'eval'      => [
                            'isBoolean' => true
                        ],
                        'sql'       => "char(1) NOT NULL default '0'"
                    ],
                'condition_type' =>
                    [
                        'label'     => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['condition_type'],
                        'exclude'   => true,
                        'inputType' => 'select',
                        'reference' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['condition'],
                        'eval'      => [
                            'mandatory'          => true,
                            'includeBlankOption' => true,
                            'submitOnChange'     => true,
                            'tl_class'           => 'w50'
                        ],
                        'sql'       => "varchar(64) NOT NULL default ''"
                    ],
                'action_type'    =>
                    [
                        'label'     => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['action_type'],
                        'exclude'   => true,
                        'inputType' => 'select',
                        'reference' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['action'],
                        'eval'      => [
                            'mandatory'          => true,
                            'includeBlankOption' => true,
                            'submitOnChange'     => true,
                            'tl_class'           => 'w50'
                        ],
                        'sql'       => "varchar(64) NOT NULL default ''"
                    ],
                'lastRun'        =>
                    [
                        'label'   => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['lastRun'],
                        'eval'    => ['rgxp' => 'datim'],
                        'exclude' => true,
                        'sql'     => "int(10) unsigned NOT NULL default '0'",
                    ],
                'lastDuration'   =>
                    [
                        'label'   => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['lastDuration'],
                        'sql'     => "int(10) unsigned NOT NULL default '0'",
                        'exclude' => true
                    ],
            ]
    ];