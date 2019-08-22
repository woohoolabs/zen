<?php
declare(strict_types=1);

namespace WoohooLabs\Zen;

use Closure;
use Psr\Container\ContainerInterface;
use WoohooLabs\Zen\Exception\NotFoundException;
use function array_key_exists;

abstract class AbstractCompiledContainer implements ContainerInterface
{
    /** @var array<string, object> */
    protected $singletonEntries = [];

    /** @var string[] */
    protected static $entryPoints = [];

    /**
     * @param string $id
     */
    public function has($id): bool
    {
        return array_key_exists($id, static::$entryPoints);
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

    /**
     * @return mixed
     */
    protected function throwNotFoundException(string $id)
    {
        throw new NotFoundException($id);
    }

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
