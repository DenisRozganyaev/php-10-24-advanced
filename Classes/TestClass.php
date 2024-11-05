<?php

namespace Classes; // root => dir Classes => namespace Classes;

use Classes\Models\TestClass as AnotherClass;
use Classes\Models\TestModel;

class TestClass
{
    public function __construct(TestModel $model)
    {
        echo $model->getTest(new AnotherClass());
    }
}
