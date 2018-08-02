<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Config\Hint;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\ContextDependent\ClassA;

class DefinitionHintTest extends TestCase
{
    /**
     * @test
     */
    public function singleton()
    {
        $hint = DefinitionHint::singleton(ClassA::class);

        $this->assertEquals("singleton", $hint->getScope());
    }

    /**
     * @test
     */
    public function prototype()
    {
        $hint = DefinitionHint::prototype(ClassA::class);

        $this->assertEquals("prototype", $hint->getScope());
    }

    /**
     * @test
     */
    public function getClassName()
    {
        $hint = DefinitionHint::prototype(ClassA::class);

        $this->assertEquals(ClassA::class, $hint->getClassName());
    }
}
