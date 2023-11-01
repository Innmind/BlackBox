# Changelog

## 5.5.4 - 2023-11-01

### Fixed

- Assertion failures in properties were displayed as unexpected exceptions

## 5.5.3 - 2023-10-22

### Fixed

- Uncaught exceptions in properties no longer crash the process

### Deprecated

- `Innmind\BlackBox\PHPUnit\Framework\TestCase::expectException()`
- `Innmind\BlackBox\PHPUnit\Framework\TestCase::expectExceptionCode()`
- `Innmind\BlackBox\PHPUnit\Framework\TestCase::expectExceptionMessage()`

## 5.5.2 - 2023-09-18

### Fixed

- `Innmind\BlackBox\Set\MutuallyExclusive` wasn't excluding strings in a different case

## 5.5.1 - 2023-09-13

### Fixed

- Custom tags loaded from PHPUnit groups were discarded

## 5.5.0 - 2023-09-13

### Added

- `Innmind\BlackBox\PHPUnit\Load::parseTagWith()`
- `Innmind\BlackBox\Application::stopOnFailure()`
- `Innmind\BlackBox\Application::map()`
- `Innmind\BlackBox\Application::when()`

## 5.4.0 - 2023-09-02

### Added

- For iTerm users a mark is now placed on failures allowing you to directly jump between errors

## 5.3.0 - 2023-08-27

### Added

- `Innmind\BlackBox\PHPUnit\Framework\TestCase`
- `Innmind\BlackBox\PHPUnit\Load`
- `Innmind\BlackBox\Application::disableMemoryLimit()`
- `Innmind\BlackBox\Runner\Assert::matches()`
- `Innmind\BlackBox\Runner\Assert::time()`
- `Innmind\BlackBox\Runner\Assert::memory()`
- `Innmind\BlackBox\Set\Slice`
- `Innmind\BlackBox\Set\MutuallyExclusive`
- `Innmind\BlackBox\Tag::ci`
- `Innmind\BlackBox\Tag::local`

### Fixed

- The message `Failing scenarii:` was always printed in PHPUnit output even when there was no failures

## 5.2.0 - 2023-08-19

### Added

- `Innmind\BlackBox\PHPUnit\Compatibility::asDataProvider()`
- `Innmind\BlackBox\Application::disableGlobalFunctions()`
- `Innmind\BlackBox\Runner\proof` function
- `Innmind\BlackBox\Runner\test` function
- `Innmind\BlackBox\Runner\given` function
- `Innmind\BlackBox\Runner\property` function
- `Innmind\BlackBox\Runner\properties` function
- PHPUnit extension now prints the generated data that resulted in an error

### Changed

- `Innmind\BlackBox\PHPUnit\BlackBox::forAll()` is now a `static` method

## 5.1.2 - 2023-07-30

### Fixed

- Increased the randomness between scenarii to avoid collisions when validating a model with state.

## 5.1.1 - 2023-07-29

### Changed

- `Innmind\BlackBox\Set\Call` now regenerate the value when shrinking

## 5.1.0 - 2023-07-14

### Added

- `Innmind\BlackBox\Set\Call`

## 5.0.0 - 2023-05-29

### Added

- `Innmind\BlackBox\Set\Nullable`
- `Innmind\BlackBox\Set::map()`
- `Innmind\BlackBox\PHPUnit\Extension`
- `Innmind\BlackBox\Application`
- `Innmind\BlackBox\Tag`
- `Innmind\BlackBox\Runner\Load`
- `Innmind\BlackBox\Runner\CodeCoverage`
- `Innmind\BlackBox\Runner\Assert`
- `Innmind\BlackBox\Runner\Printer`
- `Innmind\BlackBox\Runner\IO`
- `Innmind\BlackBox\Property::any()`

### Changed

- All `Set`s constructor are now private
- PHP `8.2` is now required
- `Innmind\BlackBox\Set\AnyType` has been renamed to `Innmind\BlackBox\Set\Type`
- `Innmind\BlackBox\Random` is now an enum
- Requires `PHPUnit` `10`
- `Innmind\BlackBox\Properties` constructor is now private, use `::of()` named constructor instead
- `Innmind\BlackBox\Properties::ensureHeldBy` now expects `Innmind\BlackBox\Runner\Assert` as a first argument
- `Innmind\BlackBox\Properties` is now longer compatible with third party test runners
- `Innmind\BlackBox\Property::ensureHeldBy` now expects `Innmind\BlackBox\Runner\Assert` as a first argument
- `Innmind\BlackBox\Property` is now longer compatible with third party test runners
- `Innmind\BlackBox\Set::take()` and `::filter()` now explicitly state that they are mutation free
- `Innmind\BlackBox\Set::take()` now requires a `positive-int` as argument
- `Innmind\BlackBox\Set\Chars` now longer implements the `Set` interface, use `Chars::any()` instead
- `Innmind\BlackBox\Set\IntegersExceptZero` now longer implements the `Set` interface, use `IntegersExceptZero::any()` instead
- `Innmind\BlackBox\Set\NaturalNumbers` now longer implements the `Set` interface, use `NaturalNumbers::any()` instead
- `Innmind\BlackBox\Set\NaturalNumbersExceptZero` now longer implements the `Set` interface, use `NaturalNumbersExceptZero::any()` instead
- `Innmind\BlackBox\Set\Sequence::of()` now longer accept the range of values as 2nd argument, use `Sequence::of()->atLeast()`, `Sequence::of()->atMost()` and `Sequence::of()->between()` instead
- `Innmind\BlackBox\Set\Strings` now longer implements the `Set` interface, use `Strings::any()` instead
- `Innmind\BlackBox\Set\Strings::any()` now longer accept the maximum length, use `Strings::atMost()` instead

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
- `Innmind\BlackBox\Property::name()`
- `Innmind\BlackBox\PHPUnit\BlackBox::seeder()`
- `Innmind\BlackBox\PHPUnit\Seeder`
- `Innmind\BlackBox\Set\Properties::chooseFrom()`, use `Properties::any()->between()` or `Properties::any()->atMost()` instead
- `Innmind\BlackBox\Set\Property`
- `Innmind\BlackBox\Set\Unicode::lengthBetween()`, use `Unicode::any()->between()` instead
