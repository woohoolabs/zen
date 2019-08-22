<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Config\Autoload;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfig;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\EntryPoint\EntryPointA;

class AutoloadConfigTest extends TestCase
{
    /**
     * @test
     */
    public function disabledGlobally(): void
    {
        $autoloadConfig = AutoloadConfig::disabledGlobally("");

        $isGlobalAutoloadEnabled = $autoloadConfig->isGlobalAutoloadEnabled();

        $this->assertFalse($isGlobalAutoloadEnabled);
    }

    /**
     * @test
     */
    public function enabledGlobally(): void
    {
        $autoloadConfig = AutoloadConfig::enabledGlobally("");

        $isGlobalAutoloadEnabled = $autoloadConfig->isGlobalAutoloadEnabled();

        $this->assertTrue($isGlobalAutoloadEnabled);
    }

    /**
     * @test
     */
    public function create(): void
    {
        $autoloadConfig = AutoloadConfig::create(true, "/var/www");

        $isGlobalAutoloadEnabled = $autoloadConfig->isGlobalAutoloadEnabled();
        $rootDirectory = $autoloadConfig->getRootDirectory();

        $this->assertTrue($isGlobalAutoloadEnabled);
        $this->assertEquals("/var/www", $rootDirectory);
    }

    /**
     * @test
     */
    public function setRootDirectory(): void
    {
        $autoloadConfig = new AutoloadConfig(true);

        $autoloadConfig->setRootDirectory("/var/www");

        $this->assertEquals("/var/www", $autoloadConfig->getRootDirectory());
    }

    /**
     * @test
     */
    public function getExcludedClassesIsEmptyByDefault(): void
    {
        $autoloadConfig = new AutoloadConfig(true);

        $excludedClasses = $autoloadConfig->getExcludedClasses();

        $this->assertEmpty($excludedClasses);
    }

    /**
     * @test
     */
    public function setExcludedClasses(): void
    {
        $autoloadConfig = new AutoloadConfig(true);

        $autoloadConfig->setExcludedClasses([EntryPointA::class]);

        $this->assertEquals([EntryPointA::class], $autoloadConfig->getExcludedClasses());
    }
}
