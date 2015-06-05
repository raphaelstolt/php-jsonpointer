# JSON Pointer for PHP

[![Build Status](https://secure.travis-ci.org/raphaelstolt/php-jsonpointer.png)](http://travis-ci.org/raphaelstolt/php-jsonpointer)

This is an implementation of [JSON Pointer](http://tools.ietf.org/html/rfc6901) written in PHP.
Triggered by @janl's node.js [implementation](https://github.com/janl/node-jsonpointer) and being
a bit bore.

## Installation via Composer

``` bash
$ composer require php-jsonpointer/php-jsonpointer:1.*
```

## Usage

Now you can use JSON Pointer for PHP via the available Composer **autoload file**.
``` php
<?php require_once 'vendor/autoload.php';

use Rs\Json\Pointer;
use Rs\Json\Pointer\InvalidJsonException;
use Rs\Json\Pointer\NonexistentValueReferencedException;

$invalidJson = '{"Missing colon" null}';

try {
    $jsonPointer = new Pointer($invalidJson);
} catch (InvalidJsonException $e) {
    $message = $e->getMessage(); // Cannot operate on invalid Json. Message: Parse error on line 1: ...
}

$json = '{"foo":1,"bar":{"baz":2},"qux":[3,4,5],"m~n":8,"a/b":0,"e^f":3}';
$jsonPointer = new Pointer($json);

try {
    $all = $jsonPointer->get(""); // string('{"foo":1,"bar":{"baz":2},"qux":[3,4,5],"m~n":8,"a/b":0,"e^f":3}')
    $one = $jsonPointer->get("/foo"); // int(1)
    $two = $jsonPointer->get("/bar/baz"); // int(2)
    $three = $jsonPointer->get("/qux/0"); // int(3)
    $four = $jsonPointer->get("/qux/1"); // int(4)
    $five = $jsonPointer->get("/qux/-"); // int(5)
    $five = $jsonPointer->get("/qux/" . Pointer::LAST_ARRAY_ELEMENT_CHAR); // int(5)
    $zero = $jsonPointer->get("/a~1b"); // int(0)
    $eight = $jsonPointer->get("/m~0n"); // int(8)
    $three = $jsonPointer->get("/e^f"); // int(3)
    $nonexistent = $jsonPointer->get("/qux/7");
} catch (NonexistentValueReferencedException $e) {
    $message = $e->getMessage(); // Json Pointer '/qux/7' reference a nonexistent value
}
```
## Testing

``` bash
$ composer test
```
## License

This library is licensed under the MIT License. Please see [License file](LICENSE.md) for more information.
