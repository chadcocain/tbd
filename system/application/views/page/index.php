<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="description" content="Two Beer Dudes is American craft beer site to discuss beer reviews, beer ratings, beer history, beer news, beer styles, and more." />
<meta name="keywords" content="beer, american craft beer, beer reviews, beer ratings, beer history, beer news, beer fun, beer styles, American crafter beer reviews ratings history news fun styles" />
<meta name="robots" content="index,follow" />
<meta name="verify-v1" content="1Oi5/exTiXnH0hm7Din6oT3jZYMlzKRNRFNcsVy7oKs=" />
<title>American Craft Beer Reviews and Ratings</title>
<script type="text/javascript" src="<?php echo base_url(); ?>js/prototype.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/reset.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/master.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/twitter.css" />
<script type="text/javascript">
/*<![CDATA[*/
window.onload = function() {
	var nm = 'webmaster';
	var dmn = 'twobeerdudes';
	var ext = 'com';
	var ml = nm + '@' + dmn + '.' + ext;
	$('email').update('<a href="mailto:' + ml + '">' + ml + '</a>');
	
	new Ajax.Request(
		'<?php echo base_url(); ?>ajax/blogRSS', {
			asynchronous: true, 
			evalScripts: true, 
			method: 'post', 
			onLoading: function() {showSpinner('blogPosts');}, 
			onComplete: function(response) {$('blogPosts').update(response.responseText);}
		}
	);
}

function showSpinner(id) {
	$(id).update('<img src="<?php echo base_url(); ?>images/spinner.gif" style="margin: 1.0em auto; display: block; width: 16px; height: 16px;" />');
}
/*]]>*/
</script>
</head>
<body>

<div id="container">
	<div id="page_header">Two Beer Dudes</div>
	
	<div id="container_left">
		<div id="contents" class="page">
			<h2>twobeerdudes.com</h2>
			<p class="subHeading">Chicago, IL</p>
			<p class="italics">&quot;Two regular dudes who happen to be huge fans of american craft beers, drinking and talking about beer.&quot;</p>
			<p>Full website launching October 2009.  Send us your thoughts and comments: <span id="email"></span>.</p>
			<h2 style="margin: 1.0em 0 0;">Brewery Hops</h2>
			<p>Brewery Hops are Two Beer Dudes excurions to breweries that we can visit.  We plan on visiting one a month.  Please forgive the lack of style on the page.</p>
			<ul style="margin-left: 2.0em; list-style-type: disc;">
				<?php echo $output; ?>
			</ul>
			<div id="blogPosts" style="margin-top: 1.0em;"></div>
			<h2 style="margin: 1.0em 0 0;">Curent Picture</h2>
			<div><img src="<?php echo base_url(); ?>images/_calibeer001.jpg" title="13 beers from california" alt="13 beers from california" /></div>
			
			<?php echo $displayData; ?>
		</div>
	</div>
	
	<div id="container_right">
		<div><p style="margin-bottom: 1.0em;">Check out the <a href="<?php echo base_url(); ?>page/index2">new design</a> of Two Beer Dudes as it progresses</p></div>
		<div id="fests">
			<p><img src="<?php echo base_url(); ?>images/fests/greatLakes.gif" /></p>
			<p>Had a great time at Great Lakes Brew Fest.  Check out our <a href="<?php echo base_url(); ?>page/gallery/1">pics</a>.</p>
		</div>
		<div id="twitter_div">
			<h2 class="sidebar-title">twobeerdudes on Twitter <span>- <a href="http://twitter.com/twobeerdudes" id="twitter-link">follow us</a></span></h2>
			<ul id="twitter_update_list"></ul>
		</div>
	</div>	
	
	<br class="both" />
	
	<div id="page_footer">
		<?php echo '<p class="quote"><span>&quot;' . $quote['quote'] . '&quot;</span> - ' . $quote['person'] . '</p>'; ?>
		<p>&copy; <?php echo date('Y'); ?> twobeerdudes.com All rights reserved</p>		
	</div>
</div>

<script type="text/javascript" src="http://twitter.com/javascripts/blogger.js"></script>
<script type="text/javascript" src="http://twitter.com/statuses/user_timeline/twobeerdudes.json?callback=twitterCallback2&amp;count=10"></script>

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