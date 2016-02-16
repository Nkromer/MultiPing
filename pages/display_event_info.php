<?php
	if( $_GET["event_id"])
	
	$event_id = $MySQL_Control->Escape($_GET["event_id"]);
	
	$event_info = $MySQL_Control->RowReturn("SELECT * FROM `events` WHERE id='{$event_id}'");
		
		$num_pings = $MySQL_Control->RowReturn("SELECT COUNT(*) AS 'thecount' FROM `ping_requests` RIGHT OUTER JOIN `ping_recipients` ON (`ping_recipients`.`request_id` = `ping_requests`.`id`) WHERE `ping_requests`.`event_id` = {$event_info['id']}");
		
		$num_pings = $num_pings['thecount'];
		
?>

<div class="page_encapsulator">

<div class="box_tabular_content">
	
	<span class="bread_crumb">
		<a href='index.php'>Home</a>
		 &rarr; <a href='index.php?func=list_events'>List Events</a>
		 &rarr; 
	</span>
	
	<br />	
		
	<h1 class="page_header"><?php echo "{$event_info['name']}"; ?></h1>
		
		<br/>
		
		<div class="button small_button"><a href="<?php echo "index.php?func=create_ping_request&event_id={$event_id}"; ?>">
		Create Ping Request
		</a></div>
			
		<br/>
		
		<h2>Pings for this event</h2>
		
		<table class="data_table">
			
			<?php
				
				$ping_list = $MultiPING_Events->ListEventPings($event_id);
				
				if(!$ping_list){
				
				    echo "<p>This event has no pings yet.</p>";
				    
				}else{
				
					echo "<tr>
							<th>ID</th>
							<!--<th>Created</th>
							<th>Expires</th>-->
							<th>Status</th>
							<th>message</th>
					     </tr>";
				
					foreach($ping_list as $irrelevant_id=>$ping_info){

						echo "<tr>
								<td>{$ping_info['id']}</td>
								<!--<td>" . date("m/d/Y H:i:s", $ping_info['time_initiated']) . "</td>
								<td>" . date("m/d/Y H:i:s", $ping_info['time_closed']) . "</td>-->
								<td>{$ping_info['status_name']}</td>
								<td><a href='index.php?func=display_ping_request&ping_request_id={$ping_info['id']}'>{$ping_info['message_body']}</a></td>
							</tr>";
						
					}
					
				}
				
			?>
			
			
		</table>
		
	</div>
	
</div>

</div>