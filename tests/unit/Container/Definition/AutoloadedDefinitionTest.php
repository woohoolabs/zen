<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Container\Definition;

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

        $this->assertEquals("", $definition->getScope(""));
    }

    /**
     * @test
     */
    public function needsDependencyResolution()
    {
        $definition = new AutoloadedDefinition(new AutoloadConfig(true), "");

        $this->assertFalse($definition->needsDependencyResolution());
    }

    /**
     * @test
     */
    public function isAutoloaded()
    {
        $definition = new AutoloadedDefinition(new AutoloadConfig(true), "");

        $this->assertTrue($definition->isAutoloaded());
    }

    /**
     * @test
     */
    public function getClassDependencies()
    {
        $definition = new AutoloadedDefinition(new AutoloadConfig(true), "");

        $this->assertEmpty($definition->getClassDependencies());
    }
}
