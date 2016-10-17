<?php
declare(strict_types=1);

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
                foreach ($entryPoint->getClassNames() as $entryPointClassName) {
                    $this->dependencyResolver->resolve($entryPointClassName);
                }
            }

            foreach ($definition->getDefinitionItems() as $key => $definitionItem) {
                $this->dependencyResolver->addDefinitionItem($key, $definitionItem);
            }
        }

        $container = "<?php\n";
        if ($namespace) {
            $container .= "namespace $namespace;\n";
        }
        $container .= "\nclass $className implements \\WoohooLabs\\Dicone\\ItemContainerInterface\n";
        $container .= "{\n";
        $container .= "    private \$items = [];\n\n";
        $container .= "    public function __construct()\n";
        $container .= "    {\n";
        $container .= "        \$this->items = \$this->getItems();\n";
        $container .= "    }\n\n";
        $container .= "    public function hasItem(string \$id): bool\n";
        $container .= "    {\n";
        $container .= "        return isset(\$this->items[\$id]);\n";
        $container .= "    }\n\n";
        $container .= "    public function getItem(string \$id)\n";
        $container .= "    {\n";
        $container .= "        return \$this->items[\$id]();\n";
        $container .= "    }\n\n";
        $container .= "    private function getItems()\n";
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

        $indent = "";
        if ($definitionItem->isSingletonScope()) {
            $containerItem .= "                static \$item = null;\n\n";
            $containerItem .= "                if (\$item === null) {\n";
            $indent = "    ";
        }

        $containerItem .= "$indent                \$item = new \\" . $definitionItem->getClassName() . "(\n";
        $constructorParams = [];
        foreach ($definitionItem->getConstructorParams() as $constructorParam) {
            if (isset($constructorParam["class"])) {
                $constructorParams[] = "$indent                    \$this->items[\"" . addslashes($constructorParam["class"]) . "\"]()";
            } elseif (array_key_exists("default", $constructorParam)) {
                $constructorParams[] = "$indent                    " . ($this->convertValueToString($constructorParam["default"]));
            }
        }
        $containerItem .= implode(",\n", $constructorParams);
        $containerItem .= (empty($constructorParams) === false ? "\n" : "") . "$indent                );\n";

        if (empty($definitionItem->getProperties()) === false) {
            $containerItem .= "\n$indent                \$reflectionObject = new \\ReflectionObject(\$item);\n";

            foreach ($definitionItem->getProperties() as $propertyName => $propertyValue) {
                $containerItem .= "$indent                \$reflectionProperty = \$reflectionObject->getProperty(\"" . $propertyName . "\");\n";
                $containerItem .= "$indent                \$reflectionProperty->setAccessible(true);\n";
                $containerItem .= "$indent                \$reflectionProperty->setValue(null, \$this->items[\"" . addslashes($propertyValue) . "\"]()" . ");\n";
            }
        }

        if ($definitionItem->isSingletonScope()) {
            $containerItem .= "                }\n";
        }

        $containerItem .= "\n                return \$item;\n";
        $containerItem .= "            }";

        return $containerItem;
    }

    private function convertValueToString($value): string
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
