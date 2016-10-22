<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\EntryPoint;

class ClassEntryPoint implements EntryPointInterface
{
    /**
     * @var string
     */
    private $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * @return string[]
     */
    public function getClassNames(): array
    {
        return [
            $this->className
        ];
    }
}
