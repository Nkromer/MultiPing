<html>
<head></head>
<body>
<?php 
	
//Includes - config
include("../config/config.inc.php");

//Master generic class
include("../classes/vyda.generic.class.php");
 
//Includes - classes
include("../classes/vyda.mysql.php");
	
//Instantiate the MySQL
$MySQL_Control = new VYDA_MySQL($wpi_db_host, $wpi_db_name, $wpi_db_user, $wpi_db_pass);

	/* 
	 *  Display All twilio_text_responses as RESPONSES
	 */
	echo "<span class='form_header'>Messages</span>
		<br/>
		<br/>";
	
	//List of all responses
	$sql_field_selection = "`twilio_text_responses`.`id` AS `id`, `twilio_text_responses`.`Body` AS `Body`, `twilio_text_responses`.`From` AS `From`, `people`.`firstname` AS `person_firstname`, `people`.`lastname` AS `person_lastname`";
	$response_list = $MySQL_Control->FullAssocReturn("SELECT {$sql_field_selection} FROM `twilio_text_responses` LEFT JOIN `people` ON (`twilio_text_responses`.`person_id` = `people`.`id`) WHERE status='0';");
	
	if($response_list){
	
		echo "<table class='data_table'>
				<tr>
					<th>id</th>
					<th>body</th>
					<th>person</th>
					<th>Phone number</th>
				</tr>";
	
		foreach($response_list as $random=>$response){
		
			echo "<tr>
					<td>{$response['id']}</td>
					<td>{$response['Body']}</td>
					<td>{$response['person_firstname']} {$response['person_lastname']}</td>
					<td>{$response['From']}</td>
				</tr>";
			
		}
	
		echo "</table>";
		
	}else{ 
	
		echo "no responses yet."; 
		
	}
	
?>
</body>
</html>