# JSON Pointer for PHP

[![Build Status](https://secure.travis-ci.org/raphaelstolt/php-jsonpointer.png)](http://travis-ci.org/raphaelstolt/php-jsonpointer)

This is an implementation of [JSON Pointer](http://tools.ietf.org/html/draft-pbryan-zyp-json-pointer-00) written in PHP. Triggered by @janl's node.js [implementation](https://github.com/janl/node-jsonpointer) and being a bit bored.

## Usage

    <?php
    require_once 'src/JsonPointer/JsonPointer.php';

    use JsonPointer\JsonPointer;

    $json = '{"foo":1,"bar":{"baz":2},"qux":[3,4,5]}';
    $jsonPointer = new JsonPointer($json);

    $all = $jsonPointer->get("/"); // string('{"foo":1,"bar":{"baz":2},"qux":[3,4,5]}')
    $one = $jsonPointer->get("/foo"); // int(1)
    $two = $jsonPointer->get("/bar/baz"); // int(2)
    $three = $jsonPointer->get("/qux/0"); // int(3)
    $five = $jsonPointer->get("/qux/2"); // int(5)
    $null = $jsonPointer->get("/qux/7"); // null

    $one = $jsonPointer->set("/foo", "something"); // int(1) + json.foo = 'something'
    $something = $jsonPointer->get("/foo"); // string('something')
    $something = $jsonPointer->set("/foo"); // string('something') + json.foo is unset/removed
    $something = $jsonPointer->get("/foo"); // null

    $all = $jsonPointer->get("/"); // string('{"bar":{"baz":2},"qux":[3,4,5]}')


## Installation and usage via [Composer](http://packagist.org/about-composer)

Download the [`composer.phar`](http://getcomposer.org/composer.phar) executable

Create/modify **composer.json** in the *your* projects \_\_ROOT_DIRECTORY__ by adding the `php-jsonpointer` dependency. 
    
    {
        "require": {
            "php-jsonpointer/php-jsonpointer": "master-dev"
        }
    }

Run Composer: `php composer.phar install` or `php composer.phar update`

Use JSON Pointer via the available Composer **autoload file**.

    <?php
    require_once 'vendor/.composer/autoload.php';

    use JsonPointer\JsonPointer;

    $json = '{"foo":1,"bar":{"baz":2},"qux":[3,4,5]}';
    $jsonPointer = new JsonPointer($json);

    $all = $jsonPointer->get("/"); // string('{"foo":1,"bar":{"baz":2},"qux":[3,4,5]}')

## Testing

    $ phpunit --configuration phpunit.xml.dist
    OK (42 tests, 77 assertions)
    $
    
## Author

(c) 2011-2012 Raphael Stolt \<raphael.stolt@gmail.com\>

## License

Doowutchyalikewithit License.