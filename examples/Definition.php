<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Examples;

use Interop\Container\ContainerInterface;
use WoohooLabs\Dicone\Definition\DefinitionHint;
use WoohooLabs\Dicone\Definition\DefinitionInterface;
use WoohooLabs\Dicone\Definition\DirectoryWildcardEntrypoint;
use WoohooLabs\Dicone\Examples\Service\AnimalService;
use WoohooLabs\Dicone\Examples\Service\AnimalServiceInterface;
use WoohooLabs\Dicone\Examples\Service\PlantService;
use WoohooLabs\Dicone\Examples\Service\PlantServiceInterface;

class Definition implements DefinitionInterface
{
    public function getEntryPoints(): array
    {
        return [
            new DirectoryWildcardEntrypoint(__DIR__ . "/Controller"),
        ];
    }

    public function getDefinitionHints(): array
    {
        return [
            ContainerInterface::class => DefinitionHint::singleton(Container::class),
            AnimalServiceInterface::class => DefinitionHint::singleton(AnimalService::class),
            PlantServiceInterface::class => DefinitionHint::prototype(PlantService::class),
        ];
    }
}
