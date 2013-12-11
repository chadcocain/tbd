
	<div id="wrapper">
		<div id="container_left">
			<div id="contents">
<?php
if(isset($leftCol)) {
	echo $leftCol;
}
?>
			</div>
		</div>
		<div id="container_right">
			<div class="sideInfo">
				<?php echo $rightCol; ?>
			
				<h4><span>More Brewery Information</span></h4>
				<ul>
					<li><a href="<?php echo base_url(); ?>brewery/hop">Brewery Hops</a></li>
					<li><a href="<?php echo base_url(); ?>brewery/addEstablishment">Add Establishment</a></li>
				</ul>
			</div>
		</div>
		<br class="both" />
	</div>
	