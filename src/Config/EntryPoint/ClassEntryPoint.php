<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\EntryPoint;

class ClassEntryPoint implements EntryPointInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var bool
     */
    private $autoloaded;

    /**
     * @var array
     */
    private $constructorParams;

    public static function create(string $className, array $constructorParams = []): ClassEntryPoint
    {
        return new ClassEntryPoint($className, $constructorParams);
    }

    public function __construct(string $className, array $constructorParams = [])
    {
        $this->className = $className;
        $this->constructorParams = $constructorParams;
        $this->autoloaded = false;
    }

    public function autoload(): ClassEntryPoint
    {
        $this->autoloaded = true;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getClassNames(): array
    {
        return [
            $this->className,
        ];
    }

    /**
     * @param string $paramName
     * @return bool
     */
    public function hasConstructorParam(string $paramName)
    {
        return array_key_exists($paramName, $this->constructorParams);
    }

    /**
     * @param string $paramName
     * @return mixed
     */
    public function getConstructorParam($paramName)
    {
        return $this->constructorParams[$paramName];
    }

    public function isAutoloaded(): bool
    {
        return $this->autoloaded;
    }
}
