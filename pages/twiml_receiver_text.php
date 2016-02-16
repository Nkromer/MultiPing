<?php

/*
 * Grab the REQUEST Data 
 */
 
if(!isset($_REQUEST['Body']) || !isset($_REQUEST['AccountSid'])){
	
	die("Not a valid POST prep.");
	
}

//Lookup the person
$person_phone = $MySQL_Control->Escape($_REQUEST['From']);

//Get link info
$person_phone_link = $MySQL_Control->RowReturn("SELECT * FROM `people_phone_numbers` WHERE `phone_number` LIKE '%" . $person_phone . "'");

//Get persons info
$person_info = $MySQL_Control->RowReturn("SELECT * FROM `people` WHERE `people`.`id` = '" . $person_phone_link['person_id'] . "'");
 
//Prepare insertion array
$insert_record_array = array(
	'time_received' => time(),
	'person_id' => $person_info['id'],
	'MessageSid' => $_REQUEST['MessageSid'],
	'SmsSid' => $_REQUEST['SmsSid'],
	'AccountSid' => $_REQUEST['AccountSid'],
	'MessagingServiceSid' => $_REQUEST['MessagingServiceSid'],
	'From' => $_REQUEST['From'],
	'To' => $_REQUEST['To'],
	'Body' => $_REQUEST['Body'],
	'NumMedia' => $_REQUEST['NumMedia'],
	'FromCity' => $_REQUEST['FromCity'],
	'FromState' => $_REQUEST['FromState'],
	'FromCountry' => $_REQUEST['FromCountry'],
	'FromZip' => $_REQUEST['FromZip'],
	'ToCity' => $_REQUEST['ToCity'],
	'ToState' => $_REQUEST['ToState'],
	'ToZip' => $_REQUEST['ToZip'],
	'ToCountry' => $_REQUEST['ToCountry']
);

//Prepare insertion query
$insert_query = $MySQL_Control->CreateInsertQuery("twilio_text_responses", $insert_record_array, true);

//Insert
$MySQL_Control->ExecuteQuery($insert_query);

	
/*
 *  Update ping recipient status 
 *     [ Status 7 = PROCESSING ] (Status 2 = SENT) (Status 3 = AFFIRMATIVE ) (Status 4 = NEGATIVE ) (Status 5 = OTHER ) (status 0 = UNSENT)
 */
 
if(strtolower($_REQUEST['Body']) == "yes"){

	$MySQL_Control->ExecuteQuery("UPDATE `ping_recipients` SET status='3', status_name='AFFIRMATIVE' WHERE person_id='" . $person_info['id'] . "'");
	
	$message = "Thanks for your response {$person_info['firstname']}. We'll let you know soon if you get the job.";

} elseif (strtolower($_REQUEST['Body']) == "no") {

	$MySQL_Control->ExecuteQuery("UPDATE `ping_recipients` SET status='4', status_name='NEGATIVE' WHERE person_id='" . $person_info['id'] . "'");

	$message = "Thanks, {$person_info['firstname']}, for letting us know. Maybe next time!";


} else {
		
	$MySQL_Control->ExecuteQuery("UPDATE `ping_recipients` SET status='5', status_name='OTHER' WHERE person_id='" . $person_info['id'] . "'");

	$message = "Thanks for your response {$person_info['firstname']}, we'll be in touch.";

} 


// output the counter response
echo "<?xml version='1.0' encoding='UTF-8' ?>
<Response>
	<Message>{$message}</Message>
</Response>";
?>