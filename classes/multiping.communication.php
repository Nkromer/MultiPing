<?php

/*
 * File: multiping.communication.php
 *         -- Handle communication bi-directionally
 * 
 *			 PingRequests: [ 0 = UNSENT | 2 = OPEN/WAITING | 7 = PROCESSING | 9 = CLOSED ]
 *			 PingRecipient: [ Status 7 = PROCESSING ] (Status 2 = SENT) (Status 3 = AFFIRMATIVE ) (Status 4 = NEGATIVE ) (Status 5 = OTHER ) (status 0 = UNSENT)
 *
 */

class MultiPING_Communication {
	
	/* Private variables */	 
	 private $last_error = "";
	 
	 private $dbl; //The DBL Reference
	 
	 /* Constructor */
	 public function __construct(&$dbl_ref){
        
        //Set proper type_name
        $this->type_name = __CLASS__;
        
        //Set the pointer of the static DB class
        $this->dbl = $dbl_ref;
        
    }
        
    /*
	 * SendSinglePing
	 *   - PingRecipient: [ Status 7 = PROCESSING ] (Status 2 = SENT) (Status 3 = AFFIRMATIVE ) (Status 4 = NEGATIVE ) (Status 5 = OTHER ) (status 0 = UNSENT)
	 */
	public function SendSinglePing($ping_request_id){
		
		//Access the global variable $Twilio_Control
		global $Twilio_Control;
		
		//Get event data
		$ping_request_info = $this->dbl->RowReturn("SELECT * FROM `ping_requests` WHERE id='{$ping_request_id}'");
		$event_info = $this->dbl->RowReturn("SELECT * FROM `events` WHERE id='" . $ping_request_info['event_id'] . "'");
		
		//Get the next ping recipient for this request
		$next_recipient = $this->dbl->RowReturn("SELECT * FROM `ping_recipients` WHERE status='0' AND `request_id` = '{$ping_request_id}'");
		
		//Check if we have one
		if(!is_numeric($next_recipient['id'])){
		
			$this->last_error = "No more recipients for Request ID: #{$ping_request_id}";
			return false;
			
		}
		
		/*
		 * Set to status 7 in progress.
		 */
		$this->dbl->ExecuteQuery("UPDATE `ping_recipients` SET status='7', status_name='PROCESSING' WHERE id='" . $next_recipient['id'] . "'");
		
		/*
		 * Select personal info & phone number
		 */
		$personal_info = $this->dbl->RowReturn("SELECT * FROM `people` LEFT JOIN `people_phone_numbers` ON `people_phone_numbers`.`person_id` = `people`.`id`  WHERE `people`.`id`={$next_recipient['person_id']}");
		
		/*
		 * Formulate Message
		 */
		$standard_append = "To confirm availability, send reply: YES";
		
		$sms_content = $ping_request_info['message_body'] . "\n\n" . $standard_append;
		
		//DEBUG: Print to stdout the info on the send
		echo "<div class='debug'>[ " . $personal_info['phone_number'] . " / {$sms_content} ]</div><br/>";
		
		/*
		 * Send the actual message with Twilio
		 */		
		$result = $Twilio_Control->sendSimpleSMS($personal_info['phone_number'], $sms_content);
		
		/*
	     * Reset status to 2
	     */
	    $this->dbl->ExecuteQuery("UPDATE `ping_recipients` SET status='2', status_name='WAITING' WHERE id='{$next_recipient['id']}'");
	    
	    //OK we are good!
	    return true;    
		
	}
	
	/*
	 * Send a ping request to all recipients
	 * 			Ping Requests: [ Status meanings: 0 = UNSENT | 2 = WAITING | 7 = PROCESSING | 9 = CLOSED ]
	 */
	public function FulfillPingRequest($ping_request_id){
	
		$open_ping = $this->dbl->RowReturn("SELECT * FROM `ping_requests` WHERE status='2'");
		
		//no open ping
		if($open_ping) return False;
	
	
		//Grab an active `ping request`
		$ping_request = $this->dbl->RowReturn("SELECT * FROM `ping_requests` WHERE id={$ping_request_id}");

		if(!is_numeric($ping_request['id'])){
	
			die("\nNot a valid PING Request ID\n");
	
		}

		//Make PingRequest in progress
		$this->dbl->ExecuteQuery("UPDATE `ping_requests` SET `status`='7', status_name='PROCESSING' WHERE id={$ping_request_id}");

		//Start philosophical thinking
		echo "<div class='debug'>Working on Ping Request #{$ping_request['id']}</div><br/>";

		//Run, until it can't run no more.
		$existentialism = true;

		//loop until...
		
		$count = 0;
		
		while($existentialism){
	
			//Send a single ping
			$thetruth = $this->SendSinglePing($ping_request_id);
	
			if(!$thetruth){
		
				//The fabric is falling apart
				$existentialism = false;
		
				//Existential Breakout
				break;
		
			}
	
			++$count;
	
			echo "<div class='debug'>Processed count: {$count}</div><br/>";
	
		}

		//Make PingRequest in progress
		$this->dbl->ExecuteQuery("UPDATE `ping_requests` SET `status`='2', status_name='SENT' WHERE id={$ping_request_id}");
		
		return "<div class='debug'>Ping processing is complete.</div><br/>";
	
	}
	
