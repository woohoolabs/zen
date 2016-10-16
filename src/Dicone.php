<?php
namespace WoohooLabs\Dicone;

use Interop\Container\ContainerInterface;
use WoohooLabs\Dicone\Exception\DiconeNotFoundException;

class Dicone implements ContainerInterface
{
    /**
     * @var array
     */
    private $container = [];

    public function __construct()
    {
    }

    public function setContainer(string $filename)
    {
        $this->container = require $filename;
    }

    public function has($id)
    {
        return isset($this->container[$id]);
    }

    public function get($id)
    {
        if ($this->has($id) === false) {
            throw new DiconeNotFoundException($id);
        }

        return $this->container;
    }
}
