<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

abstract class AbstractHint
{
    /**
     * @var string
     */
    private $scope;

    public function __construct(string $scope)
    {
        $this->scope = $scope === "prototype" ? "prototype" : "singleton";
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @return $this
     */
    public function setSingletonScope()
    {
        $this->scope = "singleton";

        return $this;
    }

    /**
     * @return $this
     */
    public function setPrototypeScope()
    {
        $this->scope = "prototype";

        return $this;
    }
}
