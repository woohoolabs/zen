<?php
namespace WoohooLabs\Dicone\Compiler;

use WoohooLabs\Dicone\Resolver\DependencyResolver;

class Compiler
{
    /**
     * @var DependencyResolver
     */
    private $dependencyResolver;

    /**
     * Compiler constructor.
     * @param DependencyResolver $dependencyResolver
     */
    public function __construct(DependencyResolver $dependencyResolver)
    {
        $this->dependencyResolver = $dependencyResolver;
    }

    public function compileDefinition(string $filename, CompilationConfig $config)
    {
        /** @var array $definition */
        $definition = require($filename);

        foreach ($definition as $item) {
            $this->dependencyResolver->resolve($item, $config);
        }
    }
}
