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

namespace EBlick\ContaoTrigger\EventListener\DataContainer;

use Contao\Controller;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TimeType;
use EBlick\ContaoTrigger\ExpressionLanguage\RowDataCompiler;
use Symfony\Component\ExpressionLanguage\SyntaxError;

class TableCondition implements FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    /** @var Connection */
    private $database;

    /** @var RowDataCompiler */
    private $rowDataCompiler;

    /**
     * TableCondition constructor.
     *
     * @param Connection      $database
     * @param RowDataCompiler $rowDataCompiler
     */
    public function __construct(Connection $database, RowDataCompiler $rowDataCompiler)
    {
        $this->database        = $database;
        $this->rowDataCompiler = $rowDataCompiler;
    }

    /**
     * Returns a list of available tables.
     *
     * @return array
     */
    public function onGetTables(): array
    {
        $tables = array_map(
            function ($table) {
                /** @var $table Table */
                return $table->getName();
            },
            $this->database->getSchemaManager()->listTables()
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
            'tl_version'
        ];
        $tables         = array_diff(
            array_values($tables),
            $excludedTables
        );

        // key equals value
        return array_combine($tables, $tables);
    }

    /**
     * Returns a list of table columns that can contain time information.
     *
     * @param DataContainer $dc
     *
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function onGetTimeColumns(DataContainer $dc): array
    {
        $table = $this->database
            ->executeQuery('SELECT cnd_table_src FROM tl_eblick_trigger WHERE id = ?', [$dc->id])
            ->fetch(\PDO::FETCH_COLUMN);

        if (!$table || !$this->database->getSchemaManager()->tablesExist([$table])) {
            return [];
        }

        $columns = [];

        // try to resolve column names
        $this->framework->initialize();

        /** @var Controller $controller */
        $controller = $this->framework->getAdapter(Controller::class);
        /** @noinspection StaticInvocationViaThisInspection */
        $controller->loadLanguageFile($table);

        foreach ($this->database->getSchemaManager()->listTableColumns($table) as $column) {
            if ($this->canBeDateTimeColumn($column)) {
                $columns[$column->getName()] = $this->buildFieldLabel($table, $column->getName());
            }
        }

        return $columns;
    }

    /**
     * @param Column $column
     *
     * @return bool
     */
    private function canBeDateTimeColumn(Column $column): bool
    {
        $type = $column->getType();

        switch (true) {
            case $type instanceof StringType:
                return 10 === $column->getLength();

            case $type instanceof IntegerType:
                return !\in_array($column->getName(), ['id', 'pid'])
                       && ($column->getLength() ? $column->getLength() >= 10 : true);

            case $type instanceof DateTimeType:
            case $type instanceof DateType:
            case $type instanceof TimeType:
                return true;
        }

        return false;
    }

    /**
     * @param $table
     * @param $field
     *
     * @return string
     */
    private function buildFieldLabel($table, $field): string
    {
        if (!array_key_exists($table, $GLOBALS['TL_LANG'])
            || !array_key_exists($field, $GLOBALS['TL_LANG'][$table])
            || !$GLOBALS['TL_LANG'][$table][$field]) {
            return $field;
        }
        $label = \is_array(
            $GLOBALS['TL_LANG'][$table][$field]
        ) ? $GLOBALS['TL_LANG'][$table][$field][0] : $GLOBALS['TL_LANG'][$table][$field];

        return sprintf('%s (%s)', $label, $field);
    }

    /**
     * @param               $var
     * @param DataContainer $dc
     *
     * @return string
     *
     * @throws SyntaxError
     */
    public function onValidateExpression($var, DataContainer $dc): string
    {
        if (!$var) {
            return $var;
        }

        $columns = [];
        foreach ($this->database->getSchemaManager()->listTableColumns($dc->activeRecord->cnd_table_src) as $column) {
            $columns[] = $column->getName();
        }

        // throws syntax error if invalid
        $this->rowDataCompiler->compileRowExpression($var, $columns);

        return $var;
    }
}