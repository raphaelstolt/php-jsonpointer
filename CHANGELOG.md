# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [v3.0.2] - 2016-08-29
### Fixed
- Fixed bug when deeper nested objects exist after an array index [#11](https://github.com/raphaelstolt/php-jsonpointer/pull/11)

## [v3.0.1] - 2016-08-25
### Fixed
- Fixed bug in checking integerish access keys [#10](https://github.com/raphaelstolt/php-jsonpointer/pull/10)

## [v3.0.0] - 2016-08-22
### Fixed
- Fixed bug to avoid casting of empty objects to arrays [#9](https://github.com/raphaelstolt/php-jsonpointer/pull/9)

## [v2.0.1] - 2016-08-12
### Fixed
- Excluded EditorConfig configuration from release

## [v2.0.0] - 2016-06-20
### Removed
- Dropped PHP 5.3 support to avoid escaped unicode [#21](https://github.com/raphaelstolt/php-jsonpatch/issues/21)

## [v1.1.0] - 2015-05-21
- Official release

## [v1.1.0-RC1] - 2014-12-02
### Removed
- Dropped handling of special URI Fragment identifier # - closes #5

## [v1.0.0-RC2] - 2013-04-19
### Fixed
- [] is considered as walkable JSON - closes #3
- Fixed bug in recursive traverse

## [v1.0.0-RC1] - 2013-04-12
### Added
- Implemented missing parts of JSON Pointer [RFC 6901](http://tools.ietf.org/html/rfc6901)
- Additional exceptions

### Changed
- Changed namespacing
- `Rs\Json\Pointer\NonexistentValueReferencedException` is thrown on nonexistent values referenced by a JSON pointer, instead of simply returning `null`

### Removed
- Dropped support of `set`, as it's not specified in RFC 6901 and should be handled by `JSON Patch` [RFC 6902](http://tools.ietf.org/html/rfc6902)

## v0.0.1 - 2012-11-20
- Initial release based on JSON Pointer [draft 00](http://tools.ietf.org/html/draft-pbryan-zyp-json-pointer-00)

[Unreleased]: https://github.com/raphaelstolt/php-jsonpointer/compare/v3.0.2...HEAD
[v3.0.2]: https://github.com/raphaelstolt/php-jsonpointer/compare/v3.0.1...v3.0.2
[v3.0.1]: https://github.com/raphaelstolt/php-jsonpointer/compare/v3.0.0...v3.0.1
[v3.0.0]: https://github.com/raphaelstolt/php-jsonpointer/compare/v2.0.1...v3.0.0
[v2.0.1]: https://github.com/raphaelstolt/php-jsonpointer/compare/v2.0.0...v2.0.1
[v2.0.0]: https://github.com/raphaelstolt/php-jsonpointer/compare/v1.1.0...v2.0.0
[v1.1.0]: https://github.com/raphaelstolt/php-jsonpointer/compare/v1.1.0-RC1...v1.1.0
[v1.1.0-RC1]: https://github.com/raphaelstolt/php-jsonpointer/compare/v1.0.0-RC2...v1.1.0-RC1
[v1.0.0-RC2]: https://github.com/raphaelstolt/php-jsonpointer/compare/v1.0.0-RC1...v1.0.0-RC2
[v1.0.0-RC1]: https://github.com/raphaelstolt/php-jsonpointer/compare/v0.0.1...v1.0.0-RC1
