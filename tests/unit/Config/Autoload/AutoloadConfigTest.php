<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Config\Autoload;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfig;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\EntryPoint\EntryPointA;

class AutoloadConfigTest extends TestCase
{
    /**
     * @test
     */
    public function disabledGlobally()
    {
        $autoloadConfig = AutoloadConfig::disabledGlobally("");

        $this->assertFalse($autoloadConfig->isGlobalAutoloadEnabled());
    }

    /**
     * @test
     */
    public function enabledGlobally()
    {
        $autoloadConfig = AutoloadConfig::enabledGlobally("");

        $this->assertTrue($autoloadConfig->isGlobalAutoloadEnabled());
    }

    /**
     * @test
     */
    public function create()
    {
        $autoloadConfig = AutoloadConfig::create(true,"/var/www");

        $this->assertTrue($autoloadConfig->isGlobalAutoloadEnabled());
        $this->assertEquals("/var/www", $autoloadConfig->getRootDirectory());
    }

    /**
     * @test
     */
    public function setRootDirectory()
    {
        $autoloadConfig = new AutoloadConfig(true);
        $autoloadConfig->setRootDirectory("/var/www");

        $this->assertEquals("/var/www", $autoloadConfig->getRootDirectory());
    }

    /**
     * @test
     */
    public function getExcludedClassesIsEmptyByDefault()
    {
        $autoloadConfig = new AutoloadConfig(true);

        $this->assertEmpty($autoloadConfig->getExcludedClasses());
    }

    /**
     * @test
     */
    public function setExcludedClasses()
    {
        $autoloadConfig = new AutoloadConfig(true);
        $autoloadConfig->setExcludedClasses([EntryPointA::class]);

        $this->assertEquals(
            [EntryPointA::class],
            $autoloadConfig->getExcludedClasses()
        );
    }
}
