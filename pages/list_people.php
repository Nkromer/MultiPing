<div class="page_encapsulator">

<div class="box_tabular_content">

	<span class="bread_crumb"><a href='index.php'>Home</a> &rarr; </span>
	
	<br />
			
	<h1 class="page_header">List People</h1>
	
	<br />
	
	<button class='button small_button' onclick="window.location='index.php?func=create_person';">Create Person</button>
	
	<br />
	
	<?PHP
		if(isset($update_message) && $update_message) {
			echo "<p class='update_message'>" . htmlspecialchars($update_message) . "</p>";
		}
	?>
	
	<br />
		
	<div id="events_container" class="table_container">
		
		<table class="data_table">
			
			<tr>
				<th>id</th>
				<th>firstname</th>
				<th>lastname</th>
				<th>email</th>
				<th>Phone number</th>
				<th>notes</th>
			</tr>
			
			<?php
			
				$people_list = $MultiPING_People->ListPeople();

				foreach($people_list as $irrelevant_id=>$person_info){
		
					
					echo "<tr>
							<td>{$person_info['id']}</td>
							<td><a href='index.php?func=display_person_info&person_id={$person_info['id']}'>{$person_info['people_firstname']}</a></td>
							<td>{$person_info['people_lastname']}</td>
							<td>{$person_info['phone_email']}</td>
							<td>{$person_info['phone_number']}</td>
							<td>{$person_info['people_notes']}</td>
						</tr>";
					
				};
								
			?>
			
		</table>
		
	</div>
	
</div>

</div>