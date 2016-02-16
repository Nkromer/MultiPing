<?php

	//catch the request id whether by GET or POST
	if( $_GET["ping_request_id"]){ $page_request = $_GET["ping_request_id"]; } elseif ( $_POST["ping_request_id"]){  $page_request = $_POST["ping_request_id"]; };
	
	if( $page_request ){
	
		$ping_request_id = $MySQL_Control->Escape($page_request);
		$ping_request_info = $MySQL_Control->RowReturn("SELECT * FROM `ping_requests` WHERE id='{$ping_request_id}'");		
		$ping_event_id = $ping_request_info['event_id'];
		$ping_event_info = $MySQL_Control->RowReturn("SELECT * FROM `events` WHERE id='{$ping_event_id}'");
		
		if($ping_request_info['status'] == 2){ 
			$ping_sent = True;
			$ping_open = True;
			$ping_closed = False;
		}elseif($ping_request_info['status'] == 9){
			$ping_closed = True;
			$ping_open = False;
		}
		
		$open_ping = $MySQL_Control->RowReturn("SELECT * FROM `ping_requests` WHERE status='2'");
		if(!$open_ping) $cleared_for_takeoff = True;
		
		// fetch the list of recipients
		$recipient_list_constraints = array("request_id"=>$ping_request_id);
		$recipients_list = $MultiPING_Messages->ListPingRecipients($recipient_list_constraints);
	
	};
	
?>

<div class="page_encapsulator">

