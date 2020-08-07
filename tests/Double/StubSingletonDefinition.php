<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\Container\DefinitionCompilation;

class StubSingletonDefinition extends TestDefinition
{
    public function __construct(
        bool $isEntryPoint = false,
        bool $isFileBased = false,
        int $singletonReferenceCount = 0,
        int $prototypeReferenceCount = 0,
        bool $isDefinitionInlinable = false
    ) {
        parent::__construct(
            self::class,
            true,
            $isEntryPoint,
            $isFileBased,
            $singletonReferenceCount,
            $prototypeReferenceCount,
            $isDefinitionInlinable
        );
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
