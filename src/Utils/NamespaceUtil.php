<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Utils;

use ReflectionClass;
use ReflectionException;
use WoohooLabs\Zen\Exception\ContainerException;
use const DIRECTORY_SEPARATOR;
use function file_exists;
use function str_replace;
use function strlen;
use function substr;
use function substr_compare;

class NamespaceUtil
{
    public static function getClassesInPsr4Namespace(string $namespace, bool $recursive, bool $onlyInstantiable): array
    {
        /** @var array|null $psr4Prefixes */
        static $psr4Prefixes = null;
        if ($psr4Prefixes === null) {
            $psr4Prefixes = require self::getPsr4AutoloaderPath();
        }

        $namespacePrefix = self::getBestMatchingPsr4Prefix($psr4Prefixes, $namespace);
        if ($namespacePrefix === "") {
            return [];
        }

        $namespacePostfix = substr($namespace, strlen($namespacePrefix));
        $prefixPaths = $psr4Prefixes[$namespacePrefix];

        $classNames = [];
        foreach ($prefixPaths as $prefixPath) {
            $postfixPath = str_replace("\\", DIRECTORY_SEPARATOR, $namespacePostfix);
            $path = $prefixPath . DIRECTORY_SEPARATOR . ($postfixPath ? $postfixPath . DIRECTORY_SEPARATOR : "");
            $pathLength = strlen($path);

            foreach (FileSystemUtil::getPhpFilesInPath($path, $recursive) as $file) {
                $fileName = str_replace(".php", "", substr($file, $pathLength));
                $className = str_replace(DIRECTORY_SEPARATOR, "\\", $fileName);
                $fqClassName = $namespacePrefix . $namespacePostfix . "\\" . $className;

                if ($onlyInstantiable) {
                    try {
                        $reflectionClass = new ReflectionClass($fqClassName);
                        if ($reflectionClass->isInstantiable()) {
                            $classNames[] = $fqClassName;
                        }
                    } catch (ReflectionException $exception) {
                        continue;
                    }
                } else {
                    $classNames[] = $fqClassName;
                }
            }
        }

        return $classNames;
    }

    private static function getPsr4AutoloaderPath(): string
    {
        static $autoloaderPath = null;
        if ($autoloaderPath !== null) {
            return (string) $autoloaderPath;
        }

        $paths = [
            __DIR__ . "/../../../../composer/autoload_psr4.php",
            __DIR__ . "/../../vendor/composer/autoload_psr4.php",
            __DIR__ . "/../vendor/composer/autoload_psr4.php",
        ];

        foreach ($paths as $file) {
            if (file_exists($file)) {
                return $autoloaderPath = $file;
            }
        }

        throw new ContainerException("PSR-4 autoloader file can not be found!");
    }

    private static function getBestMatchingPsr4Prefix(array $psr4Prefixes, string $namespace): string
    {
        $maxLength = 0;
        $maxPrefix = "";

        foreach ($psr4Prefixes as $prefix => $path) {
            $prefixLength = strlen($prefix);

            if ($prefixLength > $maxLength && substr_compare($namespace, $prefix, 0, $prefixLength) === 0) {
                $maxPrefix = $prefix;
                $maxLength = $prefixLength;
            }
        }

        return $maxPrefix;
    }
}
