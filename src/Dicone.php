<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone;

use Interop\Container\ContainerInterface;

class Dicone implements ContainerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function has($id)
    {
        return $this->container->has($id);
    }

    public function get($id)
    {
        return $this->container->get($id);
    }
}
