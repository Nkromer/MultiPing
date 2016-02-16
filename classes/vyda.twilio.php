<?php

class VYDA_Twilio extends VYDA_Generic_Class {
    
    /* Class Variables */
    var $dbl;
    
    var $webrequest;
    
    var $base_url = "";
    
    /* Constructor */
    function __construct(&$dbl_ref){
        
        //Set proper type_name
        $this->type_name = __CLASS__;
        
        //Set the pointer of the static DB class
        $this->dbl = $dbl_ref;
        
        //Start up a new VYDA_WebRequst obj!
        $this->webrequest = new VYDA_WebRequest();
        
        //base URL
        $this->base_url = "https://api.twilio.com/" . TWILIO_API_VERSION;
        
    }
    
    /*
     * Send Text Message
     */
    public function sendSimpleSMS($sms_to, $sms_content){
                
        //Send via HTTPWebRequest
        $this->webrequest->AddCURLOptions(array(
            
            "CURLOPT_POSTFIELDS" => array(
                'From' => TWILIO_PHONE_NUMBER,
                'To' => $sms_to,
                'Body' => $sms_content
            ),
            
            "CURLOPT_USERPWD" => TWILIO_ACCOUNT_SID . ":" . TWILIO_AUTH_TOKEN
            
        ));
        
        $result = $this->webrequest->Navigate( $this->base_url . "/Accounts/" . TWILIO_ACCOUNT_SID . "/Messages" );
        
        //put result in the DB
        $sql_query = $this->dbl->CreateInsertQuery('twilio_responses', array(
        	'time' => time(),
			'response_body' => $result
        ), true);
        
        //Execute SQL QUery
        $this->dbl->ExecuteQuery($sql_query);
        
        //Return result!
        return $result;
        
    }
    
}

?>