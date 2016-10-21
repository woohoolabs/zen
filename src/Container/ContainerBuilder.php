<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Container;

use WoohooLabs\Dicone\Config\CompilerConfig;
use WoohooLabs\Dicone\Config\ContainerConfigInterface;

class ContainerBuilder
{
    /**
     * @param ContainerConfigInterface[] $containerConfigs
     */
    public function build(string $filePath, string $namespace, string $className, array $containerConfigs)
    {
        $definitionHints = [];
        foreach ($containerConfigs as $containerConfig) {
            $definitionHints = array_merge($definitionHints, $containerConfig->createDefinitionHints());
        }

        $compilerConfig = new CompilerConfig($namespace, $className, true, true);
        $dependencyResolver = new DependencyResolver($compilerConfig, $definitionHints);

        foreach ($containerConfigs as $containerConfig) {
            foreach ($containerConfig->createEntryPoints() as $entryPoint) {
                foreach ($entryPoint->getClassNames() as $id) {
                    $dependencyResolver->resolve($id);
                }
            }
        }

        $compiler = new Compiler();
        $container = $compiler->compileDefinitions($compilerConfig, $dependencyResolver->getDefinitions());
        file_put_contents($filePath, $container);
    }
}
