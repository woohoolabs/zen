<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Config\EntryPoint;

class ClassEntryPoint extends AbstractEntryPoint
{
    private string $className;

    public static function create(string $className): ClassEntryPoint
    {
        return new ClassEntryPoint($className);
    }

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * @internal
     *
     * @return string[]
     */
    public function getClassNames(): array
    {
        return [
            $this->className,
        ];
    }
}
