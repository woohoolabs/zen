<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

class SelfDefinition extends AbstractDefinition
{
    public function __construct(string $className)
    {
        parent::__construct($className, str_replace("\\", "__", $className));
    }

    public function needsDependencyResolution(): bool
    {
        return false;
    }

    public function resolveDependencies()
    {
    }

    public function toPhpCode(): string
    {
        return "        return \$this;\n";
    }
}
