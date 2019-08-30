<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Config\Preload;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Preload\ClassPreload;
use WoohooLabs\Zen\Config\Preload\PreloadConfig;

class PreloadConfigTest extends TestCase
{
    /**
     * @test
     */
    public function getRelativeBasePathWhenEmpty(): void
    {
        $preloadConfig = PreloadConfig::create("");

        $relativeBasePath = $preloadConfig->getRelativeBasePath();

        $this->assertEquals("", $relativeBasePath);
    }

    /**
     * @test
     */
    public function getRelativeBasePathWhenNonEmpty(): void
    {
        $preloadConfig = PreloadConfig::create("/root");

        $relativeBasePath = $preloadConfig->getRelativeBasePath();

        $this->assertEquals("/root", $relativeBasePath);
    }

    /**
     * @test
     */
    public function getRelativeBasePathWhenBasePathHasTrailingSlash(): void
    {
        $preloadConfig = PreloadConfig::create("/root/");

        $relativeBasePath = $preloadConfig->getRelativeBasePath();

        $this->assertEquals("/root", $relativeBasePath);
    }

    /**
     * @test
     */
    public function setRelativeBasePath(): void
    {
        $preloadConfig = PreloadConfig::create("");

        $preloadConfig->setRelativeBasePath("/root/");

        $this->assertEquals("/root", $preloadConfig->getRelativeBasePath());
    }

    /**
     * @test
     */
    public function getPreloadedClassesWhenEmpty(): void
    {
        $preloadConfig = PreloadConfig::create("");

        $preloadedClasses = $preloadConfig->getPreloadedClasses();

        $this->assertEmpty($preloadedClasses);
    }

    /**
     * @test
     */
    public function getPreloadedClasses(): void
    {
        $preloadConfig = PreloadConfig::create("");

        $preloadConfig->setPreloadedClasses(
            [
                new ClassPreload(""),
            ]
        );

        $this->assertEquals(
            [
                new ClassPreload(""),
            ],
            $preloadConfig->getPreloadedClasses()
        );
    }

    /**
     * @test
     */
    public function getPreloadedFilesWhenEmpty(): void
    {
        $preloadConfig = PreloadConfig::create("");

        $preloadedFiles = $preloadConfig->getPreloadedFiles();

        $this->assertEmpty($preloadedFiles);
    }

    /**
     * @test
     */
    public function getPreloadedFiles(): void
    {
        $preloadConfig = PreloadConfig::create("");

        $preloadConfig->setPreloadedFiles(
            [
                "/root/abc.php",
            ]
        );

        $this->assertEquals(
            [
                "/root/abc.php",
            ],
            $preloadConfig->getPreloadedFiles()
        );
    }
}
