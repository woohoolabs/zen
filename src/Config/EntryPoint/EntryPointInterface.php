<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\EntryPoint;

interface EntryPointInterface
{
    /**
     * @return string[]
     */
    public function getClassNames(): array;

    public function isAutoloaded(): bool;
}
