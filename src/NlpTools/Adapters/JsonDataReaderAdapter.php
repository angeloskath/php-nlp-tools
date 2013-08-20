<?php
namespace NlpTools\Adapters;
use NlpTools\Interfaces\IDataReaderAdapter;
/**
 * A simple wrapper adapter class around json_decode
 * @author Dan Cardin
 */
class JsonDataReaderAdapter implements IDataReaderAdapter
{
    /**
     * Json encoded string
     * @var string 
     */
    protected $jsonStr;
    
    /**
     *
     * @var boolean 
     */
    protected $assoc = true;
    
    /**
     *
     * @param string $jsonStr A json string that will get decoded
     * @param boolean $assoc Default is true, When TRUE, returned objects will be converted into associative arrays
     */
    public function __construct($jsonStr, $assoc = true)
    {
        $this->jsonStr = $jsonStr;
        $this->assoc = $assoc;
    }
    
    /**
     * Returns the json data as an array
     * @return array 
     */
    public function read() 
    {
        return json_decode($this->jsonStr, $this->assoc);
    }
    
    /**
     * Unset the internal json string 
     */
    public function __destruct()
    {
        unset($this->jsonStr);
    }
}
