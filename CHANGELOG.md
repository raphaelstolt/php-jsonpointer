### v3.0.0
  * Fixed bug to avoid casting of empty objects to arrays [#9](https://github.com/raphaelstolt/php-jsonpointer/pull/9)

### v2.0.1
  * Excluded EditorConfig configuration from release

### v2.0.0
  * Dropped PHP 5.3 support to avoid escaped unicode [#21](https://github.com/raphaelstolt/php-jsonpatch/issues/21)

### v1.1.0
  * Official release

### v1.1.0-RC1
  * Dropped handling of special URI Fragment identifier # - closes #5

### v1.0.0-RC2
  * [] is considered as walkable JSON - closes #3
  * Fixed bug in recursive traverse

### v1.0.0-RC1
  * Implemented missing parts of JSON Pointer [RFC 6901](http://tools.ietf.org/html/rfc6901)
  * Dropped support of `set`, as it's not specified in RFC 6901 and should be handled by `JSON Patch` [RFC 6902](http://tools.ietf.org/html/rfc6902)
  * Changed namespacing
  * Added new Exceptions
  * `Rs\Json\Pointer\NonexistentValueReferencedException` is thrown on nonexistent values referenced by a JSON pointer, instead of simply returning `null`

### v0.0.1

  * Initial release based on JSON Pointer [draft 00](http://tools.ietf.org/html/draft-pbryan-zyp-json-pointer-00)
