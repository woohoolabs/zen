<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Examples;

use Interop\Container\ContainerInterface;
use WoohooLabs\Dicone\Config\AbstractContainerConfig;
use WoohooLabs\Dicone\Config\DefinitionHint\DefinitionHint;
use WoohooLabs\Dicone\Config\EntryPoint\DirectoryWildcardEntryPoint;
use WoohooLabs\Dicone\Examples\Service\AnimalService;
use WoohooLabs\Dicone\Examples\Service\AnimalServiceInterface;
use WoohooLabs\Dicone\Examples\Service\PlantService;
use WoohooLabs\Dicone\Examples\Service\PlantServiceInterface;

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
            PlantServiceInterface::class => DefinitionHint::prototype(PlantService::class),
        ];
    }
}
