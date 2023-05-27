# Changelog

## [Unreleased]

### Added

- `Innmind\BlackBox\Set\Nullable`
- `Innmind\BlackBox\Set::map()`

### Changed

- All `Set`s constructor are now private
- PHP `8.2` is now required
- `Innmind\BlackBox\Set\AnyType` has been renamed to `Innmind\BlackBox\Set\Type`
- `Innmind\BlackBox\Random` is now an enum
- Requires `PHPUnit` `10`

### Fixed

- Avoid trying to generate from a `Set` in `Set\Either` that can't generate data

### Removed

- `seeder` method provided by `Innmind\BlackBox\PHPUnit\Blackbox` trait
- Support for PHPUnit `8` and `9` (custom printers are now longer available)
- Support for Symfony `4` and `5`
- `Innmind\BlackBox\Set\Regex`
- `Innmind\BlackBox\Set\Strings::matching()`
- `Innmind\BlackBox\Random\RandomInt`
- `Innmind\BlackBox\Random\MtRand`
