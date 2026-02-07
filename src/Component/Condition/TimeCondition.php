<?php

declare(strict_types=1);

/*
 * @copyright LUMAS Consulting
 * @license   LGPL-3.0+
 * @link      https://github.com/lumas-consulting/contao-trigger
 */

namespace EBlick\ContaoTrigger\Component\Condition;

use Doctrine\DBAL\Connection;
use EBlick\ContaoTrigger\DataContainer\DataContainerComponentInterface;
use EBlick\ContaoTrigger\DataContainer\Definition;
use EBlick\ContaoTrigger\Execution\ExecutionContext;

class TimeCondition implements ConditionInterface, DataContainerComponentInterface
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function evaluate(ExecutionContext $context, \Closure $fireCallback): void
    {
        // get default log, only execute once
        if (!empty($context->getLog())) {
            return;
        }

        $trigger = $context->getParameters();

        $execute = $this->connection
            ->executeQuery(
                'SELECT cnd_time_executionTime <> 0 && NOW() >= FROM_UNIXTIME(cnd_time_executionTime) FROM tl_eblick_trigger WHERE id=?',
                [$trigger->id],
            )
            ->fetchOne()
        ;

        if ($execute) {
            $fireCallback(
                ['selectedTime' => $trigger->cnd_time_executionTime],
            );
        }
    }

    public function getDataPrototype(int $triggerId): array
    {
        return array_fill_keys(['selectedTime'], null);
    }

    public function getDataContainerDefinition(): Definition
    {
        $palette = 'cnd_time_executionTime';

        $fields = [
            'cnd_time_executionTime' => [
                'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_time_executionTime'],
                'exclude' => true,
                'inputType' => 'text',
                'eval' => [
                    'mandatory' => true,
                    'rgxp' => 'datim',
                    'datepicker' => true,
                    'tl_class' => 'w50 wizard',
                ],
                'sql' => 'INT(10) NULL',
            ],
        ];

        return new Definition($fields, $palette);
    }
}
