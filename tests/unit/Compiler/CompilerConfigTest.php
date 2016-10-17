<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Compiler;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Dicone\Compiler\CompilerConfig;

class CompilerConfigTest extends TestCase
{
    /**
     * @test
     */
    public function useConstructorTypeHints()
    {
        $config = new CompilerConfig(true, true);

        $this->assertEquals(
            true,
            $config->useConstructorTypeHints()
        );
    }

    /**
     * @test
     */
    public function usePropertyAnnotations()
    {
        $config = new CompilerConfig(true, false);

        $this->assertEquals(
            false,
            $config->usePropertyAnnotation()
        );
    }
}
