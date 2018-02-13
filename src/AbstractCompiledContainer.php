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
        if (isset($this->singletonEntries[$id])) {
            return $this->singletonEntries[$id];
        }

        if (isset(static::$entryPoints[$id]) === false) {
            throw new NotFoundException($id);
        }

        return $this->{static::$entryPoints[$id]}();
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
