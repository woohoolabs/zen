<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

use WoohooLabs\Zen\Utils\FileSystemUtil;

class WildcardHint extends AbstractHint implements WildcardHintInterface
{
    /**
     * @var string
     */
    private $sourcePath;

    /**
     * @var string
     */
    private $sourcePattern;

    /**
     * @var string
     */
    private $targetPattern;

    public static function singleton(string $sourcePath, string $sourcePattern, string $targetPattern): WildcardHint
    {
        return new self($sourcePath, $sourcePattern, $targetPattern);
    }

    public static function prototype(string $sourcePath, string $sourcePattern, string $targetPattern): WildcardHint
    {
        $self = new self($sourcePath, $sourcePattern, $targetPattern);
        $self->setPrototypeScope();

        return $self;
    }

    public function __construct(string $sourcePath, string $sourcePattern, string $targetPattern)
    {
        parent::__construct();
        $this->sourcePath = $sourcePath;
        $this->sourcePattern = $sourcePattern;
        $this->targetPattern = $targetPattern;
    }

    public function getDefinitionHints(): array
    {
        $sourceRegex = "/" . str_replace([".", "\\", "*"], ["\\.", "\\\\", "(.*)"], $this->sourcePattern) . "/";

        $definitionHints = [];
        foreach (FileSystemUtil::getClassesInPath($this->sourcePath, false) as $sourceClass) {
            $matches = [];
            preg_match_all($sourceRegex, $sourceClass, $matches);

            $targetClass = $this->targetPattern;
            foreach ($matches[1] as $match) {
                $targetClass = preg_replace('/\*/', $match, $targetClass, 1);
            }

            if (class_exists($targetClass) === false) {
                continue;
            }

            $definitionHint = new DefinitionHint($targetClass);
            if ($this->getScope() === "prototype") {
                $definitionHint->setPrototypeScope();
            }

            $definitionHints[$sourceClass] = $definitionHint;
        }

        return $definitionHints;
    }
}
