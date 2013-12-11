<?php
$str_error = '';
if(!empty($errors)) {
	$str_error .= '<div id="loginError">';
	foreach($errors as $error) {
		$str_error .= '<p>' . $error . '</p>';
	}
	$str_error .= '</div>';
}

$str_user = '';
if(!empty($user)) {
	$str_user = $user;
}
?>
	<div id="login_container">	
		<h2 class="center">Two Beer Dudes Site Administration</h2>	
		<div id="loginForm">
			<div id="login_top"><!-- --></div>
			<?php echo $str_error; ?>
			<form action="<?php echo base_url(); ?>admin/handleLogin" method="post">
				<label for="user">Username</label>
				<input type="text" id="user" name="user" vaule="<?php echo $str_user; ?>" />
				
				<label for="pass">Password</label>	
				<input type="password" id="pass" name="pass" />				
				
				<input type="submit" id="submit" name="submit" value="Login" />
			</form>
			<div id="login_bottom"><!-- --></div>
		</div>
	</div>	