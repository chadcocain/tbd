
	<div id="wrapper">
		<div id="container_left">
			<div id="contents">
<?php
if(isset($beerReviews)) {
	echo $beerReviews;
}

if(isset($breweryHop)) {
	echo $breweryHop;
}
?>
				<div id="blogPosts"></div>
			</div>
		</div>
		<div id="container_right">
			<div id="fests">
				<!-- <p class="center"><img src="http://www.twobeerdudes.com/images/fests/greatLakes.gif" /></p>
				<p>Had a great time at Great Lakes Brew Fest.  Check out our <a href="http://www.twobeerdudes.com/page/gallery/1">pics</a>.</p>-->
				<h3 class="brown">Keep Up To Date</h3>
				<p class="marginTop_4">
					<a href="http://www.facebook.com/twobeerdudes" target="_blank" rel="nofollow"><img src="<?php echo base_url(); ?>images/facebook.jpg" alt="two beer dudes facebook icon" /></a>
					<a href="http://twitter.com/twobeerdudes" target="_blank" rel="nofollow"><img src="<?php echo base_url(); ?>images/twitter.jpg" alt="two beer dudes twitter icon" /></a>
				</p>
			</div>
		
<?php
if(isset($season)) {
	echo $season;
}
?>
		
			<div id="twitter_div">
				<div style="margin: 0 auto; width: 170px;"><img src="<?php echo base_url(); ?>images/oldBeerDudes.jpg" /></div>
				<h3 id="twitterTitle" class="brown">Latest Tweets<!-- <a id="twitter-link" href="http://twitter.com/twobeerdudes">@twobeerdudes</a>--></h3>
				<!--<p id="twitter-link"><a href="http://twitter.com/twobeerdudes">follow @twobeerdudes on Twitter</a></p>-->
				<ul id="twitter_update_list"></ul>
			</div>
		</div>
		<br class="both" />
	</div>
