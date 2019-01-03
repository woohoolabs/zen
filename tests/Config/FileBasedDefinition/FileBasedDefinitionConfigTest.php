<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Config\FileBasedDefinition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfig;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\EntryPoint\EntryPointA;

class FileBasedDefinitionConfigTest extends TestCase
{
    /**
     * @test
     */
    public function disabledGlobally()
    {
        $fileBasedDefinitionConfig = FileBasedDefinitionConfig::disabledGlobally();

        $isGlobalFileBasedDefinitionEnabled = $fileBasedDefinitionConfig->isGlobalFileBasedDefinitionEnabled();

        $this->assertFalse($isGlobalFileBasedDefinitionEnabled);
    }

    /**
     * @test
     */
    public function enabledGlobally()
    {
        $fileBasedDefinitionConfig = FileBasedDefinitionConfig::enabledGlobally("");

        $isGlobalFileBasedDefinitionEnabled = $fileBasedDefinitionConfig->isGlobalFileBasedDefinitionEnabled();

        $this->assertTrue($isGlobalFileBasedDefinitionEnabled);
    }

    /**
     * @test
     */
    public function create()
    {
        $fileBasedDefinitionConfig = FileBasedDefinitionConfig::create(true, "/Definitions/");

        $isGlobalFileBasedDefinitionEnabled = $fileBasedDefinitionConfig->isGlobalFileBasedDefinitionEnabled();
        $definitionDirectory = $fileBasedDefinitionConfig->getRelativeDefinitionDirectory();

        $this->assertTrue($isGlobalFileBasedDefinitionEnabled);
        $this->assertEquals("Definitions", $definitionDirectory);
    }

    /**
     * @test
     */
    public function setRelativeDefinitionDirectory()
    {
        $fileBasedDefinitionConfig = new FileBasedDefinitionConfig(true);

        $fileBasedDefinitionConfig->setRelativeDefinitionDirectory("Definitions");

        $this->assertEquals("Definitions", $fileBasedDefinitionConfig->getRelativeDefinitionDirectory());
    }

    /**
     * @test
     */
    public function getExcludedClassesIsEmptyByDefault()
    {
        $fileBasedDefinitionConfig = new FileBasedDefinitionConfig(true);

        $excludedClasses = $fileBasedDefinitionConfig->getExcludedDefinitions();

        $this->assertEmpty($excludedClasses);
    }

    /**
     * @test
     */
    public function setExcludedClasses()
    {
        $fileBasedDefinitionConfig = new FileBasedDefinitionConfig(true);

        $fileBasedDefinitionConfig->setExcludedDefinitions([EntryPointA::class]);

        $this->assertEquals([EntryPointA::class], $fileBasedDefinitionConfig->getExcludedDefinitions());
    }
}
