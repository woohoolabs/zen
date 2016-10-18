<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Builder;

use WoohooLabs\Dicone\Compiler\Compiler;
use WoohooLabs\Dicone\Compiler\CompilerConfig;
use WoohooLabs\Dicone\Compiler\DependencyResolver;

class ContainerBuilder
{
    public function build(string $filePath, string $namespace, string $className, array $definitions)
    {
        $compiler = new Compiler(new DependencyResolver(new CompilerConfig(true, true)));
        $container = $compiler->compileDefinitions($namespace, $className, $definitions);
        file_put_contents($filePath, $container);
    }
}
