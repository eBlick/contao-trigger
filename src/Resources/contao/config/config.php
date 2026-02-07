<?php

declare(strict_types=1);

/*
 * @copyright LUMAS Consulting
 * @license   LGPL-3.0+
 * @link      https://github.com/lumas-consulting/contao-trigger
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
    ],
);
