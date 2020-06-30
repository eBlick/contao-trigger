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
use EBlick\ContaoTrigger\Component\Condition\TimeCondition;
use EBlick\ContaoTrigger\DataContainer\Definition;
use EBlick\ContaoTrigger\Execution\ExecutionContext;
use PHPUnit\Framework\TestCase;

class TimeConditionTest extends TestCase
{
    public function testInstantiation(): void
    {
        $obj = new TimeCondition(
            $this->createMock(Connection::class)
        );
        $this->assertInstanceOf(TimeCondition::class, $obj);
    }

    private function getTimeCondition($execute): array
    {
        $parameters                         = new \stdClass();
        $parameters->id                     = 6;
        $parameters->cnd_time_executionTime = 12345;

        $context = $this->createMock(ExecutionContext::class);
        $context->expects($this->once())
            ->method('getLog')
            ->with()
            ->willReturn([]);
        $context->expects($this->once())
            ->method('getParameters')
            ->with()
            ->willReturn($parameters);

        $statement = $this->createMock(Statement::class);
        $statement->expects($this->once())
            ->method('fetch')
            ->with(\PDO::FETCH_COLUMN)
            ->willReturn($execute);

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('executeQuery')
            ->with(
                'SELECT cnd_time_executionTime <> 0 && NOW() >= FROM_UNIXTIME(cnd_time_executionTime) FROM tl_eblick_trigger WHERE id=?',
                [6]
            )
            ->willReturn($statement);

        return [new TimeCondition($connection), $context];
    }

    public function testEvaluateWillFire(): void
    {
        $callback = function ($data) {
            $this->assertEquals(['selectedTime' => 12345], $data);
            throw new \Exception('callback called');
        };

        [$condition, $context] = $this->getTimeCondition('1');

        $this->expectExceptionMessage('callback called');

        /** @var $condition TimeCondition */
        /** @var $context ExecutionContext */
        $condition->evaluate($context, $callback);
    }

    public function testEvaluateWontFire(): void
    {
        $callback = function ($data) {
            // expect not to be called
            throw new \Exception('callback called');
        };

        [$condition, $context] = $this->getTimeCondition('0');

        /** @var $condition TimeCondition */
        /** @var $context ExecutionContext */
        $condition->evaluate($context, $callback);
    }

    public function testGetDataContainerDefinition(): void
    {
        $obj = new TimeCondition(
            $this->createMock(Connection::class)
        );

        $definition = $obj->getDataContainerDefinition();
        $this->assertInstanceOf(Definition::class, $definition);

        $this->assertCount(0, $definition->selectors);
        $this->assertCount(0, $definition->subPalettes);
        $this->assertCount(1, $definition->fields);
        $this->assertSame('cnd_time_executionTime', $definition->palette);
    }

    public function testGetDataPrototype(): void
    {
        $condition = new TimeCondition(
            $this->createMock(Connection::class)
        );

        $result = $condition->getDataPrototype(123);
        $this->assertEquals($result, ['selectedTime' => null]);
    }
}
