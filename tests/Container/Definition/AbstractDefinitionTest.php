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
        $definition = new TestDefinition("A");

        $id = $definition->getId("");

        $this->assertEquals("A", $id);
    }

    /**
     * @test
     */
    public function getHash()
    {
        $definition = new TestDefinition("A");

        $hash = $definition->getHash("");

        $this->assertEquals("A", $hash);
    }

    /**
     * @test
     */
    public function getFCQNHash()
    {
        $definition = new TestDefinition("A\\B");

        $hash = $definition->getHash("");

        $this->assertEquals("A__B", $hash);
    }

    /**
     * @test
     */
    public function isEntryPointWhenTrue()
    {
        $definition = new TestDefinition("", true, true);

        $entryPoint = $definition->isEntryPoint();

        $this->assertTrue($entryPoint);
    }

    /**
     * @test
     */
    public function isSingletonWhenTrue()
    {
        $definition = new TestDefinition("", true);

        $singleton = $definition->isSingleton("");

        $this->assertTrue($singleton);
    }

    /**
     * @test
     */
    public function isSingletonWhenFalse()
    {
        $definition = new TestDefinition("", false);

        $singleton = $definition->isSingleton("");

        $this->assertFalse($singleton);
    }

    /**
     * @test
     */
    public function isEntryPointWhenFalse()
    {
        $definition = new TestDefinition("", true, false);

        $entryPoint = $definition->isEntryPoint();

        $this->assertFalse($entryPoint);
    }

    /**
     * @test
     */
    public function isAutoloadedWhenTrue()
    {
        $definition = new TestDefinition("", true, false, true);

        $autoloaded = $definition->isAutoloaded();

        $this->assertTrue($autoloaded);
    }

    /**
     * @test
     */
    public function isAutoloadedWhenFalse()
    {
        $definition = new TestDefinition("", true, false, false);

        $autoloaded = $definition->isAutoloaded();

        $this->assertFalse($autoloaded);
    }

    /**
     * @test
     */
    public function isFileBasedWhenTrue()
    {
        $definition = new TestDefinition("", true, false, false, true);

        $fileBased = $definition->isFileBased();

        $this->assertTrue($fileBased);
    }

    /**
     * @test
     */
    public function isFileBasedWhenFalse()
    {
        $definition = new TestDefinition("", true, false, false, false);

        $fileBased = $definition->isFileBased();

        $this->assertFalse($fileBased);
    }

    /**
     * @test
     */
    public function getSingletonReferenceCountWhen0()
    {
        $definition = new TestDefinition("", true, false, false, false, 0);

        $referenceCount = $definition->getSingletonReferenceCount();

        $this->assertEquals(0, $referenceCount);
    }

    /**
     * @test
     */
    public function getSingletonReferenceCountWhenMore()
    {
        $definition = new TestDefinition("", true, false, false, false, 2);

        $referenceCount = $definition->getSingletonReferenceCount();

        $this->assertEquals(2, $referenceCount);
    }

    /**
     * @test
     */
    public function increaseReferenceCount()
    {
        $definition = new TestDefinition("", true, false, false, false, 0);

        $definition
            ->increaseReferenceCount("", true)
            ->increaseReferenceCount("", false);

        $this->assertEquals(1, $definition->getSingletonReferenceCount());
        $this->assertEquals(1, $definition->getPrototypeReferenceCount());
    }
}
