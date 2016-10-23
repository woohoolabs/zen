<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Examples;

use Interop\Container\ContainerInterface;
use WoohooLabs\Zen\Config\AbstractContainerConfig;
use WoohooLabs\Zen\Config\EntryPoint\WildcardEntryPoint;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Config\Hint\WildcardHint;
use WoohooLabs\Zen\Examples\Service\AnimalService;
use WoohooLabs\Zen\Examples\Service\AnimalServiceInterface;
use WoohooLabs\Zen\Examples\Service\PlantService;
use WoohooLabs\Zen\Examples\Service\PlantServiceInterface;

class ContainerConfig extends AbstractContainerConfig
{
    protected function getEntryPoints(): array
    {
        return [
            new WildcardEntryPoint(__DIR__ . "/Controller"),
        ];
    }

    protected function getDefinitionHints(): array
    {
        return [
            ContainerInterface::class => Container::class,
            AnimalServiceInterface::class => AnimalService::class,
            PlantServiceInterface::class => DefinitionHint::prototype(PlantService::class),
        ];
    }

    protected function getWildcardHints(): array
    {
        return [
            WildcardHint::singleton(
                'WoohooLabs\Zen\Examples\Domain\*RepositoryInterface',
                'WoohooLabs\Zen\Examples\Infrastructure\Mysql*Repository',
                __DIR__ . "/Domain"
            )
        ];
    }
}
