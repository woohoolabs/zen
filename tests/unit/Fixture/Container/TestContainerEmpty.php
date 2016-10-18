<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Container;

use \WoohooLabs\Dicone\AbstractContainer;

class TestContainerEmpty extends AbstractContainer
{
    protected function getItems()
    {
        return [
        ];
    }
}
