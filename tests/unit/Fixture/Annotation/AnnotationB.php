<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Annotation;

use WoohooLabs\Dicone\Resolver\Annotation\Dependency;

class AnnotationB extends AnnotationC
{
    /**
     * @Dependency
     * @var AnnotationD
     */
    public $d;

    public function getD(): AnnotationD
    {
        return $this->d;
    }
}
