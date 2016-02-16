<div class="home_page_encapsulator">

	
	<div class="box_tabular_content">
					
		<h1 class="logo">MultiPing</h1><span style="font-style: italic;">     Build teams fast.</span>
		
		<hr />
	
		<span class="form_header">Events</span>
	
		<br />
	
		<div class="button small_button"><a href='index.php?func=list_events'>All Events</a></div>
		<div class="button small_button"><a href='index.php?func=create_event'>Create Event</a></div>	
	
		<Br /><Br />
		
		<span class="form_header">People</span>
	
		<br />
	
		<div class="button small_button"><a href='index.php?func=list_people'>All People</a></div>
		<div class="button small_button"><a href='index.php?func=create_person'>Create Person</a></div>
	
	
		<Br /><Br />
		
		<span class="form_header">Open Ping</span>
		
		<br/>
		
		<?php 
			
			$request_list = $MySQL_Control->FullAssocReturn("SELECT * FROM `ping_requests` WHERE status='2'"); 
			
			if($request_list){
			
				echo "<table class='data_table'>";
				
				echo "<tr><th>Message</th></tr>";
				
				foreach($request_list as $irrelevant_id=>$request_info){
				
					echo "<tr><td><a href='index.php?func=display_ping_request&ping_request_id={$request_info['id']}'>{$request_info['message_body']}</a></td></tr>";
				
				}
				
				echo "</table>";
				
			}else{
			
				echo "no open ping yet";
				
			}
		
		?>
		
		<Br /><Br />
		
		<span class="form_header">Unsent Pings</span>
		
		<br/>
		
		<?php 
			
			$request_list = $MySQL_Control->FullAssocReturn("SELECT * FROM `ping_requests` WHERE status='0'"); 
			
			if($request_list){
			
				echo "<table class='data_table'>";
				
				echo "<tr><th>Message</th></tr>";
				
				foreach($request_list as $irrelevant_id=>$request_info){
				
					echo "<tr><td><a href='index.php?func=display_ping_request&ping_request_id={$request_info['id']}'>{$request_info['message_body']}</a></td></tr>";
				
				}
				
				echo "</table>";
				
			}else{
			
				echo "no unsent pings yet";
				
			}
		
		?>
		
		<Br /><Br />
		
		<span class="form_header">Closed Pings</span>
		
		<br/>
		
		<?php 
			
			$request_list = $MySQL_Control->FullAssocReturn("SELECT * FROM `ping_requests` WHERE status='9'"); 
			
			if($request_list){
			
				echo "<table class='data_table'>";
				
				echo "<tr><th>Message</th></tr>";
				
				foreach($request_list as $irrelevant_id=>$request_info){
				
					echo "<tr><td><a href='index.php?func=display_ping_request&ping_request_id={$request_info['id']}'>{$request_info['message_body']}</a></td></tr>";
				
				}
				
				echo "</table>";
				
			}else{
			
				echo "no closed pings";
				
			}
		
		?>
		
		<a href='logout.php' style="float: right;"><small>Logout</small></a>
		
		</br>
	
	</div>

</div>

<br />