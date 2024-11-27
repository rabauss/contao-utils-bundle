<?php

namespace HeimrichHannot\UtilsBundle\Tests\StaticUtil\StaticClassUtilTestAssets;

class ExampleClassUsingTrait
{
    use ExampleTrait;

    public function exampleClassUsingTraitMethod(mixed $return = 'example'): mixed
    {
        return $return;
    }
}