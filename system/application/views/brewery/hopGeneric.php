
	<div id="wrapper">
		<div id="container_left">
			<div id="contents">
				<div id="introContents">
					<h2 class="hop brown">Brewery Hops</h2>
					<p>Our brewery hops are here for all to experience breweries, beer establishments, and festivals from our point
					of view.  We will try to go somewhere once a month as our schedules allow.  Most of travels will be to the 
					midwest, centralizing around Chicagoland.</p>
				</div>
<?php
if(isset($breweryHop)) {
	echo $breweryHop;
}
?>
			</div>
		</div>
		<div id="container_right">
			<div class="sideInfo">
<?php
if(isset($hops)) {
	echo $hops;
}
?>
			</div>
		</div>
		<br class="both" />
	</div>
