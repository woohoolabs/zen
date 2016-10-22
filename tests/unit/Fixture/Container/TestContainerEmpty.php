<?php
namespace WoohooLabs\Zen\Tests\Unit\Fixture\Container;

use \WoohooLabs\Zen\AbstractArrayMapContainer;

class TestContainerEmpty extends AbstractArrayMapContainer
{
    protected function getItems(): array
    {
        return [
            'WoohooLabs\Zen\Tests\Unit\Fixture\Container\TestContainerEmpty' => function () {
                return $this;
            },
        ];
    }
}
