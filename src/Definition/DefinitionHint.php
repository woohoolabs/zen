<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Definition;

class DefinitionHint
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $scope;

    public static function singleton(string $className)
    {
        return new self($className);
    }

    public static function prototype(string $className)
    {
        $self = new self($className);
        $self->setPrototypeScope();

        return $self;
    }

    public function __construct(string $className)
    {
        $this->className = $className;
        $this->setSingletonScope();
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

    public function toDefinitionItem(): DefinitionItem
    {
        return new DefinitionItem($this->className, $this->scope);
    }
}
