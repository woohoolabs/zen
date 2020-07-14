## 3.0.0 - unreleased

ADDED:

CHANGED:

- Increased minimum version requirement to PHP 8
- Significantly improved performance

REMOVED:

- Support for built-in autoloading

FIXED:

## 2.9.1 - 2020-06-23

ADDED:

- Support for PHP 8
- Support for PHPUnit 9

## 2.9.0 - 2019-12-26

CHANGED:

- The generated container uses `declare(strict_types=1)`
- The generated container is PSR-12 compliant and uses typed properties
- Upgrade to PHPStan 0.12

## 2.8.1 - 2019-09-04

FIXED:

- Run-time PHP version check

## 2.8.0 - 2019-09-04

ADDED:

- Support for preloading for PHP 7.4+
- Support for property injection based on property type declarations
- Support for setting the memory limit

CHANGED:

- Increased minimum PHP version requirement to 7.4 as property type declarations were added
- Updated dev dependencies
- Removed unnecessary `::class` references from the generated container
- Various optimizations via using `array_key_exists()` instead of `isset()` ([further reading](https://github.com/php/php-src/pull/3360))

## 2.7.2 - 2019-03-05

CHANGED:

- Improved performance of property injection by using `static function()` (see [this commit](https://github.com/Ocramius/GeneratedHydrator/commit/bc03e8d1681cb1d8bc60c751cd6aeb2a820498f9) for background)

## 2.7.1 - 2019-01-11

FIXED:

- Edge case issues found by PHPStan

## 2.7.0 - 2019-01-07

ADDED:

- `Psr4NamespaceEntryPoint`: Provides a convenient way to define all classes in a PSR-4 namespace as Entry Point
- `Psr4WildcardHint`: Provides a convenient way to define Wildcard Hints if you use PSR-4 namespaces

CHANGED:

- `RuntimeContainer` became much-much faster
- Faster compilation by optimizing filesystem and array handling

## 2.6.0 - 2019-01-04

ADDED:

- Support for file-based container definitions
- Support for disabling autoload of an Entry Point via `ClassEntryPoint::disableAutoload()` and `WildcardEntryPoint::disableAutoload()`
- Support for autoloading reference definitions

CHANGED:

- Container definitions are inlined in the compiled container when possible
- Autoloaded definitions are inlined in the compiled container when possible
- Various other optimizations of the compiled container based on reference count of container definitions
- Interfaces and parent classes are also autoloaded when an Entry Point is autoloaded
- Optimize compilation time by minimizing class instantiations and caching
- Build the foundations of a faster dynamic container by making it possible to resolve the dependencies of a single class

FIXED:

- Definition binding
- Some issues related to autoloading

## 2.5.1 - 2018-12-21

CHANGED:

- Apply the Woohoo Labs. Coding Standard
- Slightly optimized compilation time by importing functions from the global namespace

## 2.5.0 - 2018-12-08

ADDED:

- Support for scalar injection

FIXED:

- String values containing special characters won't cause fatal error

## 2.4.0 - 2018-08-02

ADDED:

- Support for Context-Dependent Dependency Injection

## 2.3.0 - 2018-07-05

ADDED:

- Support for built-in just-in-time autoloading of the dependency graph of Entry Points

CHANGED:

- Optimized error handling in `AbstractCompiledContainer::get()` method
- Optimized generation of compiled container by removing unnecessary variables
- Optimized retrieval of prototype container entries by removing unnecessary checks
- PHPUnit 7 is minimally required to run tests

FIXED:

- Entry points can not be duplicated now

## 2.2.0 - 2018-01-22

ADDED:

- `RuntimeContainer` container implementation which doesn't need to be compiled

CHANGED:

- Renamed `AbstractContainer` to `AbstractCompiledContainer`
- Optimizing compilation time by using fully qualified function names

FIXED:

- PHP version check in the zen binary

## 2.1.0 - 2017-09-05

CHANGED:

- Increase minimum PHP version requirement to 7.1
- Require `doctrine/annotations` v1.5.0 minimally
- Do not use the deprecated `AnnotationRegistry::registerFile()` method

## 2.0.0 - 2017-02-14

ADDED:

- Support for PSR-11

CHANGED:

- Optimized `$entryPoints` array
- Optimized look-up of Singleton entries

REMOVED:

- Support for Container-Interop

## 1.2.6 - 2017-01-13

FIXED:

- Regression when the container itself can't be retrieved
- `ContainerInterface` entry references the container entry properly
- Removed unnecessary "suggest" block from composer.json

## 1.2.5 - 2017-01-08

FIXED:

- Regression when `ContainerInterface` can't be retrieved

## 1.2.4 - 2017-01-08

FIXED:

- Regression when `ContainerInterface` can't be retrieved

## 1.2.3 - 2017-01-08

FIXED:

- Only Entry Points can be retrieved from the container

## 1.2.2 - 2017-01-05

CHANGED:

- Improved property injection performance

## 1.2.1 - 2017-01-05

FIXED:

- Fixed code formatting of the generated container

## 1.2.0 - 2017-01-05

CHANGED:

- Improved performance even more
- The generated container became more consize

## 1.1.0 - 2016-12-27

CHANGED:

- Improved performance

## 1.0.1 - 2016-10-30

FIXED:

- Dependency resolution when definition hints are transitive

## 1.0.0 - 2016-10-27

- Initial release
