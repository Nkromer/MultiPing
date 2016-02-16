<?php

/* 
 * VYDAFramework 2015
 *  [ THE V.Y.D.A. success framework ]
 * 
 * File: vyda.webrequest.php
 * Purpose: Web Request Functionality Abstraction
 * Created: 09/03/2014
 * Updated: 05/05/2015 - JMD
 * Developers: Justin M. Daigle
 * 
 */

class VYDA_WebRequest extends VYDA_Generic_Class {
    
    /* 
     * Class Variables
     */
    private $default_curlopt_vars = array();
    private $override_curlopt_vars = array();
    private $additional_curlopt_vars = array();
        
    /*
     * Constructor 
     */
    function __construct(){
        
        //Set proper type_name
        $this->type_name = __CLASS__;
        
        /*
         * Define the default variables
         */
        $this->default_curlopt_vars = array(
            'CURLOPT_USERAGENT' => 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36',
            'CURLOPT_SSL_VERIFYPEER' => false,
            'CURLOPT_SSL_VERIFYHOST' => false,
            'CURLOPT_TIMEOUT' => 45,
            'CURLOPT_RETURNTRANSFER' => 1,
            'CURLOPT_FOLLOWLOCATION' => 1,
            'CURLOPT_HEADER' => 0
        );
        
    }
    
    /*
     * ActivateCURLOptions
     *    [ sets up the passed in reference with internal vars ]
     */
    private function ActivateCURLOptions(&$ch){
        
        /* Define the final curlOptions array */
        $final_array = array();
        
        /* Determine the course of action */
        if(count($this->override_curlopt_vars)){
            
            /*
             * FINAL ARRAY IS TOTALLY OVERRIDEN... ok smarty pants hope its good
             */
            $final_array = $this->override_curlopt_vars;
            
        }else{
            
            /*
             * FINAL ARRAY IS A COMBINATION
             */
            $final_array = array_merge($this->default_curlopt_vars, $this->additional_curlopt_vars);
            
        }
        
        /* Loop through all final options */
        foreach($final_array as $curlopt_name=>$curlopt_value){
            
            //Set the curl option corresponding to the value
            curl_setopt($ch, constant($curlopt_name), $curlopt_value);
            
        }
        
        /* OK */
        return true;
        
    }
    
    /*
     * SetRequestOptions
     *    [ allows user to override enire default array ]
     */
    public function setCURLOptions($array_override){
        
        
        //set the override array
        $this->override_curlopt_vars = $array_override;
        
    }
    
    /*
     * AddCURLOptions
     *    [ adds curlOPTIONS to the defualt - these options ovveride]
     */
    public function AddCURLOptions($curl_options_array){
        
        //Set additional_curlopt_vars
        $this->additional_curlopt_vars = array_merge($this->additional_curlopt_vars, $curl_options_array);
        
    }
 
    /*
     * Navigate
     *    [ linking function to perform a web request ]
     */ 
    public function Navigate($request_url){
        
        //Initialize curl
        $ch = curl_init();
        
        //Set the URL as a curl option
        $this->AddCURLOptions(array(
            "CURLOPT_URL" => $request_url
        ));
        
        //Set options as defaulted, supplied by user or hybrid
        $this->ActivateCURLOptions($ch);
        
        //Execution
        $curl_result = curl_exec($ch);
        
        //Grab error
        $curl_error = curl_error($ch);
        
        //Free the curl handler
        curl_close($ch);
        
        //Remove the variable from memory
        unset($ch);
        
        //Bugfix - Reset the added vars
        $this->additional_curlopt_vars = array();
        
        //Did this error?
        if($curl_result === false){
            
            //Set the last eror
            $this->last_error = "[cURL Said]: " . $curl_error;
            
            //We did bad, tell dad
            return false;
            
        }else{
            
            /* GOOD RESULT - Return it */
            return $curl_result;
            
        }
        
    }
    
}

