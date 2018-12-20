<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Config\Autoload\AutoloadConfigInterface;
use WoohooLabs\Zen\Utils\FileSystemUtil;
use function array_reverse;

final class AutoloadedDefinition extends AbstractDefinition
{
    /**
     * @var AutoloadConfigInterface
     */
    private $autoloadConfig;

    /**
     * @param DefinitionInterface[] $definitions
     */
    public function __construct(AutoloadConfigInterface $autoloadConfig, string $id)
    {
        $this->autoloadConfig = $autoloadConfig;
        $this->id = $id;
        parent::__construct($id, "");
    }

    public function getScope(string $parentId): string
    {
        return "";
    }

    public function isAutoloaded(): bool
    {
        return true;
    }

    public function needsDependencyResolution(): bool
    {
        return false;
    }

    public function resolveDependencies(): DefinitionInterface
    {
        return $this;
    }

    public function getClassDependencies(): array
    {
        return [];
    }

    /**
     * @param DefinitionInterface[] $definitions
     */
    public function toPhpCode(array $definitions): string
    {
        $definition = $definitions[$this->id];
        $id = $definition->getId("");
        $hash = $definition->getHash("");

        $code = $this->includeDependency($definitions, $this->id);

        $code .= "\n";
        $code .= "        self::\$entryPoints[\\$id::class] = '$hash';\n\n";
        $code .= "        return \$this->$hash();\n";

        return $code;
    }

    /**
     * @param DefinitionInterface[] $definitions
     */
    private function includeDependency(array $definitions, string $id): string
    {
        $dependencies = [];
        $this->collectDependencies($definitions, $id, $dependencies);
        $dependencies = array_reverse($dependencies);

        $code = "";
        foreach ($dependencies as $dependency) {
            $filename = FileSystemUtil::getRelativeFilename($this->autoloadConfig->getRootDirectory(), $dependency);
            if ($filename === "") {
                continue;
            }

            $code .= "        include_once \$this->rootDirectory . '$filename';\n";
        }

        $definition = $definitions[$id];
        $filename = FileSystemUtil::getRelativeFilename($this->autoloadConfig->getRootDirectory(), $definition->getId(""));
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
