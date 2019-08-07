<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Preload;

use WoohooLabs\Zen\Utils\FileSystemUtil;
use function rtrim;

class WildcardPreload extends AbstractPreload
{
    /**
     * @var string
     */
    private $directoryName;

    public static function create(string $directoryName): WildcardPreload
    {
        return new WildcardPreload($directoryName);
    }

    public function __construct(string $directoryName)
    {
        $this->directoryName = rtrim($directoryName, "\\/");
    }

    /**
     * @return string[]
     * @internal
     */
    public function getClassNames(): array
    {
        return FileSystemUtil::getClassesInPath($this->directoryName, false);
    }
}
