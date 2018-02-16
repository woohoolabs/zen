<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container;

use Psr\Container\ContainerInterface;
use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Container\Definition\AutoloadedDefinition;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Utils\FileSystemUtil;

class Compiler
{
    /**
     * @param DefinitionInterface[] $definitions
     */
    public function compile(AbstractCompilerConfig $compilerConfig, array $definitions): string
    {
        $container = "<?php\n";
        if ($compilerConfig->getContainerNamespace()) {
            $container .= "namespace " . $compilerConfig->getContainerNamespace() . ";\n";
        }
        $container .= "\nuse WoohooLabs\\Zen\\AbstractCompiledContainer;\n\n";
        $container .= "class " . $compilerConfig->getContainerClassName() . " extends AbstractCompiledContainer\n";
        $container .= "{\n";

        // Entry points
        $entryPoints = $this->getEntryPoints($compilerConfig);

        $container .= "    /**\n";
        $container .= "     * @var string[]\n";
        $container .= "     */\n";
        $container .= "    protected static \$entryPoints = [\n";
        foreach ($entryPoints as $id) {
            $methodName = $this->getHash($id);

            if (isset($definitions[$id]) && $definitions[$id]->isAutoloaded()) {
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
        foreach ($compilerConfig->getAutoloadConfig()->getAlwaysAutoloadedClasses() as $autoloadedClass) {
            $filename = FileSystemUtil::getRelativeFilename($compilerConfig->getAutoloadConfig()->getRootDirectory(), $autoloadedClass);
            $container .= "        include_once \$this->rootDirectory . '$filename';\n";
        }
        $container .= "    }\n";

        // Custom autoloading of container definitions
        foreach ($entryPoints as $id) {
            if (isset($definitions[$id]) === false) {
                continue;
            }

            $definition = $definitions[$id];
            if ($definition->isAutoloaded() === false) {
                continue;
            }

            $autoloadedDefinition = new AutoloadedDefinition($compilerConfig->getAutoloadConfig(), $definitions, $id);

            $container .= "\n    public function _proxy__" . $this->getHash($id) . "()\n    {\n";
            $container .= $autoloadedDefinition->toPhpCode();
            $container .= "    }\n";
        }

        // Container definitions
        foreach ($definitions as $id => $definition) {
            $container .= "\n    public function " . $this->getHash($id) . "()\n    {\n";
            $container .= $definition->toPhpCode();
            $container .= "    }\n";
        }

        $container .= "}\n";

        return $container;
    }

    private function getEntryPoints(AbstractCompilerConfig $compilerConfig): array
    {
        $result = [
            $compilerConfig->getContainerFqcn() => $compilerConfig->getContainerFqcn(),
            ContainerInterface::class => ContainerInterface::class,
        ];

        foreach ($compilerConfig->getContainerConfigs() as $containerConfig) {
            foreach ($containerConfig->createEntryPoints() as $entryPoint) {
                foreach ($entryPoint->getClassNames() as $id) {
                    $result[$id] = $id;
                }
            }
        }

        return array_values($result);
    }

    private function getHash(string $id): string
    {
        return str_replace("\\", "__", $id);
    }
}
