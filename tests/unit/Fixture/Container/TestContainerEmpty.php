<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Container;

class TestContainerEmpty implements \WoohooLabs\Dicone\ItemContainerInterface
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
        ];
    }
}
