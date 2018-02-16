<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\EntryPoint;

use WoohooLabs\Zen\Utils\FileSystemUtil;

class WildcardEntryPoint implements EntryPointInterface
{
    /**
     * @var string
     */
    private $directoryName;

    /**
     * @var bool
     */
    private $onlyConcreteClasses;

    /**
     * @var bool
     */
    private $autoloaded;

    public static function create(string $className, bool $onlyConcreteClasses = true): WildcardEntryPoint
    {
        return new WildcardEntryPoint($className, $onlyConcreteClasses);
    }

    public function __construct(string $directoryName, bool $onlyConcreteClasses = true)
    {
        $this->directoryName = rtrim($directoryName, "\\/");
        $this->onlyConcreteClasses = $onlyConcreteClasses;
        $this->autoloaded = false;
    }

    public function autoload(): WildcardEntryPoint
    {
        $this->autoloaded = true;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getClassNames(): array
    {
        return FileSystemUtil::getClassesInPath($this->directoryName, $this->onlyConcreteClasses);
    }

    public function isAutoloaded(): bool
    {
        return $this->autoloaded;
    }
}
