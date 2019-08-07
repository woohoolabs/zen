<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\Container\DefinitionCompilation;

class StubPrototypeDefinition extends TestDefinition
{
    public function __construct(bool $isEntryPoint = false, bool $isAutoloaded = false, bool $isFileBased = false, int $referenceCount = 0)
    {
        parent::__construct(self::class, false, $isEntryPoint, $isAutoloaded, $isFileBased, $referenceCount);
    }

    /**
     * @param string[] $preloadedClasses
     */
    public function compile(
        DefinitionCompilation $compilation,
        string $parentId,
        int $indentationLevel,
        bool $inline = false,
        array $preloadedClasses = []
    ): string {
        $indent = $this->indent($indentationLevel);

        return "${indent}// This is a dummy definition.\n";
    }
}
