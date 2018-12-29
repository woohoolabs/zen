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

    /**
     * @var bool
     */
    private $fileBased;

    public static function create(string $className): ClassEntryPoint
    {
        return new ClassEntryPoint($className);
    }

    public function __construct(string $className)
    {
        $this->className = $className;
        $this->autoloaded = false;
        $this->fileBased = false;
    }

    public function autoload(): ClassEntryPoint
    {
        $this->autoloaded = true;

        return $this;
    }

    public function notAutoloaded(): ClassEntryPoint
    {
        $this->autoloaded = false;

        return $this;
    }

    public function fileBased(): ClassEntryPoint
    {
        $this->fileBased = true;

        return $this;
    }

    public function notFileBased(): ClassEntryPoint
    {
        $this->fileBased = false;

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

    /**
     * @internal
     */
    public function isFileBased(): bool
    {
        return $this->fileBased;
    }
}
