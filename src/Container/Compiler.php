<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container;

use Interop\Container\ContainerInterface;
use Traversable;
use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;

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
        $container .= "\nuse WoohooLabs\\Zen\\AbstractContainer;\n\n";
        $container .= "class " . $compilerConfig->getContainerClassName() . " extends AbstractContainer\n";
        $container .= "{\n";

        $container .= "    /**\n";
        $container .= "     * @var string[]\n";
        $container .= "     */\n";
        $container .= "    protected \$entryPoints = [\n";

        foreach ($this->getEntryPoints($compilerConfig) as $id) {
            $container .= "        \\$id::class => '" . $this->getHash($id) . "',\n";
        }

        $container .= "    ];\n";

        foreach ($definitions as $id => $definition) {
            $container .= "\n    protected function " . $this->getHash($id) . "()\n    {\n";
            $container .= $definition->toPhpCode();
            $container .= "    }\n";
        }

        $container .= "}\n";

        return $container;
    }

    private function getEntryPoints(AbstractCompilerConfig $compilerConfig): Traversable
    {
        yield ContainerInterface::class;
        yield $compilerConfig->getContainerFqcn();

        foreach ($compilerConfig->getContainerConfigs() as $containerConfig) {
            foreach ($containerConfig->createEntryPoints() as $entryPoint) {
                foreach ($entryPoint->getClassNames() as $id) {
                    yield $id;
                }
            }
        }
    }

    private function getHash(string $id): string
    {
        return str_replace("\\", "__", $id);
    }
}
