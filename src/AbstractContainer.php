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
    protected $entryPoints = [];

    public function has($id): bool
    {
        return isset($this->entryPoints[$id]);
    }

    public function get($id)
    {
        if (isset($this->entryPoints[$id]) === false) {
            throw new NotFoundException($id);
        }

        $hash = $this->entryPoints[$id];

        return $this->singletonEntries[$hash] ?? $this->$hash();
    }

    protected function setProperties($object, array $properties)
    {
        Closure::bind(
            function () use ($properties) {
                foreach ($properties as $name => $value) {
                    $this->$name = $value;
                }
            },
            $object,
            $object
        )->__invoke();
    }
}
