<?php
declare(strict_types=1);

namespace WoohooLabs\Zen;

use Closure;
use Psr\Container\ContainerInterface;
use WoohooLabs\Zen\Exception\NotFoundException;

abstract class AbstractCompiledContainer implements ContainerInterface
{
    /**
     * @var array
     */
    protected $singletonEntries = [];

    /**
     * @var string[]
     */
    protected static $entryPoints = [];

    public function has($id): bool
    {
        return isset(static::$entryPoints[$id]);
    }

    public function get($id)
    {
        return $this->singletonEntries[$id] ?? $this->{static::$entryPoints[$id] ?? "throwNotFoundException"}($id);
    }

    protected function throwNotFoundException(string $id): void
    {
        throw new NotFoundException($id);
    }

    protected function setProperties($object, array $properties): void
    {
        Closure::bind(
            function () use ($object, $properties) {
                foreach ($properties as $name => $value) {
                    $object->$name = $value;
                }
            },
            null,
            $object
        )->__invoke();
    }
}
