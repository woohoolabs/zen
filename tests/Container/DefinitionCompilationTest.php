<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfig;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfig;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\DefinitionCompilation;

class DefinitionCompilationTest extends TestCase
{
    /**
     * @test
     */
    public function getAutoloadConfig(): void
    {
        $compilation = new DefinitionCompilation(
            AutoloadConfig::disabledGlobally(),
            FileBasedDefinitionConfig::disabledGlobally(),
            []
        );

        $autoloadConfig = $compilation->getAutoloadConfig();

        $this->assertEquals(AutoloadConfig::disabledGlobally(), $autoloadConfig);
    }

    /**
     * @test
     */
    public function getFileBasedDefinitionConfig(): void
    {
        $compilation = new DefinitionCompilation(
            AutoloadConfig::disabledGlobally(),
            FileBasedDefinitionConfig::disabledGlobally(),
            []
        );

        $fileBasedDefinitionConfig = $compilation->getFileBasedDefinitionConfig();

        $this->assertEquals(FileBasedDefinitionConfig::disabledGlobally(), $fileBasedDefinitionConfig);
    }

    /**
     * @test
     */
    public function getDefinitions(): void
    {
        $compilation = new DefinitionCompilation(
            AutoloadConfig::disabledGlobally(),
            FileBasedDefinitionConfig::disabledGlobally(),
            [
                "X\\A" => ClassDefinition::singleton(""),
            ]
        );

        $definitions = $compilation->getDefinitions();

        $this->assertEquals(
            [
                "X\\A" => ClassDefinition::singleton(""),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function getDefinition(): void
    {
        $compilation = new DefinitionCompilation(
            AutoloadConfig::disabledGlobally(),
            FileBasedDefinitionConfig::disabledGlobally(),
            [
                "X\\A" => ClassDefinition::singleton(""),
            ]
        );

        $definition = $compilation->getDefinition("X\\A");

        $this->assertEquals(ClassDefinition::singleton(""), $definition);
    }
}
