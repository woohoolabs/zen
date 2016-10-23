<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container;

use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;

class Compiler
{
    /**
     * @param DefinitionInterface[] $definitions
     */
    public function compileDefinitions(AbstractCompilerConfig $compilerConfig, array $definitions): string
    {
        $container = "<?php\n";
        if ($compilerConfig->getContainerNamespace()) {
            $container .= "namespace " . $compilerConfig->getContainerNamespace() . ";\n";
        }
        $container .= "\nuse WoohooLabs\\Zen\\AbstractContainer;\n\n";
        $container .= "class " . $compilerConfig->getContainerClassName() . " extends AbstractContainer\n";
        $container .= "{";

        foreach ($definitions as $id => $definition) {
            $container .= "\n    protected function " . $this->getHash($id) . "()\n    {\n";
            $container .= $definition->toPhpCode();
            $container .= "    }\n";
        }

        $container .= "}\n";

        return $container;
    }

    private function getHash(string $id): string
    {
        return str_replace("\\", "__", $id);
    }
}
