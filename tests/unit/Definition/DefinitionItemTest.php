<?php
namespace WoohooLabs\Dicone\Tests\Unit\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Dicone\Definition\DefinitionItem;

class DefinitionItemTest extends TestCase
{
    /**
     * @test
     */
    public function singleton()
    {
        $item = DefinitionItem::singleton("Class");

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
        $item = DefinitionItem::prototype("Class");

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
        $item = new DefinitionItem("Class");

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
        $item = new DefinitionItem("Class");

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
        $item = new DefinitionItem("Class");
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
        $item = new DefinitionItem("Class");
        $item->setPrototypeScope();

        self::assertEquals(
            "prototype",
            $item->getScope()
        );
    }

    /**
     * @test
     */
    public function addRequiredConstructorParams()
    {
        $item = DefinitionItem::singleton("Class1")
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
        $item = DefinitionItem::singleton("Class1")
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
        $item = DefinitionItem::singleton("Class1")
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
