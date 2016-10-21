<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Config\EntryPoint;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DirectoryWildcardEntryPoint implements EntryPointInterface
{
    /**
     * @var string
     */
    private $directoryName;

    public function __construct(string $directoryName)
    {
        $this->directoryName = rtrim($directoryName, "\\/");
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
            if ((isset($tokens[$i - 2][1]) && ($tokens[$i - 2][1] === "phpnamespace" || $tokens[$i - 2][1] === "namespace")) ||
                ($dlm && $tokens[$i - 1][0] == T_NS_SEPARATOR && $tokens[$i][0] == T_STRING)) {
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

            if (($tokens[$i - 2][0] === T_CLASS || $tokens[$i - 2][0] === T_INTERFACE || $tokens[$i - 2][0] === T_TRAIT)
                || (isset($tokens[$i - 2][1]) && ($tokens[$i - 2][1] === "phpclass" || $tokens[$i - 2][1] === "phpinterface" || $tokens[$i - 2][1] === "phptrait"))
                && $tokens[$i - 1][0] === T_WHITESPACE && $tokens[$i][0] === T_STRING) {
                $class_name = $tokens[$i][1];
                if (isset($classes[$namespace]) === false) {
                    $classes[$namespace] = [];
                }
                $classes[$namespace][] = $class_name;
            }
        }

        return $classes;
    }
}
