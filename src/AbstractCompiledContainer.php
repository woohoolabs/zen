<?php

declare(strict_types=1);

namespace WoohooLabs\Zen;

use Closure;
use Psr\Container\ContainerInterface;

use function property_exists;

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
                    if (property_exists($object, $name)) {
                        $object->$name = $value;
                    }
                }
            },
            null,
            $object
        )->__invoke();

        return $object;
    }
}
