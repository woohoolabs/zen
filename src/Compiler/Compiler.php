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
        $container .= "\nuse WoohooLabs\\Dicone\\AbstractContainer;\n\n";
        $container .= "class " . $config->getContainerClassName() . " extends AbstractContainer\n";
        $container .= "{\n";

        foreach ($definitionItems as $key => $definitionItem) {
            $container .= $this->compileDefinitionItem($definitionItem);
        }
        $container .= $config->getHash() . "()\n";
        $container .= "{\n";
        $container .= "    return \$this;\n";
        $container .= "}\n";

        return $container;
    }

    private function compileDefinitionItem(DefinitionItem $definitionItem)
    {
        $containerItem = "    protected function " . $definitionItem->getHash() . "()\n    {\n";

        if ($definitionItem->isReference()) {
            $containerItem .= "        return \$this->getEntry('" . $definitionItem->getClassName() . "');\n";
        } else {
            $containerItem .= "        \$entry = new \\" . $definitionItem->getClassName() . "(\n";

            $constructorParams = [];
            foreach ($definitionItem->getConstructorParams() as $constructorParam) {
                if (isset($constructorParam["class"])) {
                    $constructorParams[] = "        \$this->getEntry('" . $constructorParam["class"] . "')";
                } elseif (isset($constructorParam["default"])) {
                    $constructorParams[] = "        " . ($this->convertValueToString($constructorParam["default"]));
                }
            }
            $containerItem .= implode(",\n", $constructorParams);
            $containerItem .= (empty($constructorParams) === false ? "\n" : "") . "        );\n";

            if (empty($definitionItem->getProperties()) === false) {
                $containerItem .= "\n";
                foreach ($definitionItem->getProperties() as $propertyName => $propertyValue) {
                    $containerItem .= "        \$this->setPropertyValue(\$entry, '$propertyName', '$propertyValue');\n";
                }
            }

            if ($definitionItem->isSingletonScope()) {
                $containerItem .= "\n        \$this->singletonEntries['" . $definitionItem->getHash() . "'] = \$entry;\n\n";
            }

            $containerItem .= "        return \$entry;\n";
        }
        $containerItem .= "    }\n\n";

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
