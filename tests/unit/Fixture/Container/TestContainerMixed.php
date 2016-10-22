<?php
namespace WoohooLabs\Zen\Tests\Unit\Fixture\Container;

use \WoohooLabs\Zen\AbstractArrayMapContainer;

class TestContainerMixed extends AbstractArrayMapContainer
{
    protected function getItems(): array
    {
        return [
            'WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Container\Entrypoint\EntrypointA' => function () {
                static $item = null;

                if ($item === null) {
                    $item = new \WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Container\Entrypoint\EntrypointA(
                        $this->getItem('WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Container\ClassD')
                    );
                }

                return $item;
            },
            'WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Container\ClassD' => function () {
                static $item = null;

                if ($item === null) {
                    $item = new \WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Container\ClassD(
                    );
                }

                return $item;
            },
            'WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Container\Entrypoint\Sub\EntrypointB' => function () {
                static $item = null;

                if ($item === null) {
                    $item = new \WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Container\Entrypoint\Sub\EntrypointB(
                    );

                    $reflectionObject = new \ReflectionObject($item);
                    $this->setPropertyValue($reflectionObject, 'c', 'WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Container\ClassC');
                }

                return $item;
            },
            'WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Container\ClassC' => function () {
                $item = new \WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Container\ClassC(
                );

                return $item;
            },
            'WoohooLabs\Zen\Tests\Unit\Fixture\Container\TestContainerEmpty' => function () {
                return $this;
            },
        ];
    }
}
