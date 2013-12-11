
	<div id="wrapper">
		<div id="container_left">
			<div id="contents">
				<div id="introContents">
					<h2 class="hop brown">Establishments</h2>
					<p>Two Beer Dudes one stop for finding beer establishments throughout the United States.  This portion of the site
					will be largerly driven by the users of the site.  Most of our visits and experiences will be midwest centric.  Below
					is a list is a list of the United States:</p>
				</div>
<?php
if(isset($leftCol)) {
	echo $leftCol;
}
?>
			</div>
		</div>
		<div id="container_right">
			<div class="sideInfo">
				<?php if(isset($rightCol)) echo $rightCol; ?>
				
				<h4><span>More Establishment Information</span></h4>
				<ul>
					<li><a href="<?php echo base_url(); ?>brewery/hop">Brewery Hops</a></li>
					<li><a href="<?php echo base_url(); ?>brewery/addEstablishment">Add Establishment</a></li>
				</ul>
			</div>
		</div>
		<br class="both" />
	</div>
