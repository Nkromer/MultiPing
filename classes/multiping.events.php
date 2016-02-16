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
 
 class MultiPing_Events {
	 
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
	 * Create Event
	 */
	public function CreateEvent($event_details){
		
		//Expected variables array
		$expected_variables = array(
			'name' => true,
			'time_begin' => false,
			'time_end' => false,
			'location' => false,
			'manager' => false,
			'notes'	=> false
		);
		
		//Iterate through the expected_variables
		foreach($expected_variables as $field_name=>$requried){
			
			if($event_details[$field_name] == "" && $required){
				
				$this->last_error = "Required field `{$field_name}` not present in event_details[]";
				return false;
				
			}
			
		}
		
		//Create the final insert query
		$event_insert_query = $this->dbl->CreateInsertQuery('events', $event_details, true);
		
		//Run the event query
		$this->dbl->ExecuteQuery($event_insert_query);
		
		//Get the inserted ID
		$inserted_id = $this->dbl->InsertID();
		
		//Validate the numeric insert_id
		if( !is_numeric( $inserted_id ) ){
			
			$this->last_error = "Issue inserting event into Database";
			return false;
			
		}else{
			
			// NO HABLO ESPANOL
			//C'est bon, retourner le numero ID
			return $this->dbl->InsertID();
			
		}
		
	}
	
	/*
	 * handle the POST data from the "create event" page and redirect
	 */
	function ValidateNewEventForm($arr){
		
		//pull apart the post request
		extract($arr);
		
		//ensure nothing is null
		if(!isset($name)) return;
		
		//check that required data evals to true, else form error warnings shall appear
		if(!$name) {
			return "Please enter the event name.";
		}
		/*
		if(!$time_begin) {
			return "Please enter the begin time of the event";
		}
		if(!$time_end) {
			return "Please enter the end time of the event.";
		}
		if(!$location) {
			return "Please enter the location of the event";
		}
		*/
		
		//not required, set to empty
		if(!$time_begin) $time_begin = "";
		if(!$time_end) $time_end = "";
		if(!$location) $location = "";
		if(!$manager) $manager = "";
		if(!$venue) $venue = "";
		if(!$notes) $notes = "";
		
		
		//Create the array
		$dirty_arr = array(
			'name' => $name,
			'time_begin' => $time_begin,
			'time_end' => $time_end,
			'location' => $location,
			'manager' => $manager,
			'notes'	=> $notes,
		);
		
		//Clean the array
		$clean_arr = $this->dbl->EscapeArray($dirty_arr);
		
		//Insert to DB
		$event_id = $this->CreateEvent($clean_arr);

		//redirect
		header("Location: index.php?func=display_event_info&event_id={$event_id}");
		
	}
	 
	/*
	 * ModifyEventDetails 
	 */
	public function ModifyEventDetails($event_id, $event_details){
		
		//Clean the event ID
		$event_id = $this->dbl->Escape($event_id);
		
		//Lookup the event
		$event_info = $this->dbl->RowReturn("SELECT * FROM `events` WHERE id='{$event_id}'");
		
		//Validate the array is OK
		if(!is_numeric($event_info['id'])){
			
			//BAD
			$this->last_error = "Event not found";
			return false;
			
		}
		
		//Create the update query
		$sql_update_query = $this->dbl->CreateUpdateQuery('events', "id='{$event_id}'", $event_details);
		
		//Execute the query
		$this->dbl->ExecuteQuery($sql_update_query);
		
		//Return true 
		return true;
		
	}
	 
	/*
     * DeleteEvent
     */
    public function DeleteEvent($event_id){
	    
	    //Clean the event ID
		$event_id = $this->dbl->Escape($event_id);
		
		//Lookup the event
		$event_info = $this->dbl->RowReturn("SELECT * FROM `events` WHERE id='{$event_id}'");
		
		//Validate the array is OK
		if(!is_numeric($event_info['id'])){
			
			//BAD
			$this->last_error = "Event not found";
			return false;
			
		}
		
		//Delete the event
		$this->dbl->ExecuteQuery("DELETE FROM `events` WHERE id='{$event_id}'");
		
		//Presume true
		return true;
	    
    }
    
    /*
	 * List Events
	 */
	public function ListEvents($constraints=""){
		
		 //Default query
		 $default_query = "SELECT * FROM `events`";
		 
		 //Specify a string of constraints
		 $constraint_string = "";
		 
		 //Make the constraints string
		 if( is_array( $constraints ) ){
			 
			 //Add the where constraint suffix
			 $default_query = "{$default_query} WHERE";
			 
			 //Loop through each constraint
			 foreach($constraints as $field_name=>$constrained_value){
				 
				 $default_query = "{$default_query} `{$field_name}` = '{$constrained_value}' AND";
				 
			 }
			 
			 //Post process the query string
			 $default_query = substr($default_query, 0, strlen($default_query) - 4);
			 
		 }
		 
		 //Run the query for the list
		 $list_returned = $this->dbl->FullAssocReturn($default_query);
		 
		 //Count the values and return false if none
		 if( count($list_returned) == 0 ){
			 
			 $this->last_error = "No results returned for `events` listing";
			 return false;
			 
		 }
		 
		 //Dump back the list returned
		 return $list_returned;
		
	}
	
	public function ListEventPings($event_id){
		
		//default query
		$default_query = "SELECT * FROM `ping_requests` WHERE `event_id` = '{$event_id}'";
		
		 //Run the query for the list
		 $list_returned = $this->dbl->FullAssocReturn($default_query);
		 
		 //Count the values and return false if none
		 if( count($list_returned) == 0 ){
			 
			 $this->last_error = "Event # '{$event_id}' has no pings.";
			 return false;
			 
		 }
		 
		 //Dump back the list returned
		 return $list_returned;
		 
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