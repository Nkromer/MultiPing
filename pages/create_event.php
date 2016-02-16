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
			
<!--		Begins at: <input type="datetime-local" name="time_begin" value="<?PHP if(isset($_POST['time_begin'])) echo htmlspecialchars($_POST['time_begin']); ?>">
			<br>
			
			Ends at: <input type="datetime-local" name="time_end" value="<?PHP if(isset($_POST['time_end'])) echo htmlspecialchars($_POST['time_end']); ?>">
			<br/>
			
			Location: <input type="text" name="location" value="<?PHP if(isset($_POST['location'])) echo htmlspecialchars($_POST['location']); ?>">
			<br/>
			
			Manager: <input type="text" name="manager" value="<?PHP if(isset($_POST['manager'])) echo htmlspecialchars($_POST['manager']); ?>"><br/><br/>
			Venue: <input type="text" name="venue" value="<?PHP if(isset($_POST['venue'])) echo htmlspecialchars($_POST['venue']); ?>"><br/><br>
			notes: <input type="text" name="notes"  value="<?PHP if(isset($_POST['notes'])) echo htmlspecialchars($_POST['notes']); ?>"><br/><br>
-->			
			<br/><br/>
			
			<button class='button submit_button' type="submit" name="CreateNewEvent">Create Event</button>
		</form>

		
	</div>
	
</div>

</div>