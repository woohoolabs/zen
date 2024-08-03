<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Builder;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Container\ContainerCompiler;
use WoohooLabs\Zen\Container\ContainerDependencyResolver;
use WoohooLabs\Zen\Container\PreloadCompiler;
use WoohooLabs\Zen\Container\PreloadDependencyResolver;
use WoohooLabs\Zen\Exception\ContainerException;

use function assert;
use function dirname;
use function file_exists;
use function file_put_contents;
use function mkdir;
use function rmdir;
use function unlink;

use const DIRECTORY_SEPARATOR;

class FileSystemContainerBuilder implements ContainerBuilderInterface
{
    protected AbstractCompilerConfig $compilerConfig;
    private string $containerPath;
    private string $preloadFilePath;

    public function __construct(AbstractCompilerConfig $compilerConfig, string $containerPath, string $preloadFilePath)
    {
        $this->containerPath = $containerPath;
        $this->compilerConfig = $compilerConfig;
        $this->preloadFilePath = $preloadFilePath;
    }

    public function build(): void
    {
        $preloadedClasses = [];
        if ($this->preloadFilePath !== "") {
            $preloadedClasses = $this->buildPreloadFile();
        }

        if ($this->containerPath !== "") {
            $this->buildContainer($preloadedClasses);
        }
    }

    /**
     * @return string[]
     */
    public function buildPreloadFile(): array
    {
        $dependencyResolver = new PreloadDependencyResolver($this->compilerConfig);
        $classes = $dependencyResolver->resolvePreloads();
        $compiler = new PreloadCompiler();

        $compiledPreloadFile = $compiler->compile($this->compilerConfig, $classes);

        $result = file_put_contents($this->preloadFilePath, $compiledPreloadFile);
        if ($result === false) {
            throw new ContainerException("File '$this->preloadFilePath' cannot be written");
        }

        return $classes;
    }

    /**
     * @param string[] $preloadedClasses
     */
    public function buildContainer(array $preloadedClasses): void
    {
        $dependencyResolver = new ContainerDependencyResolver($this->compilerConfig);
        $compiler = new ContainerCompiler();

        $compiledContainerFiles = $compiler->compile($this->compilerConfig, $dependencyResolver->resolveEntryPoints(), $preloadedClasses);

        if ($compiledContainerFiles["definitions"] !== []) {
            $definitionDirectory = $this->getDefinitionDirectory();
            $this->deleteDirectory($definitionDirectory);
            $this->createDirectory($definitionDirectory);

            foreach ($compiledContainerFiles["definitions"] as $filename => $content) {
                $file = $definitionDirectory . DIRECTORY_SEPARATOR . $filename;
                $result = file_put_contents($file, $content);
                if ($result === false) {
                    throw new ContainerException("File '$$file' cannot be written");
                }
            }
        }

        $result = file_put_contents($this->containerPath, $compiledContainerFiles["container"]);
        if ($result === false) {
            throw new ContainerException("File '$this->containerPath' cannot be written");
        }
    }

    private function deleteDirectory(string $directory): void
    {
        if (file_exists($directory) === false) {
            return;
        }

        $it = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            assert($file instanceof SplFileInfo);
            if ($file->isDir()) {
                $result = rmdir($file->getRealPath());
                if ($result === false) {
                    throw new ContainerException("Directory '" . $file->getRealPath() . "' cannot be deleted");
                }
            } else {
                $result = unlink($file->getRealPath());
                if ($result === false) {
                    throw new ContainerException("File '" . $file->getRealPath() . "' cannot be deleted");
                }
            }
        }

        rmdir($directory);
    }

    /**
     * @throws ContainerException
     */
    private function createDirectory(string $directory): void
    {
        if (file_exists($directory)) {
            return;
        }

        $result = mkdir($directory);
        if ($result === false) {
            throw new ContainerException("Directory '$directory' cannot be created");
        }
    }

    private function getDefinitionDirectory(): string
    {
        $basePath = dirname($this->containerPath);
        $relativeDirectory = $this->compilerConfig->getFileBasedDefinitionConfig()->getRelativeDefinitionDirectory();

        if ($relativeDirectory === "") {
            throw new ContainerException("Relative directory of file-based definitions cannot be empty");
        }

        return $basePath . DIRECTORY_SEPARATOR . $relativeDirectory;
    }
}
