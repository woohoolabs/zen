## 2.1.0 - unreleased

ADDED:

CHANGED:

REMOVED:

FIXED:

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
