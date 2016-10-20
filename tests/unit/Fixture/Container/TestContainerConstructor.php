<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Container;

use \WoohooLabs\Dicone\AbstractArrayMapContainer;

class TestContainerConstructor extends AbstractArrayMapContainer
{
    protected function getItems(): array
    {
        return [
            'WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorA' => function () {
                static $item = null;

                if ($item === null) {
                    $item = new \WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorA(
                        $this->getItem('WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorB'),
                        $this->getItem('WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorC'),
                        true,
                        null
                    );
                }

                return $item;
            },
            'WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorB' => function () {
                static $item = null;

                if ($item === null) {
                    $item = new \WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorB(
                    );
                }

                return $item;
            },
            'WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorC' => function () {
                static $item = null;

                if ($item === null) {
                    $item = new \WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorC(
                        $this->getItem('WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorD')
                    );
                }

                return $item;
            },
            'WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorD' => function () {
                static $item = null;

                if ($item === null) {
                    $item = new \WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorD(
                    );
                }

                return $item;
            },
            'WoohooLabs\Dicone\Tests\Unit\Fixture\Container\TestContainerConstructor' => function () {
                return $this;
            },
        ];
    }
}
