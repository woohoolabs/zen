<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Builder;

use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Container\Compiler;

class FileSystemContainerBuilder extends AbstractContainerBuilder
{
    /**
     * @var string
     */
    private $filePath;

    public function __construct(AbstractCompilerConfig $compilerConfig, string $filePath)
    {
        parent::__construct($compilerConfig);
        $this->filePath = $filePath;
    }

    public function build(): void
    {
        $compiler = new Compiler();

        $compiledContainer = $compiler->compile($this->compilerConfig, $this->getDefinitions());

        file_put_contents($this->filePath, "<?php\n" . $compiledContainer);
    }
}
