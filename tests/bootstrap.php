<?php

declare(strict_types=1);

include(__DIR__ . '/../vendor/autoload.php');

spl_autoload_register(static function ($class) {
    if ('Model' === $class) {
        include __DIR__ . '/../vendor/contao/core-bundle/src/Resources/contao/library/Contao/Model.php';
    }
});
