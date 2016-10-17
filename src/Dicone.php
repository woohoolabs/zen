<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone;

use Interop\Container\ContainerInterface;
use WoohooLabs\Dicone\Exception\DiconeNotFoundException;

class Dicone implements ContainerInterface
{
    /**
     * @var ItemContainerInterface
     */
    private $container;

    public function __construct(ItemContainerInterface $itemContainer)
    {
        $this->container = $itemContainer;
    }

    public function setContainer(string $filename)
    {
        $this->container = require $filename;
    }

    public function has($id)
    {
        return $this->container->hasItem($id);
    }

    public function get($id)
    {
        if ($this->has($id) === false) {
            throw new DiconeNotFoundException($id);
        }

        return $this->container->getItem($id);
    }
}
