<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\EntryPoint;

use WoohooLabs\Zen\Utils\FileSystemUtil;
use function rtrim;

class WildcardEntryPoint extends AbstractEntryPoint
{
    /**
     * @var string
     */
    private $directoryName;

    /**
     * @var bool
     */
    private $onlyConcreteClasses;

    public static function create(string $directoryName, bool $onlyConcreteClasses = true): WildcardEntryPoint
    {
        return new WildcardEntryPoint($directoryName, $onlyConcreteClasses);
    }

    public function __construct(string $directoryName, bool $onlyConcreteClasses = true)
    {
        $this->directoryName = rtrim($directoryName, "\\/");
        $this->onlyConcreteClasses = $onlyConcreteClasses;
    }

    /**
     * @return string[]
     * @internal
     */
    public function getClassNames(): array
    {
        return FileSystemUtil::getClassesInPath($this->directoryName, $this->onlyConcreteClasses);
    }
}
