<?php
// get the argument string
$args = trim($_SERVER['argv'][0]);
// check if it starts w/ a slash
if($args[0] == '/') { 
	$args = substr($args, 1);
}
// format the arguments
$args = str_replace('/', '.', $args);
?>

<h2><?php echo $heading; ?></h2>
<p>You need to be <a href="<?php echo base_url(); ?>user/login/<?php echo $args; ?>">logged in</a> to use this functionality.  If you are not a dude, <a href="<?php echo base_url(); ?>user/createAccount">create an account</a>.</p>

</body>
</html>