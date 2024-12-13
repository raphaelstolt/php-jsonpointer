<?php declare(strict_types=1);

namespace Rs\Json;

use ArrayObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rs\Json\Pointer\InvalidJsonException;
use Rs\Json\Pointer\InvalidPointerException;
use Rs\Json\Pointer\NonexistentValueReferencedException;
use Rs\Json\Pointer\NonWalkableJsonException;

class PointerTest extends TestCase
{
    #[Test]
    #[DataProvider('invalidJsonProvider')]
    public function constructShouldThrowExpectedExceptionWhenUsingInvalidJson($invalidJson)
    {
        $this->expectException(InvalidJsonException::class);
        $this->expectExceptionMessage('Cannot operate on invalid Json.');

        $jsonPointer = new Pointer($invalidJson);
    }

    #[Test]
    #[DataProvider('invalidPointerCharProvider')]
    public function getShouldThrowExpectedExceptionWhenPointerStartsWithInvalidPointerChar($invalidPointerChar)
    {
        $this->expectException(InvalidPointerException::class);
        $this->expectExceptionMessage('Pointer starts with invalid character');

        $jsonPointer = new Pointer('{"a": 1}');
        $jsonPointer->get($invalidPointerChar);
    }

    #[Test]
    #[DataProvider('nonStringPointerProvider')]
    public function getShouldThrowExpectedExceptionWhenPointerIsNotAString($nonStringPointer)
    {
        $this->expectException(InvalidPointerException::class);
        $this->expectExceptionMessage('Pointer is not a string');

        $jsonPointer = new Pointer('{"a": 1}');
        $jsonPointer->get($nonStringPointer);
    }

    #[Test]
    #[DataProvider('nonWalkableJsonProvider')]
    public function getShouldThrowExpectedExceptionWhenUsingNonWalkableJson($nonWalkableJson)
    {
        $this->expectException(NonWalkableJsonException::class);
        $this->expectExceptionMessage('Non walkable Json to point through');

        $jsonPointer = new Pointer($nonWalkableJson);
        $jsonPointer->get('/');
    }

    #[Test]
    public function getShouldReturnGivenJsonWhenUsingOnlyARootPointer()
    {
        $givenJson = '{"status":["done","started","planned"]}';
        $jsonPointer = new Pointer($givenJson);
        $pointedJson = $jsonPointer->get('');

        $this->assertJsonStringEqualsJsonString(
            $givenJson,
            $pointedJson,
            'Unexpected mismatch between given and pointed Json'
        );
    }

    #[Test]
    public function getShouldNotCastEmptyObjectsToArrays()
    {
        $givenJson = '{"foo":{"bar":{},"baz":"qux"}}';
        $jsonPointer = new Pointer($givenJson);
        $pointedJson = $jsonPointer->get('/foo');

        $this->assertTrue(($pointedJson instanceof \stdClass));
        $this->assertTrue(($pointedJson->bar instanceof \stdClass));
    }

    #[Test]
    public function getShouldNotEscapeUnicode()
    {
        $givenJson = '{"status":["第","二","个"]}';
        $jsonPointer = new Pointer($givenJson);
        $pointedJson = $jsonPointer->get('');

        $this->assertEquals(
            $givenJson,
            $pointedJson,
            'Escaped unicode between given and pointed Json'
        );
    }

    #[Test]
    public function getPointerShouldReturnFedPointer()
    {
        $givenJson = '{"status": ["done", "started", "planned"]}';
        $jsonPointer = new Pointer($givenJson);
        $pointer = '/status/1';
        $pointedJson = $jsonPointer->get($pointer);

        $this->assertEquals($pointer, $jsonPointer->getPointer());
    }

    #[Test]
    public function getShouldReturnExpectedValueOfSecondElementBelowNamedPointer()
    {
        $givenJson = '{"status": ["done", "started", "planned"]}';
        $jsonPointer = new Pointer($givenJson);

        $this->assertEquals('started', $jsonPointer->get('/status/1'));
    }

    #[Test]
    public function getShouldReturnExpectedValueOfFourthElement()
    {
        $givenJson = '["done", "started", "planned","pending","archived"]';
        $jsonPointer = new Pointer($givenJson);

        $this->assertEquals('pending', $jsonPointer->get('/3'));
    }

    #[Test]
    public function getShouldReturnExpectedValueOfFourthElementWithNoEscapeUnicode()
    {
        $givenJson = '["done", "started", "planned","第二个","archived"]';
        $jsonPointer = new Pointer($givenJson);

        $this->assertEquals('第二个', $jsonPointer->get('/3'));
    }

    #[Test]
    #[DataProvider('nonexistentValueProvider')]
    public function getShouldThrowExpectedExceptionWhenNonexistentValueIsReferenced($givenJson, $givenPointer)
    {
        $this->expectException(NonexistentValueReferencedException::class);
        $this->expectExceptionMessage('Json Pointer');

        $jsonPointer = new Pointer($givenJson);
        $jsonPointer->get($givenPointer);
    }

    #[Test]
    public function getShouldReturnNullAsAValidValue()
    {
        $givenJson = '{"a":{"b":null}}';
        $jsonPointer = new Pointer($givenJson);

        $this->assertNull($jsonPointer->get('/a/b'));
    }

