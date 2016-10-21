<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Config\DefinitionHint;

use WoohooLabs\Dicone\Container\Definition\ClassDefinition;
use WoohooLabs\Dicone\Container\Definition\DefinitionInterface;
use WoohooLabs\Dicone\Container\Definition\ReferenceDefinition;

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

    public function toDefinition(string $id): DefinitionInterface
    {
        if ($this->className === $id) {
            return new ClassDefinition($this->className, $this->scope);
        }

        return new ReferenceDefinition($id, $this->className, $this->scope);
    }
}
