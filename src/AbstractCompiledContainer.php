<?php

declare(strict_types=1);

namespace WoohooLabs\Zen;

use Closure;
use Psr\Container\ContainerInterface;
use WoohooLabs\Zen\Exception\NotFoundException;

use function property_exists;

abstract class AbstractCompiledContainer implements ContainerInterface
{
    /** @var array<string, object> */
    protected array $singletonEntries = [];

    /**
     * @param string $id
     */
    abstract public function has(string $id): bool;

    /**
     * @param string $id
     * @throws NotFoundException
     */
    abstract public function get(string $id): mixed;

    /**
     * @param object $object
     * @param array<string, mixed> $properties
     * @return object
     */
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
