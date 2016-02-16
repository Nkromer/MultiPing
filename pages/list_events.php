<div class="page_encapsulator">

<div class="box_tabular_content">

	<span class="bread_crumb"><a href='index.php'>Home</a> &rarr; </span>
	
	<br />
			
	<h1 class="page_header">List Events</h1>
	
	<br />
	
	<button class='button small_button' onclick="window.location='index.php?func=create_event';">Create Event</button>
	
	<br />
	
	<?PHP
		if(isset($update_message) && $update_message) {
			echo "<p class='update_message'>" . htmlspecialchars($update_message) . "</p>";
		}
	?>
	
	<br />
		
	<div id="events_container" class="table_container">
		
		<table class="data_table">
		
			<?php
			
				$event_list = $MultiPING_Events->ListEvents();
				
				if($event_list) {
			
					echo "<tr>
							<th>Event ID</th>
							<th>Event Name</th>
						<!--<th>Time Begin</th>
							<th>Time End</th>
							<th>Location</th>
						    <th>Manager</th>
							<th>Venue</th>-->
							<th>Pings</th>
						</tr>";
			
				
					foreach($event_list as $irrelevant_id=>$event_info){
					
						$num_pings = $MySQL_Control->RowReturn("SELECT COUNT(*) AS 'thecount' FROM `ping_requests` RIGHT OUTER JOIN `ping_recipients` ON (`ping_recipients`.`request_id` = `ping_requests`.`id`) WHERE `ping_requests`.`event_id` = {$event_info['id']}");
					
						$num_pings = $num_pings['thecount'];
					
						echo "<tr>
								<td>{$event_info['id']}</td>
								<td><a href='index.php?func=display_event_info&event_id={$event_info['id']}'>{$event_info['name']}</a></td>
							<!--<td>" . date("m/d/Y H:i:s", $event_info['time_begin']) . "</td>
								<td>" . date("m/d/Y H:i:s", $event_info['time_end']) . "</td>
								<td>{$event_info['location']}</td>
								<!--<td>{$event_info['manager']}</td>
								<td>{$event_info['venue_name']}</td>-->
								<td>{$num_pings}</td>
								<td>
									<form action='index.php?func=list_events' method='post'>
										<input type='hidden' name='event_id' value='{$event_info['id']}'>
										<input type='hidden' name='event_name' value='{$event_info['name']}'>
										<button class='button remove_button' type='submit' name='deleteEvent'>Delete Event</button>
									</form>
								</td>
							</tr>";
					}
						
				} else {
				
					echo "There are currently no events.";
				
				}
								
			?>
			
		</table>
		
	</div>
	
</div>

</div>