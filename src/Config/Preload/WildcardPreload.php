<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Preload;

use WoohooLabs\Zen\Utils\FileSystemUtil;
use function rtrim;

class WildcardPreload extends AbstractPreload
{
    private string $directoryName;
    private bool $onlyConcreteClasses;

    public static function create(string $directoryName): WildcardPreload
    {
        return new WildcardPreload($directoryName);
    }

    public function __construct(string $directoryName)
    {
        $this->directoryName = rtrim($directoryName, "\\/");
    }

    /**
     * @internal
     *
     * @return string[]
     */
    public function getClassNames(): array
    {
        return FileSystemUtil::getClassesInPath($this->directoryName, false);
    }
}
