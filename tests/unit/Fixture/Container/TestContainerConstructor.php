<?php
namespace WoohooLabs\Zen\Tests\Unit\Fixture\Container;

use \WoohooLabs\Zen\AbstractArrayMapContainer;

class TestContainerConstructor extends AbstractArrayMapContainer
{
    protected function getItems(): array
    {
        return [
            'WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorA' => function () {
                static $item = null;

                if ($item === null) {
                    $item = new \WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorA(
                        $this->getItem('WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorB'),
                        $this->getItem('WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorC'),
                        true,
                        null
                    );
                }

                return $item;
            },
            'WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorB' => function () {
                static $item = null;

                if ($item === null) {
                    $item = new \WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorB(
                    );
                }

                return $item;
            },
            'WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorC' => function () {
                static $item = null;

                if ($item === null) {
                    $item = new \WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorC(
                        $this->getItem('WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorD')
                    );
                }

                return $item;
            },
            'WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorD' => function () {
                static $item = null;

                if ($item === null) {
                    $item = new \WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorD(
                    );
                }

                return $item;
            },
            'WoohooLabs\Zen\Tests\Unit\Fixture\Container\TestContainerConstructor' => function () {
                return $this;
            },
        ];
    }
}
