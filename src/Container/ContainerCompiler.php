<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Container;

use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;

use function array_key_exists;
use function array_keys;
use function count;
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
        $fileBasedDefinitionConfig = $compilerConfig->getFileBasedDefinitionConfig();
        $fileBasedDefinitionDirectory = $fileBasedDefinitionConfig->getRelativeDefinitionDirectory();
        $definitionCompilation = new DefinitionCompilation($fileBasedDefinitionConfig, $definitions);

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

        $entryPointIds = array_keys($compilerConfig->getEntryPointMap());

        // ContainerInterface::has()

        $container .= "    /**\n";
        $container .= "     * @param string \$id\n";
        $container .= "     */\n";
        $container .= "    public function has(\$id): bool\n";
        $container .= "    {\n";
        $container .= "        return match (\$id) {\n";

        $entryPointCount = count($entryPointIds);
        foreach ($entryPointIds as $i => $id) {
            if (array_key_exists($id, $definitions) === false) {
                continue;
            }

            $container .= "            '$id'" . ($i === $entryPointCount - 1 ? " => true" : "") . ",\n";
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
                if ($definition->isFileBased("")) {
                    $filename = $this->getHash($id) . ".php";
                    $container .= "            '$id' => require __DIR__ . '/$fileBasedDefinitionDirectory/$filename',\n";
                } else {
                    $container .= "            '$id' => " . $definition->compile(
                            $definitionCompilation,
                            "",
                            3,
                            true,
                            $preloadedClasses
                        );
                    $container .= ",\n";
                }
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

            $container .= "\n    public function " . $this->getHash($id) . "()\n    {\n";
            $container .= $definition->compile($definitionCompilation, "", 2, false, $preloadedClasses);
            $container .= "    }\n";
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