	/*
	 * Close a Ping Request to further responses 
	 *		  PingRequests: [ 0 = UNSENT | 2 = WAITING | 7 = PROCESSING | 9 = CLOSED ]
	 */
	 public function ClosePingRequest($ping_request_id) {
	 
	 	//Grab ping request to be closed
		$ping_request = $this->dbl->RowReturn("SELECT * FROM `ping_requests` WHERE id='{$ping_request_id}'");
		
		//Safety first
		if(!is_numeric($ping_request['id'])){
	
			die("<div class='debug'>{$ping_request_id} is Not a valid PING Request ID</div><br/>");
	
		}
	 
		//Make PingRequest status = closed
		$this->dbl->ExecuteQuery("UPDATE `ping_requests` SET `status`='9', status_name='CLOSED' WHERE id='{$ping_request_id}'");
		
		//anonym
		return True;
		
	}
	
	/* 
	 *  Convert all responses to logs 
	 * 
	 *  		(TODO: add checks to prevent double dipping – ensure only one ping open at a time. )
	 */
	public function ConvertRecipientsToLogs($ping_request_id){
	
		// Acces the global Messages object
	 	global $MultiPING_Messages;
		 
		// Scoop up all teh ping recipients
		$all_ping_recipients = $this->dbl->FullAssocReturn("SELECT * FROM `ping_recipients` WHERE request_id='{$ping_request_id}'");
		
		if(!$all_ping_recipients){
			
			echo "<div class='debug'>no logs were generated, becuse there were no recipients associated with this request.</div><br/>";
			return False;
			
		}
		
		// convert those babies.
		foreach($all_ping_recipients as $nothingmuch=>$recipient_info){
		
			if($recipient_info['status_name'] == "WAITING"){
				 $recipient_info['status_name'] = "Didn't Reply";
			}
			
			$dirty_required_fields = array(
				'person_id' => $recipient_info['person_id'],
				'ping_request_id' => $recipient_info['request_id'],
				'status' => $recipient_info['status'],
				'status_name' => $recipient_info['status_name']
			);

			//scrub-a-dub
			$ping_recipient_id = $this->dbl->Escape($recipient_info['id']);
			$clean_required_fields = $this->dbl->EscapeArray($dirty_required_fields);
			
		 	$test = $MultiPING_Messages->CreateRecipientLog($clean_required_fields, $ping_recipient_id);
		 	
		 	if(!$test){ 
		 	
		 		echo "<div class='debug'>Could not CreateRecipientLog for recipient #{$recipient_info['id']}</div><br/>";
		 		return False;
		 	
		 	}

			// Delete recipient
			$delete = $MultiPING_Messages->DeletePingRecipient($recipient_info['id']);
		 	
			if(!$delete){
		
				echo "<div class='debug'>>Could not delete ping_recipient #{$recipient_info['id']}</div><br/>";
				return False;

			}
			echo "<div class='debug'>Deleted recipient #{$recipient_info['id']}</div><br/>";

		}
		
			
		// Change all open text responses to closed
		$open_responses = $this->dbl->FullAssocReturn("SELECT * FROM `twilio_text_responses` WHERE status= '0'");
		
		if($open_responses){

			foreach($open_responses as $whocares=>$response_info){
		
				$this->dbl->ExecuteQuery("UPDATE `twilio_text_responses` SET status='9', status_name='ARCHIVED' WHERE id='{$response_info['id']}'");

			}
		
		}
	
		//anonymous
		return True;
	 
	 }
	 
	/* getLastError() */
	public function getLastError(){
	 
		//check error is not blank
		if($this->last_error == ""){
			 
			//if it is blank, return false (unable to return an error)
			return false;
			 
		}
		 
		//Return the error
		return $this->last_error;
		
	}
	 
	 
	
}

?>