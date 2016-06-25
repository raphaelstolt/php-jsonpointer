<?php
namespace Rs\Json;

use Rs\Json\Pointer;
use ArrayObject;

class PointerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Rs\Json\Pointer\InvalidJsonException
     * @expectedExceptionMessage Cannot operate on invalid Json.
     * @dataProvider invalidJsonProvider
     * @test
     */
    public function constructShouldThrowExpectedExceptionWhenUsingInvalidJson($invalidJson)
    {
        $jsonPointer = new Pointer($invalidJson);
    }
    /**
     * @test
     * @dataProvider invalidPointerCharProvider
     * @expectedException Rs\Json\Pointer\InvalidPointerException
     * @expectedExceptionMessage Pointer starts with invalid character
     */
    public function getShouldThrowExpectedExceptionWhenPointerStartsWithInvalidPointerChar($invalidPointerChar)
    {
        $jsonPointer = new Pointer('{"a": 1}');
        $jsonPointer->get($invalidPointerChar);
    }
    /**
     * @test
     * @dataProvider nonStringPointerProvider
     * @expectedException Rs\Json\Pointer\InvalidPointerException
     * @expectedExceptionMessage Pointer is not a string
     */
    public function getShouldThrowExpectedExceptionWhenPointerIsNotAString($nonStringPointer)
    {
        $jsonPointer = new Pointer('{"a": 1}');
        $jsonPointer->get($nonStringPointer);
    }
    /**
     * @expectedException Rs\Json\Pointer\NonWalkableJsonException
     * @expectedExceptionMessage Non walkable Json to point through
     * @dataProvider nonWalkableJsonProvider
     * @test
     */
    public function getShouldThrowExpectedExceptionWhenUsingNonWalkableJson($nonWalkableJson)
    {
        $jsonPointer = new Pointer($nonWalkableJson);
        $jsonPointer->get('/');
    }
    /**
     * @test
     */
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
    /**
     * @test
     */
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
    /**
     * @test
     */
    public function getPointerShouldReturnFedPointer()
    {
        $givenJson = '{"status": ["done", "started", "planned"]}';
        $jsonPointer = new Pointer($givenJson);
        $pointer = '/status/1';
        $pointedJson = $jsonPointer->get($pointer);
        $this->assertEquals($pointer, $jsonPointer->getPointer());
    }
    /**
     * @test
     */
    public function getShouldReturnExpectedValueOfSecondElementBelowNamedPointer()
    {
        $givenJson = '{"status": ["done", "started", "planned"]}';
        $jsonPointer = new Pointer($givenJson);
        $this->assertEquals('started', $jsonPointer->get('/status/1'));
    }
    /**
     * @test
     */
    public function getShouldReturnExpectedValueOfFourthElement()
    {
        $givenJson = '["done", "started", "planned","pending","archived"]';
        $jsonPointer = new Pointer($givenJson);
        $this->assertEquals('pending', $jsonPointer->get('/3'));
    }
    /**
     * @test
     */
    public function getShouldReturnExpectedValueOfFourthElementWithNoEscapeUnicode()
    {
        $givenJson = '["done", "started", "planned","第二个","archived"]';
        $jsonPointer = new Pointer($givenJson);
        $this->assertEquals('第二个', $jsonPointer->get('/3'));
    }
    /**
     * @test
     * @expectedException Rs\Json\Pointer\NonexistentValueReferencedException
     * @expectedExceptionMessage Json Pointer
     * @dataProvider nonexistentValueProvider
     */
    public function getShouldThrowExpectedExceptionWhenNonexistentValueIsReferenced($givenJson, $givenPointer)
    {
        $jsonPointer = new Pointer($givenJson);
        $jsonPointer->get($givenPointer);
    }
    /**
     * @test
     */
    public function getShouldReturnNullAsAValidValue()
    {
        $givenJson = '{"a":{"b":null}}';
        $jsonPointer = new Pointer($givenJson);
        $this->assertNull($jsonPointer->get('/a/b'));
    }
    /**
     * @test
     */
    public function getShouldReturnExpectedValueOfSecondElementBelowDeepNamedPointer()
    {
        $givenJson = '{"categories":{"a":{"a1":{"a1a":["a1aa"],"a1b":["a1bb"]},"a2":["a2a","a2b"]}}}';
        $jsonPointer = new Pointer($givenJson);
        $this->assertEquals('a2b', $jsonPointer->get('/categories/a/a2/1'));
    }
    /**
     * @test
     */
    public function getShouldReturnExpectedValueOfPointerWithSlashInKey()
    {
        $givenJson = '{"test/foo.txt":{"size":1521,"description":"Text File"}}';
        $jsonPointer = new Pointer($givenJson);
        $this->assertEquals(1521, $jsonPointer->get('/test%2Ffoo.txt/size'));
    }
    /**
     * @test
     * @dataProvider lastArrayElementsTestDataProvider
     */
    public function getShouldReturnLastArrayElementWhenHypenIsGiven($testData)
    {
        $givenJson = $testData['given-json'];
        $jsonPointer = new Pointer($givenJson);
        $this->assertEquals(
            $testData['expected-element'],
            $jsonPointer->get($testData['given-pointer'])
        );
    }
    /**
     * @test
     */
    public function referenceTokenGettingEvaluated()
    {
        $givenJson = '{"a/b/c": 1, "m~n": 8, "a": {"b": {"c": 12} } }';
        $jsonPointer = new Pointer($givenJson);

        $this->assertEquals(1, $jsonPointer->get('/a~1b~1c'));
        $this->assertEquals(8, $jsonPointer->get('/m~0n'));
        $this->assertEquals(12, $jsonPointer->get('/a/b/c'));
    }
    /**
     * @dataProvider specSpecialCaseProvider
     * @test
     */
    public function specialCasesFromSpecAreMatched($expectedValue, $pointer)
    {
        $givenJson = '{"foo":["bar","baz"],"":0,"a/b":1,"c%d":2,"e^f":3,"g|h":4,"k\"l":6," ":7,"m~n":8}';
        $jsonPointer = new Pointer($givenJson);
        $this->assertSame($expectedValue, $jsonPointer->get($pointer));
    }
    /**
     * @test
     */
    public function getShouldReturnEmptyJson()
    {
        $givenJson = $expectedValue = '[]';
        $jsonPointer = new Pointer($givenJson);

        $this->assertSame($expectedValue, $jsonPointer->get(''));
    }

    /**
     * @return array
     */
    public function invalidPointerCharProvider()
    {
        return array(
            array('*'),
            array('#'),
        );
    }

    /**
     * @return array
     */
    public function lastArrayElementsTestDataProvider()
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
    public function specSpecialCaseProvider()
    {
        return array(
          array('{"foo":["bar","baz"],"":0,"a\/b":1,"c%d":2,"e^f":3,"g|h":4,"k\"l":6," ":7,"m~n":8}', ''),
          array(array('bar', 'baz'), '/foo'),
          array('bar', '/foo/0'),
          array(0, '/'),
          array(1, '/a~1b'),
          array(2, '/c%d'),
          array(3, '/e^f'),
          array(4, '/g|h'),
          array(6, "/k\"l"),
          array(7, '/ '),
          array(8, '/m~0n'),
        );
    }
    /**
     * @return array
     */
    public function nonexistentValueProvider()
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
    public function nonStringPointerProvider()
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
    public function invalidJsonProvider()
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
    public function nonWalkableJsonProvider()
    {
        return array(
          array('6'),
          array(15),
          array('null'),
        );
    }
}
