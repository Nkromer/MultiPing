<?php

/*
 * MultiPING 
 *  - Prototype V1
 *
 *   PURPOSE: Define the "People" aspect of MultiPING
 *
 * Created: 05/12/2015
 * Made by: NJR, JMD
 *
 */
 
 class MultiPing_People {
	 
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
	  * List `People` from DB
	  */
	 public function ListPeople($constraints=""){
		 
		 //Field selection (SQL Code)
		 $sql_field_selection = "`people`.`id` AS `id`, `people`.`firstname` AS `people_firstname`, `people`.`lastname` AS `people_lastname`, `people`.`email` AS `people_email`, `people`.`specialty` AS `people_specialty`, `people`.`priority` AS `people_priority`, `people`.`notes` AS `people_notes`, `people_phone_numbers`.`phone_number` AS `phone_number`, `people_phone_numbers`.`primary` AS `phone_primary`";
		 
		 //Default query
		 $default_query = "SELECT {$sql_field_selection} FROM `people` LEFT JOIN `people_phone_numbers` ON (`people`.`id` = `people_phone_numbers`.`person_id`)";
		 
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
			 
			 $this->last_error = "No results returned for `people` listing";
			 return false;
			 
		 }
		 
		 //Dump back the list returned
		 return $list_returned;
		 
	 }
	 
	 /* 
	  * Create a `People` Entry
	  */
	 public function CreatePerson($new_record_info=""){
		 
		 //Array of the possible fields (True = required, false = not required)
		 $array_expected_fields = array(
			'firstname' => true,
			'lastname' => true,
			'email' => true,
			'specialty' => "None",
			'priority' => 9,
			'notes' => ""
		 );
		 
		 //Define final arary
		 $final_array = array();
		 
		 //Iterate the array of required variables
		 foreach($array_expected_fields as $field_name=>$dvalue){
			 
			 /*
			  * Determine if this is a required field
			  */
			 if($dvalue === true && $new_record_info[$field_name] == ""){
				 
				 //This is required, but no value is present
				 $this->last_error = "Field `{$field_name}` is required but blank";
				 return false;
				 
			 }	
			 
			 /*
			  * Check if blank, and a default is required
			  */	 
			 if($new_record_info[$field_name] == ""){
				
				//Assign the default value
				$new_record_info[$field_name] = $dvalue;
				 
			 }
			 
			 //Add to final array
			 $final_array[$field_name] = $new_record_info[$field_name];
			 
		 }
		 
		 /*
		  * Craete insertion query
		  */
		 $insert_people_query = $this->dbl->CreateInsertQuery('people', $final_array, true);
        
         //Execute SQL QUery
         $this->dbl->ExecuteQuery($insert_people_query);
         
         //If numweic return the ID
         if( is_numeric( $this->dbl->InsertID() ) ){
	         
	         //Give back the number
	         return $this->dbl->InsertID();
	         
         }else{
	         
	         //Return false
	         $this->last_error = "No Insert ID Provided by DB, Insertion Failed";
	         return false;
	         
         }
		 
	 }
	 
	 public function ValidateNewPersonForm($raw_post_data) {
	 
		 //pull apart the post request
		extract($raw_post_data);
		
		//ensure nothing is null
		if(!isset($firstname, $lastname, $phone_number)) return;
		
		//check that required data evals to true, else form error warnings shall appear
		if(!$firstname) {
			return "Please enter the person's first name.";
		}
		if(!$lastname) {
			return "Please enter the person's last name.";
		}
		if(!$phone_number) {
			return "Please enter the person's phone number.";
		}
		
		//not required, set to empty if false
		if(!$priority) $priority = "";
		if(!$specialty) $venue = "";
		if(!$notes) $notes = "";
		if(!$email) $email = "none";
				
		//Create the array
		$dirty_arr = array(
			'firstname' => $firstname,
			'lastname' => $lastname,
			'email' => $email,
		);
		
		//Clean the array
		$clean_arr = $this->dbl->EscapeArray($dirty_arr);
		
		//Insert to DB
		$person_id = $this->CreatePerson($clean_arr);
		
		//Create phone number
		$phone_number_id = $this->CreateLinkedPhoneNumber($person_id, $phone_number);
		
		//did it work?
		if(!is_numeric($person_id)) return "failed to create person";
		if(!is_numeric($phone_number_id)) return "Phone number failed to link.";

		//redirect
		header("Location: index.php?func=list_people");		
	 	
	 }
	  
	 /* 
	  * Modify a `People` Entry
	  */
	 public function ModifyEntry($record_id, $values_array){
		 
		 //Grab the info on the person (valiate)
		 $person_info = $this->dbl->RowReturn("SELECT * FROM `people` WHERE id='{$record_id}' LIMIT 1");
		 
		 //Validate the person is existant
		 if(!is_numeric($person_info['id'])){
			 
			 $this->last_error = "No valid person specified";
			 return false;
			 
		 }
		 
		 //Crate update query
		 $update_query = $this->dbl->CreateUpdateQuery('people', "`id`='{$record_id}'", $values_array);
		 
		 //Execute query
		 $this->dbl->ExecuteQuery($update_query);
		 
		 //Presume its OK
		 return true;
		 
	 }
	  
	 /* 
	  * Delete `People` Entry
	  */
	 public function DeleteEntry($record_id){
		 
		 //Grab the info on the person (valiate)
		 $person_info = $this->dbl->RowReturn("SELECT * FROM `people` WHERE id='{$record_id}' LIMIT 1");
		 
		 //Validate the person is existant
		 if(!is_numeric($person_info['id'])){
			 
			 $this->last_error = "No valid person specified";
			 return false;
			 
		 }
		 
		 //Execute a delete query
		 $this->dbl->ExecuteQuery("DELETE FROM `people` WHERE id='{$record_id}'");
		 
		 //Presume OK
		 return true;
		 
	 }
	  
	 /* 
	  * Create a `Phone Number` Entry
	  */
	 public function CreateLinkedPhoneNumber($person_record_id, $phone_number, $primary_pi=1){
		 
		 //Define the values array
		 $insert_array = array(
			 'person_id' => $person_record_id,
			 'phone_number' => $phone_number,
			 'primary' => $primary_pi
		 );
		 
		 //Create the insert array
		 $insert_sql_query = $this->dbl->CreateInsertQuery('people_phone_numbers', $insert_array, true);
		 
		 //Execute this insert query
		 $this->dbl->ExecuteQuery($insert_sql_query);
		 
		 //Evaluate the returned ID
		 $returned_id = $this->dbl->InsertID();
		 
		 //Return false (OR ID on success)
		 if(is_numeric($returned_id)){
			 
			 //RETURN ID
			 return $returned_id;
			 
		 }else{
			 
			 //FALSE
			 $this->last_error = "MySQL Issue, could not insert phone number";
			 return false;
			 
		 }
		 
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