<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Container\Definition\SelfDefinition;

class SelfDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function ToPhpCode()
    {
        $definition = new SelfDefinition("");

        $phpCode = <<<HERE
        return \$this;
HERE;
        $this->assertEquals($phpCode, $definition->toPhpCode());
    }
}
