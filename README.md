# JSON Pointer for PHP

This is an implementation of [JSON Pointer](http://tools.ietf.org/html/draft-pbryan-zyp-json-pointer-00) written in PHP. Triggered by @janl's node.js [implementation](https://github.com/janl/node-jsonpointer) and beeing a bit bored.

## Usage

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

## Testing

    $ phpunit --configuration phpunit.xml.dist
    OK (42 tests, 77 assertions)
    $
    
## Author

(c) 2011-2012 Raphael Stolt <@raphaelstolt>

## License

Doowutchyalikewithit License.