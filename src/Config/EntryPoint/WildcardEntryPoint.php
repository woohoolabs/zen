<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\EntryPoint;

use WoohooLabs\Zen\Utils\FileSystemUtil;
use function rtrim;

class WildcardEntryPoint extends AbstractEntryPoint
{
    private string $directoryName;

    private bool $onlyConcreteClasses;

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
     * @internal
     *
     * @return string[]
     */
    public function getClassNames(): array
    {
        return FileSystemUtil::getClassesInPath($this->directoryName, $this->onlyConcreteClasses);
    }
}
