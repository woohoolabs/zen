<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Annotation;

use WoohooLabs\Dicone\Annotation\Inject;

class AnnotationC
{
    /**
     * @Inject
     * @var AnnotationE
     */
    private $e1;

    /**
     * @Inject
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
