<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Examples;

use WoohooLabs\Zen\Config\AbstractContainerConfig;
use WoohooLabs\Zen\Config\EntryPoint\ClassEntryPoint;
use WoohooLabs\Zen\Config\EntryPoint\WildcardEntryPoint;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Config\Hint\WildcardHint;
use WoohooLabs\Zen\Examples\Controller\AnimalController;
use WoohooLabs\Zen\Examples\Service\AnimalService;
use WoohooLabs\Zen\Examples\Service\AnimalServiceInterface;
use WoohooLabs\Zen\Examples\Service\PlantService;
use WoohooLabs\Zen\Examples\Service\PlantServiceInterface;

class ContainerConfig extends AbstractContainerConfig
{
    protected function getEntryPoints(): array
    {
        return [
            ClassEntryPoint::create(AnimalController::class)
                ->autoload()
                ->fileBased(),
            new WildcardEntryPoint(__DIR__ . "/Controller"),
        ];
    }

    protected function getDefinitionHints(): array
    {
        return [
            AnimalServiceInterface::class => AnimalService::class,
            PlantServiceInterface::class => DefinitionHint::singleton(PlantService::class)
                ->setParameter("plantType", "sunflower")
                ->setProperty("plantType", "sunflower")
        ];
    }

    protected function getWildcardHints(): array
    {
        return [
            WildcardHint::singleton(
                __DIR__ . "/Domain",
                'WoohooLabs\Zen\Examples\Domain\*RepositoryInterface',
                'WoohooLabs\Zen\Examples\Infrastructure\Mysql*Repository'
            ),
        ];
    }
}
