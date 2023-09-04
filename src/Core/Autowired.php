<?php
namespace PowerDI\Core;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Autowired
{
    private $qualifier;
    
    public function __construct($qualifier = null){
        $this->qualifier = $qualifier;
    }
    
    public function getQualifier(){
        return $this->qualifier;
    }
}

