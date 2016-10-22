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

    public function __construct(string $referencedId, string $className, string $scope = "singleton")
    {
        parent::__construct($className, str_replace("\\", "__", $className));
        $this->referencedHash = str_replace("\\", "__", $referencedId);
        $this->scope = $scope;
    }

    public function toPhpCode(): string
    {
        $code = "        \$entry = \$this->getEntry('" . $this->getHash() . "');\n";
        if ($this->scope === "singleton") {
            $code .= "\n        \$this->singletonEntries['" . $this->referencedHash . "'] = \$entry;\n";
        }
        $code .= "\n        return \$entry;\n";

        return $code;
    }
}
