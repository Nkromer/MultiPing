<?php

/*
 * MultiPING - Main Index File
 */

//Error reporting
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//Start PHPSESSID (Session)
session_start();

//TODO: fix file paths to current structure

//Includes - config
include("includes/config/config.inc.php");

//Master generic class
include("includes/classes/vyda.generic.class.php");
 
//Includes - classes
include("includes/classes/vyda.mysql.php");
include("includes/classes/vyda.webrequest.php");
include("includes/classes/vyda.twilio.php");

//Includes - MultiPING stuff
include("includes/classes/multiping.people.php");
include("includes/classes/multiping.events.php");
include("includes/classes/multiping.messages.php");
include("includes/classes/multiping.communication.php");

//Instantiate the MySQL
$MySQL_Control = new VYDA_MySQL($wpi_db_host, $wpi_db_name, $wpi_db_user, $wpi_db_pass);

//Instantiate the Twilio Object
$Twilio_Control = new VYDA_Twilio($MySQL_Control);

//Instantiate the MultiPING People Class
$MultiPING_People = new MultiPing_People($MySQL_Control);

//Instantiate the MultiPING Event Class
$MultiPING_Events = new MultiPING_Events($MySQL_Control);

//Instantiate the MultiPING Messages Class
$MultiPING_Messages = new MultiPING_Messages($MySQL_Control);

//Instantiate the MultiPING_Communication
$MultiPING_Communication = new MultiPING_Communication($MySQL_Control);

//Define the requested funcitonality
$get_func = $_GET['func'];

//Database test
$full_users = $MySQL_Control->FullAssocReturn("SELECT * FROM `people`");

//Array of files
$clean_func = preg_replace('/[^0-9A-Za-z\_]/s', '', $_GET['func']);

//Publicly accessible pages
$publicly_accessible = array(
	'twiml_receiver_text' => 'twiml_receiver_text.php',
	'twiml_receiver_voice' => 'twiml_receiver_voice.php'	
);

//Check if it's publicly accessible
if($publicly_accessible[$_GET['func']] != ""){
	
	/* THIS IS A PUBLICLY ACCESSIBLE PAGE */
	include("includes/pages/" . $publicly_accessible[$_GET['func']]);
	
}else{
		
	/* TOP SEKRET PAGE */
	if($_SESSION['authenticated'] == "auth_true"){
	
		//Header
		if(!isset($_GET['suppress_header'])){
			include("includes/extra/header.html");
		}
	
		//Func
		$file_func = "includes/pages/{$clean_func}.php";
				
		//Determine if the get func is a real file
		if(file_exists($file_func)){
			
			//Include the file
			include( $file_func );
			
		}else{
			
			//Dashboard by default
			include("includes/pages/dashboard.php");
			
		}
		
		//Footer
		if(!isset($_GET['suppress_header'])){
			include("includes/extra/footer.html");
		}
		
	}else{
		
		/* Needs Authentication */
		include("includes/pages/login.php");
		
	}
	
}

?>