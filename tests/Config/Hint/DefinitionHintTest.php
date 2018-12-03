<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Config\Hint;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\ContextDependent\ClassA;

class DefinitionHintTest extends TestCase
{
    /**
     * @test
     */
    public function singleton()
    {
        $hint = DefinitionHint::singleton(ClassA::class);

        $scoppe = $hint->getScope();

        $this->assertEquals("singleton", $scoppe);
    }

    /**
     * @test
     */
    public function prototype()
    {
        $hint = DefinitionHint::prototype(ClassA::class);

        $scope = $hint->getScope();

        $this->assertEquals("prototype", $scope);
    }

    /**
     * @test
     */
    public function setSingletonScope()
    {
        $hint = DefinitionHint::prototype(ClassA::class);

        $hint->setSingletonScope();

        $this->assertEquals("singleton", $hint->getScope());
    }

    /**
     * @test
     */
    public function setPrototypeScope()
    {
        $hint = DefinitionHint::singleton(ClassA::class);

        $hint->setPrototypeScope();

        $this->assertEquals("prototype", $hint->getScope());
    }

    /**
     * @test
     */
    public function getClassName()
    {
        $hint = DefinitionHint::prototype(ClassA::class);

        $className = $hint->getClassName();

        $this->assertEquals(ClassA::class, $className);
    }
}
