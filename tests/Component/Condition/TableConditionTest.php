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

namespace EBlick\ContaoTrigger\Test\Component\Condition;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;

use EBlick\ContaoTrigger\Component\Condition\TableCondition;
use EBlick\ContaoTrigger\DataContainer\Definition;
use EBlick\ContaoTrigger\ExpressionLanguage\RowDataCompiler;

class TableConditionTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation(): void
    {
        $obj = new TableCondition(
            $this->createMock(Connection::class),
            $this->createMock(RowDataCompiler::class)
        );

        $this->assertInstanceOf(TableCondition::class, $obj);
    }

    public function testEvaluate(): void
    {
        // todo
    }

    public function testGetDataContainerDefinition(): void
    {
        $obj = new TableCondition(
            $this->createMock(Connection::class),
            $this->createMock(RowDataCompiler::class)
        );

        $definition = $obj->getDataContainerDefinition();
        $this->assertInstanceOf(Definition::class, $definition);

        $this->assertCount(2, $definition->selectors);
        $this->assertCount(2, $definition->subPalettes);
        $this->assertCount(8, $definition->fields);
        $this->assertSame('cnd_table_src,cnd_table_timed,cnd_table_expression', $definition->palette);
    }

    public function testGetDataPrototype(): void
    {
        $column1 = $this->createMock(Column::class);
        $column1->expects($this->once())->method('getName')->willReturn('testCol1');

        $column2 = $this->createMock(Column::class);
        $column2->expects($this->once())->method('getName')->willReturn('testCol2');

        $column3 = $this->createMock(Column::class);
        $column3->expects($this->once())->method('getName')->willReturn('testCol3');

        $columns = [$column1, $column2, $column3];

        $schemaManager = $this->createMock(AbstractSchemaManager::class);
        $schemaManager
            ->expects($this->once())
            ->method('listTableColumns')
            ->with('testTable')
            ->willReturn($columns);

        $statement = $this->createMock(Statement::class);
        $statement
            ->expects($this->once())
            ->method('fetch')
            ->with(\PDO::FETCH_COLUMN)
            ->willReturn('testTable');

        $connection = $this->createMock(Connection::class);
        $connection
            ->expects($this->once())
            ->method('getSchemaManager')
            ->willReturn($schemaManager);

        $connection
            ->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT cnd_table_src FROM tl_eblick_trigger WHERE id = ?', [123])
            ->willReturn($statement);


        $condition = new TableCondition(
            $connection,
            $this->createMock(RowDataCompiler::class)
        );

        $result = $condition->getDataPrototype(123);
        $this->assertEquals($result, ['testCol1' => null, 'testCol2' => null, 'testCol3' => null]);
    }
}
