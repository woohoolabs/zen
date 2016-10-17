<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Compiler;

class CompilerConfig
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
