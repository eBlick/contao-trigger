<?php

declare(strict_types=1);

use Contao\EasyCodingStandard\Set\SetList;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withSets([SetList::CONTAO])
    ->withParallel()
    ->withConfiguredRule(HeaderCommentFixer::class, [
        'header' => "@copyright LUMAS Consulting\n@license   LGPL-3.0+\n@link      https://github.com/lumas-consulting/contao-trigger",
    ])
;