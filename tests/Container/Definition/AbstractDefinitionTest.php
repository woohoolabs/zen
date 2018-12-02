<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Tests\Double\TestDefinition;

class AbstractDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function getId()
    {
        $definition = new TestDefinition("A", "");

        $id = $definition->getId("");

        $this->assertEquals("A", $id);
    }

    /**
     * @test
     */
    public function getHash()
    {
        $definition = new TestDefinition("A", "");

        $hash = $definition->getHash("");

        $this->assertEquals("A", $hash);
    }

    /**
     * @test
     */
    public function getFCQNHash()
    {
        $definition = new TestDefinition("A\\B", "");

        $hash = $definition->getHash("");

        $this->assertEquals("A__B", $hash);
    }
}
