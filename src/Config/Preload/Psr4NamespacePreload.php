<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Preload;

use WoohooLabs\Zen\Utils\NamespaceUtil;
use function trim;

class Psr4NamespacePreload extends AbstractPreload
{
    private string $namespace;
    private bool $recursive;

    public static function create(string $namespace, bool $recursive = true): Psr4NamespacePreload
    {
        return new Psr4NamespacePreload($namespace, $recursive);
    }

    public function __construct(string $namespace, bool $recursive = true)
    {
        $this->namespace = trim($namespace, "\\");
        $this->recursive = $recursive;
    }

    /**
     * @internal
     *
     * @return string[]
     */
    public function getClassNames(): array
    {
        return NamespaceUtil::getClassesInPsr4Namespace($this->namespace, $this->recursive, false);
    }
}
