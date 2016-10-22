<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\EntryPoint;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DirectoryWildcardEntryPoint implements EntryPointInterface
{
    /**
     * @var string
     */
    private $directoryName;

    private $onlyConcreteClasses;

    public function __construct(string $directoryName, bool $onlyConcreteClasses = true)
    {
        $this->directoryName = rtrim($directoryName, "\\/");
        $this->onlyConcreteClasses = $onlyConcreteClasses;
    }

    /**
     * @return string[]
     */
    public function getClassNames(): array
    {
        $items = [];

        foreach ($this->getSourceFilesInDirectory($this->directoryName) as $filePath) {
            foreach ($this->getClassesInFile($filePath) as $namespace => $classes) {
                foreach ($classes as $class) {
                    $items[] = is_string($namespace) ? $namespace . "\\" . $class : $class;
                }
            }
        }

        return $items;
    }

    private function getSourceFilesInDirectory(string $directory): array
    {
        $files = [];
        $di = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $it = new RecursiveIteratorIterator($di);

        foreach ($it as $file) {
            if ($file->getExtension() === "php" || $file->getExtension() === "hhvm") {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    private function getClassesInFile($filePath)
    {
        $classes = [];
        $namespace = 0;
        $tokens = token_get_all(file_get_contents($filePath));
        $count = count($tokens);
        $dlm = false;

        for ($i = 2; $i < $count; $i++) {
            if ($this->isNamespace($tokens, $i, $dlm)) {
                if ($dlm === false) {
                    $namespace = 0;
                }

                if (isset($tokens[$i][1])) {
                    $namespace = $namespace ? $namespace . "\\" . $tokens[$i][1] : $tokens[$i][1];
                    $dlm = true;
                }
            } elseif ($dlm && ($tokens[$i][0] != T_NS_SEPARATOR) && ($tokens[$i][0] != T_STRING)) {
                $dlm = false;
            }

            if ($this->isRequiredClass($tokens, $i)) {
                $className = $tokens[$i][1];
                if (isset($classes[$namespace]) === false) {
                    $classes[$namespace] = [];
                }
                $classes[$namespace][] = $className;
            }
        }

        return $classes;
    }

    private function isNamespace(array $tokens, int $position, bool $dlm)
    {
        return (isset($tokens[$position - 2][1]) && $tokens[$position - 2][1] === "namespace") ||
            ($dlm && $tokens[$position - 1][0] == T_NS_SEPARATOR && $tokens[$position][0] == T_STRING);
    }

    private function isRequiredClass(array $tokens, int $position): bool
    {
        if ($this->onlyConcreteClasses) {
            return $this->isClass($tokens, $position, [T_CLASS], true);
        }

        return $this->isClass($tokens, $position, [T_CLASS, T_INTERFACE], false);
    }

    private function isClass(array $tokens, int $position, array $allowedClassTokens, bool $onlyConcreteClasses): bool
    {
        $type = $tokens[$position - 4][0] ?? null;
        $class = $tokens[$position - 2][0];
        $whitespace = $tokens[$position - 1][0];
        $name = $tokens[$position][0];

        $result = in_array($class, $allowedClassTokens) && $whitespace === T_WHITESPACE && $name === T_STRING;

        return $result && ($onlyConcreteClasses === false || $type !== T_ABSTRACT);
    }
}
