<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Definition;

class ClassEntrypoint implements EntrypointInterface
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
