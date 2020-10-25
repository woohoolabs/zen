<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Attribute;

use WoohooLabs\Zen\Annotation\Inject;

class AttributeB extends AttributeC
{
    #[Inject]
    /** @var AttributeD */
    public $d;

    #[Inject]
    /** @var string */
    protected $value;

    public function getD(): AttributeD
    {
        return $this->d;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
