<?php

use WoohooLabs\Zen\AbstractCompiledContainer;

class EmptyContainerWithoutNamespace extends AbstractCompiledContainer
{
    /**
     * @var string[]
     */
    protected static $entryPoints = [
        \EmptyContainerWithoutNamespace::class => 'EmptyContainerWithoutNamespace',
        \Psr\Container\ContainerInterface::class => 'Psr__Container__ContainerInterface',
    ];

    /**
     * @var string
     */
    protected $rootDirectory;

    public function __construct(string $rootDirectory = '')
    {
        $this->rootDirectory = $rootDirectory;
    }
}
