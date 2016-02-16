<?php

/*
 * MultiPING 
 *  [x] Prototype V1
 *
 *   PURPOSE: Define the "Event Management" aspect of MultiPING
 *
 * Created: 05/12/2015
 * Made by: NJR, JMD
 *
 */
 
 class MultiPING_Messages {
	 
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
	 * CreatePingRequest 
	 			[ Status meanings: 0 = UNSENT | 2 = WAITING | 7 = closed ]
	 */
	public function CreatePingRequest($entry_parameters){
		
		//REQUIRED FIELDS
		$required_fields = array(
			'message_body' => true,
			'time_initiated' => true,
			'time_closed' => true,
			'event_id' => true,
			'status' => true,
			'status_name' => true
		);
		
		//FINAL INSERT ARRAY
		$final_insert_array = array();
		
		//Loop through the passed entry parameters
		foreach($required_fields as $field_name=>$field_required){
			
			//Check if required && blank
			if($field_required && $entry_parameters[$field_name] == ""){
				
				//False
				$this->last_error = "Field `{$field_name}` is not present in entry_parameters[]";
				return false;
				
			}
			
			//Add to final_insert_array
			$final_insert_array[$field_name] = $entry_parameters[$field_name];
						
		}
		
		//CREATE INSERT QUERY
		$final_insert_query = $this->dbl->CreateInsertQuery("ping_requests", $final_insert_array);
		
		//EXECUTE QUERY
		$this->dbl->ExecuteQuery($final_insert_query);
		
		//CHECK INSERTED ID
		$final_insert_id = $this->dbl->InsertID();
		
		//RETURN RESULT
		if(is_numeric($final_insert_id)){
			
			return $final_insert_id;
			
		}else{
			
			$this->last_error = "Could not create ping request";
			return false;
			
		}
		
	}
	
	/*
	 * Validate new ping request post data
	 */
	public function ValidatePingRequest($raw_post_data){
	
		//pull apart the post request
		extract($raw_post_data);
		
		//ensure nothing is null
		if(!isset($lifespan_in_hours, $message_body, $event_id)){
			return "One or more of the required fields are null.";
		 };
		
		//check that required data evals to true, else error will appear on the form
		if(!$message_body) {
			return "Please enter a message";
		}
		if(!$lifespan_in_hours) {
			return "Please enter the lifespan of the request (hours)";
		}
		if(!$event_id) {
			return "There is a problem with the event id. return to the event detail page and try again.";
		}
		
		// duration of message validity 
		$time_initiated = time();
		$time_closed = time() + ($lifespan_in_hours * 3600);
		
		//Create the array
		$dirty_arr = array(
			'message_body' => $message_body,
			'time_initiated' => $time_initiated,
			'time_closed' => $time_closed,
			'event_id' => $event_id,
			'status' => 0,
			'status_name' => "UNSENT"
		);
		
		//Clean the array
		$clean_arr = $this->dbl->EscapeArray($dirty_arr);
		
		//Insert to DB
		$ping_request_id = $this->CreatePingRequest($clean_arr);

		//redirect the user
		header("Location: index.php?func=display_ping_request&ping_request_id={$ping_request_id}");
	
	}
	
	 
	/*
	 * ModifyPingRequest
	 */
	public function ModifyPingRequest($ping_request_id, $ping_parameters){
		
		//Verify the ping request is valid
		$ping_request_info = $this->dbl->RowReturn("SELECT * FROM `ping_requests` WHERE id='{$ping_request_id}'");
		
		//Check ID
		if(!is_numeric($ping_request_info['id'])){
			
			$this->last_error = "Invalid PingRequest Specified";
			return false;
			
		}
		
		//Create the update query
		$update_query = $this->dbl->CreateUpdateQuery("ping_requests", "id='{$ping_request_id}'", $ping_parameters);
		
		//Execute query
		$this->dbl->ExecuteQuery($update_query);
		
		//Return true
		return true;
		
	}
	 
	/*
	 * ListPingRequests( $constraints )
	 *   -- Takes a list of constraints 
	 */
	public function ListPingRequests($constraints=""){
		
		//Initial Select List Query
		$list_sql_query = "SELECT * FROM `ping_requests`";
		
		//Determine extra SQL constraints
		if(is_array($constraints)){
			
			//add the where
			$list_sql_query = "{$list_sql_query} WHERE";
			
			//loop the constraints
			foreach($constraints as $field_name=>$field_value){
				
				$list_sql_query = "{$list_sql_query} `{$field_name}`='{$field_value}' AND";
				
			}
			
			//drop off the " AND" (last 4)
			$list_sql_query = substr($list_sql_query, 0, strlen($list_sql_query) - 4);
			
		}
		
		//Grab list from the database
		$ping_requests = $this->dbl->FullAssocReturn($list_sql_query);
		
		//Return the associative array
		return $ping_requests;		
		
	}
	
	/*
	 * DeletePingRequest
	 */
	public function DeletePingRequest($ping_request_id){
		
		//Verify the ping request is valid
		$ping_request_info = $this->dbl->RowReturn("SELECT * FROM `ping_requests` WHERE id='{$ping_request_id}'");
		
		//Valid or Invalid?
		if(!is_numeric($ping_request_info['id'])){
			
			$this->last_error = "Invalid PingRequest ID Specified for DeletePingRequest()";
			return false;
			
		}
		
		//Delete it from the table
		$this->dbl->ExecuteQuery("DELETE FROM `ping_requests` WHERE id='{$ping_request_id}'");
		
		//Presume true
		return true;
		
	}
	
	/*
	 * CreatePingRecipient($ping_request_id, $recipient_info)
	 *   -- takes a ping request ID, and takes an array of recipient info
     */
    public function CreatePingRecipient($ping_request_id, $recipient_info){
	    
	    //Verify the ping request is valid
		$ping_request_info = $this->dbl->RowReturn("SELECT * FROM `ping_requests` WHERE id='{$ping_request_id}'");
		
		//Valid or Invalid?
		if(!is_numeric($ping_request_info['id'])){
			
			$this->last_error = "Invalid PingRequest ID Specified for CreatePingRequest()";
			return false;
			
		}
		
		//Check that the request doesn't already have this person as a recipient
		$recipient_id = $recipient_info['person_id'];
		
		$id_of_current_record = $this->dbl->RowReturn("SELECT * FROM `ping_recipients` WHERE person_id='{$recipient_id}' AND request_id='{$ping_request_id}'");
		
		if(isset($id_of_current_record)) {
		
			$this->last_error = "{$recipient_info['firstname']}  {$recipient_info['lastname']} is already on the list";
			return False;
		
		}
		
		//Required info for recipient
		$required_fields = array(
			'person_id' => true,
			'request_id' => true,
			'status' => true,
			'status_name' => true
		);
		
		//Final insert array
		$final_insert_array = array();
		
		//Loop the passed recipient_info[]
		foreach($required_fields as $field_name=>$field_required){
			
			//If required, cant be blank
			if($field_required && $recipient_info[$field_name] == ""){
				
				$this->last_error = "Required field `{$field_name}` is not present in CreatePingRecipient()";
				return false;
				
			}
			
			//Assign it to the final
			$final_insert_array[$field_name] = $recipient_info[$field_name];
			
		}
		
		//Execute Insertion Code
		$final_insert_query = $this->dbl->CreateInsertQuery("ping_recipients", $final_insert_array);
		
		$this->dbl->ExecuteQuery($final_insert_query);
		
		//Get returned ID
		$returned_id = $this->dbl->InsertID();
		
		//Check returned ID
		if(!is_numeric($returned_id)){
			
			$this->last_error = "Could not create ping_recipient";
			return false;
			
		}
		
		//Result
		return $returned_id;
	    
    }
    
    
    /*
     * Validate the 'add recipients' form, create the recipient, and redirect.
     */
     
    public function ValidateRecipients($raw_post_data){
    
    	// separate the postRequest into variables
    	extract($raw_post_data);
    	
    	if(!isset ( $ping_request_id)) return;
    	
		//person selected?
    	if(empty($people_to_add)){
    		return "You haven't selected any recipients.";
    	}
    	
    	if(!$ping_request_id){
    		return "There is no request associated with these recipients,
    						 go back to the request page and try again.";	
    	}
    	
    	//loop through selected people and Create recipient
    	foreach($people_to_add as $index=>$person_id){
    	
    		//make the insert array
			$dirty_arr = array(
				'person_id' => $person_id,
				'request_id' => $ping_request_id,
				'status' => 0,
				'status_name' => 'UNSENT'
			);
			
			//Escape the array
			$clean_recipient_info = $this->dbl->EscapeArray($dirty_arr);
			
			//Create the ping recipient record
			$resulting_id = $this->CreatePingRecipient($ping_request_id, $clean_recipient_info);
    		
    	}
    	
		//redirect the user
		header("Location: index.php?func=display_ping_request&ping_request_id={$ping_request_id}");
    
    }
    
    
    /*
	 * ModifyPingRecipient
	 */
	public function ModifyPingRecipient($ping_recipient_id, $recipient_info){
		
		//Verify the ping recipient is valid
		$ping_recipient_info = $this->dbl->RowReturn("SELECT * FROM `ping_recipients` WHERE id='{$ping_recipient_id}'");
		
		//Valid or Invalid?
		if(!is_numeric($ping_recipient_info['id'])){
			
			$this->last_error = "Invalid PingRecipient ID Specified for ModifyPingRecipient()";
			return false;
			
		}
		
		//Create update query
		$update_sql_query = $this->dbl->CreateUpdateQuery("ping_recipients", "id='{$ping_recipient_id}'", $recipient_info);
		
		//Execute update query
		$this->dbl->ExecuteQuery($update_sql_query);
		
		//Anonymously return true
		return true;
		
	}
	
	/*
	 * DeletePingRecipient
	 */
	public function DeletePingRecipient($ping_recipient_id){
		
		//Verify the ping recipient is valid
		$ping_recipient_info = $this->dbl->RowReturn("SELECT * FROM `ping_recipients` WHERE id='{$ping_recipient_id}'");
		
		//Valid or Invalid?
		if(!is_numeric($ping_recipient_info['id'])){
			
			$this->last_error = "Invalid PingRecipient ID Specified for DeletePingRecipient()";
			return false;
			
		}
		
		//Delete it
		$this->dbl->ExecuteQuery("DELETE FROM `ping_recipients` WHERE id='{$ping_recipient_id}'");
		
		//Anonymously return true
		return true;
		
	}
	
	/*
	 * ListPingRecipients
	 */
	public function ListPingRecipients($constraints=""){
	
		//make the infos logical, ignore unecessary fields	 
		$sql_field_selection = "`ping_recipients`.`id` AS `id`, `ping_recipients`.`person_id` AS `recipient_id`, `ping_recipients`.`request_id` AS `request_id`, `ping_recipients`.`status` AS `message_status_code`, `ping_recipients`.`status_name` AS `message_status_name`, `people`.`firstname` AS `firstname`, `people`.`lastname` AS `lastname`, `people`.`specialty` AS `specialty`, `people`.`priority` AS `priority`";
		 
		//define SQL query
		$sql_query = "SELECT {$sql_field_selection} FROM `ping_recipients` RIGHT OUTER JOIN `people` ON (`ping_recipients`.`person_id` = `people`.`id`) ";
		
		//make the where
		if(is_array($constraints)){
			
			//where clause
			$sql_query = "{$sql_query} WHERE";
			
			//loop through each constraint
			foreach($constraints as $field_name=>$value){
				
				$sql_query = "{$sql_query} `{$field_name}`='{$value}' AND";
				
			}
			
			//remove the last 4 " AND"
			$sql_query = substr($sql_query, 0, strlen($sql_query) - 4);
			
		}
		
		//Get the associative
		$associative_array = $this->dbl->FullAssocReturn($sql_query);
		
		//Return the associative
		return $associative_array;
		
	}
	
	/*
	 * Create a Ping Recipient log, with status and event ID.
	 *		+ Delete Recipient and text responses
	 */
	 
	 public function CreateRecipientLog($log_info, $ping_recipient_id){
	 
		//check the array
		$required_fields = array(
			'person_id' => True,
			'ping_request_id' => True,
			'status' => True,
			'status_name' => True
		);
		
		//Final insert array
		$final_insert_array = array();
		
		//Loop the passed recipient_info[]
		foreach($required_fields as $field_name=>$field_required){
			
			//If required, cant be blank
			if($field_required && $log_info[$field_name] == ""){
				
				echo "Required field `{$field_name}` is not present in CreateRecipientLog()";
				return false;
				
			}
			
			//Assign it to the final
			$final_insert_array[$field_name] = $log_info[$field_name];
			
		}
		
		//Execute Insertion Code
		$final_insert_query = $this->dbl->CreateInsertQuery("ping_recipient_log", $final_insert_array);
		
		$this->dbl->ExecuteQuery($final_insert_query);
		
		//Get returned ID
		$returned_id = $this->dbl->InsertID();
		
		//Check returned ID
		if(!is_numeric($returned_id)){
			
			echo "\nCould not create ping_recipient_log\n";
			return false;
			
		}
		
		//Result
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