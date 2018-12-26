<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

class ReferenceDefinition extends AbstractDefinition
{
    /**
     * @var string
     */
    private $referrerId;

    public static function singleton(
        string $referrerId,
        string $referencedClassName,
        bool $isEntryPoint = false,
        bool $fileBased = false
    ): ReferenceDefinition {
        return new self($referrerId, $referencedClassName, "singleton", $isEntryPoint, $fileBased);
    }

    public static function prototype(
        string $referrerId,
        string $referencedClassName,
        bool $isEntryPoint = false,
        bool $fileBased = false
    ): ReferenceDefinition
    {
        return new self($referrerId, $referencedClassName, "prototype", $isEntryPoint, $fileBased);
    }

    public function __construct(
        string $referrerId,
        string $referencedClassName,
        string $scope = "singleton",
        bool $isEntryPoint = false,
        bool $fileBased = false
    ) {
        parent::__construct($referencedClassName, $scope, $isEntryPoint, $fileBased);
        $this->referrerId = $referrerId;
    }

    public function isAutoloaded(): bool
    {
        return false;
    }

    public function needsDependencyResolution(): bool
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
            $definition->getScope($this->referrerId),
            $definition->isFileBased()
        ) . ";\n";

        return $code;
    }
}
