<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Container;

class TestContainerConstructor implements \WoohooLabs\Dicone\ItemContainerInterface
{
    private $items = [];

    public function __construct()
    {
        $this->items = $this->getItems();
    }

    public function hasItem(string $id): bool
    {
        return isset($this->items[$id]);
    }

    public function getItem(string $id)
    {
        return $this->items[$id]();
    }

    private function getItems()
    {
        return [
            "WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Constructor\\ConstructorA" => function () {
                static $item = null;

                if ($item === null) {
                    $item = new \WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorA(
                        $this->items["WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Constructor\\ConstructorB"](),
                        $this->items["WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Constructor\\ConstructorC"](),
                        true,
                        null
                    );
                }

                return $item;
            },
            "WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Constructor\\ConstructorB" => function () {
                static $item = null;

                if ($item === null) {
                    $item = new \WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorB(
                    );
                }

                return $item;
            },
            "WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Constructor\\ConstructorC" => function () {
                static $item = null;

                if ($item === null) {
                    $item = new \WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorC(
                        $this->items["WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Constructor\\ConstructorD"]()
                    );
                }

                return $item;
            },
            "WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Constructor\\ConstructorD" => function () {
                static $item = null;

                if ($item === null) {
                    $item = new \WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorD(
                    );
                }

                return $item;
            },
        ];
    }
}
