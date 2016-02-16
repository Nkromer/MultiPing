<?php

	//catch the event id whether by GET or POST
	if( $_GET["event_id"]){
	
		$event_id = $MySQL_Control->Escape($_GET["event_id"]);
		
		$event_info = $MySQL_Control->RowReturn("SELECT * FROM `events` WHERE id='{$event_id}'");
		
	} elseif ($_POST['event_id']) {
	
		$event_id = $MySQL_Control->Escape($_POST["event_id"]);
		
		$event_info = $MySQL_Control->RowReturn("SELECT * FROM `events` WHERE id='{$event_id}'");
		
	} else { 
		echo "Event Id not found by get or post."; 
	};
	
?>

<div class="page_encapsulator">

<div class="box_tabular_content">

	<span class="bread_crumb">
		<a href='index.php'>Home</a>
		  &rarr; <a href='index.php?func=list_events'>List Events</a>
		  &rarr; <a href="<?php echo "index.php?func=display_event_info&event_id={$event_id}"; ?>"><?php echo "{$event_info[name]}"; ?></a>
		  &rarr; 
	</span>
	
	<br/>
			
	<h1 class="page_header">Create a Ping Request for <?php echo "{$event_info[name]}"; ?></h1>
	
	<br/>
	

	
	<?PHP
		//display the error if any
		if(isset($form_error) && $form_error) {
			echo "<p style='color: red;'>*" . htmlspecialchars($form_error) . "</p>";
		}
	?>
	
	<br />
		
	<div id="events_container" class="table_container">
		
		<form method="post" action="<?php echo htmlspecialchars('index.php?func=create_ping_request');?>">
			Message:<br/>
			
			 <textarea type="text" cols="60" rows="4" maxlength="120" name="message_body" value="<?PHP if(isset($_POST['message_body'])) echo htmlspecialchars($_POST['message_body']); ?>"></textarea>
			*
			<br/><br/>
			
			Valid for (hours): <input type="number" name="lifespan_in_hours" max="1000" value="<?PHP if(isset($_POST['lifespan_in_hours'])) echo htmlspecialchars($_POST['lifespan_in_hours']); ?>">
			*
			<br/><br/>
			
			<input type="hidden" name="event_id" id="hiddenField" value="<?PHP echo "{$event_id}"; ?>" />
			<?php echo "Event ID: {$event_id}"; ?>
			
			<br/><br/>
			
			<input type="submit" name="CreatePingRequest" value="Create Ping Request">
		</form>

		
	</div>
	
</div>

</div>