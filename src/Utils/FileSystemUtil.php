<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Utils;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class FileSystemUtil
{
    public static function getClassesInPath(string $path, bool $onlyConcreteClasses)
    {
        $result = [];

        foreach (self::getSourceFilesInPath($path) as $filePath) {
            foreach (self::getClassesInFile($filePath, $onlyConcreteClasses) as $namespace => $classes) {
                foreach ($classes as $class) {
                    $result[] = is_string($namespace) ? $namespace . "\\" . $class : $class;
                }
            }
        }

        return $result;
    }

    private static function getSourceFilesInPath(string $path): array
    {
        $di = new RecursiveDirectoryIterator($path);
        $it = new RecursiveIteratorIterator($di);
        $files = new RegexIterator($it, '#.*\.(php|hhvm)$#');

        $result = [];
        foreach($files as $file) {
            $result[] = $file->getPathname();
        }

        return $result;
    }

    private static function getClassesInFile($filePath, bool $onlyConcreteClasses): array
    {
        $classes = [];
        $namespace = 0;
        $tokens = token_get_all(file_get_contents($filePath));
        $count = count($tokens);
        $dlm = false;

        for ($i = 2; $i < $count; $i++) {
            if (self::isNamespace($tokens, $i, $dlm)) {
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

            if (self::isRequiredClass($tokens, $i, $onlyConcreteClasses)) {
                $className = $tokens[$i][1];
                if (isset($classes[$namespace]) === false) {
                    $classes[$namespace] = [];
                }
                $classes[$namespace][] = $className;
            }
        }

        return $classes;
    }

    private static function isNamespace(array $tokens, int $position, bool $dlm)
    {
        return (isset($tokens[$position - 2][1]) && $tokens[$position - 2][1] === "namespace") ||
        ($dlm && $tokens[$position - 1][0] == T_NS_SEPARATOR && $tokens[$position][0] == T_STRING);
    }

    private static function isRequiredClass(array $tokens, int $position, bool $onlyConcreteClasses): bool
    {
        if ($onlyConcreteClasses) {
            return self::isClass($tokens, $position, [T_CLASS], true);
        }

        return self::isClass($tokens, $position, [T_CLASS, T_INTERFACE], false);
    }

    private static function isClass(
        array $tokens,
        int $position,
        array $allowedClassTokens,
        bool $onlyConcreteClasses
    ): bool {
        $type = $tokens[$position - 4][0] ?? null;
        $class = $tokens[$position - 2][0];
        $whitespace = $tokens[$position - 1][0];
        $name = $tokens[$position][0];

        $result = in_array($class, $allowedClassTokens) && $whitespace === T_WHITESPACE && $name === T_STRING;

        return $result && ($onlyConcreteClasses === false || $type !== T_ABSTRACT);
    }
}
