<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\ContextDependent;

class ClassD
{
    public function __construct(InterfaceA $a)
    {
    }
}
