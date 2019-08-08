<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Examples;

use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfig;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfigInterface;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfig;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfigInterface;
use WoohooLabs\Zen\Config\Preload\PreloadConfig;
use WoohooLabs\Zen\Config\Preload\PreloadConfigInterface;
use WoohooLabs\Zen\Config\Preload\Psr4NamespacePreload;
use WoohooLabs\Zen\Examples\Controller\AbstractController;
use WoohooLabs\Zen\Examples\Controller\ControllerInterface;

class CompilerConfig extends AbstractCompilerConfig
{
    public function getContainerNamespace(): string
    {
        return "WoohooLabs\\Zen\\Examples";
    }

    public function getContainerClassName(): string
    {
        return "Container";
    }

    public function useConstructorInjection(): bool
    {
        return true;
    }

    public function usePropertyInjection(): bool
    {
        return true;
    }

    public function getAutoloadConfig(): AutoloadConfigInterface
    {
        ;
        return AutoloadConfig::enabledGlobally(dirname(__DIR__))
            ->setAlwaysAutoloadedClasses(
                [
                    ControllerInterface::class,
                ]
            )
            ->setExcludedClasses(
                [
                    AbstractController::class
                ]
            );
    }

    public function getPreloadConfig(): PreloadConfigInterface
    {
        return PreloadConfig::create()
            ->setPreloadedClasses(
                [
                    Psr4NamespacePreload::create("WoohooLabs\\Zen\\Config"),
                    Psr4NamespacePreload::create("WoohooLabs\\Zen\\Utils"),
                    Psr4NamespacePreload::create("WoohooLabs\\Zen\\Container"),
                ]
            );
    }

    public function getFileBasedDefinitionConfig(): FileBasedDefinitionConfigInterface
    {
        return FileBasedDefinitionConfig::disabledGlobally("Definitions/");
    }

    public function getContainerConfigs(): array
    {
        return [
            new ContainerConfig(),
        ];
    }
}
