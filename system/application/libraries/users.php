<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Users {
	private $ci;
	private $title = 'Users';

	public function __construct() {
		$this->ci =& get_instance();
	}
	
	public function showProfile($id) {	
		// get the user info - this will be empty if the user
		// isn't logged in.  Also need to make sure, for certain
		// functionality that the user and visitor are the same
		$userInfo = $this->ci->session->userdata('userInfo');
		
		// get the generic information for this user
		$userProfile = $this->ci->UserModel->getUserProfile($id);
		echo '<pre>'; print_r($userProfile); echo '</pre>'; 
		$lastActivity = determineTimeSinceLastActive($userProfile['secondsLastLogin']);
		
		echo '<pre>'; print_r($lastActivity); echo '</pre>'; return;
			
		// get ratings for this specific beer
		$beers = $this->ci->BeerModel->getBeerRatingsByID($id);
		// determine if the beer has been rated by rich and scot
		$twoBeerDudes = $this->ci->BeerModel->tastedTwoBeerDudes($id);
		//echo '<pre>'; print_r($beers); echo '</pre>'; exit;	
		// set up some holder values
		$ratingTotalTimes = 0;
		// check to see if there has at least one rating
		if(!empty($beers[0]['comments'])) {
			// get the rating totals to get an average		
			$ratingInfo = $this->ci->BeerModel->getBeerRating($id);
			// calculate the ratings average
			$ratingAverage =  number_format(round($ratingInfo['totalrating']/$ratingInfo['totaltimerated'], 1), 1);
			// the total number of times the beer has been rated
			$ratingTotalTimes = $ratingInfo['totaltimerated'];
			// get the types of packages the beer has come in
			$packageCount = $this->ci->BeerModel->getPackageCount($id);
			// get the avg cost per package of beer drank for the brewery
			$avgCost = $this->ci->BeerModel->getAvgCostPerPackage($id);
			// get the percentage of people who would have another
			$haveAnother = $this->ci->BeerModel->getHaveAnotherPercent($id);
		}

		//echo '<pre>'; print_r($avgCost); echo '</pre>'; exit;	
		
		// configuration for the image
		$image = array(
			'picture' => $beers[0]['picture']
			, 'id' => $beers[0]['id']
			, 'alt' => $beers[0]['beerName'] . ' - ' . $beers[0]['name']
		);
		// check if the image exists for this beer
		$img = checkForImage($image, false);
		
		// create the output for the screen
		// start w/ the brewery information
		$str_ratingTotalTimes = $ratingTotalTimes > 1 ? $ratingTotalTimes . ' dudes' : $ratingTotalTimes . ' dude';
		// check for a brewery hop
		$breweryHop = !empty($beers[0]['breweryhopsID']) ? ' <a href="' . base_url() . 'brewery/hop/' . $beers[0]['breweryhopsID'] . '">brewery hop</a>' : '';
		// check for ratings
		$str_dudes = '';
		if($ratingTotalTimes > 0) {
			 $str_dudes = $ratingAverage . ' by ' . $str_ratingTotalTimes;
		}
		
		// holder string
		$str_twodudes = '';
		// check to see if the dudes rated it
		if(!empty($twoBeerDudes)) {
			$str_twodudes = number_format($twoBeerDudes['avergeRating'], 1);
		} else {
			$str_twodudes = 'Not Rated';
		}
		
		
		$str = '
		<div id="contents_left">
			<div id="beerReview">' . $img . '
				<div id="beerInfo">
					<h2>' . $beers[0]['beerName'] . '</h2>
					<p>Overall: ' . $str_dudes . '</p>
					<p>Dudes: ' . $str_twodudes . '</p>
					<p>Vitals: ' . $beers[0]['style'] . ', ' . $beers[0]['alcoholContent'] . ' ABV</p>
					
					<ul>
						<li><a href="' . base_url() . 'brewery/info/' . $beers[0]['establishmentID'] . '">' . $beers[0]['name'] . '</a></li>
						<li>' . $beers[0]['address'] . '</li>
						<li>
							<a href="' . base_url() . 'brewery/city/' . $beers[0]['stateID'] . '/' . urlencode($beers[0]['city']) . '">' . $beers[0]['city'] . '</a>,
							<a href="' . base_url() . 'brewery/state/' . $beers[0]['stateID'] . '">' . $beers[0]['stateAbbr'] . '</a>
							' . $beers[0]['zip'] . '
						</li>
						<li>' . $breweryHop . '</li>
						<li><a href="' . $beers[0]['url'] . '" target="_blank">web site</a></li>
					</ul>
		';			
		// iterate through the different packages
		if(!empty($beers[0]['comments'])) {
			$str .= '<div id="packageinfo"><h3>Cost Breakdown</h3>';
			foreach($avgCost as $cost) {
				// there is a match so create the output
				$serving = $cost['totalServings'] > 1 || $cost['totalServings'] < 1 ? ' servings' : ' serving';
				$str .= '<p>$' . $cost['averagePrice'] . ', ' . $cost['totalServings'] . $serving . ', ' . $cost['package'] . 's</p>';
			}
			$str .= '</div>';
			
			$seasonal = $beers[0]['seasonal'] == 1 ? 'Yes' : 'No';
			$str .= $seasonal == 'No' ? '' : '<div id="seasonal"><p>Seasonal - ' . $beers[0]['seasonalPeriod'] . '</p></div>';
			
			$str .= '<div id="haveAnother">';
			foreach($haveAnother as $ha) {
				// get the percent
				$percentHaveAnother = ($ha['percentHaveAnother'] * 100);
				// determine which image to use
				$thumb = $percentHaveAnother > 50 ? 'yes' : 'no';
				// there is a match so create the output
				$str .= '<h3>Have Another</h3><p><img src="' . base_url() . 'images/haveanother_' . $thumb . '25.jpg" width="25" height="25" alt="" /> ' . $percentHaveAnother . '%</p>';
			}
			$str .= '</div>';
		}
		$str .= '	
					
					<br class="both" />
				</div>
		';
		
		// make sure we have ratings to show
		if(!empty($beers[0]['comments'])) {
			$cnt = 0;
			foreach($beers as $beer) {
				$haveAnother = $beer['haveAnother'] == 1 ? 'yes' : 'no';
				$haveAnother = '<img src="' . base_url() . 'images/haveanother_' . $haveAnother . '25.jpg" width="25" height="25" alt="" />';
				
				// see if there are any similar beers that the user has tasted
				$similar = $this->ci->BeerModel->getBeerRatingByStyleAndUserID($beer['styleID'], $beer['userID'], $beer['id']);
				// holder for output
				$str_similar = '';
				// check if there are any
				if(count($similar) > 0) {
					// start the output
					$str_similar .= '
						<table>
							<tr class="tr_bg">
								<th class="td_70">Beer</th>
								<th class="td_30 center">Rating</th>
							</tr>
					';
					// counter
					$i = 0;
					// iterate throught the results
					foreach($similar as $key) {
						$class = $i % 2 == 1 ? ' class="bg2"' : ' class="bg1"';
						$str_similar .= '
							<tr' . $class . '>
								<td width="70%"><a href="' . base_url() . 'beer/review/' . $key['id'] . '">' . $key['beerName'] . '</a></td>
								<td width="30%" class="center">' . $key['rating'] . '</td>
							</tr>
						';
						$i++;
					}
					// end the output
					$str_similar .= '
						</table>
					';
				} else {
					// there are none
					$str_similar = '<p><span class="weight700">' . $beer['username'] . '</span> hasn&#39;t reviewed enough!</p>';
				}
				
				// check for the number of beers rated and the average
				$average = $this->ci->BeerModel->getNumBeersAndAverageByUserID($beer['userID']);
				// holder for output
				$str_avg = '';
				// check if there were any tastings average
				if(count($average) > 0) {
					$rated = $average['beersTasted'] > 1 || $average['beersTasted'] < 1 ? ' beers' : 'beer';
					$str_avg = '<span class="weight700">' . $average['beersTasted'] . '</span>' . $rated . ' rated with a <span class="weight700">' . number_format($average['avergeRating'], 1) . '</span> average rating';
				}
				
				$class = $cnt % 2 == 0 ? ' bg2' : ' bg1';
				$str .= '
					<div class="toggle_beerReview' . $class . '">
						<div class="reviewer">
							<div class="rating">
								<h1>' . $beer['rating'] . '</h1>								
								<p>Have Another:<br />' . $haveAnother . '</p>
							</div>
							<div class="user_image"><img src="' . base_url() . 'images/fakepic.png" /></div>
							<div class="user_info">
								<ul>
									<li><span class="weight700">' . $beer['username'] . '</span> from ' . $beer['userCity'] . ', ' . $beer['userState'] . '</li>									
									<li>Date reviewed: ' . $beer['formatDateAdded'] . '</li>
									<li>' . $str_avg . '</li>	
								</ul>
							</div>
							<br class="left" />
						</div>
					</div>
					
					<div class="content_beerReview">
						<div class="beerReview_comments">
							<p><a href="' . base_url() . '">' . $beer['username'] . '</a> says:</p>						
							<p>' . nl2br($beer['comments']) . '</p>
						</div>
						<div class="beerReview_similar">	
							<p>Date tasted: ' . $beer['formatDateTasted'] . '</p>						
							<p>Color: ' . $beer['color'] . '</p>						
							<h3>Similar Beers Tasted</h3>
							<div class="similarBeers">
								' . $str_similar . '
							</div>
						</div>
						<br class="left" />
					</div>
				';
				$cnt++;
			}
		} else {
			$str .= '
					<div class="beerReview">
						<p>There are no reviews for this beer.</p>
					</div>
			';
		}
		$str .= '
			</div>
		</div>
		';
		
		// get configuration values for creating the seo
		$config = array(
			'beerName' => $beers[0]['beerName']
			, 'beerStyle' => $beers[0]['style']
			, 'breweryName' => $beers[0]['name']
			, 'breweryCity' => $beers[0]['city']
			, 'breweryState' => $beers[0]['stateFull']
		);
		// set the page information
		$seo = getDynamicSEO($config);
		$array = $seo + array('str' => $str);
		return $array;
	}
}
?>