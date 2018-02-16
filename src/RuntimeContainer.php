<?php
declare(strict_types=1);

namespace WoohooLabs\Zen;

use Psr\Container\ContainerInterface;
use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Container\Builder\RuntimeContainerBuilder;

class RuntimeContainer implements ContainerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(AbstractCompilerConfig $compilerConfig, string $rootDirectory = "")
    {
        $containerBuilder = new RuntimeContainerBuilder($compilerConfig);
        $containerBuilder->build();

        $className = $compilerConfig->getContainerFqcn();
        $this->container = new $className($rootDirectory);
    }

    public function has($id): bool
    {
        return $this->container->has($id);
    }

    public function get($id)
    {
        return $this->container->get($id);
    }
}
