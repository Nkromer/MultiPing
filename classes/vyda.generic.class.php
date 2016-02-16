<?php

class VYDA_Generic_Class {
    
    /*
     * Class Vars
     */
    protected $last_error_priority = 3;
    protected $last_error = "";
    protected $autolog_source = "Generic";
    protected $type_name = "undefined";
    protected $autolog_name = "AUTOLOG";
    
    /*
     * Default Constructor
     */
    function __construct(){
        
        /* Default type_name */
        $this->type_name = __CLASS__;
        
    }
    
    /*
     * Get Last Error
     */
    public function getLastError(){
        
        //Return the last error
        return $this->last_error;
        
    }
    
    /*
     * type_name
     *  [ what class is running]
     */
    public function type_name(){
        
        //sends back the name of the class running
        return $this->type_name;
        
    }
    
    /*
     * generic autolog
     *   [ this will create a log entry based on issues occuring ]
     */
    protected function AutoLog(){
        
        //DEPRECATED for test
        
    }
    
    /*
     * Destructor
     */
    function __destruct() {
        
        //Deprecated for now
        
    }
    
}

