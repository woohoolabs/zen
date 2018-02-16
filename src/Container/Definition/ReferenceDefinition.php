<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

class ReferenceDefinition extends AbstractDefinition
{
    /**
     * @var string
     */
    private $referrerId;

    /**
     * @var string
     */
    private $scope;

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
        parent::__construct($referencedClassName, str_replace("\\", "__", $referencedClassName));
        $this->referrerId = $referrerId;
        $this->scope = $scope;
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

    public function toPhpCode(): string
    {
        if ($this->scope === "singleton") {
            $code = "        return \$this->singletonEntries['{$this->referrerId}'] = ";
            $code .= $this->getEntryToPhp($this->getId(), $this->getHash()) . ";\n";

            return $code;
        }

        return "        return " . $this->getEntryToPhp($this->getId(), $this->getHash()) . ";\n";
    }
}
