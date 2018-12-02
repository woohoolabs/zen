<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfig;
use WoohooLabs\Zen\Container\Definition\AutoloadedDefinition;

class AutoloadedDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function getScope()
    {
        $definition = new AutoloadedDefinition(new AutoloadConfig(true), "");

        $scope = $definition->getScope("");

        $this->assertEquals("", $scope);
    }

    /**
     * @test
     */
    public function needsDependencyResolution()
    {
        $definition = new AutoloadedDefinition(new AutoloadConfig(true), "");

        $needsDependencyResolution = $definition->needsDependencyResolution();

        $this->assertFalse($needsDependencyResolution);
    }

    /**
     * @test
     */
    public function isAutoloaded()
    {
        $definition = new AutoloadedDefinition(new AutoloadConfig(true), "");

        $isAutoloaded = $definition->isAutoloaded();

        $this->assertTrue($isAutoloaded);
    }

    /**
     * @test
     */
    public function getClassDependencies()
    {
        $definition = new AutoloadedDefinition(new AutoloadConfig(true), "");

        $classDependencies = $definition->getClassDependencies();

        $this->assertEmpty($classDependencies);
    }
}
