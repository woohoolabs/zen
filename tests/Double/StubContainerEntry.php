<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use stdClass;

class StubContainerEntry
{
    public mixed $a;

    public function getA(): stdClass
    {
        return new stdClass();
    }
}
