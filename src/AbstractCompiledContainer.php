<?php

declare(strict_types=1);

namespace WoohooLabs\Zen;

use Closure;
use Psr\Container\ContainerInterface;
use WoohooLabs\Zen\Exception\NotFoundException;

abstract class AbstractCompiledContainer implements ContainerInterface
{
    /** @var array<string, object> */
    protected array $singletonEntries = [];

    /**
     * @param string $id
     */
    abstract public function has($id);

    /**
     * @param string $id
     * @throws NotFoundException
     */
    abstract public function get($id): mixed;

    /**
     * @param object $object
     * @param array<string, mixed> $properties
     * @return object
     */
    protected function setClassProperties($object, $properties)
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
