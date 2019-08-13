<?php
declare(strict_types=1);

namespace WoohooLabs\Zen;

use Closure;
use Psr\Container\ContainerInterface;
use WoohooLabs\Zen\Exception\NotFoundException;
use function array_key_exists;

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
        return array_key_exists($id, static::$entryPoints);
    }

    /**
     * @param string $id
     * @return mixed
     * @throws NotFoundException
     */
    public function get($id)
    {
        return $this->singletonEntries[$id] ?? (array_key_exists($id, static::$entryPoints) ? $this->{static::$entryPoints[$id]}() : $this->throwNotFoundException($id));
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
