<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Attribute;

use WoohooLabs\Zen\Annotation\Inject;

class AttributeC
{
    #[Inject]
    /** @var AttributeE */
    private $e1;

    #[Inject]
    /** @var AttributeE */
    protected $e2;

    public function getE1(): AttributeE
    {
        return $this->e1;
    }

    public function getE2(): AttributeE
    {
        return $this->e2;
    }
}
