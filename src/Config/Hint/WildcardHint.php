<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

use WoohooLabs\Zen\Utils\FileSystemUtil;

class WildcardHint extends AbstractHint implements WildcardHintInterface
{
    /**
     * @var string
     */
    private $sourcePattern;

    /**
     * @var string
     */
    private $targetPattern;

    /**
     * @var string
     */
    private $sourcePath;

    public static function singleton(string $sourcePattern, string $targetPattern, string $sourcePath)
    {
        return new self($sourcePattern, $targetPattern, $sourcePath);
    }

    public static function prototype(string $sourcePattern, string $targetPattern, string $sourcePath)
    {
        $self = new self($sourcePattern, $targetPattern, $sourcePath);
        $self->setPrototypeScope();

        return $self;
    }

    public function __construct(string $sourcePattern, string $targetPattern, string $sourcePath)
    {
        parent::__construct();
        $this->sourcePattern = $sourcePattern;
        $this->targetPattern = $targetPattern;
        $this->sourcePath = $sourcePath;
    }

    public function getDefinitionHints(): array
    {
        $sourceRegex = "/" . str_replace([".", "\\", "*"], ["\\.", "\\\\", "(.*)"], $this->sourcePattern) . "/";

        $definitions = [];
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

            $definitions[$sourceClass] = $definitionHint;
        }

        return $definitions;
    }
}
