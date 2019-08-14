<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

use WoohooLabs\Zen\Exception\ContainerException;
use WoohooLabs\Zen\Utils\NamespaceUtil;
use function class_exists;
use function interface_exists;
use function preg_match_all;
use function preg_replace;
use function str_replace;
use function strpos;
use function strrpos;
use function substr;

class Psr4WildcardHint extends AbstractHint implements WildcardHintInterface
{
    private string $sourcePattern;

    private string $targetPattern;

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
        $sourceNamespace = $this->getNamespace($this->sourcePattern);
        $targetNamespace = $this->getNamespace($this->targetPattern);

        $this->validateNamespace($this->sourcePattern, $sourceNamespace);
        $this->validateNamespace($this->targetPattern, $targetNamespace);

        $sourceRegex = "/" . str_replace([".", "\\", "*"], ["\\.", "\\\\", "(.*)"], $this->sourcePattern) . "/";

        $definitionHints = [];
        foreach (NamespaceUtil::getClassesInPsr4Namespace($sourceNamespace, false, false) as $sourceClass) {
            $matches = [];
            preg_match_all($sourceRegex, $sourceClass, $matches);

            $targetClass = $this->targetPattern;
            foreach ($matches[1] as $match) {
                $result = preg_replace("/\*/", $match, $targetClass, 1);

                if ($result !== null) {
                    $targetClass = $result;
                }
            }

            if (class_exists($targetClass) || interface_exists($targetClass)) {
                $definitionHints[$sourceClass] = new DefinitionHint($targetClass, $this->singleton ? "singleton" : "prototype");
            }
        }

        return $definitionHints;
    }

    private function getNamespace(string $pattern): string
    {
        $namespaceLength = strrpos($pattern, "\\");

        return $namespaceLength === false ? "" : substr($pattern, 0, $namespaceLength);
    }

    private function validateNamespace(string $pattern, string $namespace): void
    {
        if (strpos($namespace, "*") !== false) {
            throw new ContainerException("'$pattern' is an invalid pattern: the namespace part can't contain the asteriks character (*)!");
        }
    }
}
