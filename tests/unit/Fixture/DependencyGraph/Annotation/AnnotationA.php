<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Annotation;

use WoohooLabs\Dicone\Resolver\Annotation\Dependency;

class AnnotationA
{
    /**
     * @Dependency
     * @var AnnotationB
     */
    private $b;

    /**
     * @Dependency
     * @var AnnotationC
     */
    private $c;

    /**
     * @var AnnotationD|null
     */
    protected $d;

    public function getB(): AnnotationB
    {
        return $this->b;
    }

    public function getC(): AnnotationC
    {
        return $this->c;
    }

    /**
     * @return AnnotationD|null
     */
    public function getD()
    {
        return $this->d;
    }
}
