<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Preload;

interface PreloadInterface
{
    /**
     * @internal
     *
     * @return string[]
     */
    public function getClassNames(): array;
}
