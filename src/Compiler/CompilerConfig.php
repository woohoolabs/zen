<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Compiler;

class CompilerConfig
{
    /**
     * @var string
     */
    private $containerNamespace;

    /**
     * @var string
     */
    private $containerClassName;

    /**
     * @var bool
     */
    private $useConstructorTypeHints;

    /**
     * @var bool
     */
    private $usePropertyAnnotations;

    public function __construct(
        string $containerNamespace,
        string $containerClassName,
        bool $useConstructorTypeHints,
        bool $usePropertyAnnotations
    ) {
        $this->containerNamespace = $containerNamespace;
        $this->containerClassName = $containerClassName;
        $this->useConstructorTypeHints = $useConstructorTypeHints;
        $this->usePropertyAnnotations = $usePropertyAnnotations;
    }

    public function getContainerNamespace(): string
    {
        return $this->containerNamespace;
    }

    public function getContainerClassName(): string
    {
        return $this->containerClassName;
    }

    public function getContainerFqcn(): string
    {
        $fqcn = "";

        if ($this->containerNamespace) {
            $fqcn = $this->containerNamespace . "\\";
        }

        return $fqcn . $this->containerClassName;
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
