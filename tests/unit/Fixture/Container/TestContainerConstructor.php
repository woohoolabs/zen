<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Container;

class TestContainerConstructor
{
    private $items = [];

    public function __construct()
    {
        $this->items = $this->getItems();
    }

    protected function getItems()
    {
        return [
            "WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Constructor\\ConstructorA" => function () {
                $item = new \WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorA(
                    $this->items["WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Constructor\\ConstructorB"](),
                    $this->items["WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Constructor\\ConstructorC"](),
                    true,
                    null
                );

                return $item;
            },
            "WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Constructor\\ConstructorB" => function () {
                $item = new \WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorB(
                );

                return $item;
            },
            "WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Constructor\\ConstructorC" => function () {
                $item = new \WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorC(
                    $this->items["WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Constructor\\ConstructorD"]()
                );

                return $item;
            },
            "WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Constructor\\ConstructorD" => function () {
                $item = new \WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorD(
                );

                return $item;
            },
        ];
    }
}
