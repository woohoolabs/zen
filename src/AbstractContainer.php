<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone;

use Interop\Container\ContainerInterface;
use ReflectionObject;
use WoohooLabs\Dicone\Exception\DiconeNotFoundException;

abstract class AbstractContainer implements ContainerInterface
{
    /**
     * @var array
     */
    private $items;

    final public function __construct()
    {
        $this->items = $this->getItems();
    }

    abstract protected function getItems(): array;

    public function has($id)
    {
        return array_key_exists($id, $this->items);
    }

    public function get($id)
    {
        if ($this->has($id) === false) {
            throw new DiconeNotFoundException($id);
        }

        return $this->getItem($id);
    }

    protected function getItem(string $id)
    {
        return $this->items[$id]();
    }

    protected function setPropertyValue(ReflectionObject $reflectionObject, $object, string $name, string $item)
    {
        $property = $reflectionObject->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($object, $this->getItem($item));
    }
}
