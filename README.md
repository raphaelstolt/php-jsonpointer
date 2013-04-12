# JSON Pointer for PHP

[![Build Status](https://secure.travis-ci.org/raphaelstolt/php-jsonpointer.png)](http://travis-ci.org/raphaelstolt/php-jsonpointer)

This is an implementation of [JSON Pointer](http://tools.ietf.org/html/rfc6901) written in PHP.
Triggered by @janl's node.js [implementation](https://github.com/janl/node-jsonpointer) and being
a bit bore.

## Dependencies (managed via [Composer](http://packagist.org/about-composer))

[`JSON Lint for PHP`](https://github.com/Seldaek/jsonlint) by Jordi Boggiano.

## Installation via Composer

Download the [`composer.phar`](http://getcomposer.org/composer.phar) executable if nonexistent.

Create or modify **composer.json** in the \_\_ROOT_DIRECTORY__ of your project by adding the `php-jsonpointer/php-jsonpointer` dependency.
    
    {
        "require": {
            "php-jsonpointer/php-jsonpointer": "dev-master"
        }
    }

Run Composer: `php composer.phar install` or `php composer.phar update`

## Usage

Now you can use JSON Pointer for PHP via the available Composer **autoload file**.

    <?php
    require_once 'vendor/autoload.php';

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

    // Pointing via URI Fragment Identifier (#)

    $all = $jsonPointer->get("#"); // string('{"foo":1,"bar":{"baz":2},"qux":[3,4,5],"m~n":8,"a/b":0,"e^f":3}')
    $five = $jsonPointer->get("#/qux/-"); // int(5)
    $three = $jsonPointer->get("#/e^f"); // int(3)
    
## Testing

    $ phpunit

## License

JSON Pointer for PHP is licensed under the MIT License

Copyright (c) 2011 - 2013 Raphael Stolt

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
'Software'), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.