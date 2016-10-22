<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone;

use Closure;
use Interop\Container\ContainerInterface;
use WoohooLabs\Dicone\Exception\DiconeNotFoundException;

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
            throw new DiconeNotFoundException($id);
        }

        return $this->getEntry($hash);
    }

    protected function getEntry(string $hash)
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

    protected function setPropertyValue($object, string $name, string $hash)
    {
        $value = $this->getEntry($hash);

        Closure::bind(
            function () use ($object, $name, $value) {
                $this->$name = $value;
            },
            $object,
            $object
        )->__invoke();
    }
}
