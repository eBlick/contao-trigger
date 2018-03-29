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

namespace EBlick\ContaoTrigger\Test\EventListener\DataContainer;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Table;
use EBlick\ContaoTrigger\EventListener\DataContainer\TableCondition;
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

    public function testOnGetTables(): void
    {
        $table1 = $this->createMock(Table::class);
        $table1->expects($this->once())
            ->method('getName')
            ->willReturn('tl_eblick_trigger');

        $table2 = $this->createMock(Table::class);
        $table2->expects($this->once())
            ->method('getName')
            ->willReturn('testTable2');

        $schemaManager = $this->createMock(AbstractSchemaManager::class);
        $schemaManager->expects($this->once())
            ->method('listTables')
            ->willReturn([$table1, $table2]);

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('getSchemaManager')
            ->willReturn($schemaManager);

        $condition = new TableCondition(
            $connection,
            $this->createMock(RowDataCompiler::class)
        );

        $this->assertEquals(['testTable2' => 'testTable2'], $condition->onGetTables());
    }
}
