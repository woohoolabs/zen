<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Examples;

use WoohooLabs\Zen\Config\AbstractContainerConfig;
use WoohooLabs\Zen\Config\EntryPoint\ClassEntryPoint;
use WoohooLabs\Zen\Config\EntryPoint\Psr4NamespaceEntryPoint;
use WoohooLabs\Zen\Config\Hint\ContextDependentDefinitionHint;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Config\Hint\Psr4WildcardHint;
use WoohooLabs\Zen\Examples\Controller\AnimalController;
use WoohooLabs\Zen\Examples\Controller\Authentication\AuthenticationController;
use WoohooLabs\Zen\Examples\Controller\PlantController;
use WoohooLabs\Zen\Examples\Controller\UserController;
use WoohooLabs\Zen\Examples\Service\AnimalService2;
use WoohooLabs\Zen\Examples\Service\AnimalServiceInterface;
use WoohooLabs\Zen\Examples\Service\AuthenticationService;
use WoohooLabs\Zen\Examples\Service\ExtendedAuthenticationService;
use WoohooLabs\Zen\Examples\Service\PlantService;
use WoohooLabs\Zen\Examples\Service\PlantServiceInterface;

class ContainerConfig extends AbstractContainerConfig
{
    protected function getEntryPoints(): array
    {
        return [
            ClassEntryPoint::create(AnimalServiceInterface::class)
                ->fileBased(),
            ClassEntryPoint::create(AnimalController::class)
                ->fileBased(),
            Psr4NamespaceEntryPoint::create("WoohooLabs\\Zen\\Examples\\Controller"),
        ];
    }

    protected function getDefinitionHints(): array
    {
        return [
            AnimalController::class => DefinitionHint::prototype(AnimalController::class),
            AnimalServiceInterface::class => AnimalService2::class,
            PlantServiceInterface::class => DefinitionHint::singleton(PlantService::class)
                ->setParameter("plantType", "sunflower")
                ->setProperty("plantType", "sunflower"),
            AuthenticationService::class => ContextDependentDefinitionHint::create()
                ->setClassContext(
                    DefinitionHint::singleton(AuthenticationService::class),
                    [
                        AnimalController::class,
                    ]
                )
                ->setClassContext(
                    DefinitionHint::prototype(AuthenticationService::class),
                    [
                        PlantController::class,
                    ]
                )
                ->setClassContext(
                    DefinitionHint::singleton(ExtendedAuthenticationService::class),
                    [
                        UserController::class,
                        AuthenticationController::class,
                    ]
                ),
        ];
    }

    protected function getWildcardHints(): array
    {
        return [
            Psr4WildcardHint::singleton(
                'WoohooLabs\Zen\Examples\Domain\*RepositoryInterface',
                'WoohooLabs\Zen\Examples\Infrastructure\Mysql*Repository'
            ),
        ];
    }
}
