<?php

namespace HeimrichHannot\UtilsBundle\Tests\StaticUtil\StaticClassUtilTestAssets;

trait ExampleTrait
{
    public function exampleTraitMethod(mixed $return = 'example'): mixed
    {
        return $return;
    }
}