<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\EntryPoint;

class ClassEntryPoint implements EntryPointInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var bool
     */
    private $autoloaded;

    public static function create(string $className): ClassEntryPoint
    {
        return new ClassEntryPoint($className);
    }

    public function __construct(string $className)
    {
        $this->className = $className;
        $this->autoloaded = false;
    }

    public function autoload(): ClassEntryPoint
    {
        $this->autoloaded = true;

        return $this;
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

    /**
     * @internal
     */
    public function isAutoloaded(): bool
    {
        return $this->autoloaded;
    }
}
