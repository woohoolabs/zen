<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Preload;

class PreloadJ
{
    private PreloadF|PreloadG $a;

    public function foo(PreloadH|PreloadI $b)
    {
    }
}
