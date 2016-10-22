<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\DefinitionHint;

abstract class AbstractDefinitionHint implements DefinitionHintInterface
{
    /**
     * @var string
     */
    private $scope;

    public function __construct()
    {
        $this->setSingletonScope();
    }

    protected function getScope(): string
    {
        return $this->scope;
    }

    public function setSingletonScope()
    {
        $this->scope = "singleton";

        return $this;
    }

    public function setPrototypeScope()
    {
        $this->scope = "prototype";

        return $this;
    }
}
