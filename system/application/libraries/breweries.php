<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Breweries {
	private $ci;
	private $title = 'Breweries';

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
			$str = '<h2 class="brown">Establishments</h2><p>Sorry, but we couldn\'t find any information for the requested brewery.</p>';
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
                                // get to overall average cost of having a beer at the establishment
				$overalAverageCost = $this->ci->BreweriesModel->getOverallAverageCostOfBeerByEstablishmentID($id);
                                // get the percentage of people who would have another
				$haveAnother = $this->ci->BreweriesModel->getHaveAnotherPercent($id);
			}
            
			// create the output for the screen
			// start w/ the brewery information
			// check for a brewery hop
			$breweryHop = !empty($brewery[0]['breweryhopsID']) ? '<a href="' . base_url() . 'brewery/hop/' . $brewery[0]['breweryhopsID'] . '"><img src="' . base_url() . 'images/cone.gif" alt="brewery hop to ' . $brewery[0]['name'] . '" title="brewery hop to ' . $brewery[0]['name'] . '" /></a>' : '';
			$address = empty($brewery[0]['address']) ? '' : '<p>' . $brewery[0]['address'] . '</p>';
			$city = empty($brewery[0]['city']) ? '' : '<a href="' . base_url() . 'establishment/city/' . $brewery[0]['stateID'] . '/' . urlencode($brewery[0]['city']) . '">' . $brewery[0]['city'] . '</a>, ';
			$zip = empty($brewery[0]['zip']) ? '' : $brewery[0]['zip'];
			$url = empty($brewery[0]['url']) ? '' : '<a href="' . $brewery[0]['url'] . '" target="_blank"><img src="' . base_url() . 'images/web.jpg" alt="' . $brewery[0]['name'] . ' web site" title="' . $brewery[0]['name'] . ' web site" /></a>';
			
			// configuration for the image
			$image = array(
				'picture' => $brewery[0]['picture']
				, 'id' => $id
				, 'alt' => $brewery[0]['name']
				, 'approval' => $brewery[0]['pictureApproval']
			);
			// check if the image exists for this brewery
			$img = checkForAnImage($image, $logged, true, 'establishment');

			$str = '
				<div id="breweryInfo" style="position: relative;">
					<h2 class="brown"><a class="brown" href="' . base_url() . 'establishment/info/rating/' . $brewery[0]['id'] . '">' . $brewery[0]['name'] . '</a> ' . $url . ' ' . $breweryHop . ' <a href="' . base_url() . 'establishment/googleMaps/' . $brewery[0]['id'] . '"><img src="' . base_url() . 'images/google-map.png" alt="map for ' . $brewery[0]['name'] . '" title="map for ' . $brewery[0]['name'] . '" /></a>' . showTwitterForEstablishment($brewery[0]['twitter']) . '</h2>
					<div id="establishmentInfo">						
						<p>' . $address . '</p>
						<p>' . $city . '<a href="' . base_url() . 'establishment/state/' . $brewery[0]['stateID'] . '">' . $brewery[0]['stateAbbr'] . '</a> ' . $zip . '</p>
						<p>' . formatPhone($brewery[0]['phone']) . '</p>
			';
            // holder for the number of active beers
            $numActive = 0;
            // holder for the number of retired beers
            $numRetired = 0;
			if(!empty($beers)) {
				$totalBeers = $distinct['totalBeers'] > 1 || $distinct['totalBeers'] < 1 ? '<span class="bold">' . $distinct['totalBeers'] . '</span> different beers' : '<span class="bold">' . $distinct['totalBeers'] . '</span> different beer';
				$avgTotalBeers = $avg['totalBeers'] > 1 || $avg['totalBeers'] < 1 ? 'drank <span class="bold">' . $avg['totalBeers'] . '</span> times' : 'drank <span class="bold">' . $avg['totalBeers'] . '</span> time';
				$str .= '
						<ul class="green" style="margin-top: 1.0em">
							<li class="bold" style="text-decoration: underline;">Beer Review Stats:</li>
							<li>' . $totalBeers . '</li>
							<li>' . $avgTotalBeers . '</li>
							<li>with a <span class="bold">' . number_format($average, 1) . '</span> average rating</li>
							<li>and overall average cost of <span class="bold">$' . number_format($overalAverageCost['averagePrice'], 2) . '</span></li>
						</ul>
				';
                // determine how many of each type of beer there is
                foreach($beers as $beer) {
                    if($beer['retired'] == 1) {
                        $numRetired++;
                    } else {
                        $numActive++;
                    }
                }
			}
			$str .= '
					</div>
					' . $img . '
					<br class="left" />
			';
			$str .= '
				<p class="marginTop_8">
					<a href="' . base_url() . 'establishment/info/rating/' . $brewery[0]['id'] . '">Establishment Hops</a>
					 | <a href="' . base_url() . 'brewery/info/' . $brewery[0]['id'] . '">Beer Reviews</a>
				';
			$str .= '
					<p class="marginTop_8"><a href="' . base_url() . 'beer/addBeer/' . $id . '">Add a Beer</a></p>
				</div>
				<div id="beerTable">
					<table id="activeTable" class="tablesorter">
                        <caption class="gray2">Still Brewing (' . $numActive . ')</caption>
                        <thead>
						    <tr class="gray">
							    <th class="noPointer">&nbsp;</th>
							    <th>Beer</th>
							    <th>Style</th>
							    <th class="center">#Rev</th>
							    <th class="center">RAvg.</th>							
							    <th class="center noPointer">H.A.</th>
							    <th class="noPointer">Avg. Cost</th>
						    </tr>
                        </thead>
                        <tbody>
			';		

			if(empty($beers)) {
				$str .= '
						    <tr><td colspan="7">No beer reviews at this time.</td></tr>
                        </tbody>
					</table>
				</div>
				';
			} else {
				// holder for showing retired beers
				$retired = false;
				// counter for determing background color
				$cnt = 0;
				// iterate through the beers
				foreach($beers as $beer) {
					// see if the beer has an average cost
					//$str_avg = $beer['reviews'] == 0 ? 'N/A' : '';//echo '<pre>'; print_r($avgCost); exit;
                                    $str_avg = '';//echo '<pre>'; print_r($avgCost); exit;
                                    if(!empty($avgCost)) {
                                        foreach($avgCost as $cost) {
                                            if($cost['id'] == $beer['id']) {
                                                if(!empty($cost['averagePrice'])) {
                                                    // there is a match so create the output
                                                    if(!empty($str_avg)) {
                                                        $str_avg .= '<br />';
                                                    }
                                                    $serving = $cost['totalServings'] > 1 || $cost['totalServings'] < 1 ? ' servings' : ' serving';
                                                    if(stristr($str_avg, 'No cost data')) {
                                                        $str_avg = '';
                                                    }
                                                    $str_avg .= '$' . $cost['averagePrice'] . ', ' . $cost['totalServings'] . $serving . ', ' . $cost['package'] . 's';
                                                } else {
                                                    if(empty($str_avg)) {
                                                        $str_avg = 'No cost data';
                                                    }
                                                }
                                            } else {
                                                if(empty($str_avg)) {
                                                    $str_avg = 'No cost data';
                                                }
                                            }
                                        }
                                    } else {
                                        if(empty($str_avg)) {
                                            $str_avg = 'No cost data';
                                        }
                                    }
					// determine the percent of have another
					$str_ha = $beer['reviews'] == 0 ? 'N/A' : '0%';
					foreach($haveAnother as $ha) {
						if($ha['id'] == $beer['id']) { 
							// there is a match so create the output
							$str_ha = ($ha['percentHaveAnother'] * 100) . '%';
						}
					}
					$averagereview = (float) $beer['averagereview'];
					$class = $cnt % 2 == 1 ? ' class="gray"' : '';
					
					// check if retired text is needed
					if($beer['retired'] == '1' && $retired === false) {
						// show the retired row in the table
						$str .= '
                        </tbody>
                    </table>
                    <table id="retiredTable" class="tablesorter">
						<caption class="gray2">Dried Up Suds (' . $numRetired . ')</caption>
                        <thead>
                            <tr class="gray">
                                <th class="noPointer">&nbsp;</th>
                                <th>Beer</th>
                                <th>Style</th>
                                <th class="center">#Rev</th>
                                <th class="center">RAvg.</th>                            
                                <th class="center noPointer">H.A.</th>
                                <th class="noPointer">Avg. Cost</th>
                            </tr>
                        </thead>
                        <tbody>
						';
						// change holder to not show the text again
						$retired = true;
					}
					                          //<tr' . $class . '>
					$str .= '
						    <tr>
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
                        </tbody>
					</table>
				</div>
				';
			}

			// holder for right column text
			$rightCol = '';
            
            // get twitter if it exists
            // set the configuration values for the method
            $configTwitter = array(
                'establishment' => $brewery[0]
                , 'type' => 'establishmentHome'
            );
            // call the method to get the string of text
            $rightCol = addSocialMedia($configTwitter);            
            
			// get the highest rated breweries
			$highestRatedBreweries = $this->ci->BreweriesModel->getHighestRatedBreweries();
			// check if there were any results
			if(!empty($highestRatedBreweries)) {
				$rightCol .= '
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
			// get the overall average cost by cheapest
			$averageCostPerBrewery = $this->ci->BreweriesModel->getOverallAverageCostOfBeer('ASC');
			// check if there were any results
			if(!empty($averageCostPerBrewery)) {
				$rightCol .= '
					<h4><span>Least Expensive Breweries</span></h4>
					<ul>
				';
				// iterate over the results
				foreach($averageCostPerBrewery as $ac) {
					// get the wording
					$ts = $ac['totalServings'] == 1 ? ' total serving' : ' total servings';
					// add another item
					$rightCol .= '
						<li>
							<p><a href="' . base_url() . 'brewery/info/' . $ac['id'] . '">' . $ac['name'] . '</a></p>
							<p class="rightBreweryLink"><span class="bold">$' . number_format($ac['averagePrice'], 2) . '</span> for <span class="bold">' . $ac['totalServings'] . '</span>' . $ts . '</p>
						</li>';
				}
				// finish off the text
				$rightCol .= '
					</ul>
				';
			}
			// get the overall average cost by most expensive
			$averageCostPerBrewery = $this->ci->BreweriesModel->getOverallAverageCostOfBeer('DESC');
			// check if there were any results
			if(!empty($averageCostPerBrewery)) {
				$rightCol .= '
					<h4><span>Most Expensive Breweries</span></h4>
					<ul>
				';
				// iterate over the results
				foreach($averageCostPerBrewery as $ac) {
					// get the wording
					$ts = $ac['totalServings'] == 1 ? ' total serving' : ' total servings';
					// add another item
					$rightCol .= '
						<li>
							<p><a href="' . base_url() . 'brewery/info/' . $ac['id'] . '">' . $ac['name'] . '</a></p>
							<p class="rightBreweryLink"><span class="bold">$' . number_format($ac['averagePrice'], 2) . '</span> for <span class="bold">' . $ac['totalServings'] . '</span>' . $ts . '</p>
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
				$str .= '<li><a href="' . base_url() . 'establishment/state/' . $state['id'] . '">' . $state['stateFull'] . '</a></li>';
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
						<p><a href="' . base_url() . 'establishment/info/rating/' . $highRating['id'] . '">' . $highRating['name'] . '</a> in <a href="' . base_url() . 'establishment/city/' . $highRating['stateID'] . '/' . urlencode($highRating['city']) . '">' . $highRating['city'] . '</a>, <a href="' . base_url() . 'establishment/state/' . $highRating['stateID'] . '">' . $highRating['stateAbbr'] . '</a></p>
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
						<p><a href="' . base_url() . 'establishment/info/rating/' . $item['id'] . '">' . $item['name'] . '</a></p>
						<p class="rightBreweryLink"> in <a href="' . base_url() . 'establishment/city/' . $item['stateID'] . '/' . urlencode($item['city']) . '">' . $item['city'] . '</a>, <a href="' . base_url() . 'establishment/state/' . $item['stateID'] . '">' . $item['stateFull'] . '</a></p>
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
							<p><a href="' . base_url() . 'establishment/info/rating/' . $item['id'] . '">' . $item['name'] . '</a></p>
							<p class="rightBreweryLink">in <a href="' . base_url() . 'establishment/city/' . $item['stateID'] . '/' . urlencode($item['city']) . '">' . $item['city'] . '</a>, <a href="' . base_url() . 'establishment/state/' . $item['stateID'] . '">' . $item['stateFull'] . '</a></p>
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
		$breweryhop = $this->ci->BreweriesModel->getAllBrewreyHops(NUMBER_HOPS_TO_SHOW);
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
	
	public function showBreweriesCity($state, $city) {
		// get the brewery information
		$breweryCity = $this->ci->BreweriesModel->getBreweryByCity($state, $city);
		//echo '<pre>'; print_r($breweryCity); echo '</pre>';exit;
		
		// holder for the output
		$array = array();
		// check to see if there were any results
		if(empty($breweryCity)) {
			// there were no results for the given city
			$array = '<p>There were no breweries matching the request.</p>';
		} else {			
			// there are some breweries			
			// holder for the class depending on if there is an image
			$str = '
				<h2 class="brown">Breweries in ' . $breweryCity[0]['city'] . ', ' . $breweryCity[0]['stateAbbr'] . '</h2>
				<div id="beerTable">
				<table class="marginTop_8">
					<tr class="gray2">
						<th>Name</th>
						<th>Overall Rating</th>
						<th>Have Another</th>
					</tr>
			';
			// counter to determine the color of the row
			$cnt = 0;
			// iterate through the results
			foreach($breweryCity as $establishment) {//echo '<pre>'; print_r($establishment); exit;
				// get the percentage of people who would have another
				$haveAnother = $this->ci->BreweriesModel->getHaveAnotherPercentByEstablishment($establishment['establishmentID']);
				// get the total number of beers and the average rating
				// based on brewery id
				$avg = $this->ci->BreweriesModel->getTotalEachBeer($establishment['establishmentID']);
				// determine the average score for the entire group
				$average = $avg['totalBeers'] > 0 ? round((float) ($avg['totalPoints'] / $avg['totalBeers']), 1) : '0.0';
				// determine the percent of have another
				$str_ha = '0 beers rated';
				if(key_exists('totalBeers', $haveAnother)) {
					$str_be = $haveAnother['totalBeers'] > 1 ? $haveAnother['totalBeers'] . ' beers' : $haveAnother['totalBeers'] . ' beer';
					$str_td = $haveAnother['totalDrank'] > 1 ? $haveAnother['totalDrank'] . ' times' : $haveAnother['totalDrank'] . ' time';
					$str_ha = ($haveAnother['percentHaveAnother'] * 100) . '% on ' . $str_be . ' rated ' . $str_td;
				}
				// get the class of the row
				$class = ($cnt % 2 == 0) ? '' : ' class="gray"';
				$str .= '
					<tr' . $class . '>
						<td>
							<a href="' . base_url() . 'brewery/info/' . $establishment['establishmentID'] . '">' . $establishment['name'] . '</a><br />
							' . $establishment['address'] . '<br />
							<a href="' . base_url() . 'establishment/city/' . $establishment['stateID'] . '/' . urlencode($establishment['city']) . '">' . $establishment['city'] . '</a>, <a href="' . base_url() . 'establishment/state/' . $establishment['stateID'] . '">' . $establishment['stateAbbr'] . '</a> ' . $establishment['zip'] . '<br />
							' . formatPhone($establishment['phone']) . '
						</td>
						<td>' . number_format($average, 1) . '</td>
						<td>' . $str_ha . '</td>
					</tr>
				';
				/*if(!empty($establishment['picture']) && $establishment['pictureApproval'] == 1) {
					$str .= '
						<img src="' . base_url() . 'images/establishments/' . $establishment['picture'] . '" title="' . $establishment['name'] . ', ' . $establishment['city'] . ', ' . $establishment['stateAbbr'] . '" alt="' . $establishment['name'] . ', ' . $establishment['city'] . ', ' . $establishment['stateAbbr'] . '" />
					';
					$class = ' class="brewery_float_right"';
				}*/
				// increment the counter
				$cnt++;
			}
			// finish off the text
			$str .= '
				</table>
				</div>
			';
			
			// get configuration values for creating the seo
			$config = array(
				'breweryName' => $establishment['name']
				, 'breweryCity' => $establishment['city']
				, 'breweryState' => $establishment['stateFull']
				, 'seoType' => 'establishmentByCity'
			);
			// set the page information
			$seo = getDynamicSEO($config);
			$array = $seo + array('str' => $str);
		}
		// return the output
		return $array;
	}
	
	public function showBreweriesState($state) {
		// get the brewery information
		$breweryState = $this->ci->BreweriesModel->getBreweryByState($state);
				
		// holder for the output
		$array = array();
		// check to see if there were any results
		if(empty($breweryState)) {
			// there were no results for the given city
			$array = false;
		} else {			
			// there are some breweries			
			// holder for the class depending on if there is an image
			$str = '
				<h2>Breweries in ' . $breweryState[0]['stateFull'] . '</h2>
				<table>
					<tr>
						<th>Name</th>
						<th>Address</th>
						<th>City</th>
						<th>Phone</th>
						<th>Overall Rating</th>
						<th>Have Another</th>
					</tr>
			';
			// iterate through the results
			foreach($breweryState as $establishment) {
				// get the percentage of people who would have another
				$haveAnother = $this->ci->BreweriesModel->getHaveAnotherPercentByEstablishment($establishment['establishmentID']);
				// get the total number of beers and the average rating
				// based on brewery id
				$avg = $this->ci->BreweriesModel->getTotalEachBeer($establishment['establishmentID']);
				// determine the average score for the entire group
				$average = $avg['totalBeers'] > 0 ? round((float) ($avg['totalPoints'] / $avg['totalBeers']), 1) : '0.0';
				// determine the percent of have another
				$str_ha = '0 beers rated';
				if(key_exists('totalBeers', $haveAnother)) {
					$str_be = $haveAnother['totalBeers'] > 1 ? $haveAnother['totalBeers'] . ' beers' : $haveAnother['totalBeers'] . ' beer';
					$str_td = $haveAnother['totalDrank'] > 1 ? $haveAnother['totalDrank'] . ' times' : $haveAnother['totalDrank'] . ' time';
					$str_ha = ($haveAnother['percentHaveAnother'] * 100) . '% on ' . $str_be . ' rated ' . $str_td;
				}				
				
				$str .= '
					<tr>
						<td><a href="' . base_url() . 'brewery/info/' . $establishment['establishmentID'] . '">' . $establishment['name'] . '</a></td>
						<td>' . $establishment['address'] . '</td>
						<td><a href="' . base_url() . 'establishment/city/' . $establishment['stateID'] . '/' . urlencode($establishment['city']) . '">' . $establishment['city'] . '</a></td>
						<td>' . formatPhone($establishment['phone']) . '</td>
						<td>' . number_format($average, 1) . '</td>
						<td>' . $str_ha . '</td>
					</tr>
				';
			}
			// finish off the text
			$str .= '
				</table>
			';
			
			// get configuration values for creating the seo
			$config = array(
				'breweryName' => $establishment['name']
				, 'breweryCity' => $establishment['city']
				, 'breweryState' => $establishment['stateFull']
				, 'seoType' => 'establishmentByState'
			);
			// set the page information
			$seo = getDynamicSEO($config);
			$array = $seo + array('str' => $str);
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
			$array['leftCol'] = '
				<h2 class="brown">Add Establishment</h2>' .
				form_addEstablishment(array('categories' => $categories, 'states' => $states))
			;
		}
		// set the right column information
		$array['rightCol'] = '
			<h4><span>Create Carefully</span></h4>
			<ul>
				<li>Two Beer Dudes is about American craft beer, please make sure to add American establishments.</li>
				<li>Please check that the establishment doesn&#39;t exist already.  Duplicates create confussion.</li>
			</ul>
		';
		// return the array
		return $array;
	}
	
	public function getTitle() {
		return $this->title;
	}
}
?>