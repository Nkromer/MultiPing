<div class="page_encapsulator">

<div class="box_tabular_content">

	<span class="bread_crumb">
	<a href='index.php'>Home</a>
	 &rarr; <a href='index.php?func=list_people'>List People</a> 
	</span>
	
	<br />
		
	<h1 class="page_header">Create Person</h1>
	
	<br />
	
	<?PHP
		if(isset($form_error) && $form_error) {
			echo "<p style='color: red;'>*" . htmlspecialchars($form_error) . "</p>";
		}
	?>
	
	<br />
		
	<div id="events_container" class="table_container">
		
		<form method="post" action="<?php echo htmlspecialchars('index.php?func=create_person');?>">
		
			First name: <input type="text" name="firstname" value="<?PHP if(isset($_POST['firstname'])) echo htmlspecialchars($_POST['firstname']); ?>">
			
			<br/><br>
			
			Last name: <input type="text" name="lastname" value="<?PHP if(isset($_POST['lastname'])) echo htmlspecialchars($_POST['lastname']); ?>">
			
			<br/><br>
			
			Phone number (format +15145556657): <input type="text" name="phone_number" value="<?PHP if(isset($_POST['phone_number'])) echo htmlspecialchars($_POST['phone_number']); ?>">*
			
			<br/><br/>
			
			<button class='button submit_button' type="submit" name="CreateNewPerson">Create Person</button>
			
		</form>

		
	</div>
	
</div>

</div>