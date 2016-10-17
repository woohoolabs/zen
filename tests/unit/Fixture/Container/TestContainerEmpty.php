<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Container;

class TestContainerEmpty
{
    private $items = [];

    public function __construct()
    {
        $this->items = $this->getItems();
    }

    protected function getItems()
    {
        return [
        ];
    }
}
