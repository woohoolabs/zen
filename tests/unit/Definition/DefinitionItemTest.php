<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Dicone\Definition\Definition;

class DefinitionItemTest extends TestCase
{
    /**
     * @test
     */
    public function singleton()
    {
        $item = Definition::singleton("Class");

        self::assertEquals(
            "singleton",
            $item->getScope()
        );
    }

    /**
     * @test
     */
    public function prototype()
    {
        $item = Definition::prototype("Class");

        self::assertEquals(
            "prototype",
            $item->getScope()
        );
    }

    /**
     * @test
     */
    public function singletonByDefault()
    {
        $item = new Definition("Class");

        self::assertEquals(
            "singleton",
            $item->getScope()
        );
    }

    /**
     * @test
     */
    public function getClassName()
    {
        $item = new Definition("Class");

        self::assertEquals(
            "Class",
            $item->getClassName()
        );
    }

    /**
     * @test
     */
    public function setSingletonScope()
    {
        $item = new Definition("Class");
        $item->setSingletonScope();

        self::assertEquals(
            "singleton",
            $item->getScope()
        );
    }

    /**
     * @test
     */
    public function setPrototypeScope()
    {
        $item = new Definition("Class");
        $item->setPrototypeScope();

        self::assertEquals(
            "prototype",
            $item->getScope()
        );
    }

    /**
     * @test
     */
    public function isSingletonScopeTrue()
    {
        $item = new Definition("Class");

        self::assertTrue($item->isSingletonScope());
    }

    /**
     * @test
     */
    public function isSingletonScopeFalse()
    {
        $item = Definition::prototype("Class");

        self::assertFalse($item->isSingletonScope());
    }

    /**
     * @test
     */
    public function addRequiredConstructorParams()
    {
        $item = Definition::singleton("Class1")
            ->addRequiredConstructorParam("Class2")
            ->addRequiredConstructorParam("Class3");

        self::assertEquals(
            [
                ["class" => "Class2"],
                ["class" => "Class3"],
            ],
            $item->getConstructorParams()
        );
    }

    /**
     * @test
     */
    public function addOptionalConstructorParams()
    {
        $item = Definition::singleton("Class1")
            ->addOptionalConstructorParam(true)
            ->addOptionalConstructorParam([])
            ->addOptionalConstructorParam(null)
            ->addOptionalConstructorParam("test");

        self::assertEquals(
            [
                ["default" => true],
                ["default" => []],
                ["default" => null],
                ["default" => "test"],
            ],
            $item->getConstructorParams()
        );
    }

    /**
     * @test
     */
    public function addProperty()
    {
        $item = Definition::singleton("Class1")
            ->addProperty("property1", "Class1")
            ->addProperty("property2", "Class2")
            ->addProperty("property3", "Class3");

        self::assertEquals(
            [
                "property1" => "Class1",
                "property2" => "Class2",
                "property3" => "Class3",
            ],
            $item->getProperties()
        );
    }
}
