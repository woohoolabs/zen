<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config;

abstract class AbstractCompilerConfig
{
    abstract public function getContainerNamespace(): string;

    abstract public function getContainerClassName(): string;

    abstract public function useConstructorInjection(): bool;

    abstract public function usePropertyInjection(): bool;

    /**
     * @return AbstractContainerConfig[]
     */
    abstract public function getContainerConfigs(): array;

    public function getContainerHash(): string
    {
        return str_replace("\\", "__", $this->getContainerFqcn());
    }

    public function getContainerFqcn(): string
    {
        $namespace = $this->getContainerNamespace() ? $this->getContainerNamespace() . "\\" : "";

        return $namespace . $this->getContainerClassName();
    }
}
