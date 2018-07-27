<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Tests\Unit\Double\TestDefinition;

class AbstractDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function getId()
    {
        $definition = new TestDefinition("A", "");

        $this->assertEquals("A", $definition->getId(""));
    }

    /**
     * @test
     */
    public function getHash()
    {
        $definition = new TestDefinition("A", "");

        $this->assertEquals("A", $definition->getHash(""));
    }

    /**
     * @test
     */
    public function getFCQNHash()
    {
        $definition = new TestDefinition("A\\B", "");

        $this->assertEquals("A__B", $definition->getHash(""));
    }
}
