
	<div id="wrapper">
		<div id="container_left">
			<div id="contents">
<?php
if(isset($beerReviews)) {
	echo $beerReviews;
}
?>
			</div>
		</div>
		<div id="container_right">
			<div class="sideInfo">
				<h4><span>Beer Review Thoughts</span></h4>
				<p>Have fun with your reviews, let out your personality while keeping it clean for all members and visitors alike.</p>
				
				<h4><span>More Beer Information</span></h4>
				<ul>
					<li><a href="<?php echo base_url(); ?>beer/style">Beer Styles</a></li>
					<li><a href="<?php echo base_url(); ?>beer/srm">Beer Colors</a></li>
					<li><a href="<?php echo base_url(); ?>beer/ratingSystem">Beer Rating System</a></li>
				</ul>
			</div>
		</div>
		<br class="both" />
	</div>
