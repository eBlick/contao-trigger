<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\Component\Condition;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use EBlick\ContaoTrigger\DataContainer\DataContainerComponentInterface;
use EBlick\ContaoTrigger\DataContainer\Definition;
use EBlick\ContaoTrigger\Execution\ExecutionContext;
use EBlick\ContaoTrigger\Execution\ExecutionException;
use EBlick\ContaoTrigger\ExpressionLanguage\RowDataCompiler;
use Symfony\Component\ExpressionLanguage\SyntaxError;

class TableCondition implements ConditionInterface, DataContainerComponentInterface
{
    private AbstractSchemaManager $schemaManager;

    public function __construct(private Connection $connection, private RowDataCompiler $rowDataCompiler)
    {
        $this->schemaManager = $this->connection->createSchemaManager();
    }

    public function evaluate(ExecutionContext $context, \Closure $fireCallback): void
    {
        $trigger = $context->getParameters();

        // build query
        $log = $context->getLog($trigger->cnd_table_src);
        [$query, $params, $types] = $this->buildQuery($trigger, $log);

        // create expression callback
        $expressionCallback = $this->buildExpressionCallback($trigger->cnd_table_expression, $trigger->cnd_table_src);

        // execute query
        try {
            $affectedEntities = $this->connection
                ->executeQuery($query, $params, $types)
                ->fetchAllAssociative()
            ;
        } catch (Exception $e) {
            throw new ExecutionException($e->getMessage(), 0, $e);
        }

        foreach ($affectedEntities as $entity) {
            // check expression
            if ($expressionCallback && !$expressionCallback($entity)) {
                continue;
            }

            // fire action
            $fireCallback($entity, (int) $entity['id'], $trigger->cnd_table_src);
        }
    }

    public function getDataPrototype(int $triggerId): array
    {
        $srcTable = $this->connection
            ->executeQuery('SELECT cnd_table_src FROM tl_eblick_trigger WHERE id = ?', [$triggerId])
            ->fetchOne()
        ;

        return
            array_fill_keys(
                $this->getColumnNames($srcTable),
                null
            );
    }

