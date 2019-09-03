<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

class StubContainerEntry
{
    private bool $a = false;

    public function getA(): bool
    {
        return $this->a;
    }
}
