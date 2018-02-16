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
        parent::__construct($referencedClassName, str_replace("\\", "__", $referencedClassName), $scope);
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
            $this->getId(),
        ];
    }

    /**
     * @param DefinitionInterface[] $definitions
     */
    public function toPhpCode(array $definitions): string
    {
        $code = "        return ";
        if ($this->getScope() === "singleton") {
            $code .= "\$this->singletonEntries['{$this->referrerId}'] = ";
        }
        $code .= $this->getEntryToPhp($this->getId(), $this->getHash(), $definitions[$this->getId()]->getScope()) . ";\n";

        return $code;
    }
}
