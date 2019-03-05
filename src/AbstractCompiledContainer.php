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

    /**
     * @param string $id
     */
    public function has($id): bool
    {
        return isset(static::$entryPoints[$id]);
    }

    /**
     * @param string $id
     * @return mixed
     * @throws NotFoundException
     */
    public function get($id)
    {
        return $this->singletonEntries[$id] ?? $this->{static::$entryPoints[$id] ?? "throwNotFoundException"}($id);
    }

    protected function throwNotFoundException(string $id): void
    {
        throw new NotFoundException($id);
    }

    /**
     * @param object $object
     * @return object
     */
    protected function setClassProperties($object, array $properties)
    {
        Closure::bind(
            static function () use ($object, $properties) {
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
