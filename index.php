<?php

spl_autoload_register(function ($namespace) {
    $namespace = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
    $file = __DIR__ . "/$namespace.php";

    echo $file . '<br>';

    if (!file_exists($file)) {
        throw new Exception("Class $namespace not found");
    }

    require_once $file;
});

use Classes\TestClass;
use Classes\Models\TestModel;
use Classes\Models\TestClass as MTC;

$test = new TestClass(new TestModel());

