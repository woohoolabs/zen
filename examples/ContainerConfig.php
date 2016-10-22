<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Examples;

use Interop\Container\ContainerInterface;
use WoohooLabs\Zen\Config\AbstractContainerConfig;
use WoohooLabs\Zen\Config\DefinitionHint\ClassDefinitionHint;
use WoohooLabs\Zen\Config\EntryPoint\DirectoryWildcardEntryPoint;
use WoohooLabs\Zen\Examples\Service\AnimalService;
use WoohooLabs\Zen\Examples\Service\AnimalServiceInterface;
use WoohooLabs\Zen\Examples\Service\PlantService;
use WoohooLabs\Zen\Examples\Service\PlantServiceInterface;

class ContainerConfig extends AbstractContainerConfig
{
    public function getEntryPoints(): array
    {
        return [
            new DirectoryWildcardEntryPoint(__DIR__ . "/Controller"),
        ];
    }

    public function getDefinitionHints(): array
    {
        return [
            ContainerInterface::class => Container::class,
            AnimalServiceInterface::class => AnimalService::class,
            PlantServiceInterface::class => ClassDefinitionHint::prototype(PlantService::class),
        ];
    }
}