    public function getDataContainerDefinition(): Definition
    {
        $palette = 'cnd_table_src,cnd_table_timed,cnd_table_expression';

        $selectors = [
            'cnd_table_timed',
            'cnd_table_overwriteExecutionTime',
        ];

        $subPalettes = [
            'cnd_table_timed' => 'cnd_table_timeColumn,cnd_table_timeOffset,cnd_table_timeOffsetUnit,cnd_table_overwriteExecutionTime',
            'cnd_table_overwriteExecutionTime' => 'cnd_table_executionTime',
        ];

        $fields = [
            'cnd_table_src' => [
                'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_src'],
                'exclude' => true,
                'inputType' => 'select',
                'options_callback' => [
                    'eblick_contao_trigger.listener.datacontainer.table_condition',
                    'onGetTables',
                ],
                'eval' => [
                    'mandatory' => true,
                    'includeBlankOption' => true,
                    'submitOnChange' => true,
                    'tl_class' => 'w50',
                ],
                'sql' => "varchar(64) NOT NULL default ''",
            ],
            'cnd_table_timed' => [
                'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timed'],
                'exclude' => true,
                'inputType' => 'checkbox',
                'eval' => ['submitOnChange' => true, 'tl_class' => 'clr m12'],
                'sql' => "char(1) NOT NULL default ''",
            ],
            'cnd_table_timeColumn' => [
                'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timeColumn'],
                'exclude' => true,
                'inputType' => 'select',
                'options_callback' => [
                    'eblick_contao_trigger.listener.datacontainer.table_condition',
                    'onGetTimeColumns',
                ],
                'eval' => [
                    'mandatory' => true,
                    'includeBlankOption' => true,
                    'tl_class' => 'w50',
                ],
                'sql' => "varchar(64) NOT NULL default ''",
            ],
            'cnd_table_timeOffset' => [
                'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timeOffset'],
                'exclude' => true,
                'inputType' => 'text',
                'eval' => [
                    'rgxp' => 'digit',
                    'nospace' => true,
                    'mandatory' => true,
                    'decodeEntities' => true,
                    'tl_class' => 'w16',
                ],
                'sql' => 'int(10) NULL',
            ],
            'cnd_table_timeOffsetUnit' => [
                'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timeOffsetUnit'],
                'exclude' => true,
                'inputType' => 'select',
                'options' => ['MINUTE', 'HOUR', 'DAY'],
                'reference' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_timeOffsetUnit'],
                'eval' => [
                    'mandatory' => true,
                    'tl_class' => 'w16',
                ],
                'sql' => 'varchar(6) NULL',
            ],
            'cnd_table_overwriteExecutionTime' => [
                'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_overwriteExecutionTime'],
                'exclude' => true,
                'inputType' => 'checkbox',
                'eval' => ['submitOnChange' => true, 'tl_class' => 'clr m12'],
                'sql' => "char(1) NOT NULL default ''",
            ],
            'cnd_table_executionTime' => [
                'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_executionTime'],
                'exclude' => true,
                'inputType' => 'text',
                'eval' => [
                    'mandatory' => true,
                    'rgxp' => 'time',
                    'datepicker' => true,
                    'tl_class' => 'w25 wizard',
                ],
                'sql' => 'INT(10) NULL',
            ],
            'cnd_table_expression' => [
                'label' => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['cnd_table_expression'],
                'exclude' => true,
                'inputType' => 'text',
                'eval' => [
                    'decodeEntities' => true,
                ],
                'save_callback' => [
                    [
                        'eblick_contao_trigger.listener.datacontainer.table_condition',
                        'onValidateExpression',
                    ],
                ],
                'sql' => "varchar(255) NOT NULL default ''",
            ],
        ];

        return new Definition($fields, $palette, $selectors, $subPalettes);
    }

    /**
     * Get column names of source table.
     */
    private function getColumnNames(string $srcTable): array
    {
        return array_map(
            static fn (Column $column): string => $column->getName(),
            $this->schemaManager->listTableColumns($srcTable)
        );
    }

    /**
     * Build SQL query to select entities of the selected source table.
     */
    private function buildQuery(\stdClass $trigger, array $log): array
    {
        $logIds = !empty($log) ? array_keys($log) : [-1];

        $query = 'SELECT * FROM '.$this->connection->quoteIdentifier($trigger->cnd_table_src).' WHERE TRUE';
        $params = [];
        $types = [];

        // log condition
        if (!empty($log)) {
            $query .= ' AND id NOT IN (?)';
            $params[] = $logIds;
            $types[] = ArrayParameterType::INTEGER;
        }

        // time condition
        if ($trigger->cnd_table_timed) {
            // injection prevention
            if (
                !\in_array(
                    $timeUnit = $trigger->cnd_table_timeOffsetUnit,
                    ['MINUTE', 'HOUR', 'DAY'],
                    true
                )
            ) {
                throw new ExecutionException(sprintf('Invalid time offset "%s"!', $trigger->cnd_table_timeOffsetUnit));
            }
            $timeColumn = $this->connection->quoteIdentifier(
                $trigger->cnd_table_timeColumn
            );

            // offset condition
            $timeComparisonSql =
                'DATE_ADD('.
                // allow datetime/date/timestamp fields with fallback to Contao's string timestamps
                'IFNULL(DATE_ADD('.$timeColumn.', INTERVAL 0 SECOND), FROM_UNIXTIME('.$timeColumn.')),'.
                'INTERVAL ? '.$timeUnit.
                ')';

            $params[] = $trigger->cnd_table_timeOffset;
            $types[] = ParameterType::INTEGER;

            // overwrite time portion: extract date + concatenate time
            if ($trigger->cnd_table_overwriteExecutionTime) {
                $timeComparisonSql = 'CONCAT(DATE('.$timeComparisonSql.'), ?)';

                $params[] = ' '.date('H:m:s', (int) $trigger->cnd_table_executionTime);
                $types[] = ParameterType::STRING;
            }

            // compose
            $query .= sprintf(' AND NOW() >= %s', $timeComparisonSql);
        }

        return [$query, $params, $types];
    }

    private function buildExpressionCallback(string $expression, string $srcTable): \Closure|null
    {
        if (!$expression) {
            return null;
        }

        try {
            $expressionCallback = $this->rowDataCompiler->compileRowExpression(
                $expression,
                $this->getColumnNames($srcTable)
            );
        } catch (SyntaxError $e) {
            throw new ExecutionException($e->getMessage(), 0, $e);
        }

        return $expressionCallback;
    }
}
