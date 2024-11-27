<?php

namespace HeimrichHannot\UtilsBundle\Tests\StaticUtil\StaticClassUtilTestAssets;

class ExampleClass
{
    public function exampleClassMethod(mixed $return = 'example'): mixed
    {
        return $return;
    }
}