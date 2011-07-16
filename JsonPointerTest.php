<?php
require_once 'JsonPointer.php';

class JsonPointerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException Exception
     * @expectedExceptionMessage Pointer starts with invalid character
     */
    public function getShouldThrowExpectedExceptionWhenPointerStartsWithInvalidPointerChar()
    {
        $jsonPointer = new JsonPointer('{"a": 1}');
        $jsonPointer->get('#');
    }    
    /**
     * @test
     * @expectedException Exception
     * @expectedExceptionMessage Pointer starts with invalid character
     */
    public function setShouldThrowExpectedExceptionWhenPointerStartsWithInvalidPointerChar()
    {
        $jsonPointer = new JsonPointer('{"a": 1}');
        $jsonPointer->set('#', 'test');
    }
    /**
     * @test
     * @dataProvider nonStringPointerProvider
     * @expectedException Exception
     * @expectedExceptionMessage Pointer is not a string
     */
    public function getShouldThrowExpectedExceptionWhenPointerIsNotAString($nonStringPointer)
    {
        $jsonPointer = new JsonPointer('{"a": 1}');
        $jsonPointer->get($nonStringPointer);
    }
    /**
     * @test
     * @dataProvider emptyPointerProvider
     * @expectedException Exception
     * @expectedExceptionMessage Pointer is empty
     */
    public function getShouldThrowExpectedExceptionWhenUsingAnEmptyPointer($emptyPointer)
    {
        $jsonPointer = new JsonPointer('{"a": 1}');
        $jsonPointer->get($emptyPointer);
    }
    /**
     * @test
     * @dataProvider nonStringPointerProvider
     * @expectedException Exception
     * @expectedExceptionMessage Pointer is not a string
     */
    public function setShouldThrowExpectedExceptionWhenPointerIsNotAString($nonStringPointer)
    {
        $jsonPointer = new JsonPointer('{"a": 1}');
        $jsonPointer->set($nonStringPointer, 'test');
    }
    /**
     * @test
     * @dataProvider emptyPointerProvider
     * @expectedException Exception
     * @expectedExceptionMessage Pointer is empty
     */
    public function setShouldThrowExpectedExceptionWhenUsingAnEmptyPointer($emptyPointer)
    {
        $jsonPointer = new JsonPointer('{"a": 1}');
        $jsonPointer->set($emptyPointer, 'test');
    }
    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid Json to point through
     * @dataProvider invalidJsonProvider
     * @test
     */
    public function getShouldThrowExpectedExceptionWhenUsingInvalidJson($invalidJson)
    {
        $jsonPointer = new JsonPointer($invalidJson);
        $jsonPointer->get('/');
    }
    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid Json to point through
     * @dataProvider invalidJsonProvider
     * @test
     */
    public function setShouldThrowExpectedExceptionWhenUsingInvalidJson($invalidJson)
    {
        $jsonPointer = new JsonPointer($invalidJson);
        $jsonPointer->set('/', 'test');
    }
    /**
     * @expectedException Exception
     * @expectedExceptionMessage Non walkable Json to point through
     * @dataProvider nonWalkableJsonProvider
     * @test
     */
    public function getShouldThrowExpectedExceptionWhenUsingNonWalkableJson($nonWalkableJson)
    {
        $jsonPointer = new JsonPointer($nonWalkableJson);
        $jsonPointer->get('/');
    }
    /**
     * @expectedException Exception
     * @expectedExceptionMessage Non walkable Json to point through
     * @dataProvider nonWalkableJsonProvider
     * @test
     */
    public function setShouldThrowExpectedExceptionWhenUsingNonWalkableJson($nonWalkableJson)
    {
        $jsonPointer = new JsonPointer($nonWalkableJson);
        $jsonPointer->set('/', 'test');
    }
    /**
     * @test
     */
    public function getShouldReturnGivenJsonWhenUsingOnlyARootPointer()
    {
        $givenJson = '{"status":["done","started","planned"]}';
        $jsonPointer = new JsonPointer($givenJson);
        $pointedJson = $jsonPointer->get('/');
        $assertionMessage = 'Unexpected mismatch between given and pointed Json';
        $this->assertSame($givenJson, $pointedJson, $assertionMessage);
    }
    /**
     * @test
     */
    public function setShouldReturnGivenJsonWhenUsingOnlyARootPointer()
    {
        $givenJson = '{"status":["done","started","planned","pending"]}';
        $jsonPointer = new JsonPointer($givenJson);
        $pointedJson = $jsonPointer->set('/', 'test');
        $assertionMessage = 'Unexpected mismatch between given and pointed Json';
        $this->assertSame($givenJson, $pointedJson, $assertionMessage);
    }
    /**
     * @test
     */
    public function getShouldReturnExpectedValueOfSecondElementBelowNamedPointer()
    {
        $givenJson = '{"status": ["done", "started", "planned"]}';
        $jsonPointer = new JsonPointer($givenJson);
        $this->assertSame('started', $jsonPointer->get('/status/1'));
    }
    /**
     * @test
     */
    public function setShouldReturnExpectedValueOfSecondElementBelowNamedPointer()
    {
        $givenJson = '{"status": ["done", "started", "planned"]}';
        $jsonPointer = new JsonPointer($givenJson);
        $this->assertSame('started', $jsonPointer->set('/status/1', 'scheduled'));
    }
    /**
     * @test
     */
    public function setShouldOverwriteValueOfSecondElementBelowNamedPointer()
    {
        $givenJson = '{"status": ["done", "started", "planned"]}';
        $jsonPointer = new JsonPointer($givenJson);
		$this->assertSame('started', $jsonPointer->set('/status/1', 'scheduled'));
		$this->assertSame('scheduled', $jsonPointer->get('/status/1'));
    }
    /**
     * @test
     */
    public function setShouldOverwriteSpecifiedValueWithNewSpecialCharsContainingValue()
    {
        $givenJson = '{"status": ["done", "started", "planned"]}';
        $jsonPointer = new JsonPointer($givenJson);
		$this->assertSame('started', $jsonPointer->set('/status/1', 'urgentstuff [#pt.1] {\/}'));
		$this->assertSame('urgentstuff [#pt.1] {\/}', $jsonPointer->get('/status/1'));
    }
    /**
     * @test
     */
    public function setShouldOverwriteValueOfSecondElementBelowDeepNamedPointer()
    {
		$givenJson = '{"categories":{"a":{"a1":{"a1a":["a1aa"],"a1b":["a1bb"]},"a2":["a2a","a2b"]}}}';
        $jsonPointer = new JsonPointer($givenJson);
		$this->assertSame('a2a', $jsonPointer->set('/categories/a/a2/0', 'a222222a'));
		$this->assertSame('a222222a', $jsonPointer->get('/categories/a/a2/0'));
    }
    /**
     * @test
     */
    public function setShouldUnsetFirstElementBelowNamedPointer()
    {
        $givenJson = '{"status": ["done", "started", "planned"]}';
        $jsonPointer = new JsonPointer($givenJson);
		$this->assertSame('done', $jsonPointer->set('/status/0'));
		$this->assertTrue(count($jsonPointer->get('/status')) === 2);
		$this->assertSame('started', $jsonPointer->get('/status/0'));
		$this->assertSame('planned', $jsonPointer->get('/status/1'));
    }
    /**
     * @test
     */
	public function removalOfFooShouldKeepTheAssociativeIndexOfQux()
	{
        $givenJson = '{"foo":1,"bar":{"baz":2},"qux":[3,4,5]}';
        $jsonPointer = new JsonPointer($givenJson);
		$this->assertSame(1, $jsonPointer->set('/foo'));
		$this->assertSame('{"bar":{"baz":2},"qux":[3,4,5]}', $jsonPointer->get('/'));
	}
    /**
     * @test
     */
    public function setShouldUnsetSecondElementBelowBelowDeepNamedPointer()
    {
		$givenJson = '{"categories":{"a":{"a1":{"a1a":["a1aa"],"a1b":["a1bb"]},"a2":["a2a","a2b"]}}}';
        $jsonPointer = new JsonPointer($givenJson);
		$this->assertSame('a2b', $jsonPointer->set('/categories/a/a2/1'));
		$this->assertTrue(count($jsonPointer->get('/categories/a/a2')) === 1);
		$this->assertSame('a2a', $jsonPointer->get('/categories/a/a2/0'));
    }
    /**
     * @test
     */
    public function getShouldReturnExpectedValueOfFourthElement()
    {
        $givenJson = '["done", "started", "planned","pending","archived"]';
        $jsonPointer = new JsonPointer($givenJson);
        $this->assertSame('pending', $jsonPointer->get('/3'));
    }    
    /**
     * @test
     */
    public function getShouldReturnNullOnNonMatchingPointer()
    {
        $givenJson = '["done", "started", "planned","pending","archived"]';
        $jsonPointer = new JsonPointer($givenJson);
        $this->assertNull($jsonPointer->get('/6'));
    }
    /**
     * @test
     */
    public function setShouldReturnNullOnNonMatchingPointer()
    {
        $givenJson = '["done", "started", "planned","pending","archived"]';
        $jsonPointer = new JsonPointer($givenJson);
        $this->assertNull($jsonPointer->set('/6', 'reopened'));
    }
    /**
     * @test
     */
    public function getShouldReturnExpectedValueOfSecondElementBelowDeepNamedPointer()
    {
        $givenJson = '{"categories":{"a":{"a1":{"a1a":["a1aa"],"a1b":["a1bb"]},"a2":["a2a","a2b"]}}}';
        $jsonPointer = new JsonPointer($givenJson);
        $this->assertSame('a2b', $jsonPointer->get('/categories/a/a2/1'));
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
    public function emptyPointerProvider()
    {
        return array(
          array(''),
          array(" ")
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
          array('[]'),
        );
    }
}