<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

class ReferenceDefinition extends AbstractDefinition
{
    /**
     * @var string
     */
    private $referencedHash;

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
        $this->referencedHash = str_replace("\\", "__", $referrerId);
        $this->scope = $scope;
    }

    public function needsDependencyResolution(): bool
    {
        return false;
    }

    public function resolveDependencies()
    {
        return $this;
    }

    public function toPhpCode(): string
    {
        if ($this->scope === "singleton") {
            $code = "        \$entry = " . $this->getEntryToPhp($this->getHash()) . ";\n\n";
            $code .= "        return \$this->singletonEntries['" . $this->referencedHash . "'] = \$entry;\n";

            return $code;
        }

        return "        return " . $this->getEntryToPhp($this->getHash()) . ";\n";
    }
}
