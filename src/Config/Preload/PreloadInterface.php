<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Preload;


interface PreloadInterface
{
    /**
     * @return string[]
     * @internal
     */
    public function getClassNames(): array;
}
