<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Container;

use \WoohooLabs\Dicone\AbstractContainer;

class TestContainerEmpty extends AbstractContainer
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
