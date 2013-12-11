
	<div id="footer">
		<div id="footerContainer">
			<div class="footer_links">
				<h4>Beer</h4>
				<ul>
					<li><a href="<?php echo base_url(); ?>beer/review">Beer Reviews</a></li>
					<li><a href="<?php echo base_url(); ?>beer/style">Beer Styles</a></li>
					<li><a href="<?php echo base_url(); ?>beer/srm">Beer Colors</a></li>
					<li><a href="<?php echo base_url(); ?>beer/ratingSystem">Beer Rating System</a></li>
					<li><a href="#">Beer U</a></li>
					<li><a href="<?php echo base_url(); ?>beer/ratings">Highest Rated Beers</a></li>
				</ul>
			</div>
			
			<div class="footer_links">
				<h4>Beer Places</h4>
				<ul>
					<li><a href="<?php echo base_url(); ?>brewery/info">Establishments</a></li>
					<li><a href="#">Establishment Rating System</a></li>
					<li><a href="<?php echo base_url(); ?>brewery/hop">Brewery Hops</a></li>					
					<li><a href="<?php echo base_url(); ?>brewery/addEstablishment">Add A Place</a></li>
				</ul>
			</div>
			
			<div class="footer_links">
				<h4>Other</h4>
				<ul>
					<li><a href="http://blog.twobeerdudes.com">Sips Blog</a></li>
					<li><a href="<?php echo base_url(); ?>page/aboutUs">About Us</a></li>
					<li><a href="<?php echo base_url(); ?>page/contactUs">Contact Us</a></li>
					<li><a href="<?php echo base_url(); ?>">Home</a></li>					
				</ul>
			</div>
			
			<br class="left" />
<?php
if(isset($quote)) {	
	echo '
			<p class="quote"><span>&quot;' . $quote['quote'] . '&quot;</span> - ' . $quote['person'] . '</p>
	';
}
// get the date range for the copyright
$copyright = START_YEAR;
if(date('Y') > START_YEAR) {
	$copyright .= ' - ' . date('Y');
}
echo '
			<p class="footer_small">&copy; ' . $copyright . ' twobeerdudes.com All rights reserved</p>
';
?>				
		</div>
	</div>
</div>

<?php
$controller = $this->uri->segment(1) == false ? 'page' : $this->uri->segment(1);
$method = $this->uri->segment(2) == false ? 'index' : $this->uri->segment(2);
if($controller == 'page' && $method == 'index') {
?>
<script type="text/javascript" src="http://twitter.com/javascripts/blogger.js"></script>
<!--<script type="text/javascript" src="http://twitter.com/statuses/user_timeline/twobeerdudes.json?callback=twitterCallback2&amp;count=10"></script>-->
<script type="text/javascript" src="https://api.twitter.com/1/statuses/user_timeline.json?screen_name=twobeerdudes&amp;count=10"></script>
<?php
}
?>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-737507-2");
pageTracker._trackPageview();
} catch(err) {}
</script>

</body>
</html>