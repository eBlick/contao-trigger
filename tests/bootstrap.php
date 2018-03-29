<?php

declare(strict_types=1);

error_reporting(E_ALL);

include(__DIR__.'/../vendor/autoload.php');

// Autoload the fixture classes
$fixtureLoader = function ($class): void {
    if (class_exists($class, false) || interface_exists($class, false) || trait_exists($class, false)) {
        return;
    }

    if (false !== strpos($class, '\\') && 0 !== strncmp($class, 'Contao\\', 7)) {
        return;
    }

    if (0 === strncmp($class, 'Contao\\', 7)) {
        $class = substr($class, 7);
    }

    $file = str_replace('\\', '/', $class);

    if (file_exists(__DIR__.'/Fixtures/library/'.$file.'.php')) {
        include_once __DIR__.'/Fixtures/library/'.$file.'.php';
        class_alias('Contao\Fixtures\\'.$class, 'Contao\\'.$class);
    }

    $namespaced = 'Contao\\'.$class;

    if (class_exists($namespaced) || interface_exists($namespaced) || trait_exists($namespaced)) {
        class_alias($namespaced, $class);
    }
};

spl_autoload_register($fixtureLoader, true, true);