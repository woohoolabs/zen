<?php
declare(strict_types=1);

namespace WoohooLabs\Zen;

use Closure;
use Psr\Container\ContainerInterface;
use WoohooLabs\Zen\Exception\NotFoundException;

abstract class AbstractContainer implements ContainerInterface
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
        if (isset(static::$entryPoints[$id]) === false) {
            throw new NotFoundException($id);
        }

        $hash = static::$entryPoints[$id];

        return $this->singletonEntries[$hash] ?? $this->$hash();
    }

    protected function setProperties($object, array $properties)
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
