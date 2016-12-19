# Woohoo Labs. Zen

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

**Woohoo Labs. Zen is a very fast and simple, Container-Interop (PSR-11) compliant DI Container.**

## Table of Contents

* [Introduction](#introduction)
* [Install](#install)
* [Basic Usage](#basic-usage)
* [Advanced Usage](#advanced-usage)
* [Examples](#examples)
* [Versioning](#versioning)
* [Change Log](#change-log)
* [Testing](#testing)
* [Contributing](#contributing)
* [Credits](#credits)
* [License](#license)

## Introduction

### Rationale

Although Dependency Injection is one of the most fundamental principles of Object Oriented Programming, it doesn't
get as much attention as it should. To make things even worse, there are quite some misbeliefs around the topic which
can prevent people from applying the theory correctly.

Besides using Service Location, the biggest misbelief certainly is that Dependency Injection requires very complex tools
called DI Containers. And we all deem to know that their performance is ridiculously low. Woohoo Labs. Zen was born after
the realization of the fact that these fallacies seem to be true indeed, or at least our current ecosystem endorses
unnecessarily complex tools, sometimes offering degraded performance.

I believe that in the vast majority of the cases, very-very simple tools could do the job faster and more importantly,
while remaining less challenging mentally than a competing tool offering "everything and more" out of the box. I consider
this phenomenon as part of the [simple vs. easy problem](https://www.infoq.com/presentations/Simple-Made-Easy).

Zen doesn't - and probably will never - feature all the capabilities of the most famous DI Containers currently available.
There are things that aren't worth the hassle. On the other hand, it will try hard to enforce the correct usage of
Dependency Injection, and to make the configuration as evident and convenient as possible.

### Features

- [Container-Interop](https://github.com/container-interop/container-interop) (PSR-11) compliance
- Supports constructor and property injection
- Supports the notion of scopes (Singleton and Prototype)
- Supports autowiring, but only objects can be injected
- Generates a single class
- No caching is needed to get ultimate speed

### Use Cases of Woohoo Labs. Zen

As mentioned before, Zen is suitable for projects needing maximum performance and easy configuration but not requiring
the majority of usual DI techniques, like method or scalar value injection. If performance is not a concern for you,
but you want a fully featured container, please choose another project. In this case, I would recommend you to check out
the awesome [PHP-DI](https://github.com/php-di/php-di) instead of Zen.

But if constructor and property injection of objects is enough for you then Zen will amaze you with its simplicity (the core of the project only consists of cc. 600 lines of code), high performance (it has similar speed when you manually instantiate your objects) and its easy configuration (Zen was designed to work with the least amount of configuration).

## Install

The steps of this process are quite straightforward. The only thing you need is [Composer](http://getcomposer.org).
Run the command below to get the latest version of Zen:

```bash
$ composer require woohoolabs/zen
```
This library needs PHP 7.0+.

## Basic Usage

### Using the container

As Zen is a Container-Interop (PSR-11) compliant container, it supports the `$container->has()` and
`$container->get()` methods as defined by
[`ContainerInterface`](https://github.com/container-interop/container-interop/blob/master/src/Interop/Container/ContainerInterface.php).

### Types of injection

Only constructor and property injection of objects are supported by Zen.

In order to use constructor injection, you have to type hint the parameters or add a `@param` PHPDoc tag for them. If a parameter has a default value then this value will be injected. Here is an example of a valid constructor:

```php
/**
 * @param B $b
 */
public function __construct(A $a, $b, $c = true)
{
    // ...
}
```

In order to use property injection, you have to annotate your properties with `@Inject` (mind case-sensitivity) and provide their type with a `@var` PHPDoc tag in the following way:

```php
/**
 * @Inject
 * @var A
 */
 private $a;
```

As a rule of thumb, you should only rely on constructor injection, because using test doubles in your unit tests instead of your real dependencies becomes much easier this way. Property injection can be acceptable for those classes that aren't unit tested. I prefer this type of injection in my controllers, but nowhere else.

### Building the container

Zen is a compiled DI Container which means that every time you update a dependency of a class, you have to recompile
the container in order for it to reflect the changes. This is a major weakness of compiled containers (Zen will
certainly see major improvements in this regard in the future), but the trade-off had to be taken in order to be more
performant than "dynamic" Containers.

Compilation is possible by running the following command:

```bash
$ vendor/bin/zen build CONTAINER_PATH COMPILER_CONFIG_CLASS_NAME
```

This results in a new file `CONTAINER_PATH` which can be directly instantiated (assuming proper autoloading) in your
project. No other configuration is needed during runtime.

```php
$container = new MyContainer();
```

It's up to you where you generate the container but please be aware that file system speed (referring to the
Virtualbox FS) can affect the time consumption of the compilation as well the performance of your application.
On the other hand, it's much more convenient to put the container in a place where it is easily reachable as you will
occasionally need to debug it.

### Configuring the compiler

What about the `COMPILER_CONFIG_CLASS_NAME` argument? This must be the fully qualified name of a class which extends
`AbstractCompilerConfig`. Let's see an
[example](https://github.com/woohoolabs/zen/blob/master/Config/AbstractCompilerConfig.php)!

```php
class MyCompilerConfig extends AbstractCompilerConfig
{
    public function getContainerNamespace(): string
    {
        return "MyApp\\Config";
    }
    
    public function getContainerClassName(): string
    {
        return "Container";
    }
    
    public function useConstructorInjection(): bool
    {
        return true;
    }
    
    public function usePropertyInjection(): bool
    {
        return true;
    }
    
    public function getContainerConfigs(): array
    {
        return [
            new MyContainerConfig()
        ];
    }
}
```

By providing the prior configuration to the previous `zen build` command, a `MyApp\Config\Container` class will be
generated and the compiler will resolve constructor dependencies via type hinting and PHPDoc comments as well as property
dependencies marked by annotations.

### Configuring the container

So far we only mentioned how to configure the compiler, but we haven't talked about container configuration. This can
be done by returning an array of `AbstractContainerConfig` child instances in the `getContainerConfigs()`
method. Let's see an [example]((https://github.com/woohoolabs/zen/blob/master/Config/AbstractContainerConfig.php))
for the container configuration too!

```php
class MyContainerConfig extends AbstractContainerConfig
{
    protected function getEntryPoints(): array
    {
        return [
            new WildcardEntryPoint(__DIR__ . "/Controller"),
        ];
    }

    protected function getDefinitionHints(): array
    {
        return [
            ContainerInterface::class => MyContainer::class,
        ];
    }

    protected function getWildcardHints(): array
    {
        return [
            new WildcardHint(
                __DIR__ . "/Domain",
                'WoohooLabs\Zen\Examples\Domain\*RepositoryInterface',
                'WoohooLabs\Zen\Examples\Infrastructure\Mysql*Repository'
            )
        ];
    }
}
```

Configuring the container consist of the following two things: defining your Entry Points (in the `getEntryPoints()` method) and
passing Hints for the compiler (in the `getDefinitionHints()` and `getWildcardHints()` methods).

### Entry Points

Entry Points are such classes that are to be directly retrieved from the DI Container (for instance Controllers and Middleware
usually fall in this category). You should only retrieve Entry Points from the Container with the `$container->get()`
method. But as dependencies of these classes are automatically discovered during the compilation phase, resulting in your
full object graph (this feature is usually called "autowiring"), you are able to get other classes directly from the container too (we discourage this practice however).

The following example shows a configuration which instructs the compiler to recursively search for all classes in the `Controller` directory (please note that only concrete classes are included by default) and discover all of their dependencies.
 
```php
protected function getEntryPoints(): array
{
    return [
        new WildcardEntryPoint(__DIR__ . "/Controller"),
    ];
}
```

But you are able to define Entry Points individually too:
 
```php
protected function getEntryPoints(): array
{
    return [
        new ClassEntryPoint(UserController::class),
    ];
}
```

The first method is the preferred one, because it needs much less configuration.

### Hints

Hints tell the compiler how to properly resolve a dependency. This can be necessary when you depend on an
interface or an abstract class because they are obviously not instantiatable. With hints, you are able to bind
implementations to your interfaces or concretions to your abstract classes. The following example binds the
`MyContainer` class to `ContainerInterface` (in fact, you don't have to bind these two classes together, because this very configuration is automatically set during compilation).

```php
protected function getDefinitionHints(): array
{
    return [
        ContainerInterface::class => MyContainer::class,
    ];
}
```

Wildcard Hints can be used when you want to bind your classes in masses. Basically, they recursively search for all your
classes in a directory specified by the first parameter, and bind those classes together which can be matched by the
provided patterns. The following example

```php
protected function getWildcardHints(): array
{
    return [
        new WildcardHint(
            __DIR__ . "/Domain",
            'WoohooLabs\Zen\Examples\Domain\*RepositoryInterface',
            'WoohooLabs\Zen\Examples\Infrastructure\Mysql*Repository'
        )
    ];
}
```

will bind

`UserRepositoryInterface` to `MysqlUserRepository`.

Currently, only `*` supported as a wildcard character because your patterns are much simpler to read this way than with real regex.

### Scopes

Zen is able control the lifetime of your container entries via the notion of scopes. By default, all entries retrieved
from the container have `Singleton` scope, meaning that they are only instantiated at the first retrieval, and the same
instance will be returned on the subsequent fetches. `Singleton` scope works well for stateless objects.

On the other hand, container entries of `Prototype` scope are instantiated at every retrieval, so that is makes it
possible to store stateful objects in the container. You can hint a container entry as `Prototype` with the
`DefinitionHint::prototype()` construct as follows:

```php
protected function getDefinitionHints(): array
{
    return [
        ContainerInterface::class => DefinitioHint::prototype(MyContainer::class),
    ];
}
```

You can use `WildcardHints::prototype()` to hint your Wildcard Hints the same way too.

## Advanced Usage

## Examples

Please have a look at the [examples folder](https://github.com/woohoolabs/zen/tree/master/examples) for a
complete example!

## Versioning

This library follows [SemVer v2.0.0](http://semver.org/).

## Change Log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

Woohoo Labs. Zen has a PHPUnit test suite. To run the tests, run the following command from the project folder
after you have copied phpunit.xml.dist to phpunit.xml:

``` bash
$ phpunit
```

Additionally, you may run `docker-compose up` in order to execute the tests.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Máté Kocsis][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see the [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/woohoolabs/zen.svg
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg
[ico-travis]: https://img.shields.io/travis/woohoolabs/zen/master.svg
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/woohoolabs/zen.svg
[ico-code-quality]: https://img.shields.io/scrutinizer/g/woohoolabs/zen.svg
[ico-downloads]: https://img.shields.io/packagist/dt/woohoolabs/zen.svg

[link-packagist]: https://packagist.org/packages/woohoolabs/zen
[link-travis]: https://travis-ci.org/woohoolabs/zen
[link-scrutinizer]: https://scrutinizer-ci.com/g/woohoolabs/zen/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/woohoolabs/zen
[link-downloads]: https://packagist.org/packages/woohoolabs/zen
[link-author]: https://github.com/kocsismate
[link-contributors]: ../../contributors