<div class="box_tabular_content">
		
	<span class="bread_crumb">
		<a href='index.php'>Home</a>
		 &rarr; <a href='index.php?func=list_events'>List Events</a>
		 &rarr; <a href="index.php?func=display_event_info&event_id=<?php echo $ping_event_info[id]; ?>"><?php echo $ping_event_info[name]; ?></a>
	 	 &rarr; 
	</span>
	
	<br />		
	
	<h1 class="page_header">Ping request #<?php echo "{$ping_request_id}"; ?></h1>
	
	<?PHP
	
		// close button and alerts
		if($ping_sent) {
		
			echo "<p class='update_message'>This ping has been sent.</p>";
			
			//Freeze current recipient status in a log and enable "event closed" message.
			echo "<form method='post' action='index.php?func=display_ping_request&ping_request_id={$ping_request_id}'>
					<input type='hidden' name='ping_request_id' value='{$ping_request_id}' />
					<button class='button submit_button' name='ClosePingRequest' class='submit_button'>Close this ping for replies.</button>
				  </form>";
			
		} elseif($ping_closed) {
		
			echo "<p class='update_message'>This ping is closed.</p>";
			
		}
		
		// Send button
		if(!$ping_sent && $recipients_list){
			
			if($cleared_for_takeoff){
		
				//Send ping button
				echo "<form method='post' action='index.php?func=display_ping_request&ping_request_id={$ping_request_id}'>
						<input type='hidden' name='ping_request_id' value='{$ping_request_id}'>
						<button class='button big_button' type='submit' name='fullfillPingRequest'>Send this Ping to all recipients</button>
					</form>";
		
			} else {
		
				echo "You can't send this ping until you close your current ping: <br/>
					<a href='index.php?func=display_ping_request&ping_request_id={$open_ping['id']}'>
					{$open_ping['message_body']}
					</a>";
			}
		
		};
		
		
	?>
						
	<br/>
	<br/>
	
	<div id="events_container" class="table_container">
		
		<table class="data_table">
			
			<tr>
				<th>Ping ID</th>
				<th>Message</th>
				<!--<th>Time Sent</th>
				<th>Expires</th>-->
				<th>Status</th>
			</tr>
			
			<?php
					
				// display the message info
				echo "<tr>
						<td>{$ping_request_info['id']}</td>
						<td>{$ping_request_info['message_body']}</td>";
						//<td>" . date("m/d/Y H:i:s", $ping_request_info['time_initiated']) . "</td>
						//<td>" . date("m/d/Y H:i:s", $ping_request_info['time_closed']) . "</td>
				echo	"<td>{$ping_request_info['status']}</td>
					</tr>";
						
			?>
			
		</table>
		
		<div class="" id="response-list" onClick="startChecking()"><?php if($ping_open) echo 'No responses yet.'; ?></div>
		
		<?php
			
			if(!$ping_sent && !$ping_closed) {
				
				//add recipient button
				echo "<br/><br/><div class='button small_button'><a href='index.php?func=add_ping_recipient&ping_request_id={$ping_request_id}'>Add recipient</a></div>";
			
			}
		?>
		
		<br/>
		<br/>
		
		<table class="data_table">
		
			<?php if(!$ping_closed){
			
				echo "<span class='form_header'>Recipients</span>
					<br/>
					<br/>";
		
				if(!$recipients_list){
			
					echo "<br/>There are no recipients for this request.<br/>";
				
				} else {
				
					/* 
					 *  Display All ping_recipients as RECIPIENTS
					 */
					echo "<tr>
							<th>Id</th>
							<th>Person's ID</th>
							<th>First name</th>
							<th>Last name</th>
							<th>Status</th>";
					echo "</tr>";
			
					foreach($recipients_list as $count=>$recipient_info){
					
						$form_action = "index.php?func=display_ping_request&ping_request_id={$ping_request_id}";
				
						echo "<tr>
								<td>{$recipient_info['id']}</td>
								<td>{$recipient_info['recipient_id']}</td>
								<td>{$recipient_info['firstname']}</td>
								<td>{$recipient_info['lastname']}</td>
								<td>{$recipient_info['message_status_name']}</td>";
								if(!$ping_sent) {
									echo "<td>
											<form action='{$form_action}' method='post'>
												<input type='hidden' name='ping_recipient_id' value='{$recipient_info['id']}'>
												<button class='button remove_button' type='submit' name='deleteRecipient'>Remove</button>
											</form>
										</td>";
								}
						echo "</tr>";
						
					};
				};

			// if $ping_closed = True
			} else {
				
				/* 
				 *  Display PING LOGS as HISTORY
				 */
				echo "<span class='form_header'>History</span>
					<br/>
					<br/>";
			
				$recipient_logs_list = $MySQL_Control->FullAssocReturn("SELECT * FROM `ping_recipient_log` WHERE ping_request_id='{$ping_request_id}'");
		
				if(!$recipient_logs_list){
			
					echo "<br/>There are no recipients logged for this request.<br/>";
				
				} else {
				
					echo "<tr>
							<th>Id</th>
							<th>Person's ID</th>
							<th>First name</th>
							<th>Last name</th>
							<th>Status</th>";
					echo "</tr>";
			
					foreach($recipient_logs_list as $count=>$log_info){
					
						$person_info = $MySQL_Control->RowReturn("SELECT * FROM `people` WHERE id='{$log_info['person_id']}'");
				
						echo "<tr>
								<td>{$log_info['id']}</td>
								<td>{$log_info['person_id']}</td>
								<td>{$person_info['firstname']}</td>
								<td>{$person_info['lastname']}</td>
								<td>{$log_info['status_name']}</td>";
						echo "</tr>";
						
					};
				};
			
			}
			?>
			
			
		
		</table>
		
		<br/>
		<br/>
		
		
		
		<!-- Check for responses every couple seconds and load them onto the page. -->
		<script type="text/javascript">
		
			function checkForResponses() {
			
				 xmlhttp = new XMLHttpRequest();
				 
				 xmlhttp.onreadystatechange = function() {
				 
						if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						
							document.getElementById("response-list").innerHTML = xmlhttp.responseText;
							
						}

				};
				
				xmlhttp.open("GET", "/includes/ajax/getResponses.php", true);
				xmlhttp.send();
				
			}
			
			window.onload = function(){
			
				if(<?php echo $ping_open; ?>) {
				
					//check db every five seconds
					setInterval ( "checkForResponses()", 1000 );
						
				}
				
			}
				
		</script>

	</div>
	
</div>

</div>