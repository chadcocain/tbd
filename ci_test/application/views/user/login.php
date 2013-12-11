
	<div id="wrapper">
		<div id="container_left">
			<div id="contents">
				
				<h2 class="brown">Member Login</h2>
				<p class="marginTop_8">Login to your account using your email address and password you supplied while creating your account.</p>
<?php
// User helper contains method
echo createLoginForm(isset($error) ? $error : '');
?>
			
			</div>
		</div>
		<div id="container_right">
			<div class="sideInfo">

				h4><span>Not A Member?</span></h4>
				<ul>
					<li><a href="<?php echo base_url(); ?>user/createAccount">Create Account</a></li>
				</ul>	
				<h4><span>Agreements</span></h4>
				<ul>
					<li><a href="<?php echo base_url(); ?>page/agreement">User Agreement</a></li>
					<li><a href="<?php echo base_url(); ?>page/privacy">Terms and Conditions</a></li>
				</ul>

			</div>
		</div>
		<br class="both" />
	</div>
