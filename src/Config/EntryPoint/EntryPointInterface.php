<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\EntryPoint;

interface EntryPointInterface
{
    /**
     * @return string[]
     * @internal
     */
    public function getClassNames(): array;

    /**
     * @internal
     */
    public function isAutoloaded(): bool;

    /**
     * @internal
     */
    public function isFileBased(): bool;
}
