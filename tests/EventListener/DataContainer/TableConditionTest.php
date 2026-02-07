<?php

declare(strict_types=1);

/*
 * @copyright LUMAS Consulting
 * @license   LGPL-3.0+
 * @link      https://github.com/lumas-consulting/contao-trigger
 */

namespace EBlick\ContaoTrigger\Test\EventListener\DataContainer;

use Contao\CoreBundle\Framework\ContaoFramework;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Name\Identifier;
use Doctrine\DBAL\Schema\Name\OptionallyQualifiedName;
use Doctrine\DBAL\Schema\Table;
use EBlick\ContaoTrigger\EventListener\DataContainer\TableCondition;
use EBlick\ContaoTrigger\ExpressionLanguage\RowDataCompiler;
use PHPUnit\Framework\TestCase;

class TableConditionTest extends TestCase
{
    public function testOnGetTables(): void
    {
        $schemaManager = $this->createMock(AbstractSchemaManager::class);
        $schemaManager
            ->expects($this->once())
            ->method('introspectTables')
            ->willReturn([$this->mockTable('tl_eblick_trigger'), $this->mockTable('testTable2')])
        ;

        $connection = $this->createMock(Connection::class);
        $connection
            ->expects($this->once())
            ->method('createSchemaManager')
            ->willReturn($schemaManager)
        ;

        $condition = new TableCondition(
            $connection,
            $this->createStub(RowDataCompiler::class),
            $this->createStub(ContaoFramework::class),
        );

        $this->assertSame(['testTable2' => 'testTable2'], $condition->onGetTables());
    }

    private function mockTable(string $name): Table
    {
        $table = $this->createMock(Table::class);
        $table
            ->expects($this->once())
            ->method('getObjectName')
            ->willReturn(new OptionallyQualifiedName(Identifier::unquoted($name), null))
        ;

        return $table;
    }
}
