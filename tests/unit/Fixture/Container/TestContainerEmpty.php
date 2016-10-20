<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Container;

use \WoohooLabs\Dicone\AbstractArrayMapContainer;

class TestContainerEmpty extends AbstractArrayMapContainer
{
    protected function getItems(): array
    {
        return [
            'WoohooLabs\Dicone\Tests\Unit\Fixture\Container\TestContainerEmpty' => function () {
                return $this;
            },
        ];
    }
}
