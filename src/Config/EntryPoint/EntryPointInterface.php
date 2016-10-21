<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Config\EntryPoint;

interface EntryPointInterface
{
    /**
     * @return string[]
     */
    public function getClassNames(): array;
}
