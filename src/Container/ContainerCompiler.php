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
     * @param string[] $preloadedClasses
     */
    public function compile(AbstractCompilerConfig $compilerConfig, array $definitions, array $preloadedClasses): array
    {
        $autoloadConfig = $compilerConfig->getAutoloadConfig();
        $fileBasedDefinitionConfig = $compilerConfig->getFileBasedDefinitionConfig();
        $fileBasedDefinitionDirectory = $fileBasedDefinitionConfig->getRelativeDefinitionDirectory();
        $definitionCompilation = new DefinitionCompilation($autoloadConfig, $fileBasedDefinitionConfig, $definitions);

        $definitionFiles = [];

        $container = "<?php\n";
        if ($compilerConfig->getContainerNamespace() !== "") {
            $container .= "namespace " . $compilerConfig->getContainerNamespace() . ";\n";
        }
        $container .= "\nuse WoohooLabs\\Zen\\AbstractCompiledContainer;\n\n";
        $container .= "class " . $compilerConfig->getContainerClassName() . " extends AbstractCompiledContainer\n";
        $container .= "{\n";

        // Entry points
        $entryPointIds = array_keys($compilerConfig->getEntryPointMap());

        $container .= "    /**\n";
        $container .= "     * @var string[]\n";
        $container .= "     */\n";
        $container .= "    protected static \$entryPoints = [\n";
        foreach ($entryPointIds as $id) {
            if (isset($definitions[$id]) === false) {
                continue;
            }

            $definition = $definitions[$id];

            $methodName = $this->getHash($id);
            if ($definition->isAutoloaded() && $definition->isAutoloadingInlinable() === false && array_key_exists($id, $preloadedClasses) === false) {
                $methodName = "_proxy__$methodName";
            }

            $container .= "        \\$id::class => '" . $methodName . "',\n";
        }
        $container .= "    ];\n\n";

        // Root directory property
        $container .= "    /**\n";
        $container .= "     * @var string\n";
        $container .= "     */\n";
        $container .= "    protected \$rootDirectory;\n\n";

        // Constructor
        $container .= "    public function __construct(string \$rootDirectory = '')\n";
        $container .= "    {\n";
        $container .= "        \$this->rootDirectory = \$rootDirectory;\n";
        foreach ($autoloadConfig->getAlwaysAutoloadedClasses() as $autoloadedClass) {
            if (array_key_exists($autoloadedClass, $preloadedClasses)) {
                continue;
            }

            $filename = FileSystemUtil::getRelativeFilename($autoloadConfig->getRootDirectory(), $autoloadedClass);
            $container .= "        include_once \$this->rootDirectory . '$filename';\n";
        }
        $container .= "    }\n";

        // Entry Points
        foreach ($entryPointIds as $id) {
            if (isset($definitions[$id]) === false) {
                continue;
            }

            $definition = $definitions[$id];

            if ($definition->isAutoloaded() && $definition->isAutoloadingInlinable() === false && array_key_exists($id, $preloadedClasses) === false) {
                $autoloadedDefinition = new AutoloadedDefinition($id, true, $definition->isFileBased());

                $container .= "\n    public function _proxy__" . $this->getHash($id) . "()\n    {\n";
                if ($autoloadedDefinition->isFileBased()) {
                    $filename = "_proxy__" . $this->getHash($id) . ".php";
                    $definitionFiles[$filename] = "<?php\n\n";
                    $definitionFiles[$filename] .= $autoloadedDefinition->compile($definitionCompilation, "", 0, false, $preloadedClasses);

                    $container .= "        return require __DIR__ . '/$fileBasedDefinitionDirectory/$filename';\n";
                } else {
                    $container .= $autoloadedDefinition->compile($definitionCompilation, "", 2, false, $preloadedClasses);
                }
                $container .= "    }\n";
            }

            if ($definition->isFileBased()) {
                $filename = $this->getHash($id) . ".php";
                $definitionFiles[$filename] = "<?php\n\n";
                $definitionFiles[$filename] .= $definition->compile($definitionCompilation, "", 0, false, $preloadedClasses);

                if ($definition->isEntryPoint()) {
                    $container .= "\n    public function " . $this->getHash($id) . "()\n    {\n";
                    $container .= "        return require __DIR__ . '/$fileBasedDefinitionDirectory/$filename';\n";
                    $container .= "    }\n";
                }
            } else {
                $container .= "\n    public function " . $this->getHash($id) . "()\n    {\n";
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
