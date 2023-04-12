<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\EventListener\DataContainer;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TimeType;
use EBlick\ContaoTrigger\ExpressionLanguage\RowDataCompiler;

class TableCondition
{
    private AbstractSchemaManager $schemaManager;

    public function __construct(private Connection $connection, private RowDataCompiler $rowDataCompiler, private ContaoFramework $framework)
    {
        $this->schemaManager = $this->connection->createSchemaManager();
    }

    /**
     * Returns a list of available tables.
     */
    public function onGetTables(): array
    {
        $tables = array_map(
            static fn (Table $table): string => $table->getName(),
            $this->schemaManager->listTables()
        );

        // exclude tables
        $excludedTables = [
            // trigger framework
            'tl_eblick_trigger',
            'tl_eblick_trigger_log',

            // contao core
            'tl_cron',
            'tl_log',
            'tl_remember_me',
            'tl_search',
            'tl_search_index',
            'tl_undo',
            'tl_version',
        ];

        $tables = array_diff(
            array_values($tables),
            $excludedTables
        );

        // key equals value
        return array_combine($tables, $tables);
    }

    /**
     * Returns a list of table columns that can contain time information.
     */
    public function onGetTimeColumns(DataContainer $dc): array
    {
        $table = $this->connection
            ->executeQuery('SELECT cnd_table_src FROM tl_eblick_trigger WHERE id = ?', [$dc->id])
            ->fetchOne()
        ;

        if (!$table || !$this->schemaManager->tablesExist([$table])) {
            return [];
        }

        $columns = [];

        // try to resolve column names
        $this->framework->initialize();

        /** @var Controller $controller */
        $controller = $this->framework->getAdapter(Controller::class);
        /** @noinspection StaticInvocationViaThisInspection */
        $controller->loadLanguageFile($table);

        foreach ($this->schemaManager->listTableColumns($table) as $column) {
            if ($this->canBeDateTimeColumn($column)) {
                $columns[$column->getName()] = $this->buildFieldLabel($table, $column->getName());
            }
        }

        return $columns;
    }

    public function onValidateExpression($var, DataContainer $dc): string
    {
        if (!$var) {
            return $var;
        }

        $columns = [];

        foreach ($this->schemaManager->listTableColumns($dc->activeRecord->cnd_table_src) as $column) {
            $columns[] = $column->getName();
        }

        // throws syntax error if invalid
        $this->rowDataCompiler->compileRowExpression($var, $columns);

        return $var;
    }

    private function canBeDateTimeColumn(Column $column): bool
    {
        $type = $column->getType();

        return match (true) {
            $type instanceof StringType => 10 === $column->getLength(),
            $type instanceof IntegerType => !\in_array($column->getName(), ['id', 'pid'], true)
                && (!$column->getLength() || $column->getLength() >= 10),
            $type instanceof DateTimeType, $type instanceof DateType, $type instanceof TimeType => true,
            default => false,
        };
    }

    private function buildFieldLabel(string $table, string $field): string
    {
        if (
            !\array_key_exists($table, $GLOBALS['TL_LANG'])
            || !\array_key_exists($field, $GLOBALS['TL_LANG'][$table])
            || !$GLOBALS['TL_LANG'][$table][$field]
        ) {
            return $field;
        }
        $label = \is_array(
            $GLOBALS['TL_LANG'][$table][$field]
        ) ? $GLOBALS['TL_LANG'][$table][$field][0] : $GLOBALS['TL_LANG'][$table][$field];

        return sprintf('%s (%s)', $label, $field);
    }
}
