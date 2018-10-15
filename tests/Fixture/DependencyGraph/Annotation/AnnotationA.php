<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Annotation;

class AnnotationA
{
    /**
     * @Inject
     * @var AnnotationB
     */
    private $b;

    /**
     * @Inject
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
