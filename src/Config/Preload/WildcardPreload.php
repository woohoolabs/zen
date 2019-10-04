<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Preload;

use WoohooLabs\Zen\Utils\FileSystemUtil;

use function rtrim;

class WildcardPreload extends AbstractPreload
{
    private string $directoryName;
    private bool $onlyConcreteClasses;

    public static function create(string $directoryName, bool $onlyConcreteClasses = false): WildcardPreload
    {
        return new WildcardPreload($directoryName, $onlyConcreteClasses);
    }

    public function __construct(string $directoryName, bool $onlyConcreteClasses = false)
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
