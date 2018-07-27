<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

class ReferenceDefinition extends AbstractDefinition
{
    /**
     * @var string
     */
    private $referrerId;

    public static function singleton(string $referrerId, string $referencedClassName): ReferenceDefinition
    {
        return new self($referrerId, $referencedClassName);
    }

    public static function prototype(string $referrerId, string $referencedClassName): ReferenceDefinition
    {
        return new self($referrerId, $referencedClassName, "prototype");
    }

    public function __construct(string $referrerId, string $referencedClassName, string $scope = "singleton")
    {
        parent::__construct($referencedClassName, $scope);
        $this->referrerId = $referrerId;
    }

    public function needsDependencyResolution(): bool
    {
        return false;
    }

    public function isAutoloaded(): bool
    {
        return false;
    }

    public function resolveDependencies(): DefinitionInterface
    {
        return $this;
    }

    public function getClassDependencies(): array
    {
        return [
            $this->id,
        ];
    }

    /**
     * @param DefinitionInterface[] $definitions
     */
    public function toPhpCode(array $definitions): string
    {
        $code = "        return ";
        if ($this->scope === "singleton") {
            $code .= "\$this->singletonEntries['{$this->referrerId}'] = ";
        }

        $definition = $definitions[$this->id];

        $code .= $this->getEntryToPhp(
            $definition->getId($this->referrerId),
            $definition->getHash($this->referrerId),
            $definition->getScope($this->referrerId)
        ) . ";\n";

        return $code;
    }
}
