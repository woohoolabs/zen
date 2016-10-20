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
    protected $singletonEntries;

    public function has($id)
    {
        return $this->hasEntry($this->getEntryHash($id));
    }

    public function get($id)
    {
        $entry = $this->getEntryHash($id);

        if ($this->hasEntry($entry) === false) {
            throw new DiconeNotFoundException($id);
        }

        return $this->getEntry($entry);
    }

    protected function getEntry(string $entry)
    {
        return $this->singletonEntries[$entry] ?? $entry();
    }

    private function hasEntry(string $entry): bool
    {
        return method_exists($this, $entry);
    }

    private function getEntryHash(string $id): string
    {
        return str_replace("\\", "__", $id);
    }

    protected function setPropertyValue($object, string $name, string $entry)
    {
        function ($object, string $name, string $entry) {
            Closure::bind(function () use ($name, $entry) {
                $this->$name = $this->getEntry($entry);
            }, $object, $object)->__invoke();
        };
    }
}
