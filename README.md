# Woohoo Labs. Zen

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]
[![Gitter][ico-gitter]][link-gitter]

**Woohoo Labs. Zen is a very fast and easy-to-use, PSR-11 (former Container-Interop) compliant DI Container.**

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
* [Support](#support)
* [Credits](#credits)
* [License](#license)

## Introduction

### Rationale

Although Dependency Injection is one of the most fundamental principles of Object Oriented Programming, it doesn't
get as much attention as it should. To make things even worse, there are quite some misbeliefs around the topic which
can prevent people from applying the theory correctly.

Besides using Service Location, the biggest misbelief certainly is that Dependency Injection requires very complex tools
called DI Containers. And we all deem to know that their performance is ridiculously low. Woohoo Labs. Zen was born after
the realization of the fact that these fallacies were true indeed back in 2016.

That's why I tried to create a DI container which makes configuration as explicit and convenient as possible,
which enforces the correct usage of Dependency Injection while providing
[outstanding performance](https://rawgit.com/kocsismate/php-di-container-benchmarks/master/var/benchmark.html) according
to [my benchmarks](https://github.com/kocsismate/php-di-container-benchmarks). That's how Zen was born. Although
I imagined a very simple container initially, with only the essential feature set, over the time, Zen managed to feature
the most important capabilities of the most popular DI Containers currently available.

Fortunately, since the birth of Zen a lot of progress have been made in the DI Container ecosystem: many containers almost
doubled their performance, autowiring and compilation became more popular, but one thing didn't change: Zen is still
one of the fastest PHP containers out there.

### Features

- [PSR-11](https://www.php-fig.org/psr/psr-11/) (former Container-Interop) compliance
- Supports compilation for [maximum performance](https://rawgit.com/kocsismate/php-di-container-benchmarks/master/var/benchmark.html)
- Supports constructor and property injection
- Supports the notion of scopes (Singleton and Prototype)
- Supports autowiring
- Supports scalar and context-dependent injection
- Supports dynamic usage for development

## Install

The only thing you need is [Composer](https://getcomposer.org) before getting started. Then run the command below to get
the latest version:

```bash
$ composer require woohoolabs/zen
```

> Note: The tests and examples won't be downloaded by default. You have to use `composer require woohoolabs/zen --prefer-source`
or clone the repository if you need them.

Zen requires PHP 7.1 at least.

## Basic Usage

### Using the container

As Zen is a PSR-11 compliant container, it supports the `$container->has()` and
`$container->get()` methods as defined by [`ContainerInterface`](https://www.php-fig.org/psr/psr-11/).

### Types of injection

Only constructor and property injection of objects are supported by Zen.

In order to use constructor injection, you have to declare the type of the parameters or add a `@param` PHPDoc tag for them. If a
parameter has a default value then this value will be injected. Here is an example of a constructor with valid parameters:

```php
/**
 * @param B $b
 */
public function __construct(A $a, $b, $c = true)
{
    // ...
}
```

In order to use property injection, you have to annotate your properties with `@Inject` (mind case-sensitivity!), and
provide their type with a `@var` PHPDoc tag in the following way:

```php
/**
 * @Inject
 * @var A
 */
 private $a;
```

As a rule of thumb, you should only rely on constructor injection, because using test doubles in your unit tests
instead of your real dependencies becomes much easier this way. Property injection can be acceptable for those classes
that aren't unit tested. I prefer this type of injection in my controllers, but nowhere else.

### Building the container

Zen is a compiled DI Container which means that every time you update a dependency of a class, you have to recompile
the container in order for it to reflect the changes. This is a major weakness of compiled containers during development,
but that's why Zen also offers a dynamic container implementation which is introduced [later](#dynamic-container).

Compilation is possible by running the following command from your project's root directory:

```bash
$ ./vendor/bin/zen build CONTAINER_PATH COMPILER_CONFIG_CLASS_NAME
```

> Please make sure you escape the `COMPILER_CONFIG_CLASS_NAME` argument when using namespaces, like below:

```bash
./vendor/bin/zen build /var/www/app/Container/Container.php "App\\Container\\CompilerConfig"
```

This results in a new file `CONTAINER_PATH` (e.g.: "/var/www/app/Container/Container.php") which can be directly
instantiated (assuming autoloading is property set up) in your project. No other configuration is needed during runtime
by default.

```php
$container = new Container();
```

Besides from the CLI, you can also build the Container from PHP itself:

```php
$builder = new FileSystemContainerBuilder(new CompilerConfig(), "/var/www/src/Container/CompiledContainer.php");
$builder->build();
```

> It's up to you where you generate the container but please be aware that file system speed can affect the time consumption
of the compilation as well as the performance of your application. On the other hand, it's much more convenient to put
the container in a place where it is easily reachable as you might occasionally need to debug it.

### Configuring the compiler

What about the `COMPILER_CONFIG_CLASS_NAME` argument? This must be the fully qualified name of a class which extends
`AbstractCompilerConfig`. Let's see an [example](https://github.com/woohoolabs/zen/blob/master/examples/CompilerConfig.php)!

```php
class CompilerConfig extends AbstractCompilerConfig
{
    public function getContainerNamespace(): string
    {
        return "App\\Container";
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
            new ContainerConfig(),
        ];
    }
}
```

By providing the prior configuration to the build command, an `App\Container\Container` class will be
generated and the compiler will resolve constructor dependencies via type hinting and PHPDoc comments as well as property
dependencies marked by annotations.

### Configuring the container

We only mentioned so far how to configure the compiler, but we haven't talked about container configuration. This can
be done by returning an array of `AbstractContainerConfig` child instances in the `getContainerConfigs()`
method of the compiler config. Let's see an [example]((https://github.com/woohoolabs/zen/blob/master/examples/ContainerConfig.php))
for the container configuration too:

```php
class ContainerConfig extends AbstractContainerConfig
{
    protected function getEntryPoints(): array
    {
        return [
            // Define all classes in a PSR-4 namespace as Entry Point
            Psr4NamespaceEntryPoint::singleton('WoohooLabs\Zen\Examples\Controller'),

            // Define all classes in a directory as Entry Point
            WildcardEntryPoint::singleton(__DIR__ . "/Controller"),

            // Define a class as Entry Point
            ClassEntryPoint::singleton(UserController::class),
        ];
    }

    protected function getDefinitionHints(): array
    {
        return [
            // Bind the Container class to the ContainerInterface (Singleton scope by default)
            ContainerInterface::class => Container::class,

            // Bind the Request class to the RequestInterface (Prototype scope)
            RequestInterface::class => DefinionHint::singleton(Request::class),

            // Bind the Response class to the ResponseInterface (Singleton scope)
            ResponseInterface::class => DefinionHint::singleton(Response::class),
        ];
    }

    protected function getWildcardHints(): array
    {
        return [
            // Bind all classes in the specified PSR-4 namespaces to each other based on patterns
            new Psr4WildcardHint(
                'WoohooLabs\Zen\Examples\Domain\*RepositoryInterface',
                'WoohooLabs\Zen\Examples\Infrastructure\Mysql*Repository'
            ),

            // Bind all classes in the specified directories to each other based on patterns
            new WildcardHint(
                __DIR__ . "/Domain",
                'WoohooLabs\Zen\Examples\Domain\*RepositoryInterface',
                'WoohooLabs\Zen\Examples\Infrastructure\Mysql*Repository'
            ),
        ];
    }
}
```

Configuring the container consist of the following two things: defining your Entry Points (in the `getEntryPoints()`
method) and passing Hints for the compiler (via the `getDefinitionHints()` and `getWildcardHints()` methods).

### Entry Points

Entry Points are such classes that are to be directly retrieved from the DI Container (for instance Controllers and
Middleware usually fall in this category). This means that you can __only__ fetch Entry Points from the Container with
the `$container->get()` method and nothing else.

Entry Points are not only special because of this, but also because their dependencies are automatically discovered during
the compilation phase resulting in the full object graph (this feature is usually called as "autowiring").

The following example shows a configuration which instructs the compiler to recursively search for all classes in the
`Controller` directory and discover all of their dependencies. Please note that only concrete classes are included by default,
and detection is done recursively.
 
```php
protected function getEntryPoints(): array
{
    return [
        new WildcardEntryPoint(__DIR__ . "/Controller"),
    ];
}
```

If you use PSR-4, there is a more convenient and performant way to define multiple Entry Points at once:

```php
protected function getEntryPoints(): array
{
    return [
        new Psr4NamespaceEntryPoint('Src\Controller'),
    ];
}
```

This way, you can define all classes in a specific PSR-4 namespace as entry point. Please note that only concrete
classes are included by default and detection is done recursively.

Last but not least, you are able to define Entry Points individually too:

```php
protected function getEntryPoints(): array
{
    return [
        new ClassEntryPoint(UserController::class),
    ];
}
```

### Hints

Hints tell the compiler how to properly resolve a dependency. This can be necessary when you depend on an
interface or an abstract class because they are obviously not instantiatable. With hints, you are able to bind
implementations to your interfaces or concretions to your abstract classes. The following example binds the
`Container` class to `ContainerInterface` (in fact, you don't have to bind these two classes together, because this
very configuration is automatically set during compilation).

```php
protected function getDefinitionHints(): array
{
    return [
        ContainerInterface::class => Container::class,
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
        ),
    ];
}
```

will bind

`UserRepositoryInterface` to `MysqlUserRepository`.

If you use PSR-4, there is another - more convenient and performant - way to define the above settings:

```php
protected function getWildcardHints(): array
{
    return [
        new Psr4WildcardHint(
            'WoohooLabs\Zen\Examples\Domain\*RepositoryInterface',
            'WoohooLabs\Zen\Examples\Infrastructure\Mysql*Repository'
        ),
    ];
}
```

This does exactly the same thing as what `WildcardHint` did.

> Note that currently, namespace detection is not recursive; you are only able to use the wildcard character in the class name part,
but not in the namespace (so `WoohooLabs\Zen\Examples\*\UserRepositoryInterface` is invalid); and only `*` supported as
a wildcard character.

### Scopes

Zen is able to control the lifetime of your container entries via the notion of scopes. By default, all entries retrieved
from the container have `Singleton` scope, meaning that they are only instantiated at the first retrieval, and the same
instance will be returned on the subsequent fetches. `Singleton` scope works well for stateless objects.

On the other hand, container entries of `Prototype` scope are instantiated at every retrieval, so that is makes it
possible to store stateful objects in the container. You can hint a container entry as `Prototype` with the
`DefinitionHint::prototype()` construct as follows:

```php
protected function getDefinitionHints(): array
{
    return [
        ContainerInterface::class => DefinitionHint::prototype(Container::class),
    ];
}
```

You can use `WildcardHint::prototype()` to hint your Wildcard Hints the same way too.

## Advanced Usage

### Scalar injection

Scalar injection makes it possible to pass scalar values to an object in the form of constructor arguments or properties.
As of v2.5, Zen supports scalar injection natively. You can use [Hints](#hints) for this purpose as you can see in the
following example:

```php
protected function getDefinitionHints(): array
{
    return [
        UserRepositoryInterface::class => DefinitionHint::singleton(MySqlUserRepository::class)
            ->setParameter("mysqlUser", "root")
            ->setParameter("mysqlPassword", "root"),
            ->setParameter("mysqlPort", 3306),
            ->setProperty("mysqlModes", ["ONLY_FULL_GROUP_BY", "STRICT_TRANS_TABLES", "NO_ZERO_IN_DATE"]),
    ];
}
```

Here, we instructed the DI Container to pass MySQL connection details as constructor arguments to the `MySqlUserRepository`
class. Also, we initialized the `MySqlUserRepository::$mysqlModes` property with an array.

Alternatively, you can use the following technique to simulate scalar injection: extend the class whose constructor parameters
contain scalar types, and provide the arguments in question via `parent::__construct()` in the constructor of the child class.
Finally, add the appropriate [Hint](#hints) to the container configuration so that the child class should be used instead of
the parent class.

### Context-dependent dependency injection

Sometimes - usually for bigger projects - it can be useful to be able to inject different implementations of the same
interface as dependency. Before Zen 2.4.0, you couldn't achieve this unless you used some trick (like extending the
original interface and configuring the container accordingly). Now, context-dependent injection is supported out of the
box by Zen!

Imagine the following case:

```php
class NewRelicHandler implements LoggerInterface {}

class PhpConsoleHandler implements LoggerInterface {}

class MailHandler implements LoggerInterface {}

class ServiceA
{
    public function __construct(LoggerInterface $logger) {}
}

class ServiceB
{
    public function __construct(LoggerInterface $logger) {}
}

class ServiceC
{
    public function __construct(LoggerInterface $logger) {}
}
```

If you would like to use `NewRelicHandler` in `ServiceA`, but `PhpConsoleHandler` in `ServiceB` and `MailHandler` in any
other classes (like `ServiceC`) then you have to configure the [definition hints](#hints) this way:

```php
protected function getDefinitionHints(): array
{
    return [
        LoggerInterface::class => ContextDependentDefinitionHint::create()
            ->setClassContext(
                NewRelicHandler::class,
                [
                    ServiceA::class,
                ]
            ),
            ->setClassContext(
                new DefinitionHint(PhpConsoleHandler::class),
                [
                    ServiceB::class,
                ]
            )
            ->setDefaultClass(MailHandler::class),
    ];
}
```

The code above can be read the following way: when the classes listed in the second parameter of the `setClassContext()` methods
depend on the class/interface in the key of the specified array item (`ServiceA` depends on `LoggerInterface` in the example),
then the class/[definition hint](#hints) in the first parameter will be resolved by the container. If any other class depends
on it, then the class/[definition hint](#hints) in the first parameter of the `setDefaultClass()` method will be resolved.

> Note that if you don't set a default implementation (either via the `setDefaultClass()` method or via constructor parameter)
then a `ContainerException` will be thrown if the interface is injected as a dependency of any class other than the listed
ones in the second parameter of the `setClassContext()` method calls.

### Built-in autoloading of entry points

If you have big object graphs then autoloading can take relatively
[much time](https://blog.blackfire.io/speeding-up-autoloading-on-php-5-6-7-0-for-everyone.html).
[Inspired by Symfony](https://github.com/symfony/symfony/pull/24872), Zen offers a similar functionality
that tries to improve the situation: starting from Zen 2.3, you can configure the container to autoload your
[Entry Points](#entry-points) and all their dependencies by including them (using `include_once`) just before their
first retrieval.

There are two ways of enabling this feature:

- Globally: Configure your [Compiler Configuration](#configuring-the-compiler) by adding this method:
```php
public function getAutoloadConfig(): AutoloadConfigInterface
{
    return AutoloadConfig::enableGlobally("/var/www");
}
```

This way, all your Entry Points will be autoladed by Zen. Note that the first parameter in the example above is the root
directory of your project.

- Selectively: You can choose which Entry Points are to be autoloaded.
```php
protected function getEntryPoints(): array
{
    return [
        Psr4WildcardEntryPoint::create('Src\Controller')
            ->autoload(),

        WildcardEntryPoint::create(__DIR__ . "/Controller")
            ->autoload(),

        ClassEntryPoint::create(Class10::class)
            ->disableAutoload(),      
    ];
}
```

You can also define such classes that should be always autoloaded or which should be never autoloaded by Zen:

```php
public function getAutoloadConfig(): AutoloadConfigInterface
{
    return AutoloadConfig::enableGlobally("/var/www")
        ->setAlwaysAutoloadedClasses(
            [
                "VeryImportantClass1",
            ]
        )
        ->setNeverAutoloadedClasses(
            [
                "NotImportantClass1",
            ]
        );
}
```

This effects that the `VeryImportantClass1` is included just after the compiled container has been instantiated, while the
`NotImportantClass1` won't ever be included by Zen.

**Important:** If you autoload the classes by Zen, you have to pass the root directory of the project in the first parameter of the
compiled container's constructor like below:

```php
$container = new Container("/var/www");
```

### File-based definitions

This is another optimization which was [inspired by Symfony](https://github.com/symfony/symfony/pull/23678): if you have
hundreds or even thousands of entries in the compiled container, then you may be better off separating the content
of the container into different files.

There are two ways of enabling this feature:

- Globally: Configure your [Compiler Configuration](#configuring-the-compiler) by adding this method:
```php
public function getFileBasedDefinitionConfig(): FileBasedDefinitionConfigInterface
{
    return FileBasedDefinitionConfig::enableGlobally("Definitions");
}
```

This way, all definitions will be in separate files. Note that the first parameter in the example above is the directory
where the definitions are generated, relative to the container itself. This directory is automatically deleted and created
during compilation, so be cautious with it.

- Selectively: You can choose which Entry Points are to be separated into different files.
```php
protected function getEntryPoints(): array
{
    return [
        Psr4WildcardEntryPoint::create('Src\Controller')
            ->fileBased(),

        WildcardEntryPoint::create(__DIR__ . "/Controller")
            ->fileBased(),

        ClassEntryPoint::create(Class10::class)
            ->disableFileBased(),
    ];
}
```

### Dynamic container

You probably don't want to recompile the container all the time during development. That's where a dynamic container
helps you:

```php
$container = new RuntimeContainer(new CompilerConfig());
```

> Note that the dynamic container is only suitable for development purposes because it is much slower than the
compiled one - however it is still faster than some of the most well-known DI Containers.

## Examples

If you want to see how Zen works, have a look at the [examples](https://github.com/woohoolabs/yin/tree/master/examples)
folder, where you can find an example configuration (`CompilerConfig`). If `docker-compose` and `make` is available
on your system, then just run the following commands in order to build a container:

```bash
make composer-install  # Install the Composer dependencies
make build             # Build the container into the examples/Container.php
```

## Versioning

This library follows [SemVer v2.0.0](https://semver.org/).

## Change Log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

Woohoo Labs. Zen has a PHPUnit test suite. To run the tests, run the following command from the project folder:

``` bash
$ phpunit
```

Additionally, you may run `docker-compose up` or `make test` in order to execute the tests.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Support

Please see [SUPPORT](SUPPORT.md) for details.

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
[ico-gitter]: https://badges.gitter.im/woohoolabs/zen.svg

[link-packagist]: https://packagist.org/packages/woohoolabs/zen
[link-travis]: https://travis-ci.org/woohoolabs/zen
[link-scrutinizer]: https://scrutinizer-ci.com/g/woohoolabs/zen/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/woohoolabs/zen
[link-downloads]: https://packagist.org/packages/woohoolabs/zen
[link-author]: https://github.com/kocsismate
[link-contributors]: ../../contributors
[link-gitter]: https://gitter.im/woohoolabs/zen?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge
