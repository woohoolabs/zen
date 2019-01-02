<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use ReflectionClass;
use ReflectionException;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfigInterface;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfigInterface;
use WoohooLabs\Zen\Container\DefinitionCompilation;
use WoohooLabs\Zen\Utils\FileSystemUtil;
use function array_flip;
use function array_reverse;
use function str_repeat;
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
    private $referenceCount;

    public function __construct(string $id, string $scope, bool $isEntryPoint, bool $isAutoloaded, bool $isFileBased, int $referenceCount)
    {
        $this->id = $id;
        $this->hash = $this->hash($id);
        $this->scope = $scope;
        $this->entryPoint = $isEntryPoint;
        $this->autoloaded = $isAutoloaded;
        $this->fileBased = $isFileBased;
        $this->referenceCount = $referenceCount;
    }

    public function getId(string $parentId = ""): string
    {
        return $this->id;
    }

    public function getHash(string $parentId = ""): string
    {
        return $this->hash;
    }

    public function isSingleton(string $parentId = ""): bool
    {
        return $this->scope === "singleton";
    }

    public function isEntryPoint(string $parentId = ""): bool
    {
        return $this->entryPoint;
    }

    public function isAutoloaded(string $parentId = ""): bool
    {
        return $this->autoloaded;
    }

    public function isFileBased(string $parentId = ""): bool
    {
        return $this->fileBased;
    }

    public function getReferenceCount(string $parentId = ""): int
    {
        return $this->referenceCount;
    }

    public function increaseReferenceCount(string $parentId = ""): DefinitionInterface
    {
        $this->referenceCount++;

        return $this;
    }

    protected function compileEntryReference(
        DefinitionInterface $definition,
        DefinitionCompilation $compilation,
        int $indentationLevelWhenInlined
    ): string {
        if ($definition->isEntryPoint($this->getId()) === false) {
            return $this->compileInlinedEntry($definition, $compilation, $indentationLevelWhenInlined);
        }

        return $this->compileReferencedEntry($definition, $compilation->getFileBasedDefinitionConfig());
    }

    private function compileInlinedEntry(
        DefinitionInterface $definition,
        DefinitionCompilation $compilation,
        int $indentationLevelWhenInlined
    ): string {
        $id = $definition->getId($this->id);
        $referenceCount = $definition->getReferenceCount($this->getId());
        $isEntryPoint = $definition->isEntryPoint($this->getId());

        $code = "";

        if ($id && ($this->scope === "prototype" || $referenceCount > 1 || $isEntryPoint)) {
            $code .= "\$this->singletonEntries['$id'] ?? ";
        }

        $code .= $definition->compile($compilation, $this->getId(), $indentationLevelWhenInlined, true);

        return $code;
    }

    private function compileReferencedEntry(
        DefinitionInterface $definition,
        FileBasedDefinitionConfigInterface $fileBasedDefinitionConfig
    ): string {
        $id = $definition->getId($this->id);
        $hash = $definition->getHash($this->id);
        $isSingleton = $definition->isSingleton($this->id);
        $isEntryPoint = $definition->isEntryPoint($this->getId());
        $isFileBased = $definition->isFileBased($this->getId());
        $referenceCount = $definition->getReferenceCount($this->getId());

        if ($isFileBased) {
            $path = "__DIR__ . '/";
            if ($this->isFileBased($this->getId()) === false && $isFileBased) {
                $path .= $fileBasedDefinitionConfig->getRelativeDefinitionDirectory() . "/";
            }
            $path .= "$hash.php'";

            if ($isSingleton && ($this->scope === "prototype" || $referenceCount > 1 || $isEntryPoint)) {
                return "\$this->singletonEntries['$id'] ?? require $path";
            }

            return "require $path";
        }

        if ($isSingleton && ($this->scope === "prototype" || $referenceCount > 1 || $isEntryPoint)) {
            return "\$this->singletonEntries['$id'] ?? \$this->$hash()";
        }

        return "\$this->$hash()";
    }

    protected function hash(string $id): string
    {
        return str_replace("\\", "__", $id);
    }

    protected function indent(int $indentationLevel): string
    {
        return str_repeat(" ", $indentationLevel * 4);
    }

    protected function isOptimizable(): bool
    {
        return $this->isSingleton("") === false || ($this->referenceCount <= 1 && $this->entryPoint === false);
    }

    protected function isAutoloadable(bool $inline): bool
    {
        return $this->entryPoint && $this->autoloaded && $this->isSingleton("") && $this->referenceCount === 0 && $inline === false;
    }

    /**
     * @param DefinitionInterface[] $definitions
     */
    protected function includeRelatedClasses(
        AutoloadConfigInterface $autoloadConfig,
        array $definitions,
        string $id,
        int $indentationLevel
    ): string {
        $indent = $this->indent($indentationLevel);

        $relatedClasses = [];
        $this->collectRelatedClasses($definitions, $id, $relatedClasses);
        $relatedClasses = array_reverse($relatedClasses);

        $rootDirectory = $autoloadConfig->getRootDirectory();
        $alwaysAutoloadedClasses = array_flip($autoloadConfig->getAlwaysAutoloadedClasses());
        $neverAutoloadedClasses = array_flip($autoloadConfig->getExcludedClasses());

        $code = "";
        foreach ($relatedClasses as $relatedClass) {
            if (isset($alwaysAutoloadedClasses[$relatedClass]) || isset($neverAutoloadedClasses[$relatedClass])) {
                continue;
            }

            $filename = FileSystemUtil::getRelativeFilename($rootDirectory, $relatedClass);
            if ($filename === "") {
                continue;
            }

            $code .= "${indent}include_once \$this->rootDirectory . '$filename';\n";
        }

        return $code;
    }

    /**
     * @param DefinitionInterface[] $definitions
     */
    private function collectRelatedClasses(array $definitions, string $id, array &$relatedClasses): void
    {
        $definition = $definitions[$id];

        $relatedClasses[$id] = $id;
        $this->collectParentClasses($id, $relatedClasses);

        foreach ($definition->getClassDependencies() as $relatedClass) {
            $relatedClasses[$relatedClass] = $relatedClass;
            $this->collectRelatedClasses($definitions, $relatedClass, $relatedClasses);
            $this->collectParentClasses($relatedClass, $relatedClasses);
        }
    }

    private function collectParentClasses(string $id, array &$relatedClasses): void
    {
        try {
            $class = new ReflectionClass($id);
        } catch (ReflectionException $exception) {
            return;
        }

        while ($parent = $class->getParentClass()) {
            $name = $parent->getName();

            $relatedClasses[$name] = $name;
            foreach ($class->getInterfaceNames() as $interface) {
                if (isset($relatedClasses[$interface])) {
                    unset($relatedClasses[$interface]);
                }
                $relatedClasses[$interface] = $interface;
            }

            $class = $parent;
        }

        foreach ($class->getInterfaceNames() as $interface) {
            if (isset($relatedClasses[$interface])) {
                unset($relatedClasses[$interface]);
            }
            $relatedClasses[$interface] = $interface;
        }
    }
}
