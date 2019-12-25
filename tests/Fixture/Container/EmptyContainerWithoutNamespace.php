<?php

declare(strict_types=1);

use WoohooLabs\Zen\AbstractCompiledContainer;

class EmptyContainerWithoutNamespace extends AbstractCompiledContainer
{
    /** @var string[] */
    protected static array $entryPoints = [
    ];
    protected string $rootDirectory;

    public function __construct(string $rootDirectory = "")
    {
        $this->rootDirectory = $rootDirectory;
    }
}
