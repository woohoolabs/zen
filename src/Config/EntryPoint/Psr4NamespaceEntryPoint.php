<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\EntryPoint;

use WoohooLabs\Zen\Utils\NamespaceUtil;
use function trim;

class Psr4NamespaceEntryPoint extends AbstractEntryPoint
{
    private string $namespace;

    private bool $recursive;

    private bool $onlyInstantiable;

    public static function create(string $namespace, bool $recursive = true, bool $onlyInstantiable = true): Psr4NamespaceEntryPoint
    {
        return new Psr4NamespaceEntryPoint($namespace, $recursive, $onlyInstantiable);
    }

    public function __construct(string $namespace, bool $recursive = true, bool $onlyInstantiable = true)
    {
        $this->namespace = trim($namespace, "\\");
        $this->recursive = $recursive;
        $this->onlyInstantiable = $onlyInstantiable;
    }

    /**
     * @internal
     *
     * @return string[]
     */
    public function getClassNames(): array
    {
        return NamespaceUtil::getClassesInPsr4Namespace($this->namespace, $this->recursive, $this->onlyInstantiable);
    }
}
