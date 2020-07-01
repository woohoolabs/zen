<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

abstract class AbstractHint
{
    protected bool $singleton;

    public function __construct(string $scope)
    {
        $this->singleton = $scope === "singleton";
    }

    public function isSingleton(): bool
    {
        return $this->singleton;
    }

    public function setSingletonScope(): static
    {
        $this->singleton = true;

        return $this;
    }

    public function setPrototypeScope(): static
    {
        $this->singleton = false;

        return $this;
    }
}
