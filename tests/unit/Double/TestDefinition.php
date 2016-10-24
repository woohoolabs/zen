<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Double;

use WoohooLabs\Zen\Container\Definition\AbstractDefinition;

class TestDefinition extends AbstractDefinition
{
    public function needsDependencyResolution(): bool
    {
        return false;
    }

    public function resolveDependencies()
    {
    }

    public function toPhpCode(): string
    {
        return "";
    }
}
