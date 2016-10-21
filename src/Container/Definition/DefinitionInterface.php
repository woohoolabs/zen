<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Container\Definition;

interface DefinitionInterface
{
    public function getId(): string;

    public function getHash(): string;

    public function toPhpCode(): string;
}
