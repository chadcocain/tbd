
	<div id="wrapper">
		<div id="container_left">
			<div id="contents">
<?php
if (isset($beer_reviews))
{
	$str = '
				<div class="review_front">
					<h2 class="keg brown">Recent Beer Reviews</h2>
					<p class="rightLink"><a href="' . base_url() . 'beer/review" class="brown">View All Reviews</a></p>
					<table class="table_reviewFront">
						<tr>
							<th>Beer</th>
							<th>Brewery</th>
							<th>Member</th>
							<th>Style</th>
							<th>Rating</th>
						</tr>
	';
				
	foreach ($beer_reviews as $item)
	{
		$str .= '
						<tr>
							<td><a class="green" href="' . base_url() . 'beer/review/' . $item['beerID'] . '">' . $item['beerName'] . '</a></td>
							<td><a class="lightblue" href="' . base_url() . 'brewery/info/' . $item['establishmentID'] . '">' . $item['name'] . '</a></td>
							<td><a class="mediumgray" href="' . base_url() . 'user/profile/' . $item['userID'] . '">' . $item['username'] . '</a></td>
							<td><a class="lightblue" href="' . base_url() . 'beer/style/' . $item['styleID'] . '">' . $item['style'] . '</a></td>
							<td class="green">' . number_format(round($item['rating'], 1), 1) . '/10</td>
						</tr>	
		';
	}

	$str .= '
					</table>
				</div>
	';

	echo $str;
}

if (isset($brewery_hop))
{
	$str = '
				<div id="breweryHop">			
	';
	
	foreach($brewery_hop as $hop) {
		$str .= '
					<img class="alignleft" src="' . base_url() . 'images/' . $hop['brewerypic'] . '" width="300" height="200" alt="' . $hop['name'] . '" />
					<div class="breweryHopText">
						<h2><a class="brown" href="' . base_url() . 'brewery/hop/' . $hop['id'] . '">' . $hop['name'] . '</a></h2>
						<p class="mediumgray">Author: ' . $hop['author'] . '</p>
						<p>' . $hop['shorttext'] . '</p>
						<p class="readMore"><a href="' . base_url() . 'brewery/hop/' . $hop['id'] . '">Read More</a></p>
					</div>
		';
	}
	
	$str .= '
					<br class="left" />
				</div>
	';

	echo $str;
}
?>
				<div id="blogPosts"></div>
			</div>
		</div>
		<div id="container_right">
			<div id="twitter_div" style="margin-bottom: 8px;">
                <!--<div style="margin: 0 auto; width: 170px;"><img src="<?php echo base_url(); ?>images/oldBeerDudes.jpg" /></div>-->
                <h3 id="twitterTitle" class="brown">Latest Tweets<!-- <a id="twitter-link" href="http://twitter.com/twobeerdudes">@twobeerdudes</a>--></h3>
                <!--<p id="twitter-link"><a href="http://twitter.com/twobeerdudes">follow @twobeerdudes on Twitter</a></p>-->
                <!--<ul id="twitter_update_list">-->
                    <a class="twitter-timeline" width="291" height="800" href="https://twitter.com/twobeerdudes" data-widget-id="344866213043781632">Tweets by @twobeerdudes</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

                <!--</ul>-->
            </div>
		
<?php
if(isset($season)) {
	$str = '
	           <div id="season">
		          <h3 class="brown ' . $season[0]['className'] . '">Seasonal Indicator</h3>
	';
	
	foreach ($season as $item)
    {
  		$str .= '
            		<ul>
            			<li class="bold">' . $item['season'] . ' (' . get_month_names($item['monthrange']) . ')</li>
            			<li>' . $item['beerstyles'] . '</li>
            		</ul>			
        ';
    }
	
	$str .= '
	           </div>
	';	
	
	echo $str;
}
?>
		    <div id="fests" class="marginTop_8">
                <!-- <p class="center"><img src="http://www.twobeerdudes.com/images/fests/greatLakes.gif" /></p>
                <p>Had a great time at Great Lakes Brew Fest.  Check out our <a href="http://www.twobeerdudes.com/page/gallery/1">pics</a>.</p>-->
                <h3 class="brown">Keep Up To Date</h3>
                <p class="marginTop_4">
                    <a href="http://www.facebook.com/twobeerdudes" target="_blank" rel="nofollow"><img src="<?php echo base_url(); ?>images/facebook.jpg" alt="two beer dudes facebook icon" /></a>
                    <a href="http://twitter.com/twobeerdudes" target="_blank" rel="nofollow"><img src="<?php echo base_url(); ?>images/twitter.jpg" alt="two beer dudes twitter icon" /></a>
                </p>
            </div>
        
		</div>
		<br class="both" />
	</div>
