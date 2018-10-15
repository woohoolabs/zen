<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Compiler;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfig;
use WoohooLabs\Zen\Tests\Double\DummyCompilerConfig;
use WoohooLabs\Zen\Tests\Double\StubCompilerConfig;

class CompilerConfigTest extends TestCase
{
    /**
     * @test
     */
    public function useConstructorInjection()
    {
        $config = new StubCompilerConfig([], "", "", true, true);

        $this->assertEquals(
            true,
            $config->useConstructorInjection()
        );
    }

    /**
     * @test
     */
    public function usePropertyInjection()
    {
        $config = new StubCompilerConfig([], "", "", false, false);

        $this->assertEquals(
            false,
            $config->usePropertyInjection()
        );
    }

    /**
     * @test
     */
    public function getContainerHash()
    {
        $config = new StubCompilerConfig([], "A\\B\\C", "D");

        $this->assertEquals(
            "A__B__C__D",
            $config->getContainerHash()
        );
    }

    /**
     * @test
     */
    public function getDefaultAutoloadConfig()
    {
        $config = new DummyCompilerConfig();

        $this->assertEquals(
            AutoloadConfig::disabledGlobally(),
            $config->getAutoloadConfig()
        );
    }
}
