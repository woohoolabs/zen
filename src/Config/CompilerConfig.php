<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Config;

class CompilerConfig
{
    /*
     * @var string
     */
    private $hash;

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
    private $useConstructorInjection;

    /**
     * @var bool
     */
    private $usePropertyInjection;

    public function __construct(
        string $containerNamespace,
        string $containerClassName,
        bool $useConstructorInjection,
        bool $usePropertyInjection
    ) {
        $this->containerNamespace = $containerNamespace;
        $this->containerClassName = $containerClassName;
        $this->hash = str_replace("\\", "__", $this->getContainerFqcn());
        $this->useConstructorInjection = $useConstructorInjection;
        $this->usePropertyInjection = $usePropertyInjection;
    }

    public function getHash(): string
    {
        return $this->hash;
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
        return ($this->containerNamespace ? $this->containerNamespace . "\\" : "") . $this->containerClassName;
    }

    public function useConstructorInjection(): bool
    {
        return $this->useConstructorInjection;
    }

    public function usePropertyInjection(): bool
    {
        return $this->usePropertyInjection;
    }
}
