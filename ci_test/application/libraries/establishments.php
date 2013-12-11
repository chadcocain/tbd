<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Establishments {
	private $ci;
	private $title = 'Breweries';
	private $priceLingo = array(
		1 => 'pricey' 
		, 2 => 'little more than expected'
		, 3 => 'right about right'
		, 4 => 'little less than expected'
		, 5 => 'bargain'
	);

	public function __construct() {
		$this->ci =& get_instance();
	}

	public function getAllBreweries() {
		// get all the breweries
		$breweries = $this->ci->BreweriesModel->getAll();

		// start the output
		$str = '';
		// iterte through the list
		foreach($breweries as $brewery) {
			// format the phone, if it exists
			$phone = !empty($brewery['phone']) ? formatPhone($brewery['phone']) : '';
			// continue the output for the screen
			$str .= '
			<div id="item_' . $brewery['id'] . '" class="item">
				<div id="item_list_container_' . $brewery['id'] . '" class="list_itemContainer">
					<ul id="item_list_' . $brewery['id'] . '" class="list_item">
						<li>' . $brewery['name'] . '</li>
						<li>' . $brewery['address'] . '</li>
						<li>' . $brewery['city'] . ', ' . $brewery['stateAbbr'] . ' ' . $brewery['zip'] . '</li>
						<li>' . $phone . '</li>
						<li><a href="' . $brewery['url'] . '" target="_blank">' . $brewery['url'] . '</a></li>
					</ul>
				</div>
				<ul id="list_links_' . $brewery['id'] . '" class="list_horizontalLinks">
					<li><a href="#" id="edit_' . $brewery['id'] . '" onclick="new Ajax.Request(\'' . base_url() . 'ajax/edit/brewery/' . $brewery['id'] . '\', {asynchronous: true, evalScripts: true, method: \'post\', onLoading: function() {showSpinner(\'item_list_container_' . $brewery['id'] . '\');}, onComplete: function(response) {$(\'item_list_container_' . $brewery['id'] . '\').update(response.responseText); $(\'edit_' . $brewery['id'] . '\').style.display=\'none\'; $(\'cancel_' . $brewery['id'] . '\').style.display=\'block\';}}); return false;">Edit</a></li>
					<li><a href="#" id="cancel_' . $brewery['id'] . '" onclick="new Ajax.Request(\'' . base_url() . 'ajax/cancel/brewery/' . $brewery['id'] . '\', {asynchronous: true, evalScripts: true, method: \'get\', onLoading: function() {showSpinner(\'item_list_container_' . $brewery['id'] . '\');}, onComplete: function(response) {$(\'item_list_container_' . $brewery['id'] . '\').update(response.responseText); $(\'cancel_' . $brewery['id'] . '\').style.display=\'none\'; $(\'edit_' . $brewery['id'] . '\').style.display = \'block\';}}); return false;" style="display: none;">Cancel</a></li>
				</ul>
				<br class="both" />
			</div>
			';
		}
		// return the output
		return $str;
	}

	public function getBreweryByID($id) {
		// get the specific brewery
		$brewery = $this->ci->BreweriesModel->getBreweryByID($id);
		// format the phone, if it exists
		$phone = !empty($brewery['phone']) ? formatPhone($brewery['phone']) : '';
		$str = '
					<ul id="item_list_' . $brewery['id'] . '" class="list_item">
						<li>' . $brewery['name'] . '</li>
						<li>' . $brewery['address'] . '</li>
						<li>' . $brewery['city'] . ', ' . $brewery['stateAbbr'] . ' ' . $brewery['zip'] . '</li>
						<li>' . $phone . '</li>
						<li><a href="' . $brewery['url'] . '" target="_blank">' . $brewery['url'] . '</a></li>
					</ul>
		';
		return $str;
	}

	public function createForm($config) {
		$brewery = array(
		'abc' => ''
		, 'id' => ''
		, 'name' => ''
		, 'address' => ''
		, 'city' => ''
		, 'stateID' => ''
		, 'zip' => ''
		, 'phone' => ''
		, 'url' => ''
		, 'btnValue' => 'Add'
		, 'action' => 'action="' . base_url() . 'ajax/addData/brewery/"'
		, 'onsubmit' => 'onsubmit="new Ajax.Request(\'' . base_url() . 'ajax/addData/brewery\', {asynchronous: true, evalScripts: true, method: \'post\', parameters: Form.serialize(this), onComplete: function(response) {$(\'contents\').update(response.responseText);}}); return false;"'
		);

		if(key_exists('id', $config)) {
			$brewery = $this->ci->BreweriesModel->getBreweryByID($config['id']);
			$brewery['btnValue'] = 'Update';
			$brewery['action'] = 'action="' . base_url() . 'ajax/editData/brewery/' . $config['id'] . '"';
			$brewery['onsubmit'] = 'onsubmit="new Ajax.Request(\'' . base_url() . 'ajax/editData/brewery/' . $config['id'] . '\', {asynchronous: true, evalScripts: true, method: \'post\', parameters: Form.serialize(this), onLoading: function() {showSpinner(\'item_list_container_' . $brewery['id'] . '\');}, onComplete: function(response) {$(\'item_list_container_' . $config['id'] . '\').update(response.responseText); $(\'cancel_' . $config['id'] . '\').style.display=\'none\'; $(\'edit_' . $config['id'] . '\').style.display = \'block\';}}); return false;"';
		}

		$data = array(
		'data' => $config['states']
		, 'id' => 'slt_state'
		, 'name' => 'slt_state'
		, 'selected' => $brewery['stateID']
		);
		$stateDropDown = createDropDown($data);

		$str = '
			<form class="edit" method="post" ' . $brewery['action'] . ' ' . $brewery['onsubmit'] . '>
				<label for="txt_name">Name:</label>
				<input type="text" id="txt_name" name="txt_name" value="' . $brewery['name'] . '" />
				
				<label for="txt_address">Address:</label>
				<input type="text" id="txt_address" name="txt_address" value="' . $brewery['address'] . '" />
				
				<label for="txt_city">City:</label>
				<input type="text" id="txt_city" name="txt_city" value="' . $brewery['city'] . '" />
				
				<label for="slt_state">State:</label>
				' . $stateDropDown . '
				
				<label for="txt_zip">Zip:</label>
				<input type="text" id="txt_zip" name="txt_zip" value="' . $brewery['zip'] . '" />
				
				<label for="txt_phone">Phone:</label>
				<input type="text" id="txt_phone" name="txt_phone" value="' . $brewery['phone'] . '" />
				
				<label for="txt_url">URL:</label>
				<input type="text" id="txt_url" name="txt_url" value="' . $brewery['url'] . '" />
				
				<input type="submit" id="btn_submit" name="btn_submit" value="' . $brewery['btnValue'] . '" />
		';
		$str .= key_exists('hidden', $config) && $config['hidden'] == 'rating' ? '<input type="hidden" id="hdn_step" name="hdn_step" value="beer" />' : '';
		$str .= '
			</form>
		';
		return $str;
	}

	public function showBreweryInfo($id, $logged = false) {
		// get the brewery information
		$brewery = $this->ci->BreweriesModel->getBreweryInfoByID($id);
		// holder for return information
		$array = array();
		// make sure that information was found - this will only happen
		// if the id isn't found in the brewery table
		if(empty($brewery)) {
			// set the words for the page
			$str = '<p>Sorry, but we couldn\'t find any information for the requested brewery.</p>';
			// create the seo information
			$seo = array(
			'pagetitle' => 'Brewery Information - Two Beer Dudes'
			, 'metadescription' => 'Two Beer Dudes American craft beer brewery review, brewery rating, brewery hop, brewery'
			, 'metakeywords' => 'brewery review, brewery rating, brewery hop, brewery'
			);
			// set the return array
			$array = $seo + array('str' => $str);
		} else {
			// get the beer and rating information
			$beers = $this->ci->BreweriesModel->getAllRatingsForBreweryByID($id);
			// check to see if there are any beers that have been rated
			if(!empty($beers)) {
				// get the total number of beers and the average rating
				// based on brewery id
				$avg = $this->ci->BreweriesModel->getTotalEachBeer($id);
				// determine the average score for the entire group
				$average = '0.0';
				if($avg['totalBeers'] > 0) {
					$average = round(($avg['totalPoints'] / $avg['totalBeers']), 1);
				}
				// get the total number of distinct beers that have been tried
				// based on brewery id
				$distinct = $this->ci->BreweriesModel->getDistinctBeerCount($id);
				// get the avg cost per package of beer drank for the brewery
				$avgCost = $this->ci->BreweriesModel->getAvgCostPerPackage($id);
				// get the percentage of people who would have another
				$haveAnother = $this->ci->BreweriesModel->getHaveAnotherPercent($id);
			}

			// create the output for the screen
			// start w/ the brewery information
			// check for a brewery hop
			$breweryHop = !empty($brewery[0]['breweryhopsID']) ? '<a href="' . base_url() . 'brewery/hop/' . $brewery[0]['breweryhopsID'] . '"><img src="' . base_url() . 'images/cone.gif" alt="brewery hop to ' . $brewery[0]['name'] . '" title="brewery hop to ' . $brewery[0]['name'] . '" /></a>' : '';
			$address = empty($brewery[0]['address']) ? '' : '<p>' . $brewery[0]['address'] . '</p>';
			$city = empty($brewery[0]['city']) ? '' : '<a href="' . base_url() . 'brewery/city/' . $brewery[0]['stateID'] . '/' . urlencode($brewery[0]['city']) . '">' . $brewery[0]['city'] . '</a>, ';
			$zip = empty($brewery[0]['zip']) ? '' : $brewery[0]['zip'];
			$url = empty($brewery[0]['url']) ? '' : '<a href="' . $brewery[0]['url'] . '" target="_blank"><img src="' . base_url() . 'images/web.jpg" alt="' . $brewery[0]['name'] . ' web site" title="' . $brewery[0]['name'] . ' web site" /></a>';

			// holder for image
			$img = '';
			// check if there is an image available
			if($brewery[0]['pictureApproval'] == '1') {
				// configuration for the image
				$image = array(
				'picture' => $brewery[0]['picture']
				, 'id' => $brewery[0]['id']
				, 'alt' => $brewery[0]['name']
				);
				// check if the image exists for this brewery
				$img = checkForAnImage($image, $logged, true, 'establishment');
			} else {
				// generic image
				$img = '&nbsp;';
			}

			$str = '
				<div id="breweryInfo">
					<h2 class="brown">' . $brewery[0]['name'] . ' ' . $url . ' ' . $breweryHop . '</h2>
					<div id="establishmentInfo">						
						<p>' . $address . '</p>
						<p>' . $city . '<a href="' . base_url() . 'brewery/state/' . $brewery[0]['stateID'] . '">' . $brewery[0]['stateAbbr'] . '</a> ' . $zip . '</p>
						<p>' . formatPhone($brewery[0]['phone']) . '</p>
			';
			if(!empty($beers)) {
				$totalBeers = $distinct['totalBeers'] > 1 || $distinct['totalBeers'] < 1 ? '<span class="bold">' . $distinct['totalBeers'] . '</span> different beers' : '<span class="bold">' . $distinct['totalBeers'] . '</span> different beer';
				$avgTotalBeers = $avg['totalBeers'] > 1 || $avg['totalBeers'] < 1 ? 'drank <span class="bold">' . $avg['totalBeers'] . '</span> times' : 'drank <span class="bold">' . $avg['totalBeers'] . '</span> time';
				$str .= '
						<ul class="green" style="margin-top: 1.0em">
							<li class="bold" style="text-decoration: underline;">Beer Review Stats:</li>
							<li>' . $totalBeers . '</li>
							<li>' . $avgTotalBeers . '</li>
							<li>with a <span class="bold">' . number_format($average, 1) . '</span> average rating</li>
						</ul>
				';
			}
			$str .= '
					</div>
					' . $img . '
					<br class="left" />
				</div>
				<div id="beerTable">
					<table>
						<tr class="gray2">
							<th>&nbsp;</th>
							<th>Beer</th>
							<th>Style</th>
							<th class="center"># Reviews</th>
							<th class="center">Rate Avg.</th>							
							<th class="center">H.A.</th>
							<th>Avg. Cost</th>
						</tr>
			';		

			if(empty($beers)) {
				$str .= '
						<tr><td colspan="6">No beer reviews at this time.</td></tr>
					</table>
				</div>
				';
			} else {
				// counter for determing background color
				$cnt = 0;
				// iterate through the beers
				foreach($beers as $beer) {
					// see if the beer has an average cost
					$str_avg = '';
					foreach($avgCost as $cost) {
						if($cost['id'] == $beer['id']) {
							// there is a match so create the output
							if(!empty($str_avg)) {
								$str_avg .= '<br />';
							}
							$serving = $cost['totalServings'] > 1 || $cost['totalServings'] < 1 ? ' servings' : ' serving';
							$str_avg .= '$' . $cost['averagePrice'] . ', ' . $cost['totalServings'] . $serving . ', ' . $cost['package'] . 's';
						}
					}
					// determine the percent of have another
					$str_ha = '0%';
					foreach($haveAnother as $ha) {
						if($ha['id'] == $beer['id']) {
							// there is a match so create the output
							$str_ha = ($ha['percentHaveAnother'] * 100) . '%';
						}
					}
					$averagereview = (float) $beer['averagereview'];
					$class = $cnt % 2 == 1 ? ' class="gray"' : '';
					$str .= '
							<tr' . $class . '>
								<td class="td_first"><a href="' . base_url() . 'beer/review/' . $beer['id'] . '"><img src="' . base_url() . 'page/createImage/' . $beer['id'] . '/beer/mini" /></a></td>
								<td><a href="' . base_url() . 'beer/review/' . $beer['id'] . '">' . $beer['beerName'] . '</a></td>
								<td><a href="' . base_url() . 'beer/style/' . $beer['styleID'] . '">' . $beer['style'] . '</a></td>
								<td class="center">' . $beer['reviews'] . '</td>
								<td class="center">' . number_format($averagereview, 1) . '</td>
								<td class="center">' . $str_ha . '</td>
								<td>' . $str_avg . '</td>
							</tr>				
					';
					// increment counter
					$cnt++;
				}
				$str .= '
						</table>
					</div>
				';
			}

			// holder for right column text
			$rightCol = '';
			// get the highest rated breweries
			$highestRatedBreweries = $this->ci->BreweriesModel->getHighestRatedBreweries();
			// check if there were any results
			if(!empty($highestRatedBreweries)) {
				$rightCol = '
					<h4><span>Highest Rated Breweries</span></h4>
					<ul>
				';
				// iterate over the results
				foreach($highestRatedBreweries as $highRating) {
					// get the wording
					$brs = $highRating['beerTotal'] == 1 ? ' beer rating' : ' beer ratings';
					// add another item
					$rightCol .= '
						<li>
							<p><a href="' . base_url() . 'brewery/info/' . $highRating['id'] . '">' . $highRating['name'] . '</a></p>
							<p class="rightBreweryLink"><span class="bold">' . number_format($highRating['avgRating'], 1) . '</span> for <span class="bold">' . $highRating['beerTotal'] . '</span>' . $brs . '</p>
						</li>';
				}
				// finish off the text
				$rightCol .= '
					</ul>
				';
			}

			// get configuration values for creating the seo
			$config = array(
			'breweryName' => $brewery[0]['name']
			, 'breweryCity' => $brewery[0]['city']
			, 'breweryState' => $brewery[0]['stateFull']
			);
			// set the page information
			$seo = getDynamicSEO($config);
			$array = $seo + array('leftCol' => $str, 'rightCol' => $rightCol);
		}
		return $array;
	}

	public function showBreweryInfoGeneric($id, $logged = false) {
		// holder for left column output
		$str = '';
		// get the state information
		$states = $this->ci->StateModel->getAllStates();
		// check to make sure we have states
		if(!empty($states)) {
			// counter
			$cnt = 0;
			// iterate through the results
			foreach($states as $state) {
				// get the remainder
				$mod = $cnt % 17;
				if($mod == 0 && $cnt > 0) {
					$str .= '</ul><ul class="stateList">';
				}
				if($mod == 0 && $cnt == 0) {
					$str .= '<ul class="stateList">';
				}
				// add the state to the list
				$str .= '<li><a href="' . base_url() . 'brewery/state/' . $state['id'] . '">' . $state['stateFull'] . '</a></li>';
				// increment the counter
				$cnt++;
			}
			$str .= '</ul><br class="left" />';
		}
					
		// holder for right column text
		$rightCol = '';
		// get the highest rated breweries
		$highestRatedEstablishments = $this->ci->EstablishmentModel->getHighestRatedEstablishments();
		// check if there were any results
		if(!empty($highestRatedEstablishments)) {
			$rightCol = '
				<h4><span>Highest Rated Establishments</span></h4>
				<ul>
			';
			// iterate over the results
			foreach($highestRatedEstablishments as $highRating) {
				// get the wording
				$brs = $highRating['totalRatings'] == 1 ? ' rating' : ' ratings';
				// add another item
				$rightCol .= '
					<li>
						<p><a href="' . base_url() . 'brewery/info/' . $highRating['id'] . '">' . $highRating['name'] . '</a> in <a href="' . base_url() . 'brewery/info/' . $highRating['stateID'] . '/' . urlencode($highRating['city']) . '">' . $highRating['city'] . '</a>, <a href="' . base_url() . 'brewery/info/' . $highRating['stateID'] . '">' . $highRating['stateAbbr'] . '</a></p>
						<p class="rightBreweryLink"><span class="bold">' . number_format($highRating['avgRating'], 1) . '</span> for <span class="bold">' . $highRating['totalRatings'] . '</span>' . $brs . '</p>
					</li>';
			}
			// finish off the text
			$rightCol .= '
				</ul>
			';
		}
		/*
		// get the establishment types
		$establishmentTypes = $this->ci->EstablishmentModel->getEstablishmentTypes();
		// check that there were results
		if(!empty($establishmentTypes)) {
			$rightCol .= '
				<h4><span>Establishments Types</span></h4>
				<dl class="dl_nomargin">
			';
			//iterate over the results
			foreach($establishmentTypes as $eTypes) {
				// add the item
				$rightCol .= '
					<dt>' . ucwords($eTypes['name']) . '</dt>
					<dd>' . $eTypes['description'] . '</dd>
				';
			}
			// finish off the text
			$rightCol .= '
				</dl>
			';
		}*/
		
		// get the newest additions
		$newest = $this->ci->EstablishmentModel->getNewestAdditions();
		// check that there were results
		if(!empty($newest)) {
			$rightCol .= '
				<h4><span>Recent Additions</span></h4>
				<ul>
			';
			// iterate over the results
			foreach($newest as $item) {
				// add the item
				$rightCol .= '
					<li>
						<p><a href="' . base_url() . 'brewery/info/' . $item['id'] . '">' . $item['name'] . '</a></p>
						<p class="rightBreweryLink"> in <a href="' . base_url() . 'brewery/info/' . $item['stateID'] . '/' . urlencode($item['city']) . '">' . $item['city'] . '</a>, <a href="' . base_url() . 'brewery/info/' . $item['stateID'] . '">' . $item['stateFull'] . '</a></p>
					</li>
				';
			}
			// finish off the text
			$rightCol .= '
				</ul>
			';
		}
		
		// get the newest review additions
		$reviews = $this->ci->EstablishmentModel->getRecentReviews();
		// check that there were results
		if(!empty($reviews)) {
			$rightCol .= '
				<h4><span>Recent Reviews</span></h4>
				<ul>
			';
			// iterate over the results
			foreach($reviews as $item) {
				// add the item
				$rightCol .= '
					<li>
						<div class="bottleCap"><p>' . number_format($item['rating'], 1) . '</p></div>
						<div class="rightSimilar">
							<p><a href="' . base_url() . 'brewery/info/' . $item['id'] . '">' . $item['name'] . '</a></p>
							<p class="rightBreweryLink">in <a href="' . base_url() . 'brewery/info/' . $item['stateID'] . '/' . urlencode($item['city']) . '">' . $item['city'] . '</a>, <a href="' . base_url() . 'brewery/info/' . $item['stateID'] . '">' . $item['stateFull'] . '</a></p>
						</div>
						<br class="left" />
					</li>
				';
			}
			// finish off the text
			$rightCol .= '
				</ul>
			';
		}

		// set the page information
		$array = array('leftCol' => $str, 'rightCol' => $rightCol);
		// return the array of results
		return $array;
	}
	
	public function showBreweryHop($id) {	
		// get the brewery information
		$breweryhop = $this->ci->BreweriesModel->getBreweryHopByID($id);
		//echo '<pre>'; print_r($breweryhop); echo '</pre>';exit;
				
		// create the output for the screen
		// start w/ the brewery information
		$str = '
			<h2 class="brown">Brewery Hop to ' . $breweryhop['name'] . '</h2>
			<p style="margin-top: 0.4em;">on ' . $breweryhop['hopDate'] . '</p>			
			<div id="breweryhop">' . $breweryhop['article'] . '</div>
		';
		
		/*<div id="breweryInfo">				
				<ul>
					<li>' . $breweryhop['address'] . '</li>
					<li>
						<a href="' . base_url() . 'brewery/city/' . $breweryhop['stateID'] . '/' . urlencode($breweryhop['city']) . '">' . $breweryhop['city'] . '</a>,
						<a href="' . base_url() . 'brewery/state/' . $breweryhop['stateID'] . '">' . $breweryhop['stateAbbr'] . '</a>
						' . $breweryhop['zip'] . '
					</li>
					<li><a href="' . $breweryhop['url'] . '" target="_blank">web site</a></li>
				</ul>
			</div>*/
		
		// get configuration values for creating the seo
		$config = array(
			'breweryName' => $breweryhop['name']
			, 'breweryCity' => $breweryhop['city']
			, 'breweryState' => $breweryhop['stateFull']
		);
		// set the page information
		$seo = getDynamicSEO($config);
		$array = $seo + array('str' => $str);
		return $array;
	}
	
	public function showAllBreweryHops() {	
		// get the brewery information
		$breweryhop = $this->ci->BreweriesModel->getAllBrewreyHops();
		//echo '<pre>'; print_r($breweryhop); echo '</pre>';exit;
			
		// set the output for the screen
		$str = '<h4><span>Recent Brewery Hops</span></h4>';	
		
		// create the output for the screen
		$str .= '
			<ul id="hopList">
		';
		foreach($breweryhop as $hop) {
			$str .= '
				<li><a href="' . base_url() . 'brewery/hop/' . $hop['id'] . '">' . $hop['name'] . '</a> on ' . $hop['hopDate'] . '</li>
			';
		}
		$str .= '
			</ul>
		';
		// return formatted output
		return $str;
	}
	
	public function showBreweryHopFrontPage() {	
		// get the brewery information
		$breweryhop = $this->ci->BreweriesModel->getAllBrewreyHops(1);;
		// create the output for the screen
		$str = '
		<div id="breweryHop">			
		';
		foreach($breweryhop as $hop) {
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
		// return formatted output
		return $str;
	}
	
	public function showEstablishmentsCity($state, $city) {
		// get the brewery information
		$establishments = $this->ci->EstablishmentModel->getEstablishmentsByCity($state, $city);
		// holder for string output
		$str = '';
		// holder for the seo information
		$seo = '';
		// holder for the output
		$array = array();
		// check to see if there were any results
		if(empty($establishments)) {
			// there were no results for the given state
			// go get infromation for the state
			$stateInfo = $this->ci->StateModel->getStateByID($state); 
			// create the output
			$str = '
				<h2 class="brown">Establishments in ' . $city . ', ' . $stateInfo['stateFull'] . '</h2>
				<p>There are no records for the city and state requested.</p>
			';
			// set the page information
			$seo = getSEO();			
		} else {			
			// there are some breweries			
			// holder for the class depending on if there is an image
			$str = '
				<h2 class="brown">Establishments: ' . $establishments[0]['city'] . ', ' . $establishments[0]['stateFull'] . '</h2>
				<table class="tbl_standard">
					<tr>
						<th>Name</th>						
						<th>Contact Info.</th>
						<th>Rating</th>
						<th>Reviews</th>
						<th>Category</th>
					</tr>
			';
			// counter
			$cnt = 0;
			// iterate through the results
			foreach($establishments as $establishment) {
				// get the percentage of people who would have another
				$rating = $this->ci->EstablishmentModel->getEstablishmentRating($establishment['establishmentID']);
				// create the text for display
				$totalRatings = 'N/A';
				$averageRating = 'N/A';
				if($rating['totalRatings'] > 0) {
					$averageRating = number_format($rating['averageRating'], 1);
					$totalRatings = $rating['totalRatings'];
				}
				// determine the class for a particular row
				$class = ($cnt % 2) == 0 ? '' : ' class="gray"';
				// create the output
				$str .= '
					<tr' . $class . '>
						<td><a href="' . base_url() . 'establishment/info/rating/' . $establishment['establishmentID'] . '">' . $establishment['name'] . '</a></td>						
						<td>
							' . $establishment['address'] . '<br />
							<a href="' . base_url() . 'establishment/city/' . $establishment['stateID'] . '/' . urldecode($establishment['city']) . '">' . $establishment['city'] . '</a>, <a href="' . base_url() . 'establishment/state/' . $establishment['stateID'] . '">' . $establishment['stateAbbr'] . '</a> ' .$establishment['zip'] . '<br />
							' . formatPhone($establishment['phone']) . '
						</td>
						<td>' . $averageRating . '</td>
						<td>' . $totalRatings . '</td>
						<td>' . $establishment['category'] . '</td>
					</tr>
				';
				// increment the counter
				$cnt++;
			}
			// finish off the text
			$str .= '
				</table>
			';
			
			// get configuration values for creating the seo
			$config = array(
				'breweryCity' => $establishments[0]['city']
				, 'breweryState' => $establishments[0]['stateFull']
				, 'seoType' => 'establishmentsByCity'
			);
			// set the page information
			$seo = getDynamicSEO($config);
			$array = $seo + array('str' => $str);
		}
		// return the output
		return $array;
	}
	
	public function showEstablishmentState($state) {
		// get the brewery information
		$establishments = $this->ci->EstablishmentModel->getEstablishmentsByState($state);
		// holder for string output
		$str = '';
		// holder for the seo information
		$seo = '';
		// holder for the output
		$array = array();
		// check to see if there were any results
		if(empty($establishments)) {
			// there were no results for the given state
			// go get infromation for the state
			$stateInfo = $this->ci->StateModel->getStateByID($state); 
			// create the output
			$str = '
				<h2 class="brown">Establishments in ' . $stateInfo['stateFull'] . '</h2>
				<p>There are no records for the state requested.</p>
			';
			// set the page information
			$seo = getSEO();
		} else {			
			// there are some breweries			
			// holder for the class depending on if there is an image
			$str = '
				<h2 class="brown">Establishments in ' . $establishments[0]['stateFull'] . '</h2>
				<h3 class="est_byCity">By City</h3>				
			';
			// the number of results
			$num = count($establishments);
			// counter
			$cnt = 0;
			// divisor
			$div = ($num % 2 == 0) ? ($num / 2) : ($num / 2) + 1;
			//echo $div;exit;
			// iterate through the results
			foreach($establishments as $establishment) {
				// get the remainder
				$mod = $cnt % $div;
				if($cnt > 0 && $mod == 0) {
					$str .= '</ul><ul class="stateEst">';
				}
				if($cnt == 0) {
					$str .= '<ul class="stateEst">';
				}
				$str .= '<li><a href="' . base_url() . 'establishment/city/' . $state . '/' . urlencode($establishment['city']) . '">' . $establishment['city'] . ' (' . $establishment['totalPerCity'] . ')</a></li>';
				// increment the counter
				$cnt++;
			}
			// finish off the text
			$str .= '</ul><br class="left" />';
			
			// get them ordered by category
			$categories = $this->ci->EstablishmentModel->getEstablishmentsByCategory($state);
			//echo '<pre>'; print_r($categories); exit;
			// add to the output
			$str .= '<h3 class="est_byCity">By Category</h3>';
			// the number of results
			$num = count($categories);
			// counter
			$cnt = 0;
			// divisor
			$div = ($num % 2 == 0) ? ($num / 2) : ($num / 2) + 1;
			//echo $div;exit;
			// iterate through the results
			foreach($categories as $category) {
				// get the remainder
				$mod = $cnt % $div;
				if($cnt > 0 && $mod == 0) {
					$str .= '</ul><ul class="stateEst">';
				}
				if($cnt == 0) {
					$str .= '<ul class="stateEst">';
				}
				if($category['totalPerCategory'] > 0) {
					$str .= '<li><a href="' . base_url() . 'establishment/info/category/' . $category['id'] . '/' . $state . '">' . $category['name'] . ' (' . $category['totalPerCategory'] . ')</a></li>';
				} else {
					$str .= '<li>' . $category['name'] . ' (' . $category['totalPerCategory'] . ')</li>';
				}
				// increment the counter
				$cnt++;
			}
			// finish off the text
			$str .= '</ul><br class="left" />';
			
			
			// get configuration values for creating the seo
			$config = array(
				'breweryState' => $establishment['stateFull']
				, 'seoType' => 'establishmentsByState'
			);
			// set the page information
			$seo = getDynamicSEO($config);			
		}
		// package up the information
		$array = $seo + array('str' => $str);
		// return the output
		return $array;
	}
	
	public function showCategoryInfo($state, $categoryID, $logged = '') {
		// get the brewery information
		$establishments = $this->ci->EstablishmentModel->getEstablishmentsByCategoryState($state, $categoryID);
		// holder for string output
		$str = '';
		// holder for the seo information
		$seo = '';
		// holder for the output
		$array = array();
		// check to see if there were any results
		if(empty($establishments)) {
			// there were no results for the given state
			// go get infromation for the state
			$stateInfo = $this->ci->StateModel->getStateByID($state); 
			// load the category model
			$this->ci->load->model('CategoryModel', '', true);
			// get the specific category information
			$category = $this->ci->CategoryModel->getCategoryInfoByID($categoryID);
			//echo '<pre>'; print_r($establishments); echo '</pre>'; exit;
			// check the category to make sure it exists
			$cat = !empty($category) ? ucwords($category['name']) : 'Establishment';
			// create the output
			$str = '
				<h2 class="brown">' . $cat . ' in ' . $stateInfo['stateFull'] . '</h2>
				<p>There are no records for the state requested.</p>
			';
			// set the page information
			$seo = getSEO();	
			// return array
			$array = $seo + array('leftCol' => $str, 'rightCol' => '');		
		} else {			
			// there are some breweries			
			// holder for the class depending on if there is an image
			$str = '
				<h2 class="brown">' . ucwords($establishments[0]['category']) . ' in ' . $establishments[0]['stateFull'] . '</h2>
				<table class="tbl_standard">
					<tr>
						<th>Name &#38; Contact Info.</th>	
						<th>Reviews</th>					
						<th>Rating</th>
            ';
            if($categoryID == 1 || $categoryID == 4) {
                $str .= '						
						<th>Beers</a>
						<th>Beer Rating</th>
                ';
            }
            $str .= '
						<th>Category</th>
					</tr>
			';
			// counter
			$cnt = 0;
			// iterate through the results
			foreach($establishments as $establishment) { //echo '<pre>'; print_r($establishment); exit;
				// get the percentage of people who would have another
				$rating = $this->ci->EstablishmentModel->getEstablishmentRating($establishment['establishmentID']);
				// create the text for display
				$totalRatings = 'N/A';
				$averageRating = 'N/A';
				if($rating['totalRatings'] > 0) {
					$averageRating = number_format($rating['averageRating'], 1);
					$totalRatings = $rating['totalRatings'];
				}
				
				// get the total number of beers and the average rating
				// based on brewery id
				$avg = $this->ci->BreweriesModel->getTotalEachBeer($establishment['establishmentID']);
				// determine the average score for the entire group
				$average = 'N/A';
				if($avg['totalBeers'] > 0) {
					$average = number_format(round(($avg['totalPoints'] / $avg['totalBeers']), 1), 1);
				}
				// holder the number of beers
				$tmp_beers = 'N/A';
				// holder for the average rating
				$tmp_beerRatings = 'N/A';
				// check that this is the right type of estblishment
				if(in_array($establishment['categoryID'], array(1, 4))) {
					// set the number of beers
					$tmp_beers = $avg['totalBeers'];
					// set the beer rating average
					$tmp_beerRatings = $average;	
				}				
				
				// determine the class for a particular row
				$class = ($cnt % 2) == 0 ? '' : ' class="gray"';
				// create the output
				$str .= '
					<tr' . $class . '>
						<td>
							<a href="' . base_url() . 'establishment/info/rating/' . $establishment['establishmentID'] . '">' . $establishment['name'] . '</a><br />			
							' . $establishment['address'] . '<br />
							<a href="' . base_url() . 'establishment/city/' . $establishment['stateID'] . '/' . urldecode($establishment['city']) . '">' . $establishment['city'] . '</a>, <a href="' . base_url() . 'establishment/state/' . $establishment['stateID'] . '">' . $establishment['stateAbbr'] . '</a> ' .$establishment['zip'] . '<br />
							' . formatPhone($establishment['phone']) . '
						</td>
						<td>' . $totalRatings . '</td>
						<td>' . $averageRating . '</td>
                ';
                if($categoryID == 1 || $categoryID == 4) {
                    $str .= '
						<td>' . $tmp_beers . '</td>
						<td>' . $tmp_beerRatings . '</td>
                    ';
                }
                $str .= '
						<td>' . $establishment['category'] . '</td>
					</tr>
				';
				// increment the counter
				$cnt++;
			}
			// finish off the text
			$str .= '
				</table>
			';
			
			// get configuration values for creating the seo
			$config = array(
				'breweryCity' => $establishments[0]['city']
				, 'breweryState' => $establishments[0]['stateFull']
				, 'seoType' => 'establishmentsByCity'
			);
			// set the page information
			$seo = getDynamicSEO($config);
			$array = $seo + array('leftCol' => $str, 'rightCol' => '');
		}
		// return the output
		return $array;
	}
	
	public function showEstablishmentRatingsByID($establishmentID, $logged) {
		// get information passed in for the current estblishment
		$establishment = $this->ci->EstablishmentModel->getEstablishmentInfoByID($establishmentID);
		//echo '<pre>'; print_r($establishment); echo '</pre>'; exit;
		// holder for the output
		$str = '';
        // holder for the right column text
        $rightCol = '';
		// make sure the establishment exists
		if(empty($establishment)) {
			// couldn't find the establishment
			// create the output
			$str = '
				<h2 class="brown">Establishments</h2>
				<p class="marginTop_8">There are no records for the establishment requested.</p>
			';
			// set the page information
			$seo = getSEO();	
			// return array
			$array = $seo + array('leftCol' => $str, 'rightCol' => '');
		} else {
			// it exists
			// get the rating information for the establishment
			$er = $this->ci->EstablishmentModel->getRatingsForEstablishmentByID($establishmentID);	
			// get the rating information for two beer dudes
			$tbd = $this->ci->EstablishmentModel->getRatingsForEstablishmentByIDTwoBeerDudes($establishmentID);
		
			//echo '<pre>'; print_r($er); exit;
            
            // get twitter if it exists
            // set the configuration values for the method
            $configTwitter = array(
                'establishment' => $establishment
                , 'type' => 'establishmentReview'
            );
            // call the method to get the string of text
            $rightCol = addSocialMedia($configTwitter);
		
			// get the beer and rating information
			$beers = $this->ci->BreweriesModel->getAllRatingsForBreweryByID($establishmentID);
			// check to see if there are any beers that have been rated
			if(!empty($beers)) {
				// get the total number of beers and the average rating
				// based on brewery id
				$avg = $this->ci->BreweriesModel->getTotalEachBeer($establishmentID);
				// determine the average score for the entire group
				$average = 'N/A';
				if($avg['totalBeers'] > 0) {
					$average = round(($avg['totalPoints'] / $avg['totalBeers']), 1);
				}
				// get the total number of distinct beers that have been tried
				// based on brewery id
				$distinct = $this->ci->BreweriesModel->getDistinctBeerCount($establishmentID);
				// get the avg cost per package of beer drank for the brewery
				$avgCost = $this->ci->BreweriesModel->getAvgCostPerPackage($establishmentID);
				// get to overall average cost of having a beer at the establishment
				$overalAverageCost = $this->ci->BreweriesModel->getOverallAverageCostOfBeerByEstablishmentID($establishmentID);
				// get the percentage of people who would have another
				$haveAnother = $this->ci->BreweriesModel->getHaveAnotherPercent($establishmentID);
			}			
			
			// create the output for the screen
			// start w/ the brewery information
			// check for a brewery hop
			$breweryHop = !empty($establishment['breweryhopsID']) ? '<a href="' . base_url() . 'brewery/hop/' . $establishment['breweryhopsID'] . '"><img src="' . base_url() . 'images/cone.gif" alt="brewery hop to ' . $establishment['name'] . '" title="brewery hop to ' . $establishment['name'] . '" /></a>' : '';
			$address = empty($establishment['address']) ? '' : '<p>' . $establishment['address'] . '</p>';
			$city = empty($establishment['city']) ? '' : '<a href="' . base_url() . 'establishment/city/' . $establishment['stateID'] . '/' . urlencode($establishment['city']) . '">' . $establishment['city'] . '</a>, ';
			$zip = empty($establishment['zip']) ? '' : $establishment['zip'];
			$url = empty($establishment['url']) ? '' : '<a href="' . $establishment['url'] . '" target="_blank"><img src="' . base_url() . 'images/web.jpg" alt="' . $establishment['name'] . ' web site" title="' . $establishment['name'] . ' web site" /></a>';
			
			// configuration for the image
			$image = array(
				'picture' => $establishment['picture']
				, 'id' => $establishment['id']
				, 'alt' => $establishment['name']
				, 'approval' => $establishment['pictureApproval']
			);
			// check if the image exists for this brewery
			$img = checkForAnImage($image, $logged, true, 'establishment');

			$link = $establishment['name'];
			if($establishment['categoryID'] == 1 || $establishment['categoryID'] == 4 || $establishment['categoryID'] == 6) {
				$link = '<a class="brown" href="' . base_url() . 'brewery/info/' . $establishment['id'] . '">' . $establishment['name'] . '</a>';
			}
			$str = '
				<div id="breweryInfo" style="position: relative;">
					<h2 class="brown">' . $link . ' ' . $url . ' ' . $breweryHop . ' <a href="' . base_url() . 'establishment/googleMaps/' . $establishmentID . '"><img src="' . base_url() . 'images/google-map.png" alt="map for ' . $establishment['name'] . '" title="map for ' . $establishment['name'] . '" /></a>' . showTwitterForEstablishment($establishment['twitter']) . '</h2>
					<div id="establishmentInfo">						
						<p>' . $address . '</p>
						<p>' . $city . '<a href="' . base_url() . 'establishment/state/' . $establishment['stateID'] . '">' . $establishment['stateAbbr'] . '</a> ' . $zip . '</p>
						<p>' . formatPhone($establishment['phone']) . '</p>
			';
			
			// holder string
			$str_dudes = '';
			// holder string
			$str_twodudes = '';
			// holder string
			$str_averagePrice = '';
			// holder string
			$str_percentHaveAnother = '';
			// put up establishment hop stats
			if($er['reviews'] > 0) {				
				if($er['reviews'] > 0) {
					$str_ratingTotalTimes = $er['reviews'] > 1 ? $er['reviews'] . ' dudes' : $er['reviews'] . ' dude';
					$str_dudes = '<span class="score_medium">' . number_format($er['averagereview'], 1) . '</span>/10 by ' . $str_ratingTotalTimes;
				}
				
				// check to see if the dudes rated it
				if(!empty($tbd)) {
					$str_twodudes = '<span class="score_medium">' . number_format($tbd['averagereview'], 1) . '</span>/10';
				} else {
					$str_twodudes = 'Not Rated';
				}		

				$tmp = '';
				if($er['reviews'] > 0) {
					$tmp = '(' . $this->priceLingo[(5 - round($er['averageprice']) + 1)] . ')';
				}
				$str_averagePrice = '<p class="marginTop_4"><span class="bold">Price Index:</span> <span class="score_medium">' . number_format($er['averageprice'], 1). '</span> ' . $tmp . '</p>';
				
				// get the percent
				$percentHaveAnother = number_format(($er['averagevisitagain'] * 100));
				// determine which image to use
				$thumb = $percentHaveAnother >= 50 ? 'yes' : 'no';
				// there is a match so create the output
				$str_percentHaveAnother = '<p class="marginTop_4"><span class="bold">Visit Again:</span> <span class="score_medium">' . $percentHaveAnother . '%</span> <img style="vertical-align: middle;" src="' . base_url() . 'images/haveanother_' . $thumb . '25.jpg" width="25" height="25" alt="" /></p>';
				
				$str .= '
						<p class="marginTop_4"><span class="bold">Overall:</span> ' . $str_dudes . '</p>
						<p class="marginTop_4"><span class="bold">Dudes:</span> ' . $str_twodudes . '</p>
						' . $str_averagePrice . $str_percentHaveAnother
				;
			}						
			
			// put up beer review stats
			if(!empty($beers)) {
				$totalBeers = $distinct['totalBeers'] > 1 || $distinct['totalBeers'] < 1 ? '<span class="bold">' . $distinct['totalBeers'] . '</span> different beers' : '<span class="bold">' . $distinct['totalBeers'] . '</span> different beer';
				$avgTotalBeers = $avg['totalBeers'] > 1 || $avg['totalBeers'] < 1 ? 'drank <span class="bold">' . $avg['totalBeers'] . '</span> times' : 'drank <span class="bold">' . $avg['totalBeers'] . '</span> time';
				$str .= '
						<ul class="green" style="margin-top: 1.0em">
							<li class="bold" style="text-decoration: underline;">Beer Review Stats:</li>
							<li>' . $totalBeers . '</li>
							<li>' . $avgTotalBeers . '</li>
							<li>with a <span class="bold">' . (is_numeric($average) ? number_format($average, 1) : '0.0') . '</span> average rating</li>
							<li>and overall average cost of <span class="bold">$' . number_format($overalAverageCost['averagePrice'], 2) . '</span></li>
						</ul>
				';
			}
			// add a link for establishment reviews
			$str .= '<p style="margin-top: 1.0em;"><a href="' . base_url() . 'establishment/info/rating/' . $establishment['id'] . '">Establishment Hops</a>';
			// check if the place sells beer
			if(($establishment['categoryID'] == 1 || $establishment['categoryID'] == 4 || $establishment['categoryID'] == 6)) {
				$str .= ' | <a href="' . base_url() . 'brewery/info/' . $establishment['id'] . '">Beer Reviews</a>';
			}
			// finish off this link area
			$str .= '</p>';
			// check if the user is logged to show the create a review link
			$str .= $logged === false || $establishment['closed'] == 1 ? '' : '<p style="margin-top: 1.0em;"><a href="' . base_url() . 'establishment/createReview/' . $establishment['id'] . '">Create Establishment Hop Review</a></p>';
            // checked if the establishment was closed
            $str .= $establishment['closed'] == 1 ? '<p class="closed marginTop_8">Closed for business!</p>' : '';			
			$str .= '
					</div>
					' . $img . '
					<br class="left" />
				</div>
			';		
			
			// get the rating information
			$ratings = $this->ci->EstablishmentModel->getEstblishmentRatingsByID($establishmentID);
			// check that there is at least on review
			if(!empty($ratings) && !empty($ratings[0]['rating'])) {
				// iterate through the results
				foreach($ratings as $rating) {
					// check for user image
					$userImage = ($rating['avatar'] == 1 && !empty($rating['avatarImage'])) ? 'images/avatars/' . $rating['avatarImage'] : 'images/fakepic.png';
				
					// check for the number of beers rated and the average
					$average = $this->ci->EstablishmentModel->getNumEstablishmentsAndAverageByUserID($rating['userID']);
					// holder for output
					$str_avg = '';
					// check if there were any tastings average
					if(count($average) > 0) {
						$rated = $average['totalRatings'] > 1 || $average['totalRatings'] < 1 ? ' establishments' : ' establishment';
						$str_avg = '<span class="weight700">' . $average['totalRatings'] . '</span>' . $rated . ' rated with a <span class="weight700">' . number_format($average['avergeRating'], 1) . '</span> average rating';
					}
                    
                    // see if there are any other establishments the person has been too
                    $similar = $this->ci->EstablishmentModel->getEstablishmentsRatingUserID($rating['userID']);
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
                            $class = $i % 2 == 1 ? ' class="bg3"' : ' class="bg1"';
                            $str_similar .= '
                                        <tr' . $class . '>
                                            <td width="70%"><a href="' . base_url() . 'establishment/info/rating/' . $key['establishmentID'] . '">' . $key['name'] . '</a></td>
                                            <td width="30%" class="center">' . number_format(round($key['rating'], 1), 1) . '</td>
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
                        $str_similar = '<p><span class="weight700">' . $rating['username'] . '</span> hasn&#39;t reviewed enough!</p>';
                    }
					
					// get images for the rating
					$price = '';
                    // get the modulo by 2 and round
                    $mod = $rating['pricing'] / 2;
                    $mod = round($mod, 0);
                    $stars = 5 - $mod + 1;
					// array of values for pricing
					for($i = 0; $i < 5; $i++) {
						if($i < $stars) {
							$price .= '<img src="' . base_url() . 'images/stars/dollar.png" title="price for ' . $establishment['name'] . ' is ' . $this->priceLingo[$mod] . '" alt="price for ' . $establishment['name'] . ' is ' . $this->priceLingo[$mod] . '" />';
						} else {
							$price .= '<img src="' . base_url() . 'images/stars/dollar_fade.png"  title="price for ' . $establishment['name'] . ' is ' . $this->priceLingo[$mod] . '" alt="price for ' . $establishment['name'] . ' is ' . $this->priceLingo[$mod] . '" />';
						}
					}
                    
                    // determine if they are having another 
                    $haveAnother = $rating['visitAgain'] == 1 ? 'yes' : 'no';
                    $haveAnother = '<img src="' . base_url() . 'images/haveanother_' . $haveAnother . '25.jpg" width="25" height="25" alt="" />';
					
                    // calculate the rating 
                    $ratingCalc = number_format((($rating['drink'] * (PERCENT_DRINK / 100)) + ($rating['service'] * (PERCENT_SERVICE / 100)) + ($rating['atmosphere'] * (PERCENT_ATMOSPHERE / 100)) + ($rating['pricing'] * (PERCENT_PRICING / 100)) + ($rating['accessibility'] * (PERCENT_ACCESSIBILITY / 100))), 1);
                    
					$str .= '
					<div class="singleReviewContainer">
						<div class="topCurve">&nbsp;</div>
						<div class="reviewBorder">
							<div class="singleBeerReview">
								<div class="reviewer">
									<div class="rating">
										<!--<h1 class="h1_rating">' . $rating['rating'] . '</h1>-->
                                        <h1>' . $rating['rating'] . '</h1>                                
                                        <p>Have Another:<br />' . $haveAnother . '</p>
									</div>
									<div class="user_image"><img src="' . base_url() . $userImage . '" /></div>
									<div class="user_info">
										<ul>
											<li><span class="weight700"><a href="' . base_url() . 'user/profile/' . $rating['userID'] . '">' . $rating['username'] . '</a></span> from ' . $rating['userCity'] . ', ' . $rating['userState'] . '</li>									
											<li>Date visited: ' . $rating['formatDateVisited'] . '</li>
											<li style="line-height: 16px;">Price: ' . $price . ' (' . $this->priceLingo[$mod] . ')</li>
											<li>' . $str_avg . '</li>	
										</ul>
									</div>
									<br class="left" />
								</div>
							</div>
							
							<div class="content_beerReview">
								<!--<div class="beerReview_comments_noFloat">-->
                                <div class="beerReview_comments">
									<p>' . nl2br($rating['comments']) . '</p>
									<p class="ratingDate">Date reviewed: ' . $rating['formatDateAdded'] . '</p>
								</div>
                                <div class="beerReview_similar">    
                                    <h3 class="white" style="margin-top: 0;">Rating Breakdown</h3>
                                    <div class="similarBeers">
                                        <p>Quality: <span class="bold">' . $rating['drink'] . '</span> (' . PERCENT_DRINK . '%)</p>
                                        <p>Service: <span class="bold">' . $rating['service'] . '</span> (' . PERCENT_SERVICE . '%)</p>
                                        <p>Atmosphere: <span class="bold">' . $rating['atmosphere'] . '</span> (' . PERCENT_ATMOSPHERE . '%)</p>
                                        <p>Pricing: <span class="bold">' . $rating['pricing'] . '</span> (' . PERCENT_PRICING . '%)</p>
                                        <p>Accessibility: <span class="bold">' . $rating['accessibility'] . '</span> (' . PERCENT_ACCESSIBILITY . '%)</p>
                                        <p><span class="bold requied">Overall: ' . $ratingCalc . '</span></p>
                                    </div>
                                    <h3 class="white">Visited Establishments</h3>
                                    <div class="similarBeers">
                                        ' . $str_similar . '
                                    </div>                        
                                </div>
                                <br class="left" />
							</div>
						</div>
						<div class="bottomCurve">&nbsp;</div>
					</div>
					';
				}
			} else {
				$str .= '
					<div class="singleReviewContainer">
						<p class="notice">No ratings yet.</p>
					</div>
				';
			}			
			
			// get configuration values for creating the seo
			$config = array(
				'breweryCity' => $establishment['city']
				, 'breweryState' => $establishment['stateFull']
				, 'seoType' => 'establishmentsByCity'
			);
			$config = array(
				'breweryName' => $establishment['name']
				, 'breweryCity' => $establishment['city']
				, 'breweryState' => $establishment['stateFull']
			);
			// set the page information
			$seo = getDynamicSEO($config);
			$array = $seo + array('leftCol' => $str, 'rightCol' => $rightCol);
		}
		// return the output
		return $array;
	}
	
	public function showAddEstablishment($lowCount = false) {	
		// holder for site display
		$array = array();
		// holder for the form text
		$form = '';
		// check if they have already rated this beer			
		// get the user info
		$userInfo = $this->ci->session->userdata('userInfo');
			
		// only included for high counts
		if($lowCount == false) {
			// get the establishment categories
			$categories = $this->ci->BreweriesModel->getAllCategoriesForDropDown();
			// get the states
			$states = $this->ci->StateModel->getAllForDropDown();
			// get the form
			$array = form_addEstablishment(array('categories' => $categories, 'states' => $states));
		}
		// return the array
		return $array;
	}
	
	public function showCreateReview($id, $lowCount = false) {
		// check that the establishment exists
		$establishments = $this->ci->EstablishmentModel->getEstablishmentByID($id);

		// holder for site display
		$array = array();
		// does it exist
		if(!empty($establishments)) {	
			// holder for the form text
			$form = '';
			// check if they have already rated this beer			
			// get the user info
			$userInfo = $this->ci->session->userdata('userInfo');
			
			// holder for the edit text
			$edit = '';
			// only included for high counts
			if($lowCount == false) {
				// query the ratings table
				$rating = $this->ci->EstablishmentModel->checkForRatingByUserIDEstablishmentID($userInfo['id'], $id);			
				// holder for the edit text
				$edit = !empty($rating) ? 'Edit' : '';
				
				if(empty($_POST) && !empty($rating)) {
					// get the form		
					$form = form_estblishmentReview(array('id' => $id, 'rating' => $rating));
				} else {
					// get the form
					$form = form_estblishmentReview(array('id' => $id));
				}
			}
			
			$str = '
				<div id="contents_left">
					<div id="beerReview">
						<h2 class="brown">' . $edit . ' Establishment Review for ' . $establishments['name'] . '</h2>
						<!--<div class="beerPic_review marginTop_8"><img src="' . base_url() . 'page/createImage/' . $id . '/beer" /></div>-->
						<div id="beerInfo" class="marginTop_8">
							<ul>
								<li><a href="' . base_url() . 'establishment/info/rating/' . $establishments['establishmentID'] . '">' . $establishments['name'] . '</a></li>
								<li>' . $establishments['address'] . '</li>
								<li><a href="' . base_url() . 'establishment/city/' . $establishments['stateID'] . '/' . urlencode($establishments['city']) . '">' . $establishments['city'] . '</a>, <a href="' . base_url() . 'establishment/state/' . $establishments['stateID'] . '">' . $establishments['stateAbbr'] . '</a> ' . $establishments['zip'] . '</li>
							</ul>
						</div>
						<br class="both" />
					</div>
			';
			
			$str .= $lowCount == false ? $form . '</div>' : '</div>';
			
			// get configuration values for creating the seo
			$config = array(
				'breweryName' => $establishments['name']
				, 'breweryCity' => $establishments['city']
				, 'breweryState' => $establishments['stateFull']
				, 'seoType' => 'reviewEstablishment'
			);
			// set the page information
			$seo = getDynamicSEO($config);
			$array = $seo + array('leftCol' => $str);
		} else {
			// the beer is not in the db
			// set the page information
			$seo = getSEO();
			// create screen display error
			$str = '<p>The beer that you are trying to review couldn\'t be found.  Sober up and pay attention!</p>';
			// put the two arrays of information together
			$array = $seo + array('leftCol' => $str);						
		}
		// retunr the array
		return $array;
	}
	
	public function getTitle() {
		return $this->title;
	}
}
?>