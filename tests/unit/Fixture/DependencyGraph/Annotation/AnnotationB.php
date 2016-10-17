<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Annotation;

use WoohooLabs\Dicone\Annotation\Inject;

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
