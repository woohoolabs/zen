<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Config\Autoload\AutoloadConfigInterface;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfigInterface;
use WoohooLabs\Zen\Utils\FileSystemUtil;
use function array_reverse;
use function str_replace;

abstract class AbstractDefinition implements DefinitionInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $hash;

    /**
     * @var string
     */
    protected $scope;

    /**
     * @var bool
     */
    private $entryPoint;

    /**
     * @var bool
     */
    private $autoloaded;

    /**
     * @var bool
     */
    private $fileBased;

    /**
     * @var int
     */
    private $referenceCount = 0;

    public function __construct(string $id, string $scope, bool $isEntryPoint, bool $isAutoloaded, bool $isFileBased)
    {
        $this->id = $id;
        $this->hash = $this->hash($id);
        $this->scope = $scope;
        $this->entryPoint = $isEntryPoint;
        $this->autoloaded = $isAutoloaded;
        $this->fileBased = $isFileBased;
    }

    public function getId(string $parentId): string
    {
        return $this->id;
    }

    public function getHash(string $parentId): string
    {
        return $this->hash;
    }

    public function isSingleton(string $parentId): bool
    {
        return $this->scope === "singleton";
    }

    public function isEntryPoint(): bool
    {
        return $this->entryPoint;
    }

    public function isAutoloaded(): bool
    {
        return $this->autoloaded;
    }

    public function isFileBased(): bool
    {
        return $this->fileBased;
    }

    public function getReferenceCount(): int
    {
        return $this->referenceCount;
    }

    public function increaseReferenceCount(): DefinitionInterface
    {
        $this->referenceCount++;

        return $this;
    }

    protected function getEntryToPhp(
        string $id,
        string $hash,
        bool $isSingleton,
        DefinitionInterface $definition,
        FileBasedDefinitionConfigInterface $fileBasedDefinitionConfig
    ): string {
        $referenceCount = $definition->getReferenceCount();
        $isEntryPoint = $definition->isEntryPoint();
        $isFileBased = $definition->isFileBased();

        if ($definition->isFileBased()) {
            $path = "__DIR__ . '/";
            if ($this->isFileBased() === false && $isFileBased) {
                $path .= $fileBasedDefinitionConfig->getRelativeDirectory() . "/";
            }
            $path .= "$hash.php'";

            if ($isSingleton && ($referenceCount > 1 || $isEntryPoint)) {
                return "\$this->singletonEntries['$id'] ?? require $path";
            }

            return "require $path";
        }

        if ($isSingleton && ($referenceCount > 1 || $isEntryPoint)) {
            return "\$this->singletonEntries['$id'] ?? \$this->$hash()";
        }

        return "\$this->$hash()";
    }

    protected function hash(string $id): string
    {
        return str_replace("\\", "__", $id);
    }

    /**
     * @param DefinitionInterface[] $definitions
     */
    protected function includeDependencies(AutoloadConfigInterface $autoloadConfig, array $definitions, string $id): string
    {
        $dependencies = [];
        $this->collectDependencies($definitions, $id, $dependencies);
        $dependencies = array_reverse($dependencies);

        $code = "";
        foreach ($dependencies as $dependency) {
            $filename = FileSystemUtil::getRelativeFilename($autoloadConfig->getRootDirectory(), $dependency);
            if ($filename === "") {
                continue;
            }

            $code .= "        include_once \$this->rootDirectory . '$filename';\n";
        }

        $definition = $definitions[$id];
        $filename = FileSystemUtil::getRelativeFilename($autoloadConfig->getRootDirectory(), $definition->getId(""));
        if ($filename !== "") {
            $code .= "        include_once \$this->rootDirectory . '$filename';\n";
        }

        return $code;
    }

    /**
     * @param DefinitionInterface[] $definitions
     */
    private function collectDependencies(array $definitions, string $id, array &$dependencies): void
    {
        $definition = $definitions[$id];

        foreach ($definition->getClassDependencies() as $dependency) {
            if (isset($dependencies[$dependency])) {
                continue;
            }

            $dependencies[$dependency] = $dependency;
            $this->collectDependencies($definitions, $dependency, $dependencies);
        }
    }
}
