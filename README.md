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

Although Dependency Injection is one of the most fundamental principles of Object Oriented Programming, it doesn't
get as much attention as it should. To make things even worse, there are quite some misbeliefs around the topic which
can prevent people from applying the theory correctly.

Besides Service Location, the biggest misbelief certainly is that Dependency Injection requires very complex tools
called DI Containers. And we all know well that their performance is ridiculously low. Woohoo Labs. Zen was born after
the realization of the fact that these fears prove to be real indeed, or at least our current ecosystem endorses
more-complex-than-necessary and slower-than-necessary tools.

I believe that in the vast majority of the cases, very-very simple tools could do the job faster and more importantly,
while remaining less challenging mentally than a competing tool offering everything and more out of the box. I consider
this phenomenon as part of the [simple vs. easy problem](https://www.infoq.com/presentations/Simple-Made-Easy).

Zen doesn't - and probably will never - feature all the capabilities of the most famous DI Containers currently available.
There are things that aren't worth the hassle. On the other hand, it will try hard to enforce the correct usage of
Dependency Injection, and to make the configuration as evident and convenient as possible.

## Install

The steps of this process are quite straightforward. The only thing you need is [Composer](http://getcomposer.org).

#### Add Zen to your composer.json:

Run the command below to get the latest version of Zen:

```bash
$ composer require woohoolabs/zen
```

## Basic Usage

Zen is a compiled DI Container which means that every time you update a dependency of a class, you have to recompile
the container in order for it to reflect the changes. This is a major weakness of compiled containers (Zen will
certainly see major improvements in this regard in the future), but the trade-off had to be taken in order to gain
more speed compared to the "dynamic" Containers.

Compilation is possible by running the following command:

```bash
$ vendor/bin/zen build CONTAINER_PATH COMPILER_CONFIG_CLASS_NAME
```

This results in a new file `CONTAINER_PATH` which can be directly instantiated (assuming proper autoloading) in your
project. No other configuration is needed during runtime.

It's up to you where you generate the container but please be aware that file system speed (referring to the
Virtualbox FS) can affect the time consumption of the compilation as well the performance of your application.
On the other hand, it's much more convenient to put the container in a place where it is easily reachable as you will
occasionally need to debug it.

What about the `COMPILER_CONFIG_CLASS_NAME` argument? This must be a class name which extends the
`WoohooLabs\Zen\Config\AbstractCompilerConfig` class. Let's see an
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

By providing the prior config to the previous `zen build` command, a `MyApp\Config\Container` class will be generated
and the compiler will resolve constructor dependencies via type hinting and PHPDoc comments as well as property
dependencies marked by annotations.

## Advanced Usage

## Examples

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
