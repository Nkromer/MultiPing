<div class="page_encapsulator">

<div class="box_tabular_content">

	<span class="bread_crumb">
		<a href='index.php'>Home</a>
		 &rarr; <a href='index.php?func=list_events'>List Events</a>
		 &rarr;
	</span>
	
	<br/>
	
	<h1 class="page_header">Create an Event</h1>
	
	<br />
	
	<?PHP
		if(isset($errorMsg) && $errorMsg) {
			echo "<p class='error_message' style='color: red;'>*" . htmlspecialchars($errorMsg) . "</p>";
		}
	?>
	
	<br />
		
	<div id="events_container" class="table_container">
		
		<form method="post" action="<?php echo htmlspecialchars('index.php?func=create_event');?>">
			Event name: <input type="text" name="name" value="<?PHP if(isset($_POST['name'])) echo htmlspecialchars($_POST['name']); ?>">
			<br>
			<br/><br/>
			
			<button class='button submit_button' type="submit" name="CreateNewEvent">Create Event</button>
		</form>

		
	</div>
	
</div>

</div>