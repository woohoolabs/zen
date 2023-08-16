<?php

declare(strict_types=1);

namespace WoohooLabs\Zen;

use Closure;
use Psr\Container\ContainerInterface;

abstract class AbstractCompiledContainer implements ContainerInterface
{
    /** @var array<string, object> */
    protected array $singletonEntries = [];

    /** @param array<string, mixed> $properties */
    protected function setClassProperties(object $object, array $properties): object
    {
        Closure::bind(
            static function () use ($object, $properties): void {
                foreach ($properties as $name => $value) {
                    $object->$name = $value;
                }
            },
            null,
            $object
        )->__invoke();

        return $object;
    }
}
