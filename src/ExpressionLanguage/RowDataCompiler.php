<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\ExpressionLanguage;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class RowDataCompiler
{
    /**
     * Precompile an expression for row-based data with each column name being a variable. Use the returned closure like
     * $return($rowData) with $rowData being an associative array that has $columnNames' values as keys. The closure
     * evaluates to true/false depending on the given expression and data.
     */
    public function compileRowExpression(string $expression, array $columnNames): \Closure
    {
        $dataMapping = [];

        foreach ($columnNames as $columnName) {
            $dataMapping['rowData[\''.$columnName.'\']'] = $columnName;
        }

        $expressionLanguage = new ExpressionLanguage();

        // precompile expression
        $compiledExpression = $expressionLanguage->compile($expression, $dataMapping);

        // evaluate in callback
        return
            static fn ($rowData): bool => true === @eval('return '.$compiledExpression.';');
    }
}