    #[Test]
    public function getShouldReturnExpectedValueOfSecondElementBelowDeepNamedPointer()
    {
        $givenJson = '{"categories":{"a":{"a1":{"a1a":["a1aa"],"a1b":["a1bb"]},"a2":["a2a","a2b"]}}}';
        $jsonPointer = new Pointer($givenJson);

        $this->assertEquals('a2b', $jsonPointer->get('/categories/a/a2/1'));
    }

    #[Test]
    public function getShouldReturnExpectedValueOfPointerWithSlashInKey()
    {
        $givenJson = '{"test/foo.txt":{"size":1521,"description":"Text File"}}';
        $jsonPointer = new Pointer($givenJson);

        $this->assertEquals(1521, $jsonPointer->get('/test%2Ffoo.txt/size'));
    }

    #[Test]
    #[DataProvider('lastArrayElementsTestDataProvider')]
    public function getShouldReturnLastArrayElementWhenHypenIsGiven($testData)
    {
        $givenJson = $testData['given-json'];
        $jsonPointer = new Pointer($givenJson);

        $this->assertEquals(
            $testData['expected-element'],
            $jsonPointer->get($testData['given-pointer'])
        );
    }

    #[Test]
    public function getShouldTraverseToObjectPropertiesAfterArrayIndex()
    {
        $givenJson = '{"foo": {"bar": {"baz": [ {"bar":"baz"}, {"bar":"qux"} ] }}}';
        $jsonPointer = new Pointer($givenJson);

        $this->assertEquals('baz', $jsonPointer->get('/foo/bar/baz/0/bar'));
        $this->assertEquals('qux', $jsonPointer->get('/foo/bar/baz/1/bar'));
    }
    #[Test]
    public function referenceTokenGettingEvaluated()
    {
        $givenJson = '{"a/b/c": 1, "m~n": 8, "a": {"b": {"c": 12} } }';
        $jsonPointer = new Pointer($givenJson);

        $this->assertEquals(1, $jsonPointer->get('/a~1b~1c'));
        $this->assertEquals(8, $jsonPointer->get('/m~0n'));
        $this->assertEquals(12, $jsonPointer->get('/a/b/c'));
    }

    #[Test]
    #[DataProvider('specSpecialCaseProvider')]
    public function specialCasesFromSpecAreMatched($expectedValue, $pointer)
    {
        $givenJson = '{"foo":["bar","baz"],"":0,"a/b":1,"c%d":2,"e^f":3,"g|h":4,"k\"l":6," ":7,"m~n":8}';
        $jsonPointer = new Pointer($givenJson);

        $this->assertSame($expectedValue, $jsonPointer->get($pointer));
    }

    #[Test]
    public function getShouldReturnEmptyJson()
    {
        $givenJson = $expectedValue = '[]';
        $jsonPointer = new Pointer($givenJson);

        $this->assertSame($expectedValue, $jsonPointer->get(''));
    }

    /**
     * @return array
     */
    public static function invalidPointerCharProvider(): array
    {
        return array(
            array('*'),
            array('#'),
        );
    }

    /**
     * @return array
     */
    public static function lastArrayElementsTestDataProvider(): array
    {
        return array(
            array(array(
                'given-json' => '{"categories":{"a":{"a1":{"a1a":["a1aa"],"a1b":["a1bb"]},"a2":["a2a","a2b"]}}}',
                'expected-element' => 'a2b',
                'given-pointer' => '/categories/a/a2/-')
            ),
            array(array(
                'given-json' => '{"a2":["a2a","a2b","a2c"]}',
                'expected-element' => 'a2c',
                'given-pointer' => '/a2/-')
            ),
        );
    }
    /**
     * @return array
     */
    public static function specSpecialCaseProvider(): array
    {
        return array(
          array('{"foo":["bar","baz"],"":0,"a/b":1,"c%d":2,"e^f":3,"g|h":4,"k\"l":6," ":7,"m~n":8}', ''),
          array(array('bar', 'baz'), '/foo'),
          array('bar', '/foo/0'),
          array(0, '/'),
          array(1, '/a~1b'),
          array(2, '/c%d'),
          array(3, '/e^f'),
          array(4, '/g|h'),
          array(6, "/k\"l"),
          array(7, '/ '),
          array(8, '/m~0n')
        );
    }
    /**
     * @return array
     */
    public static function nonexistentValueProvider(): array
    {
        return array(
            array('["done", "started", "planned","pending","archived"]', '/6'),
            array('{"categories":{"a":{"a1":{"a1a":["a1aa"],"a1b":["a1bb"]},"a2":["a2a","a2b"]}}}', '/categories/b'),
            array('{"a":{"b":{"c":null}}}',  '/a/b/d'),
            array('{"foo":"bar"}', '/foo/boo'),
        );
    }
    /**
     * @return array
     */
    public static function nonStringPointerProvider(): array
    {
        return array(
          array(array()),
          array(15),
          array(new ArrayObject()),
          array(null),
        );
    }
    /**
     * @return array
     */
    public static function invalidJsonProvider(): array
    {
        return array(
          array('['),
          array('{'),
          array('{}}'),
          array('{"Missing colon" null}'),
        );
    }
    /**
     * @return array
     */
    public static function nonWalkableJsonProvider(): array
    {
        return array(
          array('6'),
          array(15),
          array('null'),
        );
    }
}
