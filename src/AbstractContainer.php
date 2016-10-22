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

    public function has($id): bool
    {
        return $this->hasEntry($this->getHash($id));
    }

    public function get($id)
    {
        $hash = $this->getHash($id);

        if ($this->hasEntry($hash) === false) {
            throw new NotFoundException($id);
        }

        return $this->getEntry($hash);
    }

    public function getEntry(string $hash)
    {
        return $this->singletonEntries[$hash] ?? $this->$hash();
    }

    private function hasEntry(string $hash): bool
    {
        return method_exists($this, $hash);
    }

    private function getHash(string $id): string
    {
        return str_replace("\\", "__", $id);
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
