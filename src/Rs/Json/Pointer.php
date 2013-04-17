<?php
namespace Rs\Json;

use Rs\Json\Pointer\InvalidJsonException;
use Rs\Json\Pointer\InvalidPointerException;
use Rs\Json\Pointer\NonexistentValueReferencedException;
use Rs\Json\Pointer\NonWalkableJsonException;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;

class Pointer
{
    const POINTER_CHAR = '/';
    const POINTER_URI_FRAGMENT_IDENTIFIER_CHAR = '#';
    const LAST_ARRAY_ELEMENT_CHAR = '-';
    
    /**
     * @var array
     */
    private $json;

    /**
     * @var string
     */
    private $pointer;

    /**
     * @param  string $json The Json structure to point through.
     * @throws Rs\Json\Pointer\InvalidJsonException
     * @throws Rs\Json\Pointer\NonWalkableJsonException
     */
    public function __construct($json) 
    {
        if ($this->lintJson($json)) {
            $this->json = json_decode($json, true);
            if (!$this->isWalkableJson()) {
                throw new NonWalkableJsonException('Non walkable Json to point through');
            }
        }
    }

    /**
     * @param  string $pointer The Json Pointer.
     * @return mixed
     * @throws Rs\Json\Pointer\InvalidPointerException
     * @throws Rs\Json\Pointer\NonexistentValueReferencedException
     */
    public function get($pointer)
    {
        if ($pointer === '' || $pointer === self::POINTER_URI_FRAGMENT_IDENTIFIER_CHAR) {
            return json_encode($this->json);
        }

        $this->validatePointer($pointer);

        if (substr($pointer, 0, 1) === self::POINTER_URI_FRAGMENT_IDENTIFIER_CHAR) {
            $pointer = substr($pointer, 1, strlen($pointer));
        }

        $this->pointer = $pointer;

        $plainPointerParts = array_slice(
            array_map('urldecode', explode('/', $pointer)), 
            1
        );
        return $this->traverse($this->json, $this->evaluatePointerParts($plainPointerParts));
    }

    /**
     * @return string
     */
    public function getPointer()
    {
        return $this->pointer;
    }

    /**
     * @param  array $json          The json_decoded Json structure.
     * @param  array $pointerParts  The parts of the fed pointer.
     * @return mixed
     * @throws Rs\Json\Pointer\NonexistentValueReferencedException
     */
    private function traverse(array &$json, array $pointerParts)
    {
        $pointerPart = array_shift($pointerParts);

        if (isset($json[$pointerPart])) {
            if (count($pointerParts) === 0) {
                return $json[$pointerPart];
            } else {
                if (is_array($json[$pointerPart]) && is_array($pointerParts)) {
                    return $this->traverse($json[$pointerPart], $pointerParts);
                }
            }
        } elseif ($pointerPart === self::LAST_ARRAY_ELEMENT_CHAR && is_array($json)) {
            return end($json);
        } elseif (is_array($json) && count($json) < $pointerPart) {
            // Do nothing, let Exception bubble up
        } elseif (array_key_exists($pointerPart, $json) && $json[$pointerPart] === NULL) {
            return $json[$pointerPart];
        }
        $exceptionMessage = sprintf(
            "Json Pointer '%s' references a nonexistent value",
            $this->getPointer()
        );
        throw new NonexistentValueReferencedException($exceptionMessage);
    }

    /**
     * @param  mixed $json The Json structure to lint.
     * @return boolean
     * @throws RuntimeException
     * @throws Rs\Json\Pointer\InvalidJsonException
     */
    private function lintJson($json)
    {
        if (!class_exists('Seld\\JsonLint\\JsonParser')) {
            throw new \RuntimeException('Unable to lint Json as JsonLint is not installed.');
        }

        $parser = new JsonParser;
        $lintResult = $parser->lint($json);

        if ($lintResult instanceof ParsingException) {
            $exceptionMessage = 'Cannot operate on invalid Json. Message: ' 
                . $lintResult->getMessage();
            throw new InvalidJsonException($exceptionMessage);
        }
        return true;
    }

    /**
     * @return boolean
     */
    private function isWalkableJson()
    {
        if ($this->json !== null && is_array($this->json)) {
            return true;
        }
        return false;
    }

    /**
     * @param  string $pointer The Json Pointer to validate.
     * @throws Rs\Json\Pointer\InvalidPointerException
     */
    private function validatePointer($pointer)
    {
        if ($pointer !== '' && !is_string($pointer)) {
            throw new InvalidPointerException('Pointer is not a string');
        }
        
        $firstPointerCharacter = substr($pointer, 0, 1);
        
        if ($firstPointerCharacter !== self::POINTER_CHAR 
            && $firstPointerCharacter !== self::POINTER_URI_FRAGMENT_IDENTIFIER_CHAR)
        {
            throw new InvalidPointerException('Pointer starts with invalid character');
        }
    }

    /**
     * @param  array $pointerParts The Json Pointer parts to evaluate.
     * @return array
     */
    private function evaluatePointerParts(array $pointerParts)
    {
        $searchables = array('~1', '~0');
        $evaluations = array('/', '~');

        $parts = array();
        array_filter($pointerParts, function($v) use(&$parts, &$searchables, &$evaluations) {
            return $parts[] = str_replace($searchables, $evaluations, $v);
        });
        return $parts;
    }
}