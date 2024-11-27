<?php

namespace StaticUtil;

use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\UtilsBundle\StaticUtil\StaticClassUtil;
use HeimrichHannot\UtilsBundle\Tests\StaticUtil\StaticClassUtilTestAssets\ExampleClass;
use HeimrichHannot\UtilsBundle\Tests\StaticUtil\StaticClassUtilTestAssets\ExampleClassUsingTrait;
use HeimrichHannot\UtilsBundle\Tests\StaticUtil\StaticClassUtilTestAssets\ExampleTrait;

class StaticClassUtilTest extends ContaoTestCase
{
    public function testHasTrait()
    {
        $this->assertFalse(StaticClassUtil::hasTrait(ExampleClass::class, ExampleTrait::class));
        $this->assertTrue(StaticClassUtil::hasTrait(ExampleClassUsingTrait::class, ExampleTrait::class));

        $entityWithoutTrait = new ExampleClass();
        $this->assertEquals('no-trait', $entityWithoutTrait->exampleClassMethod('no-trait'));
        $this->assertFalse(StaticClassUtil::hasTrait($entityWithoutTrait, ExampleTrait::class));

        $entityWithTrait = new ExampleClassUsingTrait();
        $this->assertEquals('using-trait', $entityWithTrait->exampleClassUsingTraitMethod('using-trait'));
        $this->assertEquals('from-trait', $entityWithTrait->exampleTraitMethod('from-trait'));
        $this->assertTrue(StaticClassUtil::hasTrait($entityWithTrait, ExampleTrait::class));

        $classWithoutTrait = new class {
            public function example(): string
            {
                return 'test-without-trait';
            }
        };
        $this->assertEquals('test-without-trait', $classWithoutTrait->example());
        $this->assertFalse(StaticClassUtil::hasTrait($classWithoutTrait, ExampleTrait::class));

        $classUsingTrait = new class {
            use ExampleTrait;

            public function example(): string
            {
                return 'test-with-trait';
            }
        };
        $this->assertEquals('test-with-trait', $classUsingTrait->example());
        $this->assertEquals('test-from-trait', $classUsingTrait->exampleTraitMethod('test-from-trait'));
        $this->assertTrue(StaticClassUtil::hasTrait($classUsingTrait, ExampleTrait::class));
    }
}