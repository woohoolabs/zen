<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Attribute;

use WoohooLabs\Zen\Annotation\Inject;

class AttributeA
{
    #[Inject]
    /** @var AttributeB */
    private $b;

    #[Inject]
    /** @var AttributeC */
    private $c;

    /** @var AttributeD|null */
    protected $d;

    public function getB(): AttributeB
    {
        return $this->b;
    }

    public function getC(): AttributeC
    {
        return $this->c;
    }

    /**
     * @return AttributeD|null
     */
    public function getD()
    {
        return $this->d;
    }
}
