<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Preload\PreloadConfig;
use WoohooLabs\Zen\Container\PreloadCompiler;
use WoohooLabs\Zen\Tests\Double\StubCompilerConfig;
use function dirname;
use function file_get_contents;

class PreloadCompilerTest extends TestCase
{
    /**
     * @test
     */
    public function compilePreloadFileWhenEmpty(): void
    {
        $compiler = new PreloadCompiler();

        $preloadFile = $compiler->compile(
            new StubCompilerConfig(),
            []
        );

        $this->assertEquals($this->getCompiledPreloadFileSourceCode("EmptyPreloadFile.php"), $preloadFile);
    }

    /**
     * @test
     */
    public function compilePreloadFileWithAbsoluteLinks(): void
    {
        $compiler = new PreloadCompiler();

        $preloadFile = $compiler->compile(
            new StubCompilerConfig(),
            [
                "/var/www/preloaded1.php",
                "/var/www/preloaded2.php",
            ]
        );

        $this->assertEquals($this->getCompiledPreloadFileSourceCode("PreloadFileWithAbsoluteLinks.php"), $preloadFile);
    }

    /**
     * @test
     */
    public function compilePreloadFileWithRelativeLinks(): void
    {
        $compiler = new PreloadCompiler();

        $preloadFile = $compiler->compile(
            new StubCompilerConfig(
                [],
                "",
                "",
                false,
                false,
                false,
                [],
                false,
                new PreloadConfig("/var/www")
            ),
            [
                "/var/www/preloaded1.php",
                "/var/www/preloaded2.php",
                "/var/preloaded3.php",
            ]
        );

        $this->assertEquals($this->getCompiledPreloadFileSourceCode("PreloadFileWithRelativeLinks.php"), $preloadFile);
    }

    private function getCompiledPreloadFileSourceCode(string $fileName): string
    {
        return file_get_contents(dirname(__DIR__) . "/Fixture/Container/" . $fileName);
    }
}
