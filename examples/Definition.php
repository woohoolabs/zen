<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Examples;

use WoohooLabs\Dicone\Definition\DefinitionInterface;
use WoohooLabs\Dicone\Definition\DirectoryWildcardEntrypoint;

class Definition implements DefinitionInterface
{
    public function getEntryPoints(): array
    {
        return [
            new DirectoryWildcardEntrypoint(__DIR__)
        ];
    }

    public function getDefinitionItems(): array
    {
        return [];
    }
}
