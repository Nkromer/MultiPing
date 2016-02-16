<?php

	//catch the request id whether by GET or POST
	if( $_GET["ping_request_id"]){
		
		$page_request = $_GET["ping_request_id"];
	
	} elseif ( $_POST["ping_request_id"]){ 
	
		$page_request = $_POST["ping_request_id"];
		
	};
	
	if( $page_request ){
	
		$ping_request_id = $MySQL_Control->Escape($page_request);
		
		$ping_request_info = $MySQL_Control->RowReturn("SELECT * FROM `ping_requests` WHERE id='{$ping_request_id}'");
		
		$ping_event_id = $ping_request_info['event_id'];
		
		$ping_event_info = $MySQL_Control->RowReturn("SELECT * FROM `events` WHERE id='{$ping_event_id}'");
		
	} else {
	
		echo "<span class='debug'>no page request</span>";
	
	};
	
?>

<div class="page_encapsulator">

<div class="box_tabular_content">

	<span class="bread_crumb">
		<a href='index.php'>Home</a>
		  &rarr; <a href='index.php?func=list_events'>List Events</a>
		  &rarr; <a href="index.php?func=display_event_info&event_id=<?php echo $ping_event_info[id]; ?>"><?php echo $ping_event_info[name]; ?></a>
		  &rarr; <a href="<?php echo "index.php?func=display_ping_request&ping_request_id={$ping_request_id}"; ?>"><?php echo "ping request #{$ping_request_id}"; ?></a>
		  &rarr; 
	</span>
	
	<br/>	
	
	<h1 class="page_header">Add a recipient to ping request #<?php echo "{$ping_request_id}"; ?></h1>
	
	<br/>
	
	<?PHP
		//display the error if any
		if(isset($form_error) && $form_error) {
			echo "<p style='color: red;'>*" . htmlspecialchars($form_error) . "</p>";
		}
	?>
	
	<br />
		
	<div id="events_container" class="table_container">
		
		<form method="post" action="<?php echo htmlspecialchars('index.php?func=add_ping_recipient'); ?>">
			
			<table class="data_table">
			
				<?php
		
					// fetch the list of people			
					$people_list = $MultiPING_People->ListPeople();
		
					if(!$people_list){
			
						echo "<br/>There are no people in your database.<br/>";
				
					} else {
				
						echo "<tr>
								<th>√</th>
								<th>Id</th>
								<th>First name</th>
								<th>Last name</th>
								<th>Notes</th>
							</tr>";
			
						foreach($people_list as $count=>$person_info){
							
							echo "<tr>
									<td><input type='checkbox' name='people_to_add[]' value='{$person_info['id']}'></td>
									<td>{$person_info['id']}</td>
									<td>{$person_info['people_firstname']}</td>
									<td>{$person_info['people_lastname']}</td>
									<td>{$person_info['people_notes']}</td>
								</tr>";
						
						};
					};

				?>
			
			</table>
			
			<input type="hidden" name="ping_request_id" value="<?php echo $ping_request_id; ?>" />
			
			<button class='button submit_button' type="submit" name="addRecipients">Add Recipients</button>
			
		</form>

		
	</div>
	
</div>

</div>