<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Beers {
	private $ci;
	private $title = 'Beers';
	
	public function __construct() {
		$this->ci =& get_instance();
	}
	
	public function getAllBeers() {
		// get all the beers
		$beers = $this->ci->BeerModel->getAll();
		// start the output
		$str = '';
		// iterte through the list
		foreach($beers as $beer) {
			$alcoholContent = empty($beer['alcoholContent']) ? '&nbsp;' : $beer['alcoholContent'] . '%';
			$seasonal = empty($beer['seasonal']) ? 'No' : 'Yes';
			$img = checkForImage(array('picture' => $beer['picture'], 'id' => $beer['id']));
			$str .= '
					<div id="item_' . $beer['id'] . '" class="item">
						<div id="item_list_container_' . $beer['id'] . '" class="list_itemContainer">					
							' . $img . '
							<dl class="dl_tableDisplay">
								<dt>Beer:</dt><dd>' . $beer['beerName'] . '</dd>
								<dt>Brewery:</dt><dd><a href="' . $beer['url'] . '" target="_blank">' . $beer['name'] . '</a></dd>
								<dt>Style:</dt><dd>' . $beer['style'] . '</dd>
								<dt>ABV:</dt><dd>' . $alcoholContent . '</dd>
			';
			$str .= empty($beer['malts']) ? '' : '<dt>Malts:</dt><dd>' . $beer['malts'] . '</dd>';
			$str .= empty($beer['hops']) ? '' : '<dt>Hops:</dt><dd>' . $beer['hops'] . '</dd>';
			$str .= empty($beer['yeast']) ? '' : '<dt>Yeast:</dt><dd>' . $beer['yeast'] . '</dd>';
			$str .= empty($beer['gravity']) ? '' : '<dt>Gravity:</dt><dd>' . $beer['gravity'] . '</dd>';
			$str .= empty($beer['ibu']) ? '' : '<dt>IBU:</dt><dd>' . $beer['ibu'] . '</dd>';
			$str .= empty($beer['food']) ? '' : '<dt>Food:</dt><dd>' . $beer['food'] . '</dd>';
			$str .= empty($beer['glassware']) ? '' : '<dt>Glassware:</dt><dd>' . $beer['glassware'] . '</dd>';
			$str .= '
								<dt>Seasonal:</dt><dd>' . $seasonal
			;
			$str .= $seasonal == 'No' ? '' : ' - ' . $beer['seasonalPeriod'];
			$str .= '
								</dd>
							</dl>
							<br class="both" />
						</div>
						<ul id="list_links_' . $beer['id'] . '" class="list_horizontalLinks">
							<li><a href="#" id="edit_' . $beer['id'] . '" onclick="new Ajax.Request(\'' . base_url() . 'ajax/edit/beer/' . $beer['id'] . '\', {asynchronous: true, evalScripts: true, method: \'post\', onLoading: function() {showSpinner(\'item_list_container_' . $beer['id'] . '\');}, onComplete: function(response) {$(\'item_list_container_' . $beer['id'] . '\').update(response.responseText); $(\'edit_' . $beer['id'] . '\').style.display=\'none\'; $(\'cancel_' . $beer['id'] . '\').style.display=\'block\';}}); return false;">Edit</a></li>
							<li><a href="#" id="cancel_' . $beer['id'] . '" onclick="new Ajax.Request(\'' . base_url() . 'ajax/cancel/beer/' . $beer['id'] . '\', {asynchronous: true, evalScripts: true, method: \'get\', onLoading: function() {showSpinner(\'item_list_container_' . $beer['id'] . '\');}, onComplete: function(response) {$(\'item_list_container_' . $beer['id'] . '\').update(response.responseText); $(\'cancel_' . $beer['id'] . '\').style.display=\'none\'; $(\'edit_' . $beer['id'] . '\').style.display = \'block\';}}); return false;" style="display: none;">Cancel</a></li>
						</ul>
						<br class="both" />
					</div>
			';
		}
		// return the output
		return $str;
	}
	
	public function getBeerByID($id) {
		// get the specific brewery
		$beer = $this->ci->BeerModel->getBeerByID($id);
		$alcoholContent = empty($beer['alcoholContent']) ? '&nbsp;' : $beer['alcoholContent'] . '%';
		$seasonal = empty($beer['seasonal']) ? 'No' : 'Yes';
		$img = checkForImage(array('picture' => $beer['picture'], 'id' => $id));
		$str = $img . '
					<dl class="dl_tableDisplay">
						<dt>Beer:</dt><dd>' . $beer['beerName'] . '</dd>
						<dt>Brewery:</dt><dd><a href="' . $beer['url'] . '" target="_blank">' . $beer['name'] . '</a></dd>
						<dt>Style:</dt><dd>' . $beer['style'] . '</dd>
						<dt>ABV:</dt><dd>' . $alcoholContent . '</dd>
		';
		$str .= empty($beer['malts']) ? '' : '<dt>Malts:</dt><dd>' . $beer['malts'] . '</dd>';
		$str .= empty($beer['hops']) ? '' : '<dt>Hops:</dt><dd>' . $beer['hops'] . '</dd>';
		$str .= empty($beer['yeast']) ? '' : '<dt>Yeast:</dt><dd>' . $beer['yeast'] . '</dd>';
		$str .= empty($beer['gravity']) ? '' : '<dt>Gravity:</dt><dd>' . $beer['gravity'] . '</dd>';
		$str .= empty($beer['ibu']) ? '' : '<dt>IBU:</dt><dd>' . $beer['ibu'] . '</dd>';
		$str .= empty($beer['food']) ? '' : '<dt>Food:</dt><dd>' . $beer['food'] . '</dd>';
		$str .= empty($beer['glassware']) ? '' : '<dt>Glassware:</dt><dd>' . $beer['glassware'] . '</dd>';
		$str .= '
						<dt>Seasonal:</dt><dd>' . $seasonal . '
		';
		$str .= $seasonal == 'No' ? '' : ' - ' . $beer['seasonalPeriod'];
		$str .= '
						</dd>
					</dl>
					<br class="left" />
		';
		return $str;
	}
	
	public function createForm($config) {
		$beer = array(
			'beerName' => ''
			, 'id' => ''
			, 'alcoholContent' => ''
			, 'malts' => ''
			, 'hops' => ''
			, 'yeast' => ''
			, 'gravity' => ''
			, 'ibu' => ''
			, 'food' => ''
			, 'glassware' => ''
			, 'picture' => ''
			, 'seasonal' => ''
			, 'seasonalPeriod' => ''
			, 'establishmentID' => ''
			, 'styleID' => ''
			, 'btnValue' => 'Add'
			, 'action' => 'action="' . base_url() . 'ajax/addData/brewery/"'
			, 'onsubmit' => 'onsubmit="new Ajax.Request(\'' . base_url() . 'ajax/addData/beer\', {asynchronous: true, evalScripts: true, method: \'post\', parameters: Form.serialize(this), onLoading: function() {showSpinner(\'contents\');}, onComplete: function(response) {$(\'contents\').update(response.responseText);}}); return false;"'
		);
	
		if(key_exists('establishmentID', $config)) {
			$beer['onsubmit'] = 'onsubmit="new Ajax.Request(\'' . base_url() . 'ajax/addData/beer/' . $config['establishmentID'] . '\', {asynchronous: true, evalScripts: true, method: \'post\', parameters: Form.serialize(this), onLoading: function() {showSpinner(\'contents\');}, onComplete: function(response) {$(\'contents\').update(response.responseText);}}); return false;"';
		}
			
		if(key_exists('id', $config)) {
			$beer = $this->ci->BeerModel->getBeerByID($config['id']);
			$beer['btnValue'] = 'Update';
			$beer['action'] = 'action="' . base_url() . 'ajax/editData/beer/' . $config['id'] . '"';
			$beer['onsubmit'] = 'onsubmit="new Ajax.Request(\'' . base_url() . 'ajax/editData/beer/' . $config['id'] . '\', {asynchronous: true, evalScripts: true, method: \'post\', parameters: Form.serialize(this), onLoading: function() {showSpinner(\'item_list_container_' . $beer['id'] . '\');}, onComplete: function(response) {$(\'item_list_container_' . $config['id'] . '\').update(response.responseText); $(\'cancel_' . $config['id'] . '\').style.display=\'none\'; $(\'edit_' . $config['id'] . '\').style.display = \'block\';}}); return false;"';
		}

		$breweryDropDown = '';
		if(key_exists('breweries', $config)) {
			$array = array(
				'data' => $config['breweries']
			, 'id' => 'slt_brewery'
			, 'name' => 'slt_brewery'
			, 'selected' => $beer['establishmentID']
			);
			$breweryDropDown = '<label for="slt_brewery">Brewery:</label>' . createDropDown($array);
		}

		$styleDropDown = '';
		if(key_exists('styles', $config)) {
			unset($array);
			$array = array(
				'data' => $config['styles']
			, 'id' => 'slt_style'
			, 'name' => 'slt_style'
			, 'selected' => $beer['styleID']
			);
			$styleDropDown = '<label for="slt_style">Style:</label>' . createDropDown($array);
		}

		$seasonalDropDown = '';
		//if(key_exists('seasonal', $config)) {
		unset($array);
		$array = array(
			'data' => array(array('id' => '0', 'name' => 'No'), array('id' => '1', 'name' => 'Yes'))
			, 'id' => 'slt_seasonal'
			, 'name' => 'slt_seasonal'
			, 'selected' => $beer['seasonal']
			, 'onchange' => 'onchange="hideShowBasedOnAnother(this, $(\'sp\'));"'
		);
		$seasonalDropDown = '<label for="slt_seasonal">Seasonal:</label>' . createDropDown($array);
		// determine if to show the seaonalPeriod input
		$showSeasonPeriod = $beer['seasonal'] == 0 ? ' style="display: none;"' : '';

		$str = '
			<form id="editBeerForm" class="edit" method="post" ' . $beer['action'] . ' ' . $beer['onsubmit'] . '>
				<label for="txt_beer">Beer:</label>
				<input type="text" id="txt_beer" name="txt_beer" value="' . $beer['beerName'] . '" />				
				
				' . $breweryDropDown . '				
				
				' . $styleDropDown . '
				
				<label for="txt_abv">ABV:</label>
				<input type="text" id="txt_abv" name="txt_abv" value="' . $beer['alcoholContent'] . '" />
				
				<label for="txt_malts">Malts:</label>
				<input type="text" id="txt_malts" name="txt_malts" value="' . $beer['malts'] . '" />
				
				<label for="txt_hops">Hops:</label>
				<input type="text" id="txt_hops" name="txt_hops" value="' . $beer['hops'] . '" />
				
				<label for="txt_yeast">Yeast:</label>
				<input type="text" id="txt_yeast" name="txt_yeast" value="' . $beer['yeast'] . '" />
				
				<label for="txt_gravity">Gravity:</label>
				<input type="text" id="txt_gravity" name="txt_gravity" value="' . $beer['gravity'] . '" />
				
				<label for="txt_ibu">IBU:</label>
				<input type="text" id="txt_ibu" name="txt_ibu" value="' . $beer['ibu'] . '" />
				
				<label for="txt_food">Food:</label>
				<input type="text" id="txt_food" name="txt_food" value="' . $beer['food'] . '" />
				
				<label for="txt_glassware">Glassware:</label>
				<input type="text" id="txt_glassware" name="txt_glassware" value="' . $beer['glassware'] . '" />
				
				' . $seasonalDropDown . '
				
				<span id="sp"' . $showSeasonPeriod . '> 
					<label for="txt_seasonalPeriod">Seasonal Period:</label>
					<input type="text" id="txt_seasonalPeriod" name="txt_seasonalPeriod" value="' . $beer['seasonalPeriod'] . '" />
				</span>
				
				<input type="submit" id="btn_submit" name="btn_submit" value="' . $beer['btnValue'] . '" />
		';
		$str .= key_exists('hidden', $config) && $config['hidden'] == 'rating' ? '<input type="hidden" id="hdn_step" name="hdn_step" value="rate" />' : '';
		$str .= '
			</form>
		';	
		return $str;
	}
	
    public function showBeerRatings($id, $ajax = false, $logged = false) {
        // get ratings for this specific beer
        $beers = $this->ci->BeerModel->getBeerRatingsByID($id);
        //echo '<pre>'; print_r($beers); exit;
        // determine if the beer has been rated by rich and scot
        $twoBeerDudes = $this->ci->BeerModel->tastedTwoBeerDudes($id);
        //echo '<pre>'; print_r($beers); echo '</pre>'; exit;
        // holder for the style id
        $styleID = '';
        // set up some holder values
        $ratingTotalTimes = 0;
        // holder for the beer output
        $str = '';
        // holder for the right column output
        $str_rightCol = '';
        // holder for output w/out ajax
        $array = array();
        $have = '';
        $avgCost = 0;
        // check to see if there has at least one rating
        if(!empty($beers[0]['formatDateAdded'])) {
            // check if this is an ajax call back
            if($ajax === false) {
                // get the rating totals to get an average
                $ratingInfo = $this->ci->BeerModel->getBeerRating($id);
                //echo '<pre>'; print_r($ratingInfo); echo '</pre>'; exit;
                // calculate the ratings average
                $ratingAverage =  number_format(round($ratingInfo['averagerating'], 1), 1);
                //echo '<pre>'; print_r($ratingAverage); echo '</pre>'; exit;
                // the total number of times the beer has been rated
                $ratingTotalTimes = $ratingInfo['timesrated'];
                // get the types of packages the beer has come in
                $packageCount = $this->ci->BeerModel->getPackageCount($id);
                // get the avg cost per package of beer drank for the brewery
                $avgCost = $this->ci->BeerModel->getAvgCostPerPackage($id);
                // get the percentage of people who would have another
                $have = $haveAnother = $this->ci->BeerModel->getHaveAnotherPercent($id);
                // get the average rating for the last 'x' ratings - trending
                $trending = $this->ci->BeerModel->getTrendByBeerID($id);
                //echo '<pre>'; print_r($avgCost); echo '</pre>'; exit;
            }
            // get the style of beer
            $styleID = $beers[0]['styleID'];
        } else {
            // get the style of beer
            $styleID = $this->ci->BeerModel->getStyleIDByBeerID($id);
        }

            // check if this is an ajax call back
            if($ajax === false) {
                    // configuration for the image
                    $image = array(
                            'picture' => $beers[0]['picture']
                            , 'id' => $beers[0]['id']
                            , 'alt' => $beers[0]['beerName'] . ' - ' . $beers[0]['name']
                    );
                    // check if the image exists for this beer
                    $img = checkForAnImage($image, $logged, true);

                    // create the output for the screen
                    // start w/ the brewery information
                    $str_ratingTotalTimes = $ratingTotalTimes > 1 ? $ratingTotalTimes . ' dudes' : $ratingTotalTimes . ' dude';
                    // check for a brewery hop
                    $breweryHop = !empty($beers[0]['breweryhopsID']) ? ' <a href="' . base_url() . 'brewery/hop/' . $beers[0]['breweryhopsID'] . '"><img src="' . base_url() . 'images/cone.gif" title="brewery hop to ' . $beers[0]['name'] . '" alt="brewery hop to ' . $beers[0]['name'] . '" /></a>' : '';
                    // check for ratings
                    $str_dudes = '';
                    if($ratingTotalTimes > 0) {
                            $str_dudes = '<span class="score_medium">' . $ratingAverage . '</span>/10 by ' . $str_ratingTotalTimes;
                    } else {
                        $str_dudes = 'No Ratings';
                    }
//echo '<pre>'; print_r($trending); exit;
                    // holder string
                    $str_twodudes = '';
                    // check to see if the dudes rated it
                    if(!empty($twoBeerDudes)) {
                            $str_twodudes = '<span class="score_medium">' . number_format($twoBeerDudes['avergeRating'], 1) . '</span>/10';
                    } else {
                            $str_twodudes = 'Not Rated';
                    }
                    
                    // holder for trend
                    $str_trend = '';
                    if($ratingTotalTimes > 0) {
                        $str_trend = '<span class="score_medium">' . number_format($trending['trendrating'], 1) . '</span>/10';
                    }

                    // get the session information
                    $userInfo = $this->ci->session->userdata('userInfo');
                    // holder for beer review information
                    $createReview = '';
                    // holder for the name, which includes updating
                    $updating = '';
                    // see if the user is logged in
                    if ($userInfo != FALSE && $beers[0]['closed'] != 1)
                    {
                            $createReview = '
                                    <p class="marginTop_8 bold">
                                            <a href="' . base_url() . 'beer/createReview/' . $beers[0]['id'] . '">Add a Beer Review</a>
                                            or <a href="' . base_url() . 'beer/createReview/' . $beers[0]['id'] . '/short">Add a Short Beer Review</a>
                                    </p>
                            ';
                            // show the ability to make updates
                            $updating = '<a href="' . base_url() . 'page/updateInfo/beer/' . $id . '"><img src="' . base_url() . 'images/update.gif" title="update beer information" alt="update beer information" /></a> ';
                    }
                    else
                    {
            // substr($this->ci->uri->uri_string(), 1);
            $array = array(
                                    'uri' => $this->ci->uri->uri_string()
                                    , 'search' => array('/')
                                    , 'replace' => '_'
                            );
                            $args = swapOutURI($array);
                            if ($beers[0]['closed'] != 1)
                            {
                                $createReview = '
                                    <p class="marginTop_8 bold">
                                            <a href="' . base_url() . 'user/login/' . $args . '">Add a Beer Review</a>
                                            or <a href="' . base_url() . 'user/login/' . $args . '">Add a Short Beer Review</a>
                                    </p>
                                ';
                            }
                    }
                    //echo '<pre>'; print_r($beers); exit;
                    // holder for retired text
                    $retired = '';
                    // checked if the beer is retired or the establishment is closed
                    if ($beers[0]['retired'] == '1' && $beers[0]['closed'] == '1')
                    {
                        $retired = '<p class="retired">Retired, no longer in production AND the brewery is closed</p>';
                    }
                    elseif ($beers[0]['retired'] == '1')
                    {
                            $retired = '<p class="retired">Retired, no longer in production</p>';
                    }
                    elseif ($beers[0]['closed'] == 1)
                    {
                        $retired = '<p class="retired">Retired, the brewery is no longer open</p>';
                    }
                    // holder for alias text
                    $alias = '';
                    // check if the beer has an alias name
                    if(!empty($beers[0]['alias'])) {
                        $alias = '<p class="alias brown">Formally known as: ' . $beers[0]['alias'] . '</p>';
                    }
                    
                    // checked if the url exists for the brewery
                    $brewery_url = (empty($beers[0]['url']) ? '' : '<a style="margin-left: 10px;" href="' . $beers[0]['url'] . '" target="_blank"><img src="' . base_url() . 'images/web.jpg" title="' . $beers[0]['name'] . ' web site" alt="' . $beers[0]['name'] . ' web site" /></a>');
                    
                    $str = '
                                    <div id="beerReview">
                                            <div id="beerInfoContainer">
                                                    ' . $img . '
                                                    <div id="beerInfo">
                                                            <h2 class="brown">' . $updating . $beers[0]['beerName'] . '</h2>
                                                            ' . $alias . '
                                                            ' . $retired . '
                                                            <div class="establishmentLocation bold">
                                                                    <p>
                                                                            <a href="' . base_url() . 'brewery/info/' . $beers[0]['establishmentID'] . '">' . $beers[0]['name'] . '</a> 
                                                                            ' . $brewery_url . ' 
                                                                            ' . $breweryHop . '
                                                                            <a href="' . base_url() . 'establishment/googleMaps/' . $beers[0]['establishmentID'] . '"><img src="' . base_url() . 'images/google-map.png" alt="map for ' . $beers[0]['name'] . '" title="map for ' . $beers[0]['name'] . '" /></a>
                                                                            ' . showTwitterForEstablishment($beers[0]['twitter']) . '
                                                                    </p>
                                                                    <p>
                                                                            <a href="' . base_url() . 'establishment/city/' . $beers[0]['stateID'] . '/' . urlencode($beers[0]['city']) . '">' . $beers[0]['city'] . '</a>,
                                                                            <a href="' . base_url() . 'establishment/state/' . $beers[0]['stateID'] . '">' . $beers[0]['stateAbbr'] . '</a>
                                                                    </p>
                                                            </div>

                                                            <p class="marginTop_4"><span class="bold">Overall:</span> ' . $str_dudes . '</p>
                                                            <p class="marginTop_4"><span class="bold">Dudes:</span> ' . $str_twodudes . '</p>
                                                            <p class="marginTop_4"><span class="bold">Trend:</span> ' . $str_trend . ' over past ' . TREND_BEER_RATING_LIMIT . ' reviews <a href="' . base_url() . 'graph/graph.php?id=' . $id . '&KeepThis=true&TB_iframe=true&height=350&width=500" class="thickbox"><img src="' . base_url() . 'images/chart1.png" /></a></p>
                                                            <p class="marginTop_4"><span class="bold">Style:</span> <a href="' . base_url() . 'beer/style/' . $beers[0]['styleID'] . '">' . $beers[0]['style'] . '</a></p>
                    ';
                    
                    // add ABV if available
                    $str .= !empty($beers[0]['alcoholContent']) ? '<p class="marginTop_4"><span class="bold">ABV:</span> ' . $beers[0]['alcoholContent'] . '%</p>' : '';

                    // add IBU if available
                    $str .= !empty($beers[0]['ibu']) ? '<p class="marginTop_4"><span class="bold">IBU:</span> ' . $beers[0]['ibu'] . '</p>' : '';

                    // iterate through the different packages
                    if(!empty($beers[0]['formatDateAdded'])) {
                            // check seasonal avalability
                            $seasonal = $beers[0]['seasonal'] == 1 ? 'Yes' : 'No';
                            $str .= $seasonal == 'No' ? '<p id="seasonal marginTop_4"><span class="bold">Seasonal:</span> Year Round</p>' : '<p id="seasonal"><span class="bold">Seasonal:</span> ' . $beers[0]['seasonalPeriod'] . '</p>';

/*
                            $str .= '<div id="packageinfo" class="marginTop_8"><h3 class="brown">Cost Breakdown</h3>';
                            if(count($avgCost) > 0) {
                                    foreach($avgCost as $cost) {
                                            // there is a match so create the output
                                            $serving = $cost['totalServings'] > 1 || $cost['totalServings'] < 1 ? ' servings' : ' serving';
                                            $str .= '<p>$' . $cost['averagePrice'] . ', ' . $cost['totalServings'] . $serving . ', ' . $cost['package'] . 's</p>';
                                    }					
                            } else {
                                    $str .= '<p>No cost data.</p>';
                            }
                            $str .= '</div>';
*/
                            $str .= '<div id="haveAnother" class="marginTop_8">';
                            foreach($haveAnother as $ha) {
                                    // get the percent
                                    $percentHaveAnother = ($ha['percentHaveAnother'] * 100);
                                    // determine which image to use
                                    $thumb = $percentHaveAnother >= 50 ? 'yes' : 'no';
                                    // there is a match so create the output
                                    $str .= '<h3 class="brown">Have Another</h3><p><img style="vertical-align: middle;" src="' . base_url() . 'images/haveanother_' . $thumb . '25.jpg" width="25" height="25" alt="" /> ' . $percentHaveAnother . '%</p>';
                            }
                            $str .= '</div>';
                    }
                    $str .= $createReview . '
                                                            <br class="both" />
                                                    </div>
                                                    <br class="both" />
                                            </div>
                    ';

                    // make sure we have ratings to show
                    if(!empty($beers[0]['formatDateAdded'])) {
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
                                                    $class = $i % 2 == 1 ? ' class="bg3"' : ' class="bg1"';
                                                    $str_similar .= '
                                                                            <tr' . $class . '>
                                                                                    <td width="70%"><a href="' . base_url() . 'beer/review/' . $key['id'] . '">' . $key['beerName'] . '</a></td>
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

                                    // check for user image
                                    $userImage = ($beer['avatar'] == 1 && !empty($beer['avatarImage'])) ? 'images/avatars/' . $beer['avatarImage'] : 'images/fakepic.png';

                                    // calculate the rating 
                                    $ratingCalc = number_format((($beer['aroma'] * (PERCENT_AROMA / 100)) + ($beer['taste'] * (PERCENT_TASTE / 100)) + ($beer['look'] * (PERCENT_LOOK / 100)) + ($beer['drinkability'] * (PERCENT_DRINKABILITY / 100))), 1);
                                    // check for the type of rating
                                    if($beer['shortrating'] == "1") { //echo $beer['rating'];						
                                            $str .= '
                                    <div class="singleReviewContainer">
                                            <div class="topCurve">&nbsp;</div>
                                            <div class="reviewBorder">
                                                    <div class="singleBeerReview">
                                                            <div class="reviewer">
                                                                    <div class="rating">
                                                                            <h1>' . $ratingCalc . '</h1>								
                                                                            <p>Have Another:<br />' . $haveAnother . '</p>
                                                                    </div>
                                                                    <div class="user_image"><img src="' . base_url() . $userImage . '" /></div>
                                                                    <div class="user_info">
                                                                            <ul>
                                                                                    <li><span class="weight700"><a href="' . base_url() . 'user/profile/' . $beer['userID'] . '">' . $beer['username'] . '</a></span> from ' . $beer['userCity'] . ', ' . $beer['userState'] . '</li>									
                                                                                    <li>Date reviewed: ' . $beer['formatDateAdded'] . '</li>
                                                                                    <li>' . $str_avg . '</li>	
                                                                            </ul>
                                                                    </div>
                                                                    <br class="left" />
                                                            </div>
                                                    </div>

                                                    <div class="content_beerReview">
                                                            <div class="beerReview_comments">
                                                                    <p class="bold">Short Rating</p>
                                                                    <p class="marginTop_4">Aroma: <span class="bold">' . $beer['aroma'] . '</span> (' . PERCENT_AROMA . '%)</p>
                                                                    <p>Taste: <span class="bold">' . $beer['taste'] . '</span> (' . PERCENT_TASTE . '%)</p>
                                                                    <p>Look: <span class="bold">' . $beer['look'] . '</span> (' . PERCENT_LOOK . '%)</p>
                                                                    <p>Drinkability: <span class="bold">' . $beer['drinkability'] . '</span> (' . PERCENT_DRINKABILITY . '%)</p>
                                                                    <p class="marginTop_8"><span class="bold requied">Overall: ' . $ratingCalc . '</span></p>
                                                            </div>
                                                            <div class="beerReview_similar">	
                                                                    <p>Date tasted: ' . $beer['formatDateTasted'] . '</p>						
                                                                    <h3 class="white">Similar Beers Tasted</h3>
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
                                    } else {
                                            $str .= '
                                    <div class="singleReviewContainer">
                                            <div class="topCurve">&nbsp;</div>
                                            <div class="reviewBorder">
                                                    <div class="singleBeerReview">
                                                            <div class="reviewer">
                                                                    <div class="rating">
                                                                            <h1>' . $ratingCalc . '</h1>								
                                                                            <p>Have Another:<br />' . $haveAnother . '</p>
                                                                    </div>
                                                                    <div class="user_image"><img src="' . base_url() . $userImage . '" /></div>
                                                                    <div class="user_info">
                                                                            <ul>
                                                                                    <li><span class="weight700"><a href="' . base_url() . 'user/profile/' . $beer['userID'] . '">' . $beer['username'] . '</a></span> from ' . $beer['userCity'] . ', ' . $beer['userState'] . '</li>									
                                                                                    <li>Date reviewed: ' . $beer['formatDateAdded'] . '</li>
                                                                                    <li>$' . $beer['price'] . ' for ' . $beer['package'] . '</li>
                                                                                    <li>' . $str_avg . '</li>	
                                                                            </ul>
                                                                    </div>
                                                                    <br class="left" />
                                                            </div>
                                                    </div>

                                                    <div class="content_beerReview">
                                                            <div class="beerReview_comments">
                                                                    <p>' . nl2br($beer['comments']) . '</p>
                                                            </div>
                                                            <div class="beerReview_similar">	
                                                                    <p>Date tasted: ' . $beer['formatDateTasted'] . '</p>						
                                                                    <p>Color: ' . $beer['color'] . '</p>
                                                                    <h3 class="white">Rating Breakdown</h3>
                                                                    <div class="similarBeers">
                                                                            <p>Aroma: <span class="bold">' . $beer['aroma'] . '</span> (' . PERCENT_AROMA . '%)</p>
                                                                            <p>Taste: <span class="bold">' . $beer['taste'] . '</span> (' . PERCENT_TASTE . '%)</p>
                                                                            <p>Look: <span class="bold">' . $beer['look'] . '</span> (' . PERCENT_LOOK . '%)</p>
                                                                            <p>Drinkability: <span class="bold">' . $beer['drinkability'] . '</span> (' . PERCENT_DRINKABILITY . '%)</p>
                                                                            <p><span class="bold requied">Overall: ' . $ratingCalc . '</span></p>
                                                                    </div>						
                                                                    <h3 class="white">Similar Beers Tasted</h3>
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
                    ';

                    // start the right column output
                    $str_rightCol = '<div class="sideInfo">'; 

                    // get twitter if it exists
                    // set the configuration values for the method
                    $configTwitter = array(
                            'id' => $id
                            , 'beer' => $beers[0]
                            , 'type' => 'beerReview'
                    );
                    // call the method to get the string of text
                    $str_rightCol .= addSocialMedia($configTwitter);

        if(!empty($beers[0]['formatDateAdded']) && count($avgCost) > 0) {
            $str_rightCol .= '<h4><span>Cost Breakdown</span></h4>';
            foreach($avgCost as $cost) {
                // there is a match so create the output
                $serving = $cost['totalServings'] > 1 || $cost['totalServings'] < 1 ? ' servings' : ' serving';
                $str_rightCol .= '<p>$' . $cost['averagePrice'] . ', ' . $cost['totalServings'] . $serving . ', ' . $cost['package'] . 's</p>';
            }					
        } else {
            $str_rightCol .= '<h4><span>Cost Breakdown</span></h4>';
            $str_rightCol .= '<p>No cost data.</p>';
        }
                    
        // check to see if there are beer notes
        if(!empty($beers[0]['beerNotes'])) {
            $str_rightCol .= '
                <h4><span>Beer Notes</span></h4>
                <p>' . nl2br($beers[0]['beerNotes']) . '</p>
            ';
        }
        
        /*foreach($have as $ha) {
            // get the percent
            $percentHaveAnother = ($ha['percentHaveAnother'] * 100);
            // determine which image to use
            $thumb = $percentHaveAnother >= 50 ? 'yes' : 'no';
            // there is a match so create the output
            $str_rightCol .= '
                <h4><span>Have Another</span></h4>
                <p><img style="vertical-align: middle;" src="' . base_url() . 'images/haveanother_' . $thumb . '25.jpg" width="25" height="25" alt="" /> ' . $percentHaveAnother . '%</p>';
        }*/
        
                    // get similar beer rating information
                    $similarBeers = $this->ci->BeerModel->similarBeerByBeerIDAndStyleID($id, $styleID);
                    // get the text set up for the similar beers	
                    $str_rightCol .= '
                                    <h4><span>Highest Rated Similar Beers<span></h4>
                                    <ul>
                    ';			
                    // check if any similar beers were found
                    if(count($similarBeers) > 0) {
                            // iterate through the results
                            foreach($similarBeers as $similar) {
                                    $str_rightCol .= '
                                            <li>
                                                    <div class="bottleCap"><p>' . $similar['avgRating'] . '</p></div>
                                                    <div class="rightSimilar">
                                                            <p><a href="' . base_url() . 'beer/review/' . $similar['id'] . '">' . $similar['beerName'] . '</a></p>
                                                            <p class="rightBreweryLink">by <a href="' . base_url() . 'brewery/info/' . $similar['establishmentID'] . '">' . $similar['name'] . '</a></p>
                                                    </div>
                                                    <br class="left" />
                                            </li>
                                    ';
                            }
                    } else {
                            // no similar beers, show something nice
                            $str_rightCol .= '<li>No others in style.  Get busy!</li>';
                    }

                    // create the swap ins and outs
                    $str_rightCol .= '
                                    </ul>

                                    <h4><span>Beer Swapping</span></h3>
                                    <div id="swapsInfo">
                    ';
            }

            // get the ins and outs totals
            $insOuts = $this->ci->SwapModel->getInsAndOutsByBeerID($id);
            // check if this is an ajax call back
            if($ajax === false) {
                    // create the url based on if they are logged in
                    $ins = $outs = ' href="' . base_url() . 'user/login"';
                    if($userInfo != false) {
                            $ins = ' href="#" onclick="new Ajax.Request(\'' . base_url() . 'ajax/swapadd/ins/' . $id . '\', {asynchronous: true, evalScripts: true, method: \'get\', onLoading: function() {showSpinner(\'swapsInfo\');}, onComplete: function(response) {$(\'swapsInfo\').update(response.responseText);}}); return false;"';
                            $outs = ' href="#" onclick="new Ajax.Request(\'' . base_url() . 'ajax/swapadd/outs/' . $id . '\', {asynchronous: true, evalScripts: true, method: \'get\', onLoading: function() {showSpinner(\'swapsInfo\');}, onComplete: function(response) {$(\'swapsInfo\').update(response.responseText);}}); return false;"';
                    }
                    $str_rightCol .= '
                                            <ul>
                                                    <li><a' . $ins . '>add to swap ins</a></li>
                                                    <li><a' . $outs . '>add to swap outs</a></li>
                                            </ul>
                    ';
            }

            $str_rightCol .= '
                                            <p>
                                                    <a href="' . base_url() . 'beer/swaps/ins/' . $id . '"><span class="bold">' . $insOuts['ins'] . '</span></a> swap ins and 
                                                    <a href="' . base_url() . 'beer/swaps/outs/' . $id . '"><span class="bold">' . $insOuts['outs'] . '</span></a> swap outs
                                            </p>			
            ';
            // check if this is an ajax call back
            if($ajax === false) {
                    $str_rightCol .= '
                                                    </div>
                    ';
                    // check if malt is set
                    if(!empty($beers[0]['malts'])) {
                            $str_rightCol .= '<h4><span>Malts</span></h4><p>' . $beers[0]['malts'] . '</p>';
                    }
                    // check if hops is set
                    if(!empty($beers[0]['hops'])) {
                            $str_rightCol .= '<h4><span>Hops</span></h4><p>' . $beers[0]['hops'] . '</p>';
                    }
                    // check if yeast is set
                    if(!empty($beers[0]['yeast'])) {
                            $str_rightCol .= '<h4><span>Yeast</span></h4><p>' . $beers[0]['yeast'] . '</p>';
                    }
                    // check if glassware is set
                    if(!empty($beers[0]['glassware'])) {
                            $str_rightCol .= '<h4><span>Glassware</span></h4><p>' . $beers[0]['glassware'] . '</p>';
                    }
                    // check if food is set
                    if(!empty($beers[0]['food'])) {
                            $str_rightCol .= '<h4><span>Food</span></h4><p>' . $beers[0]['food'] . '</p>';
                    }
                    // finish of the output
                    $str_rightCol .= '
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
                    $array = $seo + array('str' => $str, 'str_rightCol' => $str_rightCol);
            }	

            // check if this is an ajax call back
            if($ajax === false) {
                    return $array;
            } else {
                    echo $str_rightCol;
            }
    }
	
	public function showCreateReview($id, $lowCount = false, $type = '') {
		// check that the beer exists
		$beers = $this->ci->BeerModel->getBeerByID($id);
		
		// holder for site display
		$array = array();
		// does it exist
		if(!empty($beers)) {
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
				$rating = $this->ci->RatingModel->checkForRatingByUserIDBeerID($userInfo['id'], $id);
				//echo '<pre>'; print_r($rating); echo '</pre>';exit;
				// holder for the edit text
				$edit = !empty($rating) ? 'Edit' : '';
			
				if(empty($_POST) && !empty($rating)) {
					// get the form
					// check which type to get
					if($rating['shortrating'] == "1") {
						// they did a short rating
						$form = form_beerShortReview(array('id' => $id, 'rating' => $rating));
					} else {
						// did a normal rating
						$form = form_beerReview(array('id' => $id, 'rating' => $rating));
					}					
				} else {
					// get the form
					// check which type to get
					if($type == 'short') {
						// they did a short rating
						$form = form_beerShortReview(array('id' => $id));
					} else {
						$form = form_beerReview(array('id' => $id));
					}					
				}
			}
			//echo '<pre>'; print_r($beers);exit;
			$str = '
				<div id="contents_left">
					<div id="beerReview">
						<h2 class="brown">' . $edit . ' Beer Review for ' . $beers['beerName'] . '</h2>
						<div class="beerPic_review marginTop_8"><img src="' . base_url() . 'page/createImage/' . $id . '/beer" /></div>
						<div id="beerInfo" class="marginTop_8">
							<ul>
								<li>Style: <a href="' . base_url() . 'beer/style/' . $beers['styleID'] . '">' . $beers['style'] . '</a></li>
								<li>ABV:' . $beers['alcoholContent'] . '%</li>							
								<li><a href="' . base_url() . 'brewery/info/' . $beers['establishmentID'] . '">' . $beers['name'] . '</a></li>
								<li>' . $beers['address'] . '</li>
								<li><a href="' . base_url() . 'establishment/city/' . $beers['stateID'] . '/' . urlencode($beers['city']) . '">' . $beers['city'] . '</a>, <a href="' . base_url() . 'establishment/state/' . $beers['stateID'] . '">' . $beers['stateAbbr'] . '</a> ' . $beers['zip'] . '</li>
							</ul>
						</div>
						<br class="both" />
					</div>
			';		
			$str .= $lowCount == false ? $form . '</div>' : '</div>';
			
			// get configuration values for creating the seo
			$config = array(
				'beerName' => $beers['beerName']
				, 'beerStyle' => $beers['style']
				, 'breweryName' => $beers['name']
				, 'breweryCity' => $beers['city']
				, 'breweryState' => $beers['stateFull']
			);
			// set the page information
			$seo = getDynamicSEO($config);
			$array = $seo + array('str' => $str);
		} else {
			// the beer is not in the db
			// set the page information
			$seo = getSEO();
			// create screen display error
			$str = '<p>The beer that you are trying to review couldn\'t be found.  Sober up and pay attention!</p>';
			// put the two arrays of information together
			$array = $seo + array('str' => $str);
		}
		// return the array
		return $array;
	}
	
	public function uploadImage($item) {
	$str = '
				<form id="editBeerForm" class="edit" method="post" action="' . base_url() . 'admin/cropImage/' . $item['id'] . '" enctype="multipart/form-data">
					<input type="hidden" id="hdn_brewery" name="hdn_brewery" value="' . $item['name'] . '" />
					<input type="hidden" id="hdn_beer" name="hdn_beer" value="' . $item['beerName'] . '" />
					
					<span id="frm_picture_container">
						<label for="fl_picture">Add Image:</label>
						<input type="file" id="fl_picture" name="fl_picture" />	
					</span>
					<span id="spn_spinner"></span>
					<span id="spn_picture_name"></span>
					<input type="hidden" id="hdn_picture" name="hdn_picture" value="" />
					
					<input type="submit" id="btn_submit" name="btn_submit" value="Continue - Crop Image" style="display: none;" />
				</form>
				
				<script type="text/javascript">
				/*<![CDATA[*/
				var button = $(\'fl_picture\');
				document.observe("dom:loaded", function() {
					new Ajax_upload(button,{
						action: \'' . base_url() . 'ajax/uploadFile/beerPic\',
						name: \'beerImage\',
						onSubmit : function(file, ext){
							showSpinner(\'spn_spinner\');
						},
						onComplete: function(file, response){
							// check if this was successful
							if(response.indexOf(\'gif\') != -1 || response.indexOf(\'jpg\') != -1 || response.indexOf(\'png\') != -1) {
								// an image name was returned
								// hide the image
								$(\'spn_spinner\').hide();
								$(\'frm_picture_container\').hide();
								// set the holder with the name of the image that was uploaded
								$(\'spn_picture_name\').show();
								$(\'spn_picture_name\').update(response);
								// set the name of the image to be uploaded in the hidden form field
								$(\'hdn_picture\').value = file;
								// show the submit button
								$(\'btn_submit\').show();
							} else {
								// error was returned
								$(\'spn_spinner\').hide();						
								// set the holder with an error string
								$(\'spn_picture_name\').update(response);
								// set the hidden form field value to an empty string
								$(\'hdn_picture\').value = \'\';
								// place the form element back and give them a chance to upload again
								//$(\'spn_picture\').update(\'<input type="file" id="fl_picture" name="fl_picture" />\');
							}
						}
					});
				});
				/*]]>*/
				</script>	
			';
	return $str;
	}
	
	public function cropImage($item) {
	$str = '
				<img src="' . base_url() . 'images/beers/tmp/' . $item['fileName'] . '" id="cropThisImage" />
							
				<form method="post" action="' . base_url() . 'admin/cropImage/' . $item['id'] . '">
					<input type="hidden" id="x1" name="x1" value="" />
					<input type="hidden" id="x2" name="x2" value="" />
					<input type="hidden" id="y1" name="y1" value="" />
					<input type="hidden" id="y2" name="y2" value="" />
					<input type="hidden" id="width" name="width" value="" />
					<input type="hidden" id="height" name="height" value="" />
					<input type="hidden" id="hdn_fileName" name="hdn_fileName" value="' . $item['fileName'] . '" />
					<input type="submit" id="btn_crop" name="btn_crop" value="Crop Image" disabled="disabled" />
				</form>
				
				<script type="text/javascript">
				/*<![CDATA[*/
				document.observe("dom:loaded", function() {
					new Cropper.Img(
						\'cropThisImage\', {
							ratioDim: {
								x: 150,
								y: 350
							},
							displayOnInit: true,
							onEndCrop: endCrop
						}
					);
				});
				
				function endCrop(coords, dimensions) {
					$(\'x1\').value = coords.x1;
					$(\'x2\').value = coords.x2;
					$(\'y1\').value = coords.y1;
					$(\'y2\').value = coords.y2;
					$(\'width\').value = dimensions.width;
					$(\'height\').value = dimensions.height;
					
					if(dimensions.width == 0 || dimensions.height == 0) {
						$(\'btn_crop\').disabled = true;
					} else {
						$(\'btn_crop\').disabled = false;
					}
				}
				/*]]>*/
				</script>
			';
	
	return $str;
	}
	
	public function showStyles($id = '') {
		// holder for the output
		$str = $output = '<div id="styles">';
		// right column input
		$rightCol = '';
		if(empty($id) || !is_numeric($id)) {
			// no real id, so get all styles
			// create the output via the library
			$array = $this->ci->BeerModel->getAllBeerStyles();
			//echo '<pre>'; print_r($array); echo '</pre>';exit;
			// check if there was output
			if(!empty($array)) {
				// holder for origins
				$origin = array();
				// holder for styles
				$style = array();
				// start off the output
				$str .= '
					<h2 class="brown">Beer Style</h2>
					<p>Beer styles help to define the beer that we are drinking.  Understanding beer styles will give one a better
					idea of the characteristics of a particular beer: visually, aroma, taste, mouthfeel, and overall.  These
					guidelines should be used when reviewing a beer and deciding if you like it or not.  There is always the
					first impression of a beer but if it is made with a clear understanding of style, the beer can be enjoyed for
					what the brewer intended.</p>
				';
		
				$i = 0;
				$j = 0;
				foreach($array as $key) {
					if(empty($style) || !in_array($key['styleType'], $style)) {
						//$str .= (!empty($style) || !in_array($style, $style)) ? '</ul></div>' : '';
						$str .= $i > 0 ? '</ul></div><br class="left" />' : '';
						$class = $i > 0 ? ' notFirst' : '';
						$str .= '<h2 class="brown' . $class . '">' . $key['styleType'] . 's</h2>';
						$style[] = $key['styleType'];
						$i++;
						$j = 0;
					}
				
					$testVal = $key['styleType'] . '_' . $key['origin'];
					if(empty($testVal) || !in_array($testVal, $origin)) {
						//$str .= (!empty($testVal) && !in_array($testVal, $origin)) ? '</ul></div>' : '';
						$str .= $j > 0 ? '</ul></div>' : '';
						$str .= ($j > 0 && $j % 2 == 0) ? '<br class="left" />' : '';
						$str .= $key['origin'] != null ? '<div class="subStyle"><h3>' . $key['origin'] . ' ' . $key['styleType'] . 's</h3>' : '';
						$str .= '<ul>';
						$origin[] = $key['styleType'] . '_' . $key['origin'];
						$j++;
					}
				
					$str .= '<li><a href="' . base_url() . 'beer/style/' . $key['id'] . '">' . $key['style'] . '</a></li>';
				}
			} else {
				$str .= '<p>No beer styles were found</p>';
			}
			$str .= '</div>';
			// set the right column info
			$rightCol = '
				<h4><span>Beer Style Thoughts</span></h4>
				<p>Beer styles are an ever changing and ever evolving list.  American craft brewers can be thought of as artists and,
				like any great artist, the outstanding brewers will define and redefine a style based on their perception.  So
				beer styles should evolve in order to keep the American craft beer industry progressing forward.</p>
				
				<h4><span>Beer Information</span></h4>
				<ul>
					<li><a href="' . base_url() . 'beer/srm">Beer Color</a></li>
					<li><a href="' . base_url() . 'beer/ratingSystem">Beer Rating System</a></li>
				</ul>
				
				<h4><span>Beer Judge Certification Program</span></h4>
				<p>The Beer Judge Certification Program (BJCP) is a great way to become more involved with beer and to truly appreciate
				American craft beer.  The BJCP was founded in 1985 to promote beer literacy, appreciation of real beer, and recognize 
				beer tasting and evalution skills (from their site).  An examination is given in order to receive certification.</p>
			';
			
				// create an array to send back
				$str = array('str' => $str . '<br class="left" />', 'rightCol' => $rightCol) ;
		} else {
			$offset = $this->ci->uri->segment(4);
			if(empty($offset) || !ctype_digit($offset)) {
				$offset = 0;
			}
			// id is set, so get specific style information
			// create the output via the library
			$records = $this->ci->BeerModel->getBeerStyleByID($id, $offset);
			//echo '<pre>'; print_r($records); echo '</pre>'; exit;
			// check if there was output
			if(is_array($records)) {
				// total number of results
				$totalResults = $records['total'];
				// get the array of results
				$array = $records['rs'];
			
				// get configuration values for creating the seo
				$config = array(
					'style' => $array[0]['style']
					, 'description' => $array[0]['description']
					, 'origin' => $array[0]['origin']
					, 'styleType' => $array[0]['styleType']
				);
					// set the page information
				$seo = getDynamicSEO($config);
		
				//$output = '';
				if(count($array) == 1 && $array[0]['totalRatings'] == 0) {
					// only one result in which there where no beers
					$output .= '
						<div id="styleInfo">
							<h2 class="brown">' . $array[0]['origin'] . ' ' . $array[0]['styleType'] . 's: ' . $array[0]['style'] . '</h2>
							<p>' . $array[0]['description'] . '</p>
						</div>
						
						<div id="beerTable">
							<p>No beers have been rated fitting this style</p>
						</div>
					';
				} else {
					// at least one beer fits the style
						
					// get rolling with pagnation
					$this->ci->load->library('pagination');
					// configuration array for pagination
					$config['base_url'] = base_url() . 'beer/style/' . $id;
					$config['total_rows'] = $totalResults;
					$config['per_page'] = BEER_STYLE_PAGINATION;
					$config['uri_segment'] = 4;
					$config['num_links'] = 3;
					$config['full_tag_open'] = '<p>';
					$config['full_tag_close'] = '</p>';
					$this->ci->pagination->initialize($config);
					$num_pages = $totalResults / BEER_STYLE_PAGINATION;
					$pagination = '
						<div class="pagnation" style="margin-bottom: 1.0em;">
							<div class="green"><span class="bold">' . number_format($totalResults) . '</span> ' . $array[0]['style'] . ' Beers Reviewed</div>
							<br class="both" />
						</div>
					';
					$pagination_bottom = '';
			
					if($num_pages > 1) {
						$pagination = '
							<div class="pagnation">
								<div class="green"><span class="bold">' . number_format($totalResults) . '</span> ' . $array[0]['style'] . ' Beers Reviewed</div>
								' . $this->ci->pagination->create_links() . '
								<br class="both" />
							</div>
						';
						$pagination_bottom = '
							<div class="pagnation">
								' . $this->ci->pagination->create_links() . '
								<br class="both" />
							</div>
						';
					}
		
					// start the output
					$output .= '
						<div id="styleInfo">
							<h2 class="brown">' . $array[0]['origin'] . ' ' . $array[0]['styleType'] . 's: ' . $array[0]['style'] . '</h2>
							<p>' . $array[0]['description'] . '</p>
						</div>
						
						' . $pagination . '
			
						<div id="beerTable">
							<table>
								<tr class="gray2">
									<td>&nbsp;</td>
									<th>Beer</th>
									<th>Brewery</th>
									<th class="center"># Reviews</th>
									<th class="center">Rate Avg.</th>
									<th class="center">H.A.</th>
									<!--<th>Avg. Cost</th>-->
								</tr>
					';
				
					//$totalBeers = $this->ci->BeerModel->getBeerStyleByID($id, true);
					// get rolling with pagnation
					/*$this->ci->load->library('pagination');
					 // configuration array for pagination
					 $config['base_url'] = base_url() . 'beer/style/' . $id;
					 $config['total_rows'] = $totalResults;
					 $config['per_page'] = BEER_STYLE_PAGINATION;
					 $config['uri_segment'] = 4;
					 $config['num_links'] = 3;
					 $config['full_tag_open'] = '<p>';
					 $config['full_tag_close'] = '</p>';
					 $this->pagination->initialize($config);
					 $num_pages = $totalResults / $perpage;
					 $this->_data['pagination'] = $num_pages > 1 ? $this->pagination->create_links() : ''; */
			
				
					//echo '<pre>'; print_r($array); echo '</pre>'; exit;
					// counter for determing background color
					$cnt = 0;
					// iterate through the result set
					foreach($array as $style) {
						// get the avg cost per package of beer drank for the brewery
						//$avgCost = $this->ci->BeerModel->getAvgCostPerPackage($style['beerID']);
						// get the percentage of people who would have another
						$haveAnother = $this->ci->BeerModel->getHaveAnotherPercent($style['beerID']);
						
						$str = '';
						/*foreach($avgCost as $cost) {
						 // there is a match so create the output
						 $serving = $cost['totalServings'] > 1 || $cost['totalServings'] < 1 ? ' servings' : ' serving';
						 $str .= '<p>$' . $cost['averagePrice'] . ', ' . $cost['totalServings'] . $serving . ', ' . $cost['package'] . 's</p>';
						 }*/
						//echo '<pre>'; print_r($style); echo '</pre>';
						// configuration for the image
						/*$image = array(
						 'picture' => $style['picture']
						 , 'id' => $style['beerID']
						 , 'alt' => $style['name'] . ' - ' . $style['beerName']
						 , 'width' => 30
						 , 'height' => 70
						 );
						 // check if the image exists for this beer
						 $img = checkForImage($image, false, false);*/
						
						// check if the rating is set - will only happen for a beer
						// that has not been rated
						$avgRating = empty($style['avgRating']) ? '0.0' : $style['avgRating'];
						// check if have another is set - will only happen for a beer
						// that has not been rated
						$ha = !key_exists(0, $haveAnother) ? '0' : ($haveAnother[0]['percentHaveAnother'] * 100);
						$class = $cnt % 2 == 1 ? ' class="gray"' : '';
						$output .= '
							<tr' . $class . '>
								<td class="td_first"><a href="' . base_url() . 'beer/review/' . $style['beerID'] . '"><img src="' . base_url() . 'page/createImage/' . $style['beerID'] . '/beer/mini" /></a></td>
								<td><a href="' . base_url() . 'beer/review/' . $style['beerID'] . '">' . $style['beerName'] . '</a></td>
								<td><a href="' . base_url() . 'brewery/info/' . $style['establishmentID'] . '">' . $style['name'] . '</a></td>
								<td class="center">' . number_format($style['totalRatings']) . '</td>
								<td class="center">' . $avgRating . '</td>
								<td class="center">' . $ha . '%</td>
								<!--<td class="td_last">' . $str . '</td>-->
							</tr>
						';
						// increment counter
						$cnt++;
					}
		
					$output .= '
							</table>
						</div>
						
						' . $pagination_bottom . '
					';					
				}
	
				// get right column information
				$rightCol = '
					<h4><span>ABV Range</span></h4>
					<p class="alignRight bold">' . (!empty($array[0]['abvrange']) ? $array[0]['abvrange'] . '%' : 'N/A') . '</p>
					
					<h4><span>IBU Range</span></h4>
					<p class="alignRight bold">' . (!empty($array[0]['iburange']) ? $array[0]['iburange'] : 'N/A') . '</p>
					
					<h4><span>SRM Range</span></h4>
					<p class="alignRight bold">' . (!empty($array[0]['srm']) ? $array[0]['srm'] : 'N/A') . '</p>
					
					<h4><span>Original Gravity Range</span></h4>
					<p class="alignRight bold">' . (!empty($array[0]['ogravity']) ? $array[0]['ogravity'] : 'N/A') . '</p>
					
					<h4><span>Final Gravity Range</span></h4>
					<p class="alignRight bold">' . (!empty($array[0]['fgravity']) ? $array[0]['fgravity'] : 'N/A') . '</p>					
				';
	
				// beers similar in style
				$similarBeers = $this->ci->BeerModel->similarBeerByStyleID($id);
				// check if any similar beers were found
				if(count($similarBeers) > 0) {
					$rightCol .= '
						<h4><span>Highest Rated In Style</span></h4>
						<ul>
					';
					// iterate through the results
					foreach($similarBeers as $similar) {
						$rightCol .= '
							<li>
								<div class="bottleCap"><p>' . $similar['avgRating'] . '</p></div>
								<div class="rightSimilar">
									<p><a href="' . base_url() . 'beer/review/' . $similar['id'] . '">' . $similar['beerName'] . '</a></p>
									<p class="rightBreweryLink">by <a href="' . base_url() . 'brewery/info/' . $similar['establishmentID'] . '">' . $similar['name'] . '</a></p>
								</div>
								<br class="left" />
							</li>
						';
					}
					$rightCol .= '</ul>';
				}
		
				/*$rightCol .= '
				 <p class="alignRight bold">' . (!empty($array[0]['fgravity']) ? $array[0]['fgravity'] : 'N/A') . '</p>
				 ';
				
				 $str_rightCol .= '
				 <h4><span>Highest Rated Similar Beers<span></h4>
				 <ul>
				 ';		*/
				
				// combine the arrays
				$str = $seo + array('str' => $output . '</div><br class="left" />', 'rightCol' => $rightCol) ;
			} else {
				// this is a precaution
				// should only be triggered if a value was entered
				// that is out of range for the database
				header('Location: ' . base_url());
				exit;
			}
		}
		// return the marked up output
		return $str;
	}
	
	public function showRatingSystem() {
		// counter to help with class
		$cnt = 0;
		// holder for the output
		$str = '
			<h2 class="ratingStar brown">Beer Rating System</h2>
			<p class="marginTop_8">Our beer rating system and rating scale is based on overall feel for the beer and the necessity or lack there of, to have another one of the beers in hand. The scale should be similar to other rating scales that you have seen and used. This scale should be pliable to the point in which a you might insinuate your own descriptions for the values, after all, the site is about your personality and subjective views of the beer. So the 10 point scale was born.</p>
			<p class="marginTop_8">The 10 point scale is just from 1 to 10, with 1 being the lowest rating and 10 being the highest. As mentioned before: something you are familiar with from other areas of life.</p>
		';
		// get the rating information
		$info = $this->ci->RatingSystemModel->getRatingSystem();
		// iterate through the results
		if(!empty($info)) {
			// start the output
			$str .= '<table id="ratingSystem">';
			// iterate through the result set
			foreach($info as $key) {
				// class holder for row color
				$class = ($cnt % 2 == 0) ? '' : ' class="gray"';
				// continue the screen output
				$str .= '
					<tr' . $class . '>
						<td class="ratingValue">' . $key['ratingValue'] . '</td>
						<td>' . $key['description'] . '</td>
					</tr>
				';
				// add to the counter
				$cnt++;
			}
			// end the output
			$str .= '
					</table>
				<p class="marginTop_8">Remember when you write a beer review, more than likely, you are taking your first taste of a beer, possibly only, and that there is a lot that factors into the reason you felt the way you did when you wrote a review. Some factors: mood, feelings, like or dislike of beer style, food, other beers drank before, etc. These are the reasons that Two Beer Dudes will more than likely never put a review on the site below a 5. We will try and give the beer another chance on another day.</p>
				<p class="marginTop_8">One last thing: when tasting beer only have about 2 to 3 ounces and drink beers of the same beer style. Split a 12 ounce bottle between friends, formulate your own opinion and then share with each other. Each of you may have found something different within the confines of the glass container. Really try to enjoy the beer for the style that it is and appreciate the effort that the brewer put into making the beer. Trying to be unbiased will give the best overall chance and rating for a beer. Enjoy!</p>
			';
		} else {
			// no results
			$str = '<p>There is no information that matches your request.</p>';
		}
		// send back the output
		return $str;
	}
	
	public function showSRM() {
		$str = '';
		// send back the output
		return $str;
	}
	
	public function swap($beerID, $type) {
	    // holder for swap search
	    $swap = '';
	    // holder for number of swaps
	    $num = 0;
        // holder for the number of swap outs
        $numSwapOuts = $this->ci->SwapModel->numberSwapOutsByBeerID($beerID);
        // holder for the number of swap outs
        $numSwapIns = $this->ci->SwapModel->numberSwapInsByBeerID($beerID);
	    // get info according to type
	    if($type == 'ins') {
	        // get a record set of swap ins
	        $swap = $this->ci->SwapModel->getSwapInsByBeerID($beerID);
	        // get the number of swapins
	        $num = count($swap);
	    } else {
	        // swapouts
	        $swap = $this->ci->SwapModel->getSwapOutsByBeerID($beerID);
	        // get the number of swapouts
	        $num = count($swap);
	    }
	
	    // get the information about the beer
	    $beers = $this->ci->BeerModel->getBeerByID($beerID);
		
	    // check for a brewery hop
	    $breweryHop = !empty($beers['breweryhopsID']) ? '<li><a href="' . base_url() . 'brewery/hop/' . $beers['breweryhopsID'] . '">brewery hop</a></li>' : '';
	
	    $str = '
			<div id="contents_left">
				<div id="beerReview">
					<div class="admin_beerPic"><img src="' . base_url() . 'page/createImage/' . $beerID . '/beer" alt="" /></div>
					<div id="beerInfo">
						<h2><a href="' . base_url() . 'beer/review/' . $beerID . '">' . $beers['beerName'] . '</a></h2>
						<p><a href="' . base_url() . 'brewery/info/' . $beers['establishmentID'] . '">' . $beers['name'] . '</a></p>
						<p>
							<a href="' . base_url() . 'establishment/city/' . $beers['stateID'] . '/' . urlencode($beers['city']) . '">' . $beers['city'] . '</a>,
							<a href="' . base_url() . 'establishment/state/' . $beers['stateID'] . '">' . $beers['stateAbbr'] . '</a>
						</p>
						<p>Vitals: <a href="' . base_url() . 'beer/style/' . $beers['styleID'] . '">' . $beers['style'] . '</a>, ' . $beers['alcoholContent'] . '% ABV</p>
						
						<ul>
							' . $breweryHop . '
							<li><a href="' . $beers['url'] . '" target="_blank">web site</a></li>
						</ul>
					</div>
					<br class="both" />
				</div>
				<div id="swapsInfo">
					<p>' . $num . ' swap ' . strtolower($type) . '</p>
		';
	
	    if($num > 0) {
	        // there are swap ins to show
	        $str .= '
					<table id="swapsTable">
						<tr>
							<th>&nbsp;</th>
							<th>Dude</th>
						</tr>
			';
	        // keep the count of the swaps
	        $cnt = 1;
	        // check for any swaps
	        if($num > 0) {
	            // iterate through the results
	            foreach($swap as $item) {
	                // create the string info for each beer
	                $str .= '
						<tr>
							<td>' . $cnt . '.</td>
							<td><a href="' . base_url() . 'user/profile/' . $item['userID'] . '">' . $item['username'] . '</a> lives in ' . $item['city'] . ', ' . $item['state'] . '</td>
						</tr>
					';
	                // iterate the counter
	                $cnt++;
	            }
	        }
	        // finish off the list
	        $str .= '
					</table>
			';
	    } else {
	        $str .= '<p>No one currently has this beer on their swap <span class="bold">' . strtolower($type) . '</span> list.</p>';
	    }
	    $str .= '
				</div>
			</div>
		';	
        // return the output
	    return $str;
	}
	
	public function showAddBeer($lowCount = false, $establishmentInfo = array()) {
	    // holder for site display
	    $array = array();
	    // holder for the form text
	    $form = '';
	    // check if they have already rated this beer
	    // get the user info
	    $userInfo = $this->ci->session->userdata('userInfo');
		    
	    // only included for high counts
	    if($lowCount == false) {
	        // get the breweries
	        //$breweries = $this->ci->BreweriesModel->getAllForDropDown();
	        // get the styles
	        $styles = $this->ci->StyleModel->getAllForDropDown();
	        // header title for the page
	        $h2 = 'Add A Beer';
	        // check if establishment info is set
	        if(!empty($establishmentInfo)) {
	            $h2 = 'Add A Beer For ' . $establishmentInfo['name'];
	        }
	        // get the form
	        //$array['leftCol'] = '<h2 class="brown">Add A Beer</h2>' . form_addBeer(array('breweries' => $breweries, 'styles' => $styles));
	        $array['leftCol'] = '<h2 class="brown">' . $h2 . '</h2>' . form_addBeer(array('styles' => $styles, 'establishmentID' => $establishmentInfo['id']));
	    }
	    // holder for the other beers
	    $otherBeers = '';
	    // get the other beers
	    $ob = $this->ci->BeerModel->getBeersByEstablishmentID($establishmentInfo['id']);
	    // check that there were results
	    if($otherBeers == false) {
	        // no other beers
	        $otherBeers = 'N/A';
	    } else {
	        // start the list
	        $otherBeers = '<ul>';
	        // iterate through the results
	        foreach($ob as $br) {
	            $otherBeers .= '<li><a href="' . base_url() . 'beer/review/' . $br['id'] . '">' . $br['beerName'] . '</a> - <a class="green" href="' . base_url() . 'beer/style/' . $br['styleID'] . '">' . $br['style'] . '</a></li>';
	        }
	        // finish off the list
	        $otherBeers .= '<ul>';
	    }
	    
	    // set the right column information
	    $array['rightCol'] = '
				    <h4><span>Create Carefully</span></h4>
				    <ul>
					    <li>Two Beer Dudes is about American craft beer.</li>
					    <li>Please check that the beer doesn&#39;t exist already.  Duplicates create confussion.</li>
				    </ul>
				    <h4><span>Beers Already Added</span></h4>
				    ' . $otherBeers
	    ;
	    // return the array
	    return $array;
	}
	
	public function getBestWorstRatings($config) {
		// get the data based on config
		$beers = $this->ci->BeerModel->getBestWorstBeers($config);
		
		$this->ci->_data['leftCol'] = '<h2 class="brown">Highest Rated American Craft Beers</a>';
		
		if(empty($beers)) {
			// no beers to display
			$this->ci->_data['leftCol'] .= '<p class="marginTop_8">There are no beer matching the searched criteria.</p>';
		} else {
			// holder for the string
			$str = '
				<table id="bestRated">
					<tr class="gray2">
						<th>&nbsp;</th>
						<th>American Craft Beer</th>
						<th class="center">Rating</th>
						<th class="center">Reviews</th>
					</tr>
			';
			// counter for determing bg color
			$cnt = 0;
			// iterate through the results
			foreach($beers as $item) {
				// get the bg color of the row
				$class = $cnt % 2 == 1 ? ' class="gray"' : '';
				// continue with the string
				$str .= '
					<tr' . $class . '>
						<td class="td_first"><a href="' . base_url() . 'beer/review/' . $item['id'] . '"><img src="' . base_url() . 'page/createImage/' . $item['id'] . '/beer/mini" /></a></td>
						<td>
							<p><a href="' . base_url() . 'beer/review/' . $item['id'] . '">' . $item['beerName'] . '</a></p>
							<p><a href="' . base_url() . 'brewery/info/' . $item['establishmentID'] . '">' . $item['name'] . '</a></p>
							<p><a href="' . base_url() . 'beer/style/' . $item['styleID'] . '">' . $item['style'] . '</a></p>						
						</td>
						<td class="center">' . number_format($item['averagerating'], 2) . '</td>
						<td class="center">' . number_format($item['totalratings'], 0, '.', ',') . '</td>
					</tr>
				';
				// increment counter
				$cnt++;
			}
			$this->ci->_data['leftCol'] .= $str . '</table>';
		}
		
		// beers similar in style
		$bestStyles = $this->ci->BeerModel->getBestStyles();
		// holder highest rated styles
		$rightCol = '
				<h4><span>Top 50</span></h4>
				<ul>
					<li>More functionality needed.  Let us know: <script type="text/javascript">document.write(\'web\' + \'master\' + \'@\' + \'twobeer\' + \'dudes.com\');</script>.</li>
				</ul>
		';
		// check if any similar beers were found
		if(count($bestStyles) > 0) {
			$rightCol .= '
				<h4><span>Highest Rated Styles</span></h4>
				<ul>
			';
			// iterate through the results
			foreach($bestStyles as $item) {
				// check if the time should be plural
				$times = $item['totalRatings'] == 1 ? '' : 's';
				// check if the beer should be plural
				$brs = $item['totalBeers'] == 1 ? '' : 's';
				// add to the screen output
				$rightCol .= '
					<li>
						<div class="bottleCap"><p>' . $item['avgRating'] . '</p></div>
						<div class="rightSimilar">
							<p><a href="' . base_url() . 'beer/style/' . $item['id'] . '">' . $item['style'] . '</a></p>
							<p class="rightBreweryLink">' . number_format($item['totalBeers'], 0, '.', ',') . ' beer' . $brs . ', rated ' . number_format($item['totalRatings'], 0, '.', ',') . ' time' . $times . '</a></p>
						</div>
						<br class="left" />
					</li>
				';
			}
			$rightCol .= '</ul>';
		}
		$this->ci->_data['rightCol'] = $rightCol;
	}
	
	public function getTitle() {
		return $this->title;
	}
}
?>