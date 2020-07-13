<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Container;

use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Container\Definition\AutoloadedDefinition;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Utils\FileSystemUtil;

use function array_key_exists;
use function array_keys;
use function str_replace;

final class ContainerCompiler
{
    /**
     * @param DefinitionInterface[] $definitions
     * @param string[]              $preloadedClasses
     * @return array<string, mixed>
     */
    public function compile(AbstractCompilerConfig $compilerConfig, array $definitions, array $preloadedClasses): array
    {
        $autoloadConfig = $compilerConfig->getAutoloadConfig();
        $fileBasedDefinitionConfig = $compilerConfig->getFileBasedDefinitionConfig();
        $fileBasedDefinitionDirectory = $fileBasedDefinitionConfig->getRelativeDefinitionDirectory();
        $definitionCompilation = new DefinitionCompilation($autoloadConfig, $fileBasedDefinitionConfig, $definitions);

        $definitionFiles = [];

        $container = "<?php\n";
        $container .= "\ndeclare(strict_types=1);\n";

        if ($compilerConfig->getContainerNamespace() !== "") {
            $container .= "\nnamespace " . $compilerConfig->getContainerNamespace() . ";\n";
        }
        $container .= "\nuse WoohooLabs\\Zen\\AbstractCompiledContainer;\n";
        $container .= "use WoohooLabs\\Zen\\Exception\\NotFoundException;\n\n";
        $container .= "class " . $compilerConfig->getContainerClassName() . " extends AbstractCompiledContainer\n";
        $container .= "{\n";

        // Root directory property
        $container .= "    protected string \$rootDirectory;\n\n";

        // Constructor
        $container .= "    public function __construct(string \$rootDirectory = \"\")\n";
        $container .= "    {\n";
        $container .= "        \$this->rootDirectory = \$rootDirectory;\n";
        foreach ($autoloadConfig->getAlwaysAutoloadedClasses() as $autoloadedClass) {
            if (array_key_exists($autoloadedClass, $preloadedClasses)) {
                continue;
            }

            $filename = FileSystemUtil::getRelativeFilenameForClass($autoloadConfig->getRootDirectory(), $autoloadedClass);
            $container .= "        include_once \$this->rootDirectory . '/$filename';\n";
        }
        $container .= "    }\n\n";

        $entryPointIds = array_keys($compilerConfig->getEntryPointMap());

        // ContainerInterface::has()

        $container .= "    /**\n";
        $container .= "     * @param string \$id\n";
        $container .= "     */\n";
        $container .= "    public function has(\$id): bool\n";
        $container .= "    {\n";
        $container .= "        return match (\$id) {\n";

        foreach ($entryPointIds as $id) {
            if (array_key_exists($id, $definitions) === false) {
                continue;
            }

            $container .= "            '$id' => true,\n";
        }
        $container .= "            default => false,\n";
        $container .= "        };\n";
        $container .= "    }\n\n";

        // ContainerInterface::get()

        $container .= "    /**\n";
        $container .= "     * @param string \$id\n";
        $container .= "     * @throws NotFoundException\n";
        $container .= "     */\n";
        $container .= "    public function get(\$id): mixed\n";
        $container .= "    {\n";
        $container .= "        return \$this->singletonEntries[\$id] ?? match (\$id) {\n";

        foreach ($entryPointIds as $id) {
            if (array_key_exists($id, $definitions) === false) {
                continue;
            }

            $definition = $definitions[$id];

            if ($definition->isDefinitionInlinable("")) {
                $container .= "            '$id' => " . $definition->compile(
                    $definitionCompilation,
                    "",
                    3,
                    true,
                    $preloadedClasses
                );
                $container .= ",\n";
            } else {
                $methodName = $this->getHash($id);
                $container .= "            '$id' => \$this->$methodName(),\n";
            }

        }
        $container .= "            default => throw new NotFoundException(\$id),\n";
        $container .= "        };\n";
        $container .= "    }\n";

        // Entry Points
        foreach ($entryPointIds as $id) {
            if (array_key_exists($id, $definitions) === false) {
                continue;
            }

            $definition = $definitions[$id];

            if ($definition->isDefinitionInlinable("")) {
                continue;
            }

            $autoloadingCode = "";
            if ($definition->isAutoloaded() && array_key_exists($id, $preloadedClasses) === false) {
                $autoloadedDefinition = new AutoloadedDefinition($id, true, $definition->isFileBased());
                $autoloadingCode .= $autoloadedDefinition->compile($definitionCompilation, "", $definition->isFileBased() ? 0 : 2, false, $preloadedClasses);
            }

            if ($definition->isFileBased()) {
                $filename = $this->getHash($id) . ".php";
                $definitionFiles[$filename] = "<?php\n\n";
                $definitionFiles[$filename] .= $autoloadingCode;
                $definitionFiles[$filename] .= $definition->compile($definitionCompilation, "", 0, false, $preloadedClasses);

                if ($definition->isEntryPoint()) {
                    $container .= "\n    public function " . $this->getHash($id) . "()\n    {\n";
                    $container .= "        return require __DIR__ . '/$fileBasedDefinitionDirectory/$filename';\n";
                    $container .= "    }\n";
                }
            } else {
                $container .= "\n    public function " . $this->getHash($id) . "()\n    {\n";
                $container .= $autoloadingCode;
                $container .= $definition->compile($definitionCompilation, "", 2, false, $preloadedClasses);
                $container .= "    }\n";
            }
        }

        $container .= "}\n";

        return [
            "container" => $container,
            "definitions" => $definitionFiles,
        ];
    }

    private function getHash(string $id): string
    {
        return str_replace("\\", "__", $id);
    }
}
