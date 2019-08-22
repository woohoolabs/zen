<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Config\EntryPoint;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfig;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfig;
use WoohooLabs\Zen\Tests\Double\TestEntryPoint;

class AbstractEntryPointTest extends TestCase
{
    /**
     * @test
     */
    public function isAutoloadedFalseByDefaultWhenDisabledGlobally(): void
    {
        $entryPoint = new TestEntryPoint();

        $isAutoloaded = $entryPoint->isAutoloaded(AutoloadConfig::disabledGlobally());

        $this->assertFalse($isAutoloaded);
    }

    /**
     * @test
     */
    public function isAutoloadedTrueByDefaultWhenEnabledGlobally(): void
    {
        $entryPoint = new TestEntryPoint();

        $isAutoloaded = $entryPoint->isAutoloaded(AutoloadConfig::enabledGlobally(""));

        $this->assertTrue($isAutoloaded);
    }

    /**
     * @test
     */
    public function autoloadWhenDisabledGlobally(): void
    {
        $entryPoint = new TestEntryPoint();

        $entryPoint->autoload();

        $this->assertTrue($entryPoint->isAutoloaded(AutoloadConfig::disabledGlobally()));
    }

    /**
     * @test
     */
    public function autoloadWhenEnabledGlobally(): void
    {
        $entryPoint = new TestEntryPoint();

        $entryPoint->autoload();

        $this->assertTrue($entryPoint->isAutoloaded(AutoloadConfig::enabledGlobally("")));
    }

    /**
     * @test
     */
    public function disableAutoloadWhenDisabledGlobally(): void
    {
        $entryPoint = new TestEntryPoint();

        $entryPoint->disableAutoload();

        $this->assertFalse($entryPoint->isAutoloaded(AutoloadConfig::disabledGlobally()));
    }

    /**
     * @test
     */
    public function disabledAutoloadWhenEnabledGlobally(): void
    {
        $entryPoint = new TestEntryPoint();

        $entryPoint->autoload();
        $entryPoint->disableAutoload();

        $this->assertFalse($entryPoint->isAutoloaded(AutoloadConfig::enabledGlobally("")));
    }

    /**
     * @test
     */
    public function isFileBasedFalseByDefaultWhenDisabledGlobally(): void
    {
        $entryPoint = new TestEntryPoint();

        $isFileBased = $entryPoint->isFileBased(FileBasedDefinitionConfig::disabledGlobally());

        $this->assertFalse($isFileBased);
    }

    /**
     * @test
     */
    public function isFileBasedTrueByDefaultWhenEnabledGlobally(): void
    {
        $entryPoint = new TestEntryPoint();

        $isFileBased = $entryPoint->isFileBased(FileBasedDefinitionConfig::enabledGlobally(""));

        $this->assertTrue($isFileBased);
    }

    /**
     * @test
     */
    public function fileBasedWhenDisabledGlobally(): void
    {
        $entryPoint = new TestEntryPoint();

        $entryPoint->fileBased();

        $this->assertTrue($entryPoint->isFileBased(FileBasedDefinitionConfig::disabledGlobally()));
    }

    /**
     * @test
     */
    public function fileBasedWhenEnabledGlobally(): void
    {
        $entryPoint = new TestEntryPoint();

        $entryPoint->fileBased();

        $this->assertTrue($entryPoint->isFileBased(FileBasedDefinitionConfig::enabledGlobally("")));
    }

    /**
     * @test
     */
    public function disableFileBasedWhenDisabledGlobally(): void
    {
        $entryPoint = new TestEntryPoint();

        $entryPoint->fileBased();
        $entryPoint->disableFileBased();

        $this->assertFalse($entryPoint->isFileBased(FileBasedDefinitionConfig::disabledGlobally()));
    }

    /**
     * @test
     */
    public function disableFileBasedWhenEnabledGlobally(): void
    {
        $entryPoint = new TestEntryPoint();

        $entryPoint->fileBased();
        $entryPoint->disableFileBased();

        $this->assertFalse($entryPoint->isFileBased(FileBasedDefinitionConfig::enabledGlobally("")));
    }
}
