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

    /**
     * @return $this
     */
    public function setSingletonScope()
    {
        $this->singleton = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function setPrototypeScope()
    {
        $this->singleton = false;

        return $this;
    }
}
