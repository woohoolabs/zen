<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;

class ClassDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function getHash()
    {
        $definition = new ClassDefinition("A\\B");

        $this->assertEquals(
            "A__B",
            $definition->getHash()
        );
    }

    /**
     * @test
     */
    public function getClassName()
    {
        $definition = new ClassDefinition("A\\B");

        $this->assertEquals(
            "A\\B",
            $definition->getClassName()
        );
    }

    /**
     * @test
     */
    public function needsDependencyResolutionByDefault()
    {
        $definition = new ClassDefinition("");

        $this->assertTrue($definition->needsDependencyResolution());
    }

    /**
     * @test
     */
    public function resolveDependencies()
    {
        $definition = new ClassDefinition("");
        $definition->resolveDependencies();

        $this->assertFalse($definition->needsDependencyResolution());
    }

    /**
     * @test
     */
    public function singletonClassToPhpCode()
    {
        $definition = new ClassDefinition("A");

        $phpCode = <<<HERE
        \$entry = new \A();
        
        \$this->singletonEntries['A'] = \$entry;
        
        return \$entry;
HERE;
        $this->assertEquals($phpCode, $definition->toPhpCode());
    }

    /**
     * @test
     */
    public function prototypeWithRequiredConstructorDependenciesToPhpCode()
    {
        $definition = ClassDefinition::prototype("A")
            ->addRequiredConstructorArgument("B")
            ->addRequiredConstructorArgument("C");

        $phpCode = <<<HERE
        \$entry = new \A(
            \$this->getEntry('B'),
            \$this->getEntry('C')
        );
        
        return \$entry;
HERE;
        $this->assertEquals($phpCode, $definition->toPhpCode());
    }

    /**
     * @test
     */
    public function prototypeWithOptionalConstructorDependenciesToPhpCode()
    {
        $definition = ClassDefinition::prototype("A")
            ->addOptionalConstructorArgument("")
            ->addOptionalConstructorArgument(true)
            ->addOptionalConstructorArgument(1)
            ->addOptionalConstructorArgument(null)
            ->addOptionalConstructorArgument(["a" => false]);

        $phpCode = <<<HERE
        \$entry = new \A(
            "",
            true,
            1,
            null,
            ["a" => false,]
        );
        
        return \$entry;
HERE;
        $this->assertEquals($phpCode, $definition->toPhpCode());
    }

    /**
     * @test
     */
    public function prototypeWithPropertyDependenciesToPhpCode()
    {
        $definition = ClassDefinition::prototype("A")
            ->addProperty("b", "B")
            ->addProperty("c", "C");

        $phpCode = <<<HERE
        \$entry = new \A();
        \$this->setProperties(
            \$entry,
            [
                'b' => 'B',
                'c' => 'C',
            ]
        );
        
        return \$entry;
HERE;
        $this->assertEquals($phpCode, $definition->toPhpCode());
    }
}
