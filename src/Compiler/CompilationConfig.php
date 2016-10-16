<?php
namespace WoohooLabs\Dicone\Compiler;

class CompilationConfig
{
    /**
     * @var bool
     */
    private $useConstructorTypeHints;

    /**
     * @var bool
     */
    private $usePropertyAnnotations;

    public function __construct(bool $useConstructorTypeHints, bool $usePropertyAnnotations)
    {
        $this->useConstructorTypeHints = $useConstructorTypeHints;
        $this->usePropertyAnnotations = $usePropertyAnnotations;
    }

    public function useConstructorTypeHints(): bool
    {
        return $this->useConstructorTypeHints;
    }

    public function usePropertyAnnotation(): bool
    {
        return $this->usePropertyAnnotations;
    }
}
