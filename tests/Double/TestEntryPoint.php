<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\Config\EntryPoint\AbstractEntryPoint;

class TestEntryPoint extends AbstractEntryPoint
{
    /**
     * @return string[]
     */
    public function getClassNames(): array
    {
        return [];
    }
}
