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

    /**
     * @var bool
     */
    private $onlyConcreteClasses;

    public static function create(string $directoryName, bool $onlyConcreteClasses = true): WildcardPreload
    {
        return new WildcardPreload($directoryName, $onlyConcreteClasses);
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
