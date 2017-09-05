<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Builder;

use WoohooLabs\Zen\Config\AbstractCompilerConfig;

class FileSystemContainerBuilder extends AbstractContainerBuilder
{
    public function build(string $filePath, AbstractCompilerConfig $compilerConfig): void
    {
        file_put_contents($filePath, $this->getContainer($compilerConfig));
    }
}
