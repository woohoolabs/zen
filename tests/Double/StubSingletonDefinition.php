<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Container\DefinitionCompilation;

class StubSingletonDefinition extends TestDefinition implements DefinitionInterface
{
    public function __construct(bool $isEntryPoint = false, bool $isAutoloaded = false, bool $isFileBased = false, int $referenceCount = 0)
    {
        parent::__construct(self::class, "singleton", $isEntryPoint, $isAutoloaded, $isFileBased, $referenceCount);
    }

    public function compile(DefinitionCompilation $compilation, int $indentationLevel, bool $inline = false): string
    {
        $indent = $this->indent($indentationLevel);

        return "${indent}// This is a dummy definition.\n";
    }
}
