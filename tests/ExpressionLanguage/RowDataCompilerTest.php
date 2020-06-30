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

namespace EBlick\ContaoTrigger\Test\ExpressionLanguage;

use EBlick\ContaoTrigger\ExpressionLanguage\RowDataCompiler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\SyntaxError;

class RowDataCompilerTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(RowDataCompiler::class, new RowDataCompiler());
    }

    public function testCompileRowExpression(): void
    {
        $compiler = new RowDataCompiler();

        $expression  = 'a + b == (5 - c)';
        $columnNames = ['a', 'b', 'c'];

        $closure = $compiler->compileRowExpression($expression, $columnNames);

        $this->assertFalse($closure(['a' => 1, 'b' => 2, 'c' => 3])); // 1 + 2 == 5 - 3
        $this->assertTrue($closure(['a' => 1, 'b' => 2, 'c' => 2])); // 1 + 2 == 5 - 2
    }

    public function testCompileRowExpressionWithSyntaxError(): void
    {
        $compiler = new RowDataCompiler();

        $expression  = 'a + b =x= (5 - c)';
        $columnNames = ['a', 'b', 'c'];

        $this->expectException(SyntaxError::class);
        $compiler->compileRowExpression($expression, $columnNames);
    }

    public function testCompileRowExpressionWithMissingColumnNames(): void
    {
        $compiler = new RowDataCompiler();

        $expression  = 'a + b == (5 - c)';
        $columnNames = ['a', 'b'];

        $this->expectException(SyntaxError::class);
        $compiler->compileRowExpression($expression, $columnNames);
    }
}
