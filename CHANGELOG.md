# Changelog

## [Unreleased]

### Added

- `Innmind\BlackBox\Set\Nullable`

### Changed

- All `Set`s constructor are now private
- PHP `8.2` is now required
- `Innmind\BlackBox\Set\AnyType` has been renamed to `Innmind\BlackBox\Set\Type`
- `Innmind\BlackBox\Random` is now an enum

### Removed

- `seeder` method provided by `Innmind\BlackBox\PHPUnit\Blackbox` trait
- Support for PHPUnit `8`
- Support for Symfony `4` and `5`
- `Innmind\BlackBox\Set\Regex`
- `Innmind\BlackBox\Set\Strings::matching()`
- `Innmind\BlackBox\Random\RandomInt`
- `Innmind\BlackBox\Random\MtRand`
