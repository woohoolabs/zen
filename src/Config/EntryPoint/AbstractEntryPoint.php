<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\EntryPoint;

use WoohooLabs\Zen\Config\Autoload\AutoloadConfigInterface;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfigInterface;

abstract class AbstractEntryPoint implements EntryPointInterface
{
    /** @var bool|null */
    private $autoloaded;
    /** @var bool|null */
    private $fileBased;

    /**
     * @return $this
     */
    public function autoload()
    {
        $this->autoloaded = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableAutoload()
    {
        $this->autoloaded = false;

        return $this;
    }

    /**
     * @internal
     */
    public function isAutoloaded(AutoloadConfigInterface $autoloadConfig): bool
    {
        return $this->autoloaded ?? $autoloadConfig->isGlobalAutoloadEnabled();
    }

    /**
     * @return $this
     */
    public function fileBased()
    {
        $this->fileBased = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableFileBased()
    {
        $this->fileBased = false;

        return $this;
    }

    /**
     * @internal
     */
    public function isFileBased(FileBasedDefinitionConfigInterface $fileBasedDefinitionConfig): bool
    {
        return $this->fileBased ?? $fileBasedDefinitionConfig->isGlobalFileBasedDefinitionEnabled();
    }
}
