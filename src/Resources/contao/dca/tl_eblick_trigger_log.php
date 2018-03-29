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

$GLOBALS['TL_DCA']['tl_eblick_trigger_log'] =
    [
        // Config
        'config' =>
            [
                'dataContainer'    => 'Table',
                'ptable'           => 'tl_eblick_trigger',
                'enableVersioning' => false,
                'notEditable'      => true,
                'closed'           => true,
                'sql'              =>
                    [
                        'keys' =>
                            [
                                'id'                  => 'primary',
                                'pid,origin,originId' => 'index',
                            ]
                    ],
            ],

        // List
        'list'   =>
            [
                'sorting'           =>
                    [
                        'mode'        => 1,
                        'fields'      => ['tstamp'],
                        'flag'        => 5,
                        'panelLayout' => 'limit'
                    ],
                'label'             =>
                    [
                        'fields'         => ['tstamp'],
                        'label_callback' => [
                            'eblick_contao_trigger.listener.datacontainer.trigger_log',
                            'onGenerateLabel'
                        ]
                    ],
                'global_operations' => [],
                'operations'        =>
                    [
                        'show'   =>
                            [
                                'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger_log']['show'],
                                'href'  => 'act=show',
                                'icon'  => 'show.svg'
                            ],
                        'delete' =>
                            [
                                'label'      => &$GLOBALS['TL_LANG']['tl_eblick_trigger_log']['delete'],
                                'href'       => 'act=delete',
                                'icon'       => 'delete.svg',
                                'attributes' => 'onclick="if(!confirm(\''
                                                . $GLOBALS['TL_LANG']['tl_eblick_trigger_log']['deleteConfirm']
                                                . '\'))return false;Backend.getScrollOffset()"'
                            ]
                    ]
            ],

        // Fields
        'fields' =>
            [
                // contao
                'id'       =>
                    [
                        'sql' => 'int(10) unsigned NOT NULL auto_increment'
                    ],
                'pid'      =>
                    [
                        'foreignKey' => 'tl_eblick_trigger.title',
                        'sql'        => "int(10) unsigned NOT NULL default '0'",
                        'relation'   => ['type' => 'belongsTo', 'load' => 'lazy']
                    ],
                'tstamp'   =>
                    [
                        'sql' => "int(10) unsigned NOT NULL default '0'"
                    ],
                'origin'   =>
                    [
                        'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger_log']['origin'],
                        'sql'   => "varchar(64) NOT NULL default 'tl_eblick_trigger'"
                    ],
                'originId' =>
                    [
                        'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger_log']['originId'],
                        'sql'   => "int(10) unsigned NOT NULL default '0'"
                    ]
            ]
    ];