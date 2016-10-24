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

    private $onlyConcreteClasses;

    public function __construct(string $directoryName, bool $onlyConcreteClasses = true)
    {
        $this->directoryName = rtrim($directoryName, "\\/");
        $this->onlyConcreteClasses = $onlyConcreteClasses;
    }

    /**
     * @return string[]
     */
    public function getClassNames(): array
    {
        return FileSystemUtil::getClassesInPath($this->directoryName, $this->onlyConcreteClasses);
    }
}
