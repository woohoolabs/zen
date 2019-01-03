<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\Config\EntryPoint\AbstractEntryPoint;

class TestEntryPoint extends AbstractEntryPoint
{
    public function getClassNames(): array
    {
        return [];
    }
}
