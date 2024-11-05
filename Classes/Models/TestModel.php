<?php

namespace Classes\Models;
// root/Classes/Models => namespace Classes\Models;

class TestModel
{
    public function getTest(TestClass $test): string
    {
        return $test->smth();
    }
}
