<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Container\Definition\ReferenceDefinition;

class ReferenceDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function singletonToPhpCode()
    {
        $definition = ReferenceDefinition::singleton("A", "B");

        $phpCode = <<<HERE
        \$entry = \$this->getEntry('B');

        $this->singletonEntries['A'] = \$entry;

        return \$entry;
HERE;
        $this->assertEquals($phpCode, $definition->toPhpCode());
    }

    /**
     * @test
     */
    public function prototypeToPhpCode()
    {
        $definition = ReferenceDefinition::prototype("A", "B");

        $phpCode = <<<HERE
        \$entry = \$this->getEntry('B');

        $this->singletonEntries['A'] = \$entry;

        return \$entry;
HERE;
        $this->assertEquals($phpCode, $definition->toPhpCode());
    }
}
