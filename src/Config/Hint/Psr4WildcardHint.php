<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

use WoohooLabs\Zen\Utils\NamespaceUtil;
use function class_exists;
use function interface_exists;
use function preg_match_all;
use function preg_replace;
use function str_replace;
use function strrpos;
use function substr;

class Psr4WildcardHint extends AbstractHint implements WildcardHintInterface
{
    /**
     * @var string
     */
    private $sourcePattern;

    /**
     * @var string
     */
    private $targetPattern;

    public static function singleton(string $sourcePattern, string $targetPattern): Psr4WildcardHint
    {
        return new self($sourcePattern, $targetPattern, "singleton");
    }

    public static function prototype(string $sourcePattern, string $targetPattern): Psr4WildcardHint
    {
        return new self($sourcePattern, $targetPattern, "prototype");
    }

    public function __construct(string $sourcePattern, string $targetPattern, string $scope = "singleton")
    {
        parent::__construct($scope);
        $this->sourcePattern = $sourcePattern;
        $this->targetPattern = $targetPattern;
    }

    /**
     * @return DefinitionHintInterface[]
     * @internal
     */
    public function getDefinitionHints(): array
    {
        $sourceNamespaceLength = strrpos($this->sourcePattern, "\\");
        $sourceNamespace = substr($this->sourcePattern, 0, $sourceNamespaceLength);
        $sourceRegex = "/" . str_replace([".", "\\", "*"], ["\\.", "\\\\", "(.*)"], $this->sourcePattern) . "/";

        $definitionHints = [];
        foreach (NamespaceUtil::getClassesInPsr4Namespace($sourceNamespace, false, false) as $sourceClass) {
            $matches = [];
            preg_match_all($sourceRegex, $sourceClass, $matches);

            $targetClass = $this->targetPattern;
            foreach ($matches[1] as $match) {
                $targetClass = preg_replace("/\*/", $match, $targetClass, 1);
            }

            if (class_exists($targetClass) || interface_exists($targetClass)) {
                $definitionHints[$sourceClass] = new DefinitionHint($targetClass, $this->singleton ? "singleton" : "prototype");
            }
        }

        return $definitionHints;
    }
}
