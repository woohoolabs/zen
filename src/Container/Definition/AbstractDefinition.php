<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Config\Autoload\AutoloadConfigInterface;
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
    private $fileBased;

    /**
     * @var int
     */
    private $referenceCount = 0;

    public function __construct(string $id, string $scope, bool $isEntryPoint, bool $fileBased)
    {
        $this->id = $id;
        $this->hash = $this->hash($id);
        $this->scope = $scope;
        $this->entryPoint = $isEntryPoint;
        $this->fileBased = $fileBased;
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

    public function isFileBased(): bool
    {
        return $this->fileBased;
    }

    public function getReferenceCount(): int
    {
        return $this->referenceCount;
    }

    public function increaseReferenceCount(): void
    {
        $this->referenceCount++;
    }

    protected function getEntryToPhp(string $id, string $hash, bool $isSingleton, DefinitionInterface $definition): string
    {
        $referenceCount = $definition->getReferenceCount();
        $isEntryPoint = $definition->isEntryPoint();

        if ($definition->isFileBased()) {
            if ($isSingleton && ($referenceCount > 1 || $isEntryPoint)) {
                return "\$this->singletonEntries['$id'] ?? require __DIR__ . '/$hash.php'";
            }

            return "require __DIR__ . '/$hash.php'";
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
