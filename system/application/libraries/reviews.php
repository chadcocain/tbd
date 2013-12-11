<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Reviews {
	private $ci;
	private $title = 'Review';

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
	
	public function getRatingsLimit($limit) {
		// get all the beers
		$items = $this->ci->RatingModel->getAll($limit);
		// start the output 
		$str = '
			<div class="item">
				<h3>Latest ' . $limit . ' reviews:</h3>
		';
		// iterte through the list
		foreach($items as $item) {
			$alcoholContent = empty($item['alcoholContent']) ? '&nbsp;' : $item['alcoholContent'] . '%';
			$seasonal = empty($item['seasonal']) ? 'No' : 'Yes';
			$str .= '			
				<div class="list_itemContainer">					
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
						<dt>Date Tasted:</dt><dd>' . $item['dateTasted'] . ' by ' . $item['firstname'] . '</dd>
						<dt>Rating:</dt><dd>' . $item['rating'] . '</dd>
						<dt>Color:</dt><dd>' . $item['color'] . '</dd>
						<dt>Packaging:</dt><dd>' . $item['package'] . '</dd>
						<dt>Price:</dt><dd>$' . $item['price'] . '</dd>
						<dt class="borderBottom_none">Comments:</dt><dd class="borderBottom_none">' .nl2br($item['comments']) . '</dd>
					</dl>
					<br class="left" />
				</div>
			';
		}
		$str .= '
			</div>
		';
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
	
	public function createForm($config) {//echo '<pre>'; print_r($_POST); echo '</pre>';
		// default button text
		$buttonText = 'Add Review';
		
		// check if this is for a beer
		$beerID = key_exists('beerID', $config) ? $config['beerID'] : '';
		// check if there is a review for this user and review
		$rating = $this->ci->RatingModel->getRatingsByUserIDEstablishmentID($config['userID'], $config['establishmentID'], $beerID);
		// check if a rating exists
		if(!empty($rating)) {
			$buttonText = 'Update Review';
		}		
		
		// holder for the output
		$str = '';
		switch($config['type']) {
			case 'beer':
				$vRating = set_value('slt_rating');
				$vDateTasted = set_value('txt_dateTasted');
				$vColor = set_value('txt_color');
				$vComments = set_value('ttr_comments');
				$vHaveAnother = set_value('slt_haveAnother');
				$vPackage = set_value('slt_package');
				$vPrice = set_value('txt_price');
				
				// get the information about the beer, if it is a beer reveiw
				$item = $this->ci->BeerModel->getBeerByID($beerID);
				// get the image to show
				$img = checkForImage(array('picture' => $item['picture'], 'id' => $beerID), false);
				
				$alcoholContent = empty($item['alcoholContent']) ? '&nbsp;' : $item['alcoholContent'] . '%';
				$seasonal = empty($item['seasonal']) ? 'No' : 'Yes';
				$str = '
				<div class="item">
					<div class="list_itemContainer">
						' . $img . '					
						<dl class="dl_tableDisplay">
							<dt>Beer:</dt><dd>' . $item['beerName'] . '</dd>
							<dt>Brewery:</dt><dd><a href="' . base_url() . 'brewery/info/' . $config['establishmentID'] . '">' . $item['name'] . '</a></dd>
							<dt>Web Site:</dt><dd><a href="' . $item['url'] . '" target="_blank">' . $item['name'] . '</a></dd>
							<dt>Style:</dt><dd>' . $item['style'] . '</dd>
							<dt>ABV:</dt><dd>' . $alcoholContent . '</dd>
				';
				$str .= empty($item['malts']) ? '' : '<dt>Malts:</dt><dd>' . $item['malts'] . '</dd>';
				$str .= empty($item['hops']) ? '' : '<dt>Hops:</dt><dd>' . $item['hops'] . '</dd>';
				$str .= empty($item['yeast']) ? '' : '<dt>Yeast:</dt><dd>' . $item['yeast'] . '</dd>';
				$str .= empty($item['gravity']) ? '' : '<dt>Gravity:</dt><dd>' . $item['gravity'] . '</dd>';
				$str .= empty($item['ibu']) ? '' : '<dt>Malts:</dt><dd>' . $item['ibu'] . '</dd>';
				$str .= empty($item['food']) ? '' : '<dt>Food:</dt><dd>' . $item['food'] . '</dd>';
				$str .= empty($item['glassware']) ? '' : '<dt>Glassware:</dt><dd>' . $item['glassware'] . '</dd>';
				$str .= '
							<dt>Seasonal:</dt><dd>' . $seasonal . '
				';
				
				$str .= $seasonal == 'No' ? '' : ' - ' . $item['seasonalPeriod'];
				$str .= '
							</dd>
						</dl>
						<br class="left" />
					</div>
					<br class="both" />
				</div>
				';
				
				$packageID = '';
				if(!empty($vPackage)) { 
					$packageID = $vPackage;
				} else if(key_exists('packageID', $rating)) {
					$packageID = $rating['packageID'];
				}
				$array = array(
					'data' => $config['packages']
					, 'id' => 'slt_package'
					, 'name' => 'slt_package'
					, 'selected' => $packageID
				);
				$packageDropDown = '<label for="slt_package"><span class="required">*</span> Package:</label>' . createDropDown($array);
				
				unset($array);
				$haveAnother = '';
				if(!empty($vHaveAnother)) { 
					$haveAnother = $vHaveAnother;
				} else if(key_exists('haveAnother', $rating)) {
					$haveAnother = $rating['haveAnother'];
				}
				$array = array(
					'data' => array(
						array('id' => '0', 'name' => 'No')
						, array('id' => '1', 'name' => 'Yes')
					)
					, 'id' => 'slt_haveAnother'
					, 'name' => 'slt_haveAnother'
					, 'selected' => $haveAnother
				);
				$haveAnotherDropDown = '<label for="slt_haveAnother"><span class="required">*</span> Have Another:</label>' . createDropDown($array);
					
				
				unset($array);
				$rate = '';
				if(!empty($vRating)) { 
					$rate = $vRating;
				} else if(key_exists('rating', $rating)) {
					$rate = $rating['rating'];
				}
				$array = array(
					'data' => array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10')
					, 'id' => 'slt_rating'
					, 'name' => 'slt_rating'
					, 'selected' => $rate
				);
				$ratingDropDown = createDropDownNoKeys($array);		
					
				$mdate = '';
				if(!empty($vDateTasted)) {
					$mdate = $vDateTasted;
				} else if(!empty($rating)) {
					$mdate = $rating['mdate'];	
				}
				
				$color = '';
				if(!empty($vColor)) {
					$color = $vColor;
				} else if(!empty($rating)) {
					$color = $rating['color'];
				}
				
				$comments = '';
				if(!empty($vComments)) {
					$comments = $vComments;
				} else if(!empty($rating)) {
					$comments = $rating['comments'];
				}
				
				$price = '';
				if(!empty($vPrice)) {
					$price = $vPrice;
				} else if(!empty($rating)) {
					$price = $rating['price'];
				}
				
				$str .= '
					<form class="edit" method="post" action="' . base_url() . substr($this->ci->uri->uri_string(), 1) . '">
						<div class="formBlock' . (form_error('slt_rating') ? ' formBlockError' : '') . '">
				';
				if(form_error('slt_rating')) {
					$str .= '<div class="formError">' . form_error('slt_rating') . '</div>';
				}
				$str .= '
							<label for="slt_rating"><span class="required">*</span> Rating:</label>
							' . $ratingDropDown . '
							<div class="explanation">On a scale of 1 to 10 what you thought of the beer.  Five (5) would be an average beer.</div>
						</div>
						
						<div class="formBlock' . (form_error('txt_dateTasted') ? ' formBlockError' : '') . '">
				';
				if(form_error('txt_dateTasted')) {
					$str .= '<div class="formError">' . form_error('txt_dateTasted') . '</div>';
				}
				$str .= '
							<label for="txt_dateTasted"><span class="required">*</span> Date Tasted:</label>
							<input type="text" id="txt_dateTasted" name="txt_dateTasted" value="' . $mdate . '" />
							<div class="explanation">Date you enjoyed the beer.  Format: yyyy-mm-dd.  The calendar helper should automatically do this for you.</div>
						</div>
						
						<div class="formBlock' . (form_error('txt_color') ? ' formBlockError' : '') . '">
				';
				if(form_error('txt_color')) {
					$str .= '<div class="formError">' . form_error('txt_color') . '</div>';
				}
				$str .= '
							<label for="txt_color"><span class="required">*</span> Color:</label>
							<input type="text" id="txt_color" name="txt_color" value="' . $color . '" />
							<div class="explanation">The color of the beer.  Can inlcude clarity but remember American craft beers have a tendency to be hazy/cloudy when first poured at low temperatures.</div>
						</div>	
						
						<div class="formBlock' . (form_error('ttr_comments') ? ' formBlockError' : '') . '">
				';
				if(form_error('ttr_comments')) {
					$str .= '<div class="formError">' . form_error('ttr_comments') . '</div>';
				}
				$str .= '
							<label for="ttr_comments"><span class="required">*</span> Comments:</label>
							<textarea id="ttr_comments" name="ttr_comments">' . $comments . '</textarea>
							<div class="explanation">Your review of the beer.  Have fun, inject some of yourself, but please keep them clean.</div>
						</div>				
						
						<div class="formBlock' . (form_error('slt_haveAnother') ? ' formBlockError' : '') . '">
				';
				if(form_error('slt_haveAnother')) {
					$str .= '<div class="formError">' . form_error('slt_haveAnother') . '</div>';
				}
				$str .= '
							' . $haveAnotherDropDown . '
							<div class="explanation">Would you enjoy another one these?  It doesn&#39;t have to be sessioned to "have another."</div>
						</div>				
						
						<div class="formBlock' . (form_error('slt_package') ? ' formBlockError' : '') . '">
				';
				if(form_error('slt_package')) {
					$str .= '<div class="formError">' . form_error('slt_package') . '</div>';
				}
				$str .= '
							' . $packageDropDown . '
							<div class="explanation">The type of serving.</div>
						</div>
						
						<div class="formBlock' . (form_error('txt_price') ? ' formBlockError' : '') . '">
				';
				if(form_error('txt_price')) {
					$str .= '<div class="formError">' . form_error('txt_price') . '</div>';
				}
				$str .= '
							<label for="txt_price"><span class="required">*</span> Price:</label>
							<input type="text" id="txt_price" name="txt_price" value="' . $price . '" />
							<div class="explanation">Price should be a decimal value with two digits following the decimal: n.nn</div>
						</div>
					
						<input type="submit" id="btn_submit" name="btn_submit" value="' . $buttonText . '" />
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
				break;
		}
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