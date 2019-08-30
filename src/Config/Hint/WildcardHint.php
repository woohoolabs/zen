<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

use WoohooLabs\Zen\Utils\FileSystemUtil;
use function class_exists;
use function preg_match_all;
use function preg_replace;
use function str_replace;

class WildcardHint extends AbstractHint implements WildcardHintInterface
{
    private string $sourcePath;
    private string $sourcePattern;
    private string $targetPattern;

    public static function singleton(string $sourcePath, string $sourcePattern, string $targetPattern): WildcardHint
    {
        return new self($sourcePath, $sourcePattern, $targetPattern, "singleton");
    }

    public static function prototype(string $sourcePath, string $sourcePattern, string $targetPattern): WildcardHint
    {
        return new self($sourcePath, $sourcePattern, $targetPattern, "prototype");
    }

    public function __construct(string $sourcePath, string $sourcePattern, string $targetPattern, string $scope = "singleton")
    {
        parent::__construct($scope);
        $this->sourcePath = $sourcePath;
        $this->sourcePattern = $sourcePattern;
        $this->targetPattern = $targetPattern;
    }

    /**
     * @internal
     *
     * @return DefinitionHintInterface[]
     */
    public function getDefinitionHints(): array
    {
        $sourceRegex = "/" . str_replace([".", "\\", "*"], ["\\.", "\\\\", "(.*)"], $this->sourcePattern) . "/";

        $definitionHints = [];
        foreach (FileSystemUtil::getClassesInPath($this->sourcePath, false) as $sourceClass) {
            $matches = [];
            preg_match_all($sourceRegex, $sourceClass, $matches);

            $targetClass = $this->targetPattern;
            foreach ($matches[1] as $match) {
                $result = preg_replace("/\*/", $match, $targetClass, 1);

                if ($result !== null) {
                    $targetClass = $result;
                }
            }

            if (class_exists($targetClass) === false) {
                continue;
            }

            $definitionHints[$sourceClass] = new DefinitionHint($targetClass, $this->singleton ? "singleton" : "prototype");
        }

        return $definitionHints;
    }
}
