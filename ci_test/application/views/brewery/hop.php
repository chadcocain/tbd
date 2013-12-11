
<?php
//echo $output;
?>


<div id="wrapper">
		<div id="container_left">
			<div id="contents">
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
