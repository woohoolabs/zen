<?php
declare(strict_types=1);

namespace WoohooLabs\Zen;

use Closure;
use Interop\Container\ContainerInterface;
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
    protected $hashMap = [];

    public function has($id): bool
    {
        return isset($this->hashMap[$id]);
    }

    public function get($id)
    {
        if (isset($this->hashMap[$id]) === false) {
            throw new NotFoundException($id);
        }

        return $this->getEntry($this->hashMap[$id]);
    }

    public function getEntry(string $hash)
    {
        return $this->singletonEntries[$hash] ?? $this->$hash();
    }

    protected function setProperties($object, array $properties)
    {
        $self = $this;

        Closure::bind(
            function () use ($self, $properties) {
                foreach ($properties as $name => $hash) {
                    $this->$name = $self->getEntry($hash);
                }
            },
            $object,
            $object
        )->__invoke();
    }
}
