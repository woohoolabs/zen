<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor;

class ConstructorA
{
    /**
     * @param ConstructorC $c
     * @param bool $d
     * @param null $e
     */
    public function __construct(ConstructorB $b, $c, bool $d = true, $e = null)
    {
    }
}
