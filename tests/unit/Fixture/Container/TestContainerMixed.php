<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Container;

class TestContainerMixed
{
    private $items = [];

    public function __construct()
    {
        $this->items = $this->getItems();
    }

    protected function getItems()
    {
        return [
            "WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Container\\Entrypoint\\EntrypointA" => function () {
                static $item = null;

                if ($item === null) {
                    $item = new \WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Container\Entrypoint\EntrypointA(
                        $this->items["WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Container\\ClassD"]()
                    );
                }

                return $item;
            },
            "WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Container\\ClassD" => function () {
                static $item = null;

                if ($item === null) {
                    $item = new \WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Container\ClassD(
                    );
                }

                return $item;
            },
            "WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\DependencyGraph\\Container\\ClassC" => function () {
                $item = new \WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Container\ClassC(
                );

                return $item;
            },
        ];
    }
}
