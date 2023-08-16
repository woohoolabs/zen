<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Utils;

use DirectoryIterator;
use IteratorIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;
use Throwable;

use function array_key_exists;
use function assert;
use function count;
use function file_get_contents;
use function in_array;
use function is_string;
use function ltrim;
use function strlen;
use function strpos;
use function substr;
use function substr_compare;
use function token_get_all;

use const T_ABSTRACT;
use const T_CLASS;
use const T_INTERFACE;
use const T_NS_SEPARATOR;
use const T_STRING;
use const T_WHITESPACE;

class FileSystemUtil
{
    public static function getRelativeFilenameForClass(string $basePath, string $className): string
    {
        static $pathCache = [];
        $key = $basePath . "/" . $className;

        if (array_key_exists($key, $pathCache)) {
            return $pathCache[$key];
        }

        // Get absolute filename
        try {
            $reflectionClass = new ReflectionClass($className);
            $filename = $reflectionClass->getFileName();
        } catch (Throwable $e) {
            $pathCache[$key] = "";

            return "";
        }

        if ($filename === false) {
            $pathCache[$key] = "";

            return "";
        }

        // Make the filename relative to the root directory
        $filename = self::getRelativeFilename($basePath, $filename);

        $pathCache[$key] = $filename;

        return $filename;
    }

    public static function getRelativeFilename(string $basePath, string $filename): string
    {
        if ($basePath !== "" && strpos($filename, $basePath) === 0) {
            $filename = ltrim(substr($filename, strlen($basePath)), "\\/");
        }

        return $filename;
    }

    /**
     * @return string[]
     */
    public static function getClassesInPath(string $path, bool $onlyConcreteClasses): array
    {
        $result = [];

        foreach (self::getPhpFilesInPath($path) as $filePath) {
            foreach (self::getClassesInFile($filePath, $onlyConcreteClasses) as $namespace => $classes) {
                foreach ($classes as $class) {
                    $result[] = is_string($namespace) ? $namespace . "\\" . $class : $class;
                }
            }
        }

        return $result;
    }

    /**
     * @return string[]
     */
    public static function getPhpFilesInPath(string $path, bool $recursive = true, bool $caseInsensitive = true): array
    {
        if ($recursive) {
            $directoryIterator = new RecursiveDirectoryIterator($path);
            $iterator = new RecursiveIteratorIterator($directoryIterator);
        } else {
            $directoryIterator = new DirectoryIterator($path);
            $iterator = new IteratorIterator($directoryIterator);
        }

        $result = [];
        foreach ($iterator as $file) {
            assert($file instanceof SplFileInfo);
            $path = $file->getPathname();

            if (isset($path[4]) && substr_compare($path, ".php", -4, null, $caseInsensitive) === 0) {
                $result[] = $path;
            }
        }

        return $result;
    }

    /**
     * @return string[][]
     */
    private static function getClassesInFile(string $filePath, bool $onlyConcreteClasses): array
    {
        $classes = [];
        $namespace = 0;
        $content = file_get_contents($filePath);
        if ($content === false) {
            return [];
        }

        $tokens = token_get_all($content);
        $count = count($tokens);
        $dlm = false;

        for ($i = 2; $i < $count; $i++) {
            if (self::isNamespace($tokens, $i, $dlm)) {
                if ($dlm === false) {
                    $namespace = 0;
                }

                if (isset($tokens[$i][1])) {
                    $namespace = $namespace !== 0 ? $namespace . "\\" . $tokens[$i][1] : $tokens[$i][1];
                    $dlm = true;
                }
            } elseif ($dlm && ($tokens[$i][0] !== T_NS_SEPARATOR) && ($tokens[$i][0] !== T_STRING)) {
                $dlm = false;
            }

            if (self::isRequiredClass($tokens, $i, $onlyConcreteClasses)) {
                $className = $tokens[$i][1];
                if (array_key_exists($namespace, $classes) === false) {
                    $classes[$namespace] = [];
                }
                $classes[$namespace][] = $className;
            }
        }

        return $classes;
    }

    /**
     * @param array<int, array<int, int|string>|string> $tokens
     */
    private static function isNamespace(array $tokens, int $position, bool $dlm): bool
    {
        return (isset($tokens[$position - 2][1]) && $tokens[$position - 2][1] === "namespace") ||
            ($dlm && $tokens[$position - 1][0] === T_NS_SEPARATOR && $tokens[$position][0] === T_STRING);
    }

    /**
     * @param array<int, array<int, int|string>|string> $tokens
     */
    private static function isRequiredClass(array $tokens, int $position, bool $onlyConcreteClasses): bool
    {
        if ($onlyConcreteClasses) {
            return self::isClass($tokens, $position, [T_CLASS], true);
        }

        return self::isClass($tokens, $position, [T_CLASS, T_INTERFACE], false);
    }

    /**
     * @param array<int, array<int, int|string>|string> $tokens
     * @param array<int, int> $allowedClassTokens
     */
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

        $result = in_array($class, $allowedClassTokens, true) && $whitespace === T_WHITESPACE && $name === T_STRING;

        return $result && ($onlyConcreteClasses === false || $type !== T_ABSTRACT);
    }
}
