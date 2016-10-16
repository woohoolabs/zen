<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Annotation;

use WoohooLabs\Dicone\Resolver\Annotation\Dependency;

class AnnotationC
{
    /**
     * @Dependency
     * @var AnnotationE
     */
    private $e1;

    /**
     * @Dependency
     * @var AnnotationE
     */
    protected $e2;

    public function getE1(): AnnotationE
    {
        return $this->e1;
    }

    public function getE2(): AnnotationE
    {
        return $this->e2;
    }
}
