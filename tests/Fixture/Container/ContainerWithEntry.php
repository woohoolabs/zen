<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\Container;

use WoohooLabs\Zen\AbstractCompiledContainer;

class ContainerWithEntry extends AbstractCompiledContainer
{
    protected static array $entryPoints = [
    ];
    protected string $rootDirectory;

    public function __construct(string $rootDirectory = "")
    {
        $this->rootDirectory = $rootDirectory;
    }
}
