<?php
namespace WoohooLabs\Dicone\Definition;

interface EntrypointInterface
{
    /**
     * @return string[]
     */
    public function getClassNames(): array;
}
