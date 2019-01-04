<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Annotation;

class AnnotationB extends AnnotationC
{
    /**
     * @Inject
     * @var AnnotationD
     */
    public $d;

    /**
     * @Inject
     * @var string
     */
    protected $value;

    public function getD(): AnnotationD
    {
        return $this->d;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
