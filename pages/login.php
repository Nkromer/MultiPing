<?php

/*
 * LOGIN PAGE 
 */
 
//Hardcoded login credentials
$valid_username = "admin";
$valid_password = "password";

//Process the login 
if(isset($_POST['username'])){
	
	//Do the login
	if($_POST['username'] == $valid_username && $_POST['password'] == $valid_password){
		
		//Authenticate the session
		$_SESSION['authenticated'] = "auth_true";
		
		//Login is valid
		header("Location: index.php");
		
		echo "<a href='index.php'>Click here to continue ... </a>";
		
	}else{		
		
		//Login is invalid
		header("Location: index.php?failed=1");
		
		//Login is failed.
		echo "<a href='index.php'>Click here to continue ... </a>";
		
	}
	
	
}else{

?>

<html>
	
<head>
	
	<title>MultiPING Prototype</title>
		
	<meta http-equiv="cache-control" content="max-age=0" />
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
	<meta http-equiv="pragma" content="no-cache" />
		
	<link rel="stylesheet" href="css/multiping.css">
	<link rel="stylesheet" href="css/normalize.css">
		
</head>
	
<body>

<div class="page_encapsulator">

<form action="index.php" method="post">
	
<div class="box_form_submission">
		
	<span class="page_header">Login</span><br />
	
		<span class='input_label'>Username:</span><br />
		<input type="text" class="input_textbox" id="username" name="username" /> 
	<br/>

		<span class='input_label'>Password:</span><br />
		<input type="password" class="input_textbox" id="password" name="password" /> 

	
	<?php
		if(isset($_GET['failed'])){
			echo "<br /><hr /><span style='color: red;'>Your login is invalid.</span><br />";
		}	
	?>
		
	<div class="form_field" style="text-align: right;">
		<input type="submit" id="submit1" name="submit1" class='button submit_button' value="Login" />
	</div>

</div>

</form>

</div>

</body>

</html>

<?php } //Done with the ELSE on the login. ?>