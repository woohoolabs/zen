<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Builder;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Container\ContainerCompiler;
use WoohooLabs\Zen\Container\ContainerDependencyResolver;
use WoohooLabs\Zen\Container\PreloadCompiler;
use WoohooLabs\Zen\Container\PreloadDependencyResolver;
use WoohooLabs\Zen\Exception\ContainerException;
use function dirname;
use function file_exists;
use function file_put_contents;
use function mkdir;
use function rmdir;
use function unlink;
use const DIRECTORY_SEPARATOR;

class FileSystemContainerBuilder implements ContainerBuilderInterface
{
    /**
     * @var AbstractCompilerConfig
     */
    protected $compilerConfig;

    /**
     * @var string
     */
    private $containerPath;

    /**
     * @var string
     */
    private $preloadFilePath;

    public function __construct(AbstractCompilerConfig $compilerConfig, string $containerPath, string $preloadFilePath)
    {
        $this->containerPath = $containerPath;
        $this->compilerConfig = $compilerConfig;
        $this->preloadFilePath = $preloadFilePath;
    }

    public function build(): void
    {
        if ($this->containerPath !== "") {
            $this->buildContainer();
        }

        if ($this->preloadFilePath !== "") {
            $this->buildPreloadFile();
        }
    }

    public function buildContainer(): void
    {
        $dependencyResolver = new ContainerDependencyResolver($this->compilerConfig);
        $compiler = new ContainerCompiler();

        $compiledContainerFiles = $compiler->compile($this->compilerConfig, $dependencyResolver->resolveEntryPoints());

        if (empty($compiledContainerFiles["definitions"]) === false) {
            $definitionDirectory = $this->getDefinitionDirectory();
            $this->deleteDirectory($definitionDirectory);
            $this->createDirectory($definitionDirectory);

            foreach ($compiledContainerFiles["definitions"] as $filename => $content) {
                file_put_contents($definitionDirectory . DIRECTORY_SEPARATOR . $filename, $content);
            }
        }

        file_put_contents($this->containerPath, $compiledContainerFiles["container"]);
    }

    public function buildPreloadFile(): void
    {
        $dependencyResolver = new PreloadDependencyResolver($this->compilerConfig);
        $compiler = new PreloadCompiler();

        $compiledPreloadFile = $compiler->compile($dependencyResolver->resolvePreloads());

        file_put_contents($this->preloadFilePath, $compiledPreloadFile);
    }

    private function deleteDirectory(string $directory): void
    {
        if (file_exists($directory) === false) {
            return;
        }

        $it = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
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
            throw new ContainerException("Directory '$directory' can not be created!");
        }
    }

    private function getDefinitionDirectory(): string
    {
        $basePath = dirname($this->containerPath);
        $relativeDirectory = $this->compilerConfig->getFileBasedDefinitionConfig()->getRelativeDefinitionDirectory();

        if ($relativeDirectory === "") {
            throw new ContainerException("Relative directory of file-based definitions can not be empty!");
        }

        return $basePath . DIRECTORY_SEPARATOR . $relativeDirectory;
    }
}
