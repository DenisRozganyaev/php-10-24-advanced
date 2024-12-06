<?php

namespace Core;

use Core\Traits\Queryable;

abstract class Model
{
    public int $id;

    use Queryable;
}
