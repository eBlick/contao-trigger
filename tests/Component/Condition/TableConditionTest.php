<?php

declare(strict_types=1);

/*
 * @copyright LUMAS Consulting
 * @license   LGPL-3.0+
 * @link      https://github.com/lumas-consulting/contao-trigger
 */

namespace EBlick\ContaoTrigger\Test\Component\Condition;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Name\Identifier;
use Doctrine\DBAL\Schema\Name\UnqualifiedName;
use EBlick\ContaoTrigger\Component\Condition\TableCondition;
use EBlick\ContaoTrigger\ExpressionLanguage\RowDataCompiler;
use PHPUnit\Framework\TestCase;

class TableConditionTest extends TestCase
{
    public function testGetDataContainerDefinition(): void
    {
        $obj = new TableCondition(
            $this->createStub(Connection::class),
            $this->createStub(RowDataCompiler::class),
        );

        $definition = $obj->getDataContainerDefinition();

        $this->assertCount(2, $definition->selectors);
        $this->assertCount(2, $definition->subPalettes);
        $this->assertCount(8, $definition->fields);
        $this->assertSame('cnd_table_src,cnd_table_timed,cnd_table_expression', $definition->palette);
    }

    public function testGetDataPrototype(): void
    {
        $columns = [
            $this->mockColumn('testCol1'),
            $this->mockColumn('testCol2'),
            $this->mockColumn('testCol3'),
        ];

        $schemaManager = $this->createStub(AbstractSchemaManager::class);
        $schemaManager
            ->method('introspectTableColumnsByUnquotedName')
            ->with('testTable')
            ->willReturn($columns)
        ;

        $result = $this->createStub(Result::class);
        $result
            ->method('fetchOne')
            ->willReturn('testTable')
        ;

        $connection = $this->createMock(Connection::class);
        $connection
            ->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT cnd_table_src FROM tl_eblick_trigger WHERE id = ?', [123])
            ->willReturn($result)
        ;

        $connection
            ->method('createSchemaManager')
            ->willReturn($schemaManager)
        ;

        $condition = new TableCondition(
            $connection,
            $this->createStub(RowDataCompiler::class),
        );

        $result = $condition->getDataPrototype(123);

        $this->assertSame(['testCol1' => null, 'testCol2' => null, 'testCol3' => null], $result);
    }

    private function mockColumn(string $name): Column
    {
        $column = $this->createMock(Column::class);
        $column
            ->expects($this->once())
            ->method('getObjectName')
            ->willReturn(new UnqualifiedName(Identifier::unquoted($name)))
        ;

        return $column;
    }
}
