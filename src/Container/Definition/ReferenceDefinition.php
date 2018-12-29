<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Container\DefinitionCompilation;

class ReferenceDefinition extends AbstractDefinition
{
    /**
     * @var string
     */
    private $referencedId;

    public static function singleton(
        string $referrerId,
        string $referencedId,
        bool $isEntryPoint = false,
        bool $isAutoloaded = false,
        bool $isFileBased = false
    ): ReferenceDefinition {
        return new self($referrerId, $referencedId, "singleton", $isEntryPoint, $isAutoloaded, $isFileBased);
    }

    public static function prototype(
        string $referrerId,
        string $referencedId,
        bool $isEntryPoint = false,
        bool $isAutoloaded = false,
        bool $isFileBased = false
    ): ReferenceDefinition {
        return new self($referrerId, $referencedId, "prototype", $isEntryPoint, $isAutoloaded, $isFileBased);
    }

    public function __construct(
        string $referrerId,
        string $referencedId,
        string $scope = "singleton",
        bool $isEntryPoint = false,
        bool $isAutoloaded = false,
        bool $isFileBased = false
    ) {
        parent::__construct($referrerId, $scope, $isEntryPoint, $isAutoloaded, $isFileBased);
        $this->referencedId = $referencedId;
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
            $this->referencedId,
        ];
    }

    /**
     * @param DefinitionInterface[] $definitions
     */
    public function compile(DefinitionCompilation $compilation): string
    {
        $code = "";

        if ($this->isEntryPoint() && $this->isAutoloaded() && $this->isSingleton("") && $this->getReferenceCount() === 0) {
            $code .= $this->includeRelatedClasses($compilation->getAutoloadConfig(), $compilation->getDefinitions(), $this->id) . "\n";
        }

        $code .= "        return ";
        if ($this->isSingleton("") && ($this->getReferenceCount() > 1 || $this->isEntryPoint())) {
            $code .= "\$this->singletonEntries['{$this->id}'] = ";
        }

        $definition = $compilation->getDefinition($this->referencedId);

        $code .= $this->getEntryToPhp(
            $definition->getId($this->id),
            $definition->getHash($this->id),
            $definition->isSingleton($this->id),
            $definition,
            $compilation->getFileBasedDefinitionConfig()
        ) . ";\n";

        return $code;
    }
}
