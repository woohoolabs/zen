<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use ReflectionClass;
use ReflectionException;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfigInterface;
use WoohooLabs\Zen\Container\DefinitionCompilation;
use WoohooLabs\Zen\Utils\FileSystemUtil;

use function array_flip;
use function array_key_exists;
use function array_reverse;
use function str_repeat;
use function str_replace;

abstract class AbstractDefinition implements DefinitionInterface
{
    protected string $id;
    protected string $hash;
    protected bool $singleton;
    private bool $entryPoint;
    private bool $fileBased;
    private int $singletonReferenceCount;
    private int $prototypeReferenceCount;

    public function __construct(
        string $id,
        bool $isSingleton,
        bool $isEntryPoint,
        bool $isFileBased,
        int $singletonReferenceCount,
        int $prototypeReferenceCount
    ) {
        $this->id = $id;
        $this->hash = $this->hash($id);
        $this->singleton = $isSingleton;
        $this->entryPoint = $isEntryPoint;
        $this->fileBased = $isFileBased;
        $this->singletonReferenceCount = $singletonReferenceCount;
        $this->prototypeReferenceCount = $prototypeReferenceCount;
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
        return $this->singleton;
    }

    public function isEntryPoint(string $parentId = ""): bool
    {
        return $this->entryPoint;
    }

    public function isFileBased(string $parentId = ""): bool
    {
        return $this->fileBased;
    }

    public function increaseReferenceCount(string $parentId, bool $isParentSingleton): DefinitionInterface
    {
        if ($isParentSingleton) {
            $this->singletonReferenceCount++;
        } else {
            $this->prototypeReferenceCount++;
        }

        return $this;
    }

    public function getSingletonReferenceCount(): int
    {
        return $this->singletonReferenceCount;
    }

    public function getPrototypeReferenceCount(): int
    {
        return $this->prototypeReferenceCount;
    }

    public function isDefinitionInlinable(string $parentId = ""): bool
    {
        return false;
    }

    public function isSingletonCheckEliminable(string $parentId = ""): bool
    {
        if ($this->singleton === false) {
            return true;
        }

        return $this->entryPoint === false && $this->singletonReferenceCount <= 1 && $this->prototypeReferenceCount === 0;
    }

    /**
     * @param string[] $preloadedClasses
     */
    protected function compileEntryReference(
        DefinitionInterface $definition,
        DefinitionCompilation $compilation,
        int $indentationLevelWhenInlined,
        array $preloadedClasses
    ): string {
        if ($definition->isEntryPoint($this->id) === false) {
            return $this->compileInlinedEntry($definition, $compilation, $indentationLevelWhenInlined, $preloadedClasses);
        }

        return $this->compileReferencedEntry($definition, $compilation->getFileBasedDefinitionConfig());
    }

    /**
     * @param string[] $preloadedClasses
     */
    private function compileInlinedEntry(
        DefinitionInterface $definition,
        DefinitionCompilation $compilation,
        int $indentationLevelWhenInlined,
        array $preloadedClasses
    ): string {
        $id = $definition->getId($this->id);

        $code = "";

        if ($definition->isSingletonCheckEliminable($this->id) === false) {
            $code .= "\$this->singletonEntries['$id'] ?? ";
        }

        $code .= $definition->compile($compilation, $this->id, $indentationLevelWhenInlined, true, $preloadedClasses);

        return $code;
    }

    private function compileReferencedEntry(
        DefinitionInterface $definition,
        FileBasedDefinitionConfigInterface $fileBasedDefinitionConfig
    ): string {
        $id = $definition->getId($this->id);
        $hash = $definition->getHash($this->id);
        $isFileBased = $definition->isFileBased($this->id);

        if ($isFileBased) {
            $path = "__DIR__ . '/";
            if ($this->isFileBased($this->id) === false) {
                $path .= $fileBasedDefinitionConfig->getRelativeDefinitionDirectory() . "/";
            }
            $path .= "$hash.php'";

            if ($definition->isSingletonCheckEliminable($this->id) === false) {
                return "\$this->singletonEntries['$id'] ?? require $path";
            }

            return "require $path";
        }

        if ($definition->isSingletonCheckEliminable($this->id) === false) {
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

    /**
     * @param array<string, string> $relatedClasses
     */
    protected function collectParentClasses(string $id, array &$relatedClasses): void
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
                if (array_key_exists($interface, $relatedClasses)) {
                    unset($relatedClasses[$interface]);
                }
                $relatedClasses[$interface] = $interface;
            }

            $class = $parent;
        }

        foreach ($class->getInterfaceNames() as $interface) {
            if (array_key_exists($interface, $relatedClasses)) {
                unset($relatedClasses[$interface]);
            }
            $relatedClasses[$interface] = $interface;
        }
    }

    /**
     * @param DefinitionInterface[] $definitions
     * @param array<string, string> $relatedClasses
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
}
