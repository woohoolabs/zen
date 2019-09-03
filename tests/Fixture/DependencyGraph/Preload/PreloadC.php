<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Preload;

interface PreloadC
{
    public function preloadD(): PreloadD;
}
