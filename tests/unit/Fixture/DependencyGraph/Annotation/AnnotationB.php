<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Annotation;

class AnnotationB extends AnnotationC
{
    /**
     * @Inject
     * @var AnnotationD
     */
    public $d;

    public function getD(): AnnotationD
    {
        return $this->d;
    }
}
