<?php
namespace WoohooLabs\Dicone\Compiler;

use WoohooLabs\Dicone\Definition\DefinitionInterface;
use WoohooLabs\Dicone\Definition\DefinitionItem;
use WoohooLabs\Dicone\Resolver\DependencyResolver;

class Compiler
{
    /**
     * @var DependencyResolver
     */
    private $dependencyResolver;

    public function __construct(DependencyResolver $dependencyResolver)
    {
        $this->dependencyResolver = $dependencyResolver;
    }

    /**
     * @param DefinitionInterface[] $definitions
     */
    public function compileDefinitions(string $namespace, string $className, array $definitions): string
    {
        foreach ($definitions as $definition) {
            foreach ($definition->getEntryPoints() as $entryPoint) {
                foreach ($entryPoint->getClassNames() as $entryPointClassName)
                $this->dependencyResolver->resolve($entryPointClassName);
            }

            foreach ($definition->getDefinitionItems() as $key => $definitionItem) {
                $this->dependencyResolver->addDefinitionItem($key, $definitionItem);
            }
        }

        $container = "<?php\n";
        if ($namespace) {
           $container .= "namespace $namespace;\n";
        }
        $container .= "\nclass $className\n";
        $container .= "{\n";
        $container .= "    private \$items = [];\n\n";
        $container .= "    public function __construct()\n";
        $container .= "    {\n";
        $container .= "        \$this->items = \$this->getItems();\n";
        $container .= "    }\n\n";
        $container .= "    protected function getItems()\n";
        $container .= "    {\n";
        $container .= "        return [\n";
        foreach ($this->dependencyResolver->getDefinitionItems() as $key => $definitionItem) {
            $container .= "            \"" . addslashes($key) . "\" => " . $this->compileDefinitionItem($definitionItem) . ",\n";
        }
        $container .= "        ];\n";
        $container .= "    }\n";
        $container .= "}\n";

        return $container;
    }

    private function compileDefinitionItem(DefinitionItem $definitionItem)
    {
        $containerItem = "function () {\n";
        $containerItem .= "                \$item = new \\" . $definitionItem->getClassName() . "(\n";
        $constructorParams = [];
        foreach ($definitionItem->getConstructorParams() as $constructorParam) {
            if (isset ($constructorParam["class"])) {
                $constructorParams[] = "                    \$this->items[\"" . addslashes($constructorParam["class"]) . "\"]()";
            } elseif (array_key_exists("default", $constructorParam)) {
                $constructorParams[] = "                    " . ($this->convertValuetoString($constructorParam["default"]));
            }
        }
        $containerItem .= implode(",\n", $constructorParams);
        $containerItem .= (empty($constructorParams) === false ? "\n" : "") . "                );\n\n";
        $containerItem .= "                return \$item;\n";
        $containerItem .= "            }";

        return $containerItem;
    }

    private function convertValuetoString($value): string
    {
        if (is_string($value)) {
            return '"' . $value . '"';
        }

        if ($value === null) {
            return "null";
        }

        if (is_bool($value)) {
            return $value === true ? "true" : "false";
        }

        return $value;
    }
}
