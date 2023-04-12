<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\Test\Component\Condition;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use EBlick\ContaoTrigger\Component\Condition\TimeCondition;
use EBlick\ContaoTrigger\DataContainer\Definition;
use EBlick\ContaoTrigger\Execution\ExecutionContext;
use PHPUnit\Framework\TestCase;

class TimeConditionTest extends TestCase
{
    public function testEvaluateWillFire(): void
    {
        $callback = function ($data): void {
            $this->assertSame(['selectedTime' => 12345], $data);

            throw new \Exception('callback called');
        };

        [$condition, $context] = $this->getTimeCondition('1');

        $this->expectExceptionMessage('callback called');

        /** @var TimeCondition $condition */
        /** @var ExecutionContext $context */
        $condition->evaluate($context, $callback);
    }

    public function testEvaluateWontFire(): void
    {
        $callback = static function ($data): void {
            // expect not to be called
            throw new \Exception('callback called');
        };

        [$condition, $context] = $this->getTimeCondition('0');

        /** @var TimeCondition $condition */
        /** @var ExecutionContext $context */
        $condition->evaluate($context, $callback);
    }

    public function testGetDataContainerDefinition(): void
    {
        $obj = new TimeCondition(
            $this->createMock(Connection::class)
        );

        $definition = $obj->getDataContainerDefinition();
        self::assertInstanceOf(Definition::class, $definition);

        self::assertCount(0, $definition->selectors);
        self::assertCount(0, $definition->subPalettes);
        self::assertCount(1, $definition->fields);
        self::assertSame('cnd_time_executionTime', $definition->palette);
    }

    public function testGetDataPrototype(): void
    {
        $condition = new TimeCondition(
            $this->createMock(Connection::class)
        );

        $result = $condition->getDataPrototype(123);
        self::assertEquals($result, ['selectedTime' => null]);
    }

    private function getTimeCondition($execute): array
    {
        $parameters = new \stdClass();
        $parameters->id = 6;
        $parameters->cnd_time_executionTime = 12345;

        $context = $this->createMock(ExecutionContext::class);
        $context
            ->expects(self::once())
            ->method('getLog')
            ->with()
            ->willReturn([])
        ;
        $context
            ->expects(self::once())
            ->method('getParameters')
            ->with()
            ->willReturn($parameters)
        ;

        $result = $this->createMock(Result::class);
        $result
            ->expects(self::once())
            ->method('fetchOne')
            ->willReturn($execute)
        ;

        $connection = $this->createMock(Connection::class);
        $connection
            ->expects(self::once())
            ->method('executeQuery')
            ->with(
                'SELECT cnd_time_executionTime <> 0 && NOW() >= FROM_UNIXTIME(cnd_time_executionTime) FROM tl_eblick_trigger WHERE id=?',
                [6]
            )
            ->willReturn($result)
        ;

        return [new TimeCondition($connection), $context];
    }
}
