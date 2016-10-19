<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Compiler;

use WoohooLabs\Dicone\Definition\DefinitionItem;

class Compiler
{
    /**
     * @param DefinitionItem[] $definitionItems
     */
    public function compileDefinitions(CompilerConfig $config, array $definitionItems): string
    {
        $container = "<?php\n";
        if ($config->getContainerNamespace()) {
            $container .= "namespace " . $config->getContainerNamespace() . ";\n";
        }
        $container .= "\nuse \\WoohooLabs\\Dicone\\AbstractContainer;\n\n";
        $container .= "class " . $config->getContainerClassName() . " extends AbstractContainer\n";
        $container .= "{\n";
        $container .= "    protected function getItems(): array\n";
        $container .= "    {\n";
        $container .= "        return [\n";
        foreach ($definitionItems as $key => $definitionItem) {
            $container .= "            '" . $key . "' => " . $this->compileDefinitionItem($definitionItem) . ",\n";
        }
        $container .= "            '" . $config->getContainerFqcn() . "' => function () {\n";
        $container .= "                return \$this;\n";
        $container .= "            },\n";
        $container .= "        ];\n";
        $container .= "    }\n";
        $container .= "}\n";

        return $container;
    }

    private function compileDefinitionItem(DefinitionItem $definitionItem)
    {
        $containerItem = "function () {\n";

        if ($definitionItem->isReference()) {
            $containerItem .= "                return \$this->getItem('" . $definitionItem->getClassName() . "');\n";
        } else {
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
                    $constructorParams[] = "$indent                    \$this->getItem('" . $constructorParam["class"] . "')";
                } elseif (array_key_exists("default", $constructorParam)) {
                    $constructorParams[] = "$indent                    " . ($this->convertValueToString($constructorParam["default"]));
                }
            }
            $containerItem .= implode(",\n", $constructorParams);
            $containerItem .= (empty($constructorParams) === false ? "\n" : "") . "$indent                );\n";

            if (empty($definitionItem->getProperties()) === false) {
                $containerItem .= "\n$indent                \$reflectionObject = new \\ReflectionObject(\$item);\n";
                foreach ($definitionItem->getProperties() as $propertyName => $propertyValue) {
                    $containerItem .= "$indent                \$this->setPropertyValue(\$reflectionObject, '$propertyName', '$propertyValue');\n";
                }
            }

            if ($definitionItem->isSingletonScope()) {
                $containerItem .= "                }\n";
            }

            $containerItem .= "\n                return \$item;\n";
        }
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
