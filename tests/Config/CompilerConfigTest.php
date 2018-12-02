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
        $config = new StubCompilerConfig([], "", "", true, false);

        $useConstructorInjection = $config->useConstructorInjection();

        $this->assertEquals(true, $useConstructorInjection);
    }

    /**
     * @test
     */
    public function usePropertyInjection()
    {
        $config = new StubCompilerConfig([], "", "", true, false);

        $usePropertyInjection = $config->usePropertyInjection();

        $this->assertEquals(false, $usePropertyInjection);
    }

    /**
     * @test
     */
    public function getContainerHash()
    {
        $config = new StubCompilerConfig([], "A\\B\\C", "D");

        $containerHash = $config->getContainerHash();

        $this->assertEquals("A__B__C__D", $containerHash);
    }

    /**
     * @test
     */
    public function getDefaultAutoloadConfig()
    {
        $config = new DummyCompilerConfig();

        $autoloadConfig = $config->getAutoloadConfig();

        $this->assertEquals(AutoloadConfig::disabledGlobally(), $autoloadConfig);
    }
}
