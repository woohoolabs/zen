<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Preload;

class ClassPreload extends AbstractPreload
{
    private string $className;

    public static function create(string $className): ClassPreload
    {
        return new ClassPreload($className);
    }

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * @return string[]
     * @internal
     */
    public function getClassNames(): array
    {
        return [
            $this->className,
        ];
    }
}
