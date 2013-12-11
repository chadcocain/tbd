<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Rating {
	private $ci;
	private $title = 'Rating';

	public function __construct() {
		$this->ci =& get_instance();
	}
	
	public function getAllRatings() {
		// get all the beers
		$items = $this->ci->RatingModel->getAll();
		// start the output 
		$str = '';
		// iterte through the list
		foreach($items as $item) {
			$alcoholContent = empty($item['alcoholContent']) ? '&nbsp;' : $item['alcoholContent'] . '%';
			$seasonal = empty($item['seasonal']) ? 'No' : 'Yes';
			$str .= '
			<div id="item_' . $item['id'] . '" class="item">
				<div id="item_list_container_' . $item['id'] . '" class="list_itemContainer">					
					<dl class="dl_tableDisplay">
						<dt>Beer:</dt><dd>' . $item['beerName'] . '</dd>
						<dt>Brewery:</dt><dd><a href="' . $item['url'] . '" target="_blank">' . $item['name'] . '</a></dd>
						<dt>Style:</dt><dd>' . $item['style'] . '</dd>
						<dt>ABV:</dt><dd>' . $alcoholContent . '</dd>
			';
			$str .= empty($item['malts']) ? '' : '<dt>Malts:</dt><dd>' . $item['malts'] . '</dd>';
			$str .= empty($item['hops']) ? '' : '<dt>Hops:</dt><dd>' . $item['hops'] . '</dd>';
			$str .= empty($item['yeast']) ? '' : '<dt>Yeast:</dt><dd>' . $item['yeast'] . '</dd>';
			$str .= empty($item['gravity']) ? '' : '<dt>Gravity:</dt><dd>' . $item['gravity'] . '</dd>';
			$str .= empty($item['ibu']) ? '' : '<dt>IBU:</dt><dd>' . $item['ibu'] . '</dd>';
			$str .= empty($beer['food']) ? '' : '<dt>Food:</dt><dd>' . $beer['food'] . '</dd>';
			$str .= empty($beer['glassware']) ? '' : '<dt>Glassware:</dt><dd>' . $beer['glassware'] . '</dd>';
			$str .= '
						<dt>Seasonal:</dt><dd>' . $seasonal . '
			';
			$str .= $seasonal == 'No' ? '' : ' - ' . $item['seasonalPeriod'];
			$str .= '
						</dd>
						<dt>Date Tasted:</dt><dd>' . $item['dateTasted'] . ' by ' . $item['firstname'] . ' ' . $item['lastname'] . '</dd>
						<dt>Rating:</dt><dd>' . $item['rating'] . '</dd>
						<dt>Color:</dt><dd>' . $item['color'] . '</dd>
						<dt>Packaging:</dt><dd>' . $item['package'] . '</dd>
						<dt>Price:</dt><dd>$' . $item['price'] . '</dd>
						<dt class="borderBottom_none">Comments:</dt><dd class="borderBottom_none">' .nl2br($item['comments']) . '</dd>
					</dl>
					<br class="left" />
				</div>
				<ul id="list_links_' . $item['id'] . '" class="list_horizontalLinks">
					<li><a href="#" id="edit_' . $item['id'] . '" onclick="new Ajax.Request(\'' . base_url() . 'ajax/edit/rating/' . $item['id'] . '\', {asynchronous: true, evalScripts: true, method: \'post\', onLoading: function() {showSpinner(\'item_list_container_' . $item['id'] . '\');}, onComplete: function(response) {$(\'item_list_container_' . $item['id'] . '\').update(response.responseText); $(\'edit_' . $item['id'] . '\').style.display=\'none\'; $(\'cancel_' . $item['id'] . '\').style.display=\'block\';}}); return false;">Edit</a></li>
					<li><a href="#" id="cancel_' . $item['id'] . '" onclick="new Ajax.Request(\'' . base_url() . 'ajax/cancel/rating/' . $item['id'] . '\', {asynchronous: true, evalScripts: true, method: \'get\', onLoading: function() {showSpinner(\'item_list_container_' . $item['id'] . '\');}, onComplete: function(response) {$(\'item_list_container_' . $item['id'] . '\').update(response.responseText); $(\'cancel_' . $item['id'] . '\').style.display=\'none\'; $(\'edit_' . $item['id'] . '\').style.display = \'block\';}}); return false;" style="display: none;">Cancel</a></li>
				</ul>
				<br class="both" />
			</div>
			';
		}
		// return the output
		return $str;
	}
	
	public function getRatingsReviewHome() {
		$offset = $this->ci->uri->segment(4);
		if(empty($offset) || !ctype_digit($offset)) {
			$offset = 0;
		}
		// get all the ratings
		$records = $this->ci->RatingModel->getAllPagination($offset);
		
		// check that there was output
		if(is_array($records)) {
			// total number of results
			$totalResults = $records['total'];
			// get the total number of beers
			$totalNumberBeers = $this->ci->BeerModel->getTotalBeersInDB();
			// get the array of results
			$items = $records['rs'];
			
			// get rolling with pagnation
			$this->ci->load->library('pagination');
			// configuration array for pagination
			$config['base_url'] = base_url() . 'beer/review/pgn';
			$config['total_rows'] = $totalResults;
			$config['per_page'] = BEER_REVIEWS;
			$config['uri_segment'] = 4;
			$config['num_links'] = 2;
			$config['full_tag_open'] = '<p>';
			$config['full_tag_close'] = '</p>';
			$this->ci->pagination->initialize($config);
			$num_pages = $totalResults / BEER_REVIEWS;
			$pagination = '
				<div class="pagnation" style="margin-bottom: 1.0em;">
					<div class="green">
						<span class="bold">' . number_format($totalNumberBeers) . '</span> Beers,
						<span class="bold">' . number_format($totalResults) . '</span> Reviews
					</div>
					<br class="both" />
				</div>
			';
			$pagination_bottom = '';
			if($num_pages > 1) {
				$pagination = '
					<div class="pagnation">
						<div class="green">
							<span class="bold">' . number_format($totalNumberBeers) . '</span> Beers,
							<span class="bold">' . number_format($totalResults) . '</span> Reviews
						</div>
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
			$str = '
			<div class="review_review">
				<h2 class="keg brown">Recent Beer Reviews</h2>
				<br class="both" />
				' . $pagination
			;
			// counter for changing background color
			$cnt = 0;
			// iterte through the list
			foreach($items as $item) {
				// get the rating totals to get an average		
				$ratingInfo = $this->ci->BeerModel->getBeerRating($item['beerID']);
				// calculate the ratings average
				//$ratingAverage =  number_format(round($ratingInfo['totalrating']/$ratingInfo['totaltimerated'], 1), 1);
				$ratingAverage =  number_format(round($ratingInfo['averagerating'], 1), 1);
				// the total number of times the beer has been rated
				//$ratingTotalTimes = $ratingInfo['totaltimerated'];	
				$ratingTotalTimes = $ratingInfo['timesrated'];		
				// determine the rating total times lingo
				$ratingTotalTimes .= $ratingTotalTimes == 1 ? ' dude' : ' dudes';
				// get the percentage of people who would have another
				$haveAnother = $this->ci->BeerModel->getHaveAnotherPercent($item['beerID']);
				//echo '<pre>'; print_r($haveAnother); echo '</pre>';
				// get the percent
				$percentHaveAnother = ($haveAnother[0]['percentHaveAnother'] * 100);
				// determine which image to use
				$thmb = $percentHaveAnother >= 50 ? 'yes' : 'no';
				// there is a match so create the output
				$haveMore = '<img src="' . base_url() . 'images/haveanother_' . $thmb . '25.jpg" width="25" height="25" title="have another ' . $item['beerName'] . ' by ' . $item['name'] . '" alt="have another ' . $item['beerName'] . ' by ' . $item['name'] . '" /> ' . $percentHaveAnother . '%</p>';
				
				// css for the first beer
				$css = $cnt == 0 ? ' style="margin-top: 1.0em;"' : '';
				
				// determine if they would have another
				$thumb = $item['haveAnother'] == 1 ? 'yes' : 'no';
				$have = '<img src="' . base_url() . 'images/haveanother_' . $thumb . '25.jpg" width="25" height="25" title="have another ' . $item['beerName'] . ' by ' . $item['name'] . '" alt="have another ' . $item['beerName'] . ' by ' . $item['name'] . '" />';
				// continue output
				$str .= '
				<div class="singleReviewContainer"' . $css . '>
					<div class="topCurve_white">&nbsp;</div>
					<div class="reviewBorder">
						<div class="genericReview" style="background-image: url(' . base_url() . 'page/createImage/' . $item['beerID'] . '/beer);">
							<div class="genericReview_container">
								<p><a class="bold" href="' . base_url() . 'beer/review/' . $item['beerID'] . '">' . $item['beerName'] . '</a></p>
								<p><a href="' . base_url() . 'brewery/info/' . $item['establishmentID'] . '">' . $item['name'] . '</a></p>
								<p><a href="' . base_url() . 'beer/style/' . $item['styleID'] . '">' . $item['style'] . '</a></p>
								<p><span class="score">' . number_format(round($item['rating'], 1), 1) . '</span>/10 by <a href="' . base_url() . 'user/profile/' . $item['userID'] . '">' . $item['username'] . '</a> <span class="ha">' . $have . '</p>
				';
				if($item['shortrating'] == "1") {
					$ratingCalc = number_format((($item['aroma'] * (PERCENT_AROMA / 100)) + ($item['taste'] * (PERCENT_TASTE / 100)) + ($item['look'] * (PERCENT_LOOK / 100)) + ($item['drinkability'] * (PERCENT_DRINKABILITY / 100))), 1);
					$str .= '
								<p class="bold">Short Rating</p>
								<p>Aroma: <span class="bold">' . $item['aroma'] . '</span> (' . PERCENT_AROMA . '%)</p>
								<p>Taste: <span class="bold">' . $item['taste'] . '</span> (' . PERCENT_TASTE . '%)</p>
								<p>Look: <span class="bold">' . $item['look'] . '</span> (' . PERCENT_LOOK . '%)</p>
								<p>Drinkability: <span class="bold">' . $item['drinkability'] . '</span> (' . PERCENT_DRINKABILITY . '%)</p>
								<p><span class="bold requied">Overall: ' . $ratingCalc . '</span></p>
					';
				} else {
					$str .= '<p>' . substr($item['comments'], 0, 500) . '...<a href="' . base_url() . 'beer/review/' . $item['beerID'] . '">read more</a></p>';
				}
				$str .= '
								<p class="ratingInfo">Overall Rating: <span class="green">' . number_format($ratingAverage, 1) . '</span>/10 by <span>' . $ratingTotalTimes . '</span> <span class="ha">' . $haveMore . '</span></p>
							</div>
						</div>
					</div>
					<div class="bottomCurve">&nbsp;</div>	
				</div>
				';
				// increment the counter
				$cnt++;
			}
			$str .= 
				$pagination_bottom . '
			</div>
			';
		}
		// return the output
		return $str;
	}
	
	public function getRatingByID($id) {
		// get the specific brewery
		$item = $this->ci->RatingModel->getRatingByID($id);
		$alcoholContent = empty($item['alcoholContent']) ? '&nbsp;' : $item['alcoholContent'] . '%';
		$seasonal = empty($item['seasonal']) ? 'No' : 'Yes';
		$str = '
					<dl class="dl_tableDisplay">
						<dt>Beer:</dt><dd>' . $item['beerName'] . '</dd>
						<dt>Brewery:</dt><dd><a href="' . $item['url'] . '" target="_blank">' . $item['name'] . '</a></dd>
						<dt>Style:</dt><dd>' . $item['style'] . '</dd>
						<dt>ABV:</dt><dd>' . $alcoholContent . '</dd>
		';
		$str .= empty($item['malts']) ? '' : '<dt>Malts:</dt><dd>' . $item['malts'] . '</dd>';
		$str .= empty($item['hops']) ? '' : '<dt>Hops:</dt><dd>' . $item['hops'] . '</dd>';
		$str .= empty($item['yeast']) ? '' : '<dt>Yeast:</dt><dd>' . $item['yeast'] . '</dd>';
		$str .= empty($item['gravity']) ? '' : '<dt>Gravity:</dt><dd>' . $item['gravity'] . '</dd>';
		$str .= empty($item['ibu']) ? '' : '<dt>IBU:</dt><dd>' . $item['ibu'] . '</dd>';
		$str .= empty($beer['food']) ? '' : '<dt>Food:</dt><dd>' . $beer['food'] . '</dd>';
		$str .= empty($beer['glassware']) ? '' : '<dt>Glassware:</dt><dd>' . $beer['glassware'] . '</dd>';
		$str .= '
						<dt>Seasonal:</dt><dd>' . $seasonal . '
		';
		$str .= $seasonal == 'No' ? '' : ' - ' . $item['seasonalPeriod'];
		$str .= '
						</dd>
						<dt>Date Tasted:</dt><dd>' . $item['dateTasted'] . ' by ' . $item['firstname'] . ' ' . $item['lastname'] . '</dd>
						<dt>Rating:</dt><dd>' . $item['rating'] . '</dd>
						<dt>Color:</dt><dd>' . $item['color'] . '</dd>
						<dt>Packaging:</dt><dd>' . $item['package'] . '</dd>
						<dt>Price:</dt><dd>$' . $item['price'] . '</dd>
						<dt class="borderBottom_none">Comments:</dt><dd class="borderBottom_none">' .nl2br($item['comments']) . '</dd>
					</dl>
					<br class="left" />
		';
		return $str;
	}
	
	public function getRatingsByUserID($id) {
		$ratings = $this->ci->RatingModel->getRatingsByUserID($id);
		
		$str = '';
		$numberOfRatings = count($ratings);
		if($numberOfRatings > 0) {
			foreach($ratings as $rating) {
				$str .= '
					<div class="list_ratings">
						<div class="atoggle">
							<h1>' . $rating['rating'] . '</h1>
							<dl>
								<dt>' . $rating['beerName'] . ' (' . $rating['alcoholContent'] . '% ABV)</dt>
								<dd>' . $rating['name'] . '</dd>
							</dl>
							<br class="left" />
						</div>
						
						<div class="accordion_content">
							<ul class="list_item">
								<li>' . $rating['dateTasted'] . '</li>
								<li>' . $rating['package'] . ' for $' . $rating['price'] . '</li>
								<li>' . $rating['color'] . '</li>
								<li class="comments">' . nl2br($rating['comments']) . '</li>
							</ul>
						</div>
					</div>
				';
			}
		} else {
			$str = 'You have not rated any beers to this point.';
		}
		
		//echo '<pre>'; print_r($ratings); echo '</pre>'; exit;
		return $str;
	}
	
	public function createRating($config) {//echo '<pre>'; print_r($config); echo '</pre>';
		$rating = array(
			'beerName' => ''
			, 'id' => ''
			, 'alcoholContent' => ''
			, 'price' => ''
			, 'establishmentID' => ''
			, 'styleID' => ''
			, 'btnValue' => 'Rate'
			, 'action' => 'action="' . base_url() . 'ajax/addData/rating/"'
			, 'onsubmit' => 'onsubmit="new Ajax.Request(\'' . base_url() . 'ajax/addData/rating\', {asynchronous: true, evalScripts: true, method: \'post\', parameters: Form.serialize(this), onLoading: function() {showSpinner(\'contents\');}, onComplete: function(response) {$(\'contents\').update(response.responseText);}}); return false;"'
		);
		
		array_unshift($config['beer'], array('id' => '', 'name' => 'Select a Beer'));
		$array = array(
			'data' => $config['beer']
			, 'id' => 'slt_beer'
			, 'name' => 'slt_beer'
			, 'selected' => ''
		);
		$beerDropDown = createDropDown($array);		
		
		$str = '
			<form class="edit" method="post" ' . $rating['action'] . ' ' . $rating['onsubmit'] . '>
				<label for="slt_beer">Choose Beer:</label>
				' . $beerDropDown . '
				
				<input type="submit" id="btn_submit" name="btn_submit" value="' . $rating['btnValue'] . '" />
				<input type="hidden" id="hdn_step" name="hdn_step" value="begin_rating" />
			</form>	
			
			<h3>OR</h3>

			<a href="#" onclick="new Ajax.Request(\'' . base_url() . 'ajax/addData/rating/brewery\', {asynchronous: true, evalScripts: true, method: \'post\', onLoading: function() {showSpinner(\'contents\');}, onComplete: function(response) {$(\'contents\').update(response.responseText);}}); return false;">Add New Beer</a>			
		';	
		return $str;
	}
	
	public function createForm($config) {//echo '<pre>'; print_r($config);exit;
		$rating = array(
			'beer' => ''
			, 'beerID' => ''
			, 'styleID' => ''
			, 'packageID' => ''
			, 'id' => ''
			, 'btnValue' => 'Add'
			, 'hdnValue' => 'save_rating'
			, 'action' => 'action="' . base_url() . 'ajax/addData/rating/"'
			, 'onsubmit' => 'onsubmit="new Ajax.Request(\'' . base_url() . 'ajax/addData/rating\', {asynchronous: true, evalScripts: true, method: \'post\', parameters: Form.serialize(this), onLoading: function() {showSpinner(\'contents\');}, onComplete: function(response) {$(\'contents\').update(response.responseText);}}); return false;"'
			, 'rating' => ''
			, 'dateTasted' => ''
			, 'mdate' => ''
			, 'color' => ''
			, 'comments' => ''
			, 'haveAnother' => ''
			, 'price' => ''			
		);		
				
		if(key_exists('id', $config)) {
			$rating = $this->ci->RatingModel->getRatingByID($config['id']);
			$rating['btnValue'] = 'Update';
			$rating['hdnValue'] = 'update_rating';
			$rating['action'] = 'action="' . base_url() . 'ajax/editData/beer/' . $config['id'] . '"';
			$rating['onsubmit'] = 'onsubmit="new Ajax.Request(\'' . base_url() . 'ajax/editData/rating/' . $config['id'] . '\', {asynchronous: true, evalScripts: true, method: \'post\', parameters: Form.serialize(this), onLoading: function() {showSpinner(\'item_list_container_' . $rating['id'] . '\');}, onComplete: function(response) {$(\'item_list_container_' . $config['id'] . '\').update(response.responseText); $(\'cancel_' . $config['id'] . '\').style.display=\'none\'; $(\'edit_' . $config['id'] . '\').style.display = \'block\';}}); return false;"';
		}
		
		$str = '';
		$hdnID = '';
		if(!key_exists('id', $config)) {				
			$alcoholContent = empty($config['beer']['alcoholContent']) ? '&nbsp;' : $config['beer']['alcoholContent'] . '%';
			//$seasonal = empty($item['seasonal']) ? 'No' : 'Yes';
			$seasonal = empty($config['beer']['seasonal']) ? 'No' : 'Yes - ' . $config['beer']['seasonalPeriod'];
			$str = '
			<div class="item">
				<div class="list_itemContainer">					
					<dl class="dl_tableDisplay">
						<dt>Beer:</dt><dd>' . $config['beer']['beerName'] . '</dd>
						<dt>Brewery:</dt><dd><a href="' . $config['beer']['url'] . '" target="_blank">' . $config['beer']['name'] . '</a></dd>
						<dt>Style:</dt><dd>' . $config['beer']['style'] . '</dd>
						<dt>ABV:</dt><dd>' . $alcoholContent . '</dd>
			';
			$str .= empty($item['malts']) ? '' : '<dt>Malts:</dt><dd>' . $item['malts'] . '</dd>';
			$str .= empty($item['hops']) ? '' : '<dt>Hops:</dt><dd>' . $item['hops'] . '</dd>';
			$str .= empty($item['yeast']) ? '' : '<dt>Yeast:</dt><dd>' . $item['yeast'] . '</dd>';
			$str .= empty($item['gravity']) ? '' : '<dt>Gravity:</dt><dd>' . $item['gravity'] . '</dd>';
			$str .= empty($item['ibu']) ? '' : '<dt>Malts:</dt><dd>' . $item['ibu'] . '</dd>';
			$str .= empty($beer['food']) ? '' : '<dt>Food:</dt><dd>' . $beer['food'] . '</dd>';
			$str .= empty($beer['glassware']) ? '' : '<dt>Glassware:</dt><dd>' . $beer['glassware'] . '</dd>';
			$str .= '
						<dt>Seasonal:</dt><dd>' . $seasonal . '</dd>
			';

			$str .= '
						</dd>
					</dl>
					<br class="left" />
				</div>
				<br class="both" />
			</div>
			';
			$hdnID = '<input type="hidden" id="hdn_beerID" name="hdn_beerID" value="' . $config['beerID'] . '" />';
		} else {
			$alcoholContent = empty($rating['alcoholContent']) ? '&nbsp;' : $rating['alcoholContent'] . '%';
			$str = '
			<div class="item">
				<div class="list_itemContainer">					
					<dl class="dl_tableDisplay">
						<dt>Beer:</dt><dd>' . $rating['beerName'] . '</dd>
						<dt>Brewery:</dt><dd><a href="' . $rating['url'] . '" target="_blank">' . $rating['name'] . '</a></dd>
						<dt>Style:</dt><dd>' . $rating['style'] . '</dd>
						<dt>ABV:</dt><dd>' . $alcoholContent . '</dd>
					</dl>
					<br class="left" />
				</div>
				<br class="both" />
			</div>
			';
			//$hdnID = '<input type="hidden" id="hdn_id" name="hdn_id" value="' . $rating['id'] . '" />';
		}
		
		$array = array(
			'data' => $config['packages']
			, 'id' => 'slt_package'
			, 'name' => 'slt_package'
			, 'selected' => $rating['packageID']
		);
		$packageDropDown = '<label for="slt_package">Package:</label>' . createDropDown($array);
		
		unset($array);
		$array = array(
			'data' => array(
				array('id' => '0', 'name' => 'No')
				, array('id' => '1', 'name' => 'Yes')
			)
			, 'id' => 'slt_haveAnother'
			, 'name' => 'slt_haveAnother'
			, 'selected' => $rating['haveAnother']
		);
		$haveAnotherDropDown = '<label for="slt_haveAnother">Have Another:</label>' . createDropDown($array);
			
		
		unset($array);
		$array = array(
			'data' => array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10')
			, 'id' => 'slt_rating'
			, 'name' => 'slt_rating'
			, 'selected' => $rating['rating']
		);
		$ratingDropDown = createDropDownNoKeys($array);		
						
		$str .= '
			<form class="edit" method="post" ' . $rating['action'] . ' ' . $rating['onsubmit'] . '>
				<label for="slt_rating">Rating:</label>
				' . $ratingDropDown . '
				
				<label for="txt_dateTasted">Date Tasted:</label>
				<input type="text" id="txt_dateTasted" name="txt_dateTasted" value="' . $rating['mdate'] . '" />
				
				<label for="txt_color">Color:</label>
				<input type="text" id="txt_color" name="txt_color" value="' . $rating['color'] . '" />	
				
				<label for="ttr_comments">Comments:</label>
				<textarea id="ttr_comments" name="ttr_comments">' . $rating['comments'] . '</textarea>					
				
				' . $haveAnotherDropDown . '				
				
				' . $packageDropDown . '
				
				<label for="txt_price">Price:</label>
				<input type="text" id="txt_price" name="txt_price" value="' . $rating['price'] . '" />
			
				<input type="submit" id="btn_submit" name="btn_submit" value="' . $rating['btnValue'] . '" />
				<input type="hidden" id="hdn_step" name="hdn_step" value="' . $rating['hdnValue'] . '" />
				' . $hdnID . '
			</form>
			<script type="text/javascript">
			/*<![CDATA[*/
			Calendar.setup({
				dateField : \'txt_dateTasted\',
				triggerElement : \'txt_dateTasted\'
			})
			/*]]>*/
			</script>
		';	
		return $str;
	}
	
	public function showBreweries($config) {
		// rating array
		$rating = array(
			'beerName' => ''
			, 'id' => ''
			, 'alcoholContent' => ''
			, 'price' => ''
			, 'establishmentID' => ''
			, 'styleID' => ''
			, 'btnValue' => 'Continue (Select Beer)'
			, 'action' => 'action="' . base_url() . 'ajax/addData/rating/"'
			, 'onsubmit' => 'onsubmit="new Ajax.Request(\'' . base_url() . 'ajax/addData/rating\', {asynchronous: true, evalScripts: true, method: \'post\', parameters: Form.serialize(this), onLoading: function() {showSpinner(\'contents\');}, onComplete: function(response) {$(\'contents\').update(response.responseText);}}); return false;"'
		);
		
		array_unshift($config['breweries'], array('id' => '', 'name' => 'Select a Brewery'));
		$array = array(
			'data' => $config['breweries']
			, 'id' => 'slt_brewery'
			, 'name' => 'slt_brewery'
			, 'selected' => ''
		);
		$breweryDropDown = createDropDown($array);		
		
		$str = '
			<form class="edit" method="post" ' . $rating['action'] . ' ' . $rating['onsubmit'] . '>
				<label for="slt_brewery">Choose Brewery:</label>
				' . $breweryDropDown . '
				
				<input type="submit" id="btn_submit" name="btn_submit" value="' . $rating['btnValue'] . '" />
				<input type="hidden" id="hdn_step" name="hdn_step" value="beer" />
			</form>	
			
			<h3>OR</h3>

			<a href="#" onclick="new Ajax.Request(\'' . base_url() . 'ajax/addData/rating/brewery_form\', {asynchronous: true, evalScripts: true, method: \'post\', onLoading: function() {showSpinner(\'contents\');}, onComplete: function(response) {$(\'contents\').update(response.responseText);}}); return false;">Add New Brewery</a>			
		';	
		return $str;
	}
	
	public function showBeersForBreweryForm($config, $establishmentID = '', $dropDown = true) {
		// rating array
		$rating = array(
			'btnValue' => 'Continue (Rate Beer)'
			, 'action' => 'action="' . base_url() . 'ajax/addData/rating/"'
			, 'onsubmit' => 'onsubmit="new Ajax.Request(\'' . base_url() . 'ajax/addData/rating\', {asynchronous: true, evalScripts: true, method: \'post\', parameters: Form.serialize(this), onLoading: function() {showSpinner(\'contents\');}, onComplete: function(response) {$(\'contents\').update(response.responseText);}}); return false;"'
		);
		
		array_unshift($config['beers'], array('id' => '', 'name' => 'Select a Beer'));
		$array = array(
			'data' => $config['beers']
			, 'id' => 'slt_beer'
			, 'name' => 'slt_beer'
			, 'selected' => ''
		);
		$beerDropDown = createDropDown($array);		
		
		$str = '';
		if($dropDown === true) {
			$str = '
				<form class="edit" method="post" ' . $rating['action'] . ' ' . $rating['onsubmit'] . '>
					<label for="slt_brewery">Choose Beer:</label>
					' . $beerDropDown . '
					
					<input type="submit" id="btn_submit" name="btn_submit" value="' . $rating['btnValue'] . '" />
					<input type="hidden" id="hdn_step" name="hdn_step" value="begin_rating" />
				</form>	
				
				<h3>OR</h3>
			';
		}
		$str .= '
			<a href="#" onclick="new Ajax.Request(\'' . base_url() . 'ajax/addData/rating/beer_form/' . $establishmentID . '\', {asynchronous: true, evalScripts: true, method: \'post\', onLoading: function() {showSpinner(\'contents\');}, onComplete: function(response) {$(\'contents\').update(response.responseText);}}); return false;">Add New Beer</a>			
		';	
		return $str;
	}
	
	public function getTitle() {
		return $this->title;
	}
}
?>