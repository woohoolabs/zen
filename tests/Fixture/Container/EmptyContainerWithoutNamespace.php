<?php

use WoohooLabs\Zen\AbstractCompiledContainer;

class EmptyContainerWithoutNamespace extends AbstractCompiledContainer
{
    /**
     * @var string[]
     */
    protected static $entryPoints = [
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
