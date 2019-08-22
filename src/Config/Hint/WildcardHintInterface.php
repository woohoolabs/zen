<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

interface WildcardHintInterface
{
    /**
     * @internal
     *
     * @return DefinitionHintInterface[]
     */
    public function getDefinitionHints(): array;
}
