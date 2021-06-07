<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Preload;

use stdClass;

class PreloadA extends PreloadB
{
    /**
     * @param PreloadA $a
     */
    public function __construct($a, PreloadH $h)
    {
    }

    public function preloadD(): PreloadD
    {
        return new PreloadD();
    }

    public function preloadG(PreloadG $g): stdClass
    {
        return new stdClass();
    }
}
