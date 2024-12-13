<?php

namespace Core;

use Core\Traits\Queryable;
use ReflectionProperty;

abstract class Model
{
    public int $id;

    use Queryable;


    public function toArray(): array
    {
        $properties = [];

        $reflect = new \ReflectionClass($this);
        $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($props as $prop) {
            if (in_array($prop->getName(), ['tableName', 'commands'])) {
                continue;
            }

            $properties[$prop->getName()] = $prop->getValue($this);
        }

        return $properties;
    }
}
