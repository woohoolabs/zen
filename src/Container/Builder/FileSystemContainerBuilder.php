<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Builder;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Container\Compiler;
use WoohooLabs\Zen\Container\DependencyResolver;
use WoohooLabs\Zen\Exception\ContainerException;
use function file_put_contents;

class FileSystemContainerBuilder extends AbstractContainerBuilder
{
    /**
     * @var string
     */
    private $containerPath;

    public function __construct(AbstractCompilerConfig $compilerConfig, string $containerPath)
    {
        parent::__construct($compilerConfig);
        $this->containerPath = $containerPath;
    }

    public function build(): void
    {
        $compiler = new Compiler();
        $dependencyResolver = new DependencyResolver($this->compilerConfig);

        $compiledContainerFiles = $compiler->compile($this->compilerConfig, $dependencyResolver->resolveEntryPoints());

        $definitionDirectory = $this->getDefinitionDirectory();
        $this->deleteDirectory($definitionDirectory);
        $this->createDirectory($definitionDirectory);

        file_put_contents($this->containerPath, $compiledContainerFiles["container"]);
        foreach ($compiledContainerFiles["definitions"] as $filename => $content) {
            file_put_contents("$definitionDirectory" . DIRECTORY_SEPARATOR . "$filename", $content);
        }
    }

    private function deleteDirectory(string $directory): void
    {
        if (file_exists($directory) === false) {
            return;
        }

        $it = new RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach($files as $file) {
            if ($file->isDir()){
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
        $relativeDirectory = $this->compilerConfig->getFileBasedDefinitionConfig()->getRelativeDirectory();

        return $basePath . DIRECTORY_SEPARATOR . $relativeDirectory;
    }
}
