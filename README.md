# JSON Pointer for PHP

[![Build Status](https://secure.travis-ci.org/raphaelstolt/php-jsonpointer.png)](http://travis-ci.org/raphaelstolt/php-jsonpointer)

This is an implementation of [JSON Pointer](http://tools.ietf.org/html/draft-pbryan-zyp-json-pointer-00) written in PHP. Triggered by @janl's node.js [implementation](https://github.com/janl/node-jsonpointer) and being a bit bored.

## Dependencies (managed via [Composer](http://packagist.org/about-composer))

[`JSON Lint for PHP`](https://github.com/Seldaek/jsonlint) by Jordi Boggiano.

## Installation via Composer

Download the [`composer.phar`](http://getcomposer.org/composer.phar) executable if not existent.

Create or modify **composer.json** in the \_\_ROOT_DIRECTORY__ of your project by adding the `php-jsonpointer` dependency. 
    
    {
        "require": {
            "php-jsonpointer/php-jsonpointer": "master-dev"
        }
    }

Run Composer: `php composer.phar install` or `php composer.phar update`

## Usage

Now you can use JSON Pointer for PHP via the available Composer **autoload file**.

    <?php
    require_once 'vendor/autoload.php';

    use JsonPointer\JsonPointer;

    $invalidJson = '{"Missing colon" null}';
    $jsonPointer = new JsonPointer($invalidJson); // throws a JsonPointer\Exception

    $json = '{"foo":1,"bar":{"baz":2},"qux":[3,4,5]}';
    $jsonPointer = new JsonPointer($json);

    $all = $jsonPointer->get("/"); // string('{"foo":1,"bar":{"baz":2},"qux":[3,4,5]}')
    $one = $jsonPointer->get("/foo"); // int(1)
    $two = $jsonPointer->get("/bar/baz"); // int(2)
    $three = $jsonPointer->get("/qux/0"); // int(3)
    $four = $jsonPointer->get("/qux/1"); // int(4)
    $five = $jsonPointer->get("/qux/-"); // int(5)
    $five = $jsonPointer->get("/qux/" . JsonPointer::LAST_ARRAY_ELEMENT_CHAR); // int(5)
    $null = $jsonPointer->get("/qux/7"); // null

    $one = $jsonPointer->set("/foo", "something"); // int(1) + json.foo = 'something'
    $something = $jsonPointer->get("/foo"); // string('something')
    $something = $jsonPointer->set("/foo"); // string('something') + json.foo is unset/removed
    $something = $jsonPointer->get("/foo"); // null

    $all = $jsonPointer->get("/"); // string('{"bar":{"baz":2},"qux":[3,4,5]}')
    
## Testing

    $ phpunit --configuration phpunit.xml.dist
    OK (41 tests, 73 assertions)
    $

## License

JSON Pointer for PHP is licensed under the MIT License

Copyright (c) 2011-2012 Raphael Stolt

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