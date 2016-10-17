<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Definition;

interface EntrypointInterface
{
    /**
     * @return string[]
     */
    public function getClassNames(): array;
}
