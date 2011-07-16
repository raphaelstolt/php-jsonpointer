<?php
/**
  * @author  Raphael Stolt <raphael.stolt@gmail.com>
  * @license http://is.gd/doowutchyalikewithit Doowutchyalikewithit License
  */
class JsonPointer
{
    const POINTER_CHAR = '/';
    
    /**
     * @var array
     */
    private $json;
    /**
     * @param string $json The Json structure to point through. Should be at least a walkable array.
     */
    public function __construct($json) 
    {
        $this->json = json_decode($json, true);
    }
    /**
     * @param string $pointer The Json Pointer
     * @return array
     */
    public function get($pointer)
    {
        $this->validateJson();
        $this->validatePointer($pointer);
        
        if ($pointer === self::POINTER_CHAR) {
            return json_encode($this->json);
        }
        $plainPointerParts = array_slice(explode('/', $pointer), 1);

        return $this->traverse($this->json, $plainPointerParts);
    }    
    /**
     * @param string $pointer The Json Pointer
     * @param string $value   The value to set when pointer matches (null == unset/remove).
     * @return array
     */
    public function set($pointer, $value = null)
    {
        $this->validateJson();
        $this->validatePointer($pointer);
        
        if ($pointer === self::POINTER_CHAR) {
            return json_encode($this->json);
        }
        $plainPointerParts = array_slice(explode('/', $pointer), 1);

        $overwrite = false;
        if ($value === null) {
            $overwrite = true;
        }

        return $this->traverse($this->json, $plainPointerParts, $value, $overwrite);
    }
    /**
     * @param  array $json          The json_decode'd Json structure.
     * @param  array $pointerParts  The parts of the feed pointer.
     * @param  mixed $value         The value to use in the set/unset case.
     * @param  mixed $overwrite     Boolean flag for the unset case.
     * @return mixed
     */
    private function traverse(array &$json, array $pointerParts, $value = null, $overwrite = false)
    {
        $pointerPart = array_shift($pointerParts);

        if (isset($json[$pointerPart])) {
            if (count($pointerParts) === 0) {
                if ($value !== null) {
                    $formerValue = $json[$pointerPart]; 
                    $json[$pointerPart] = $value;

                    return $formerValue;
                } elseif ($value === null && $overwrite === true) {
                    $formerValue = $json[$pointerPart]; 
                    unset($json[$pointerPart]);
                    if (ctype_digit($pointerPart)) {
                        $json = array_values($json);
                    }
                                  
                    return $formerValue;
                }

                return $json[$pointerPart];
            } else {
                return $this->traverse($json[$pointerPart], $pointerParts, $value, $overwrite);
            }
        }

        return null;
    }
    private function validateJson()
    {
        if ($this->json === null) {
            throw new Exception('Invalid Json to point through');
        }
        if (!is_array($this->json) || count($this->json) === 0) {
            throw new Exception('Non walkable Json to point through');
        }
    }
    /**
     * @param string $pointer The Json Pointer
     */
    private function validatePointer($pointer)
    {
        if (!is_string($pointer)) {
            throw new Exception('Pointer is not a string');
        }
        if (trim($pointer) === '') {
            throw new Exception('Pointer is empty');
        }       
        if (substr($pointer, 0, 1) !== self::POINTER_CHAR) {
            throw new Exception('Pointer starts with invalid character');
        }
    }
}