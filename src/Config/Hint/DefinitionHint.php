<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Container\Definition\ReferenceDefinition;

class DefinitionHint extends AbstractHint implements DefinitionHintInterface
{
    /**
     * @var string
     */
    private $className;

    public static function singleton(string $className): DefinitionHint
    {
        return new self($className);
    }

    public static function prototype(string $className): DefinitionHint
    {
        $self = new self($className);
        $self->setPrototypeScope();

        return $self;
    }

    public function __construct(string $className)
    {
        parent::__construct();
        $this->className = $className;
        $this->setSingletonScope();
    }

    /**
     * @param DefinitionHintInterface[] $definitionHints
     * @return DefinitionInterface[]
     */
    public function toDefinitions(array $definitionHints, string $id): array
    {
        if ($this->className === $id) {
            return [
                $id => new ClassDefinition($this->className, $this->getScope())
            ];
        }

        $result = [
            $id => new ReferenceDefinition($id, $this->className, $this->getScope())
        ];

        if (isset($definitionHints[$this->className])) {
            $result = array_merge(
                $result,
                $definitionHints[$this->className]->toDefinitions($definitionHints, $this->className)
            );
        } else {
            $result[$this->className] = new ClassDefinition($this->className, $this->getScope());
        }

        return $result;
    }
}
