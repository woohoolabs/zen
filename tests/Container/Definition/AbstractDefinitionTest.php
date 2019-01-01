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

    /**
     * @test
     */
    public function isEntryPointWhenTrue()
    {
        $definition = new TestDefinition("", "", true);

        $entryPoint = $definition->isEntryPoint();

        $this->assertTrue($entryPoint);
    }

    /**
     * @test
     */
    public function isSingletonWhenTrue()
    {
        $definition = new TestDefinition("", "singleton");

        $singleton = $definition->isSingleton("");

        $this->assertTrue($singleton);
    }

    /**
     * @test
     */
    public function isSingletonWhenFalse()
    {
        $definition = new TestDefinition("", "prototype");

        $singleton = $definition->isSingleton("");

        $this->assertFalse($singleton);
    }

    /**
     * @test
     */
    public function isEntryPointWhenFalse()
    {
        $definition = new TestDefinition("", "", false);

        $entryPoint = $definition->isEntryPoint();

        $this->assertFalse($entryPoint);
    }

    /**
     * @test
     */
    public function isAutoloadedWhenTrue()
    {
        $definition = new TestDefinition("", "", false, true);

        $autoloaded = $definition->isAutoloaded();

        $this->assertTrue($autoloaded);
    }

    /**
     * @test
     */
    public function isAutoloadedWhenFalse()
    {
        $definition = new TestDefinition("", "", false, false);

        $autoloaded = $definition->isAutoloaded();

        $this->assertFalse($autoloaded);
    }

    /**
     * @test
     */
    public function isFileBasedWhenTrue()
    {
        $definition = new TestDefinition("", "", false, false, true);

        $fileBased = $definition->isFileBased();

        $this->assertTrue($fileBased);
    }

    /**
     * @test
     */
    public function isFileBasedWhenFalse()
    {
        $definition = new TestDefinition("", "", false, false, false);

        $fileBased = $definition->isFileBased();

        $this->assertFalse($fileBased);
    }

    /**
     * @test
     */
    public function getReferenceCountWhen0()
    {
        $definition = new TestDefinition("", "", false, false, false, 0);

        $referenceCount = $definition->getReferenceCount();

        $this->assertEquals(0, $referenceCount);
    }

    /**
     * @test
     */
    public function getReferenceCountWhenMore()
    {
        $definition = new TestDefinition("", "", false, false, false, 2);

        $referenceCount = $definition->getReferenceCount();

        $this->assertEquals(2, $referenceCount);
    }

    /**
     * @test
     */
    public function increaseReferenceCount()
    {
        $definition = new TestDefinition("", "", false, false, false, 0);

        $definition
            ->increaseReferenceCount()
            ->increaseReferenceCount();

        $this->assertEquals(2, $definition->getReferenceCount());
    }
}
