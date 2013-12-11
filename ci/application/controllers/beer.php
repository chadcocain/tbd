<?php
class Beer extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper(array('url', 'admin', 'users', 'form', 'js'));
		// helper to get the quote for the footer - in users_helper.php
		getFooterQuote();
	}
	
	private function doLoad($config) {
		$array = array(
			'header' => 'inc/normalHeader.inc.php'
			, 'headerFrontEnd' => 'inc/header_frontend.inc.php'
			, 'formMast' => 'inc/formMast.inc.php'
			, 'navigation' => 'inc/navigation.inc.php'
			, 'review' => 'beer/review.php'
			, 'reviewGeneric' => 'beer/reviewGeneric.php'
			, 'createReview' => 'beer/createReview.php'
			, 'ratingSystem' => 'beer/ratingSystem.php'
			, 'srm' => 'beer/srm.php'
			, 'style' => 'beer/style.php'
			, 'swaps' => 'beer/swaps.php'
			, 'ratings' => 'beer/ratings.php'
			, 'addBeer' => 'beer/add.php'
			, 'masthead' => 'admin/masthead.php'
			, 'footer' => 'inc/footer.inc.php'
			, 'footerFrontEnd' => 'inc/footer_frontend.inc.php'
		);
		
		foreach($config['pages'] as $page => $data) {
			if($data === true) {
				$this->load->view($array[$page], $config['data']);
			} else {
				$this->load->view($array[$page]);
			}
		}
	}
	
	public function review() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// load the beer model
		$this->load->model('BeerModel', '', true);
		// load the swap model
		$this->load->model('SwapModel', '', true);
		// load the beer library
		$this->load->library('beers');
		// load the js helper
		$this->load->helper(array('js', 'social_media'));
		
		// get the id that is passed
		$id = $this->uri->segment(3);
		
		if($id === false || !is_numeric($id)) {
			// show the generic page for reviews
			// load the beer model
			$this->load->model('RatingModel', '', true);
			// load the beer library
			$this->load->library('rating');
			// get the three newest ratings
			$this->_data['beerReviews'] = $this->rating->getRatingsReviewHome();
			
			// set the page information
			$this->_data['seo'] = getSEO();
			// get the information ready for display
			$arr_load = array(
				'pages' => array(				
					'headerFrontEnd' => true
					, 'formMast' => true
					, 'navigation' => true
					, 'reviewGeneric' => true
					, 'footerFrontEnd' => true
				)
				, 'data' => $this->_data
			);				
			// load all parts for the view
			$this->doLoad($arr_load);
		} else {
			// get the information for the particular beer
			$beer = $this->beers->showBeerRatings($id, false, $logged);
			// set the output for the screen for the left side
			$this->_data['leftCol'] = $beer['str'];
			// set the output for the screen for the right side
			$this->_data['rightCol'] = $beer['str_rightCol'];			
			// set the page seo information
			$this->_data['seo'] = array_slice($beer, 0, 3);
				
			// get the information ready for display
			$arr_load = array(
				'pages' => array(				
					'headerFrontEnd' => true
					, 'formMast' => true
					, 'navigation' => true
					, 'review' => true
					, 'footerFrontEnd' => true
				)
				, 'data' => $this->_data
			);			
			// load all parts for the view
			$this->doLoad($arr_load);
		}
	}
	
	public function createReview() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// get the type of form to display
		$type = $this->uri->segment(4);
		
		// create the right column information that will
		// appear no matter if the situation
		$this->_data['rightCol'] = '
			<h4><span>Tips on Reviewing</span></h4>
			<ul>
				<li>Choose the type of review style you prefer: normal or short.</li>
				<li>Once you choose a review type and submit your review, you will not be able to change the review type.  Choose wisely.</li>
				<li>You can change the review type from beer to beer, but we think you try to choose one that best suites your preferences.</li>
				<li>10s should be few and far between: the beer is perfect and needs no improvement.</li>
				<li>Reviews should be recent and, preferrably, from handwritten notes as it is sometimes difficult to remember the caveats of a beer.</li>
				<li>Try to review a beer with an understanding of the style of beer that it is.  Sometimes preconceived biasis on a style can cause trouble.  Understand the effort involved in brewing the style.</li>
		';
		if($type == false) {
			$this->_data['rightCol'] .= '<li>Have fun with your review (within reason) let your personality show through.</li>';
		}
		$this->_data['rightCol'] .= '
				<li>This is not a race to see who can review the most beers in a day, week, month, year, etc.  Like your beer, enjoy the experience.</li>
			</ul>
		
			<h4><span>Rules of Reviewing</span></h4>
			<ul>
				<li>Only <span class="bold">American craft beers</span> should be reviewed here.</li>
		';
		if($type == false) {
			$this->_data['rightCol'] .= '
				<li>Use <span class="bold">English</span>!</li>
				<li>Some people are offended by off language, keep it to a minimum.</li>
			';
		}
		if($type == false) {
			$this->_data['rightCol'] .= '<li>Review the beer and the characteristics of the beer.  Don&#39;t review a beer if you have nothing constructive to say about the experience.  This isn&#39;t a way to achieve a personal vendetta against a brewery.</li>';
		} else {
			$this->_data['rightCol'] .= '<li>Review the beer and the characteristics of the beer.  This isn&#39;t a way to achieve a personal vendetta against a brewery.</li>';
		}
		if($type == false) {
			$this->_data['rightCol'] .= '<li>Don&#39;t slam another user for their review.  This isn&#39;t a he said, she said debate; it is your opinion about the beer.  Period!</li>';
		}
		$this->_data['rightCol'] .= '
			</ul>
		';
		
		if($logged === true) {
			// load the beer model
			$this->load->model('BeerModel', '', true);
			// load the rating model
			$this->load->model('RatingModel', '', true);
			// load the beer library
			$this->load->library('beers');
			// user session info
			$userInfo = $this->session->userdata('userInfo');
			
			// get the id that is passed
			// this is the id of the beer that will be reviewed
			$id = $this->uri->segment(3);			
			
			// get the number of reviews
			//$reviewCount = $this->BeerModel->getBeerReviewCount($userInfo['id']);
			
			// check to see if they have reviewed enough beers			
			//if($reviewCount >= MIN_REVIEW_COUNT) {								
			// load the form validation library
			$this->load->library('form_validation');
			// load the helpers
			$this->load->helper(array('js', 'form'));							
			
			if($type !== false) {
				// run the validation and return the result
				if($this->form_validation->run('addShortReviewBeer') == false) {				
					// get the information for the particular beer
					$info = $this->beers->showCreateReview($id, false, $type);
					//echo '<pre>'; print_r($info); echo '</pre>'; exit;
					// set the output for the screen
					$this->_data['leftCol'] = $info['str'];
					
					// set the page seo information
					$this->_data['seo'] = array_slice($info, 0, 3);
					// get the information ready for display
					$arr_load = array(
						'pages' => array(				
							'headerFrontEnd' => true
							, 'formMast' => true
							, 'navigation' => true
							, 'createReview' => true
							, 'footerFrontEnd' => true
						)
						, 'data' => $this->_data
					);			
					// load all parts for the view
					$this->doLoad($arr_load);
				} else {
					// successfull information, so store it
					// get the information about the beer
					$beer = $this->BeerModel->getBeerByID($id);
					// create the data array to store the information
					$array = array(
						'establishmentID' => $beer['establishmentID']
						, 'beerID' => $id
						, 'userID' => $userInfo['id']
						, 'dateTasted' => trim($_POST['txt_dateTasted'])
						, 'aroma' => trim($_POST['aroma'])
						, 'taste' => trim($_POST['taste'])
						, 'look' => trim($_POST['look'])
						, 'drinkability' => trim($_POST['drinkability'])
						, 'haveAnother' => trim($_POST['slt_haveAnother'])
					);
					
					// query the ratings table
					$rating = $this->RatingModel->checkForRatingByUserIDBeerID($userInfo['id'], $id);
					// holder for the rating id
					$ratingID = '';
					// store that information
					if(empty($rating)) {
						// create a new rating
						$ratingID = $this->RatingModel->createShortRating($array);
					} else {
						// add to the array of values
						$array['id'] = $rating['id'];
						// update a rating
						$this->RatingModel->updateShortRatingByID($array);
						// rating id
						$ratingID = $rating['id'];
					}
					
					// check if creation notice is required
					if(SEND_CREATION_NOTICE === true) {
						// include the mail helper
						$this->load->helper('email');
						// get the information about the beer rating
						$ratingInfo = $this->RatingModel->getRatingByID($ratingID);
						// create the configuration array
						$ratingInfo += array('action' => 'shortbeer', 'userID' => $userInfo['id'], 'username' => $userInfo['username'], 'subject' => 'Short Beer Review Addition');
						// send out an email to the admins
						sendFormMail($ratingInfo);
					}
						
					// take them to the page for the beer
					header('Location: ' . base_url() . 'beer/review/' . $id . '/short');
					exit;
				}
			} else {
				// run the validation and return the result
				if($this->form_validation->run('addReviewBeer') == false) {				
					// get the information for the particular beer
					$info = $this->beers->showCreateReview($id, false, $type);
					//echo '<pre>'; print_r($info); echo '</pre>'; exit;
					// set the output for the screen
					$this->_data['leftCol'] = $info['str'];
					
					// set the page seo information
					$this->_data['seo'] = array_slice($info, 0, 3);
					// get the information ready for display
					$arr_load = array(
						'pages' => array(				
							'headerFrontEnd' => true
							, 'formMast' => true
							, 'navigation' => true
							, 'createReview' => true
							, 'footerFrontEnd' => true
						)
						, 'data' => $this->_data
					);			
					// load all parts for the view
					$this->doLoad($arr_load);
				} else {
					// successfull information, so store it
					// get the information about the beer
					$beer = $this->BeerModel->getBeerByID($id);
					// create the data array to store the information
					$array = array(
						'establishmentID' => $beer['establishmentID']
						, 'beerID' => $id
						, 'userID' => $userInfo['id']
						, 'packageID' => trim($_POST['slt_package'])
						, 'dateTasted' => trim($_POST['txt_dateTasted'])
						, 'color' => trim($_POST['txt_color'])
						/*, 'rating' => trim($_POST['slt_rating'])*/
						, 'aroma' => trim($_POST['aroma'])
						, 'taste' => trim($_POST['taste'])
						, 'look' => trim($_POST['look'])
						, 'drinkability' => trim($_POST['drinkability'])
						, 'comments' => trim($_POST['ttr_comments'])
						, 'haveAnother' => trim($_POST['slt_haveAnother'])
						, 'price' => trim($_POST['txt_price'])
					);
					
					// query the ratings table
					$rating = $this->RatingModel->checkForRatingByUserIDBeerID($userInfo['id'], $id);
					// holder for the rating id
					$ratingID = '';
					// store that information
					if(empty($rating)) {
						// create a new rating
						$ratingID = $this->RatingModel->createRating($array);
					} else {
						// add to the array of values
						$array['id'] = $rating['id'];
						// update a rating
						$this->RatingModel->updateRatingByID($array);
						// rating id
						$ratingID = $rating['id'];
					}
					
					// check if creation notice is required
					if(SEND_CREATION_NOTICE === true) {
						// include the mail helper
						$this->load->helper('email');
						// get the information about the beer rating
						$ratingInfo = $this->RatingModel->getRatingByID($ratingID);
						// create the configuration array
						$ratingInfo += array('action' => 'beer', 'userID' => $userInfo['id'], 'username' => $userInfo['username'], 'subject' => 'Beer Review Addition');
						// send out an email to the admins
						sendFormMail($ratingInfo);
					}
						
					// take them to the page for the beer
					header('Location: ' . base_url() . 'beer/review/' . $id);
					exit;
				}	
			}
			/*} else {
				// they haven't reviewed enough to create a 
				// get the information for the particular beer
				$info = $this->beers->showCreateReview($id, true);
				
				// set the output for the screen
				$this->_data['leftCol'] = 
					$info['str'] . '
					<p style="clear: both;">You haven\'t reviewed enough beers (you need ' . MIN_REVIEW_COUNT . ' reviews) to add a new beer.</p>
				';
				
				// set the page seo information
				$this->_data['seo'] = array_slice($info, 0, 3);
				// get the information ready for display
				$arr_load = array(
					'pages' => array(				
						'headerFrontEnd' => true
						, 'formMast' => true
						, 'navigation' => true
						, 'createReview' => true
						, 'footerFrontEnd' => true
					)
					, 'data' => $this->_data
				);			
				// load all parts for the view
				$this->doLoad($arr_load);
			}*/
		} else {
			$array = array(
				'uri' => substr($this->uri->uri_string(), 1)
				, 'search' => array('_')
				, 'replace' => '/'
			);
			$args = swapOutURI($array);
			header('Location: ' . base_url() . $args);
		}
	}
	
	public function style() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// load the beer model
		$this->load->model('BeerModel', '', true);
		// load the beer library
		$this->load->library('beers');		
		
		// get the id that is passed
		// this can be empty if we are at the broad page
		$id = $this->uri->segment(3);
		
		if($id === false || !is_numeric($id)) {
			// set the page information
			$this->_data['seo'] = getSEO();
			// create the output via the library
			//$this->_data['leftCol'] = $this->beers->showStyles($id);
			$style = $this->beers->showStyles($id);
			// set the output for the left column
			$this->_data['leftCol'] = $style['str'];
			// set the output for the right column
			$this->_data['rightCol'] = $style['rightCol'];
		} else {
			// create the information for the particular style
			$style = $this->beers->showStyles($id);
			// set the output for the screen
			$this->_data['leftCol'] = $style['str'];
			// set the output for the right column
			$this->_data['rightCol'] = $style['rightCol'];
			// set the page seo information
			$this->_data['seo'] = array_slice($style, 0, 3);
		}		
		
		// get the information ready for display
		$arr_load = array(
			'pages' => array(				
				'headerFrontEnd' => true
				, 'formMast' => true
				, 'navigation' => true
				, 'style' => true
				, 'footerFrontEnd' => true
			)
			, 'data' => $this->_data
		);					
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function ratingSystem() {
		// get the login boolean
		$logged = checkLogin();

		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');
		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// load the rating system model
		$this->load->model('RatingSystemModel', '', true);
		// load the beer library
		$this->load->library('beers');
			
		// get the information for rating system
		$info = $this->beers->showRatingSystem();
		// set the output for the screen
		$this->_data['leftCol'] = $info;
		// set the output for the right side column
		$this->_data['rightCol'] = '
			<h4><span>More Beer Information</h4>
			<ul>
				<li><a href="' . base_url() . 'beer/style">Beer Styles</a></li>
				<li><a href="' . base_url() . 'beer/srm">Beer Colors</a></li>
				<li><a href="' . base_url() . 'beer/ratingSystem">Beer Rating System</a></li>
			</ul>
		';
		// set the page seo information
		$this->_data['seo'] = getSEO();
		
		// get the information ready for display
		$arr_load = array(
			'pages' => array(				
				'headerFrontEnd' => true
				, 'formMast' => true
				, 'navigation' => true
				, 'ratingSystem' => true
				, 'footerFrontEnd' => true
			)
			, 'data' => $this->_data
		);			
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function srm() {
		// get the login boolean
		$logged = checkLogin();

		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');
		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// load the beer library
		$this->load->library('beers');
		
		// get the information for the srm scale
		$info = $this->beers->showSRM();
		
		// set the output for the screen
		$this->_data['output'] = $info;
		
		// set the page seo information
		$this->_data['seo'] = getSEO();
		// create the heading
		$this->_data['heading'] = 'US Beer Color - Standard Reference Method (SRM)';
		// create the heading
		/*$this->_data['intro'] = '
			<p>Our beer rating system and rating scale is based on overall feel for the beer and the necessity or lack there of, to have another one of the beers in hand. The scale should be similar to other rating scales that you have seen and used. This scale should be pliable to the point in which a you might insinuate your own descriptions for the values, after all, the site is about your personality and subjective views of the beer. So the 10 point scale was born.</p>
			<p>The 10 point scale is just from 1 to 10, with 1 being the lowest rating and 10 being the highest. As mentioned before: something you are familiar with from other areas of life.</p>
		';
		$this->_data['closing'] = '
			<p>Remember when you write a beer review, more than likely, you are taking your first taste of a beer, possibly only, and that there is a lot that factors into the reason you felt the way you did when you wrote a review. Some factors: mood, feelings, like or dislike of beer style, food, other beers drank before, etc. These are the reasons that Two Beer Dudes will more than likely never put a review on the site below a 5. We will try and give the beer another chance on another day.</p>
			<p>One last thing: when tasting beer only have about 2 to 3 ounces and drink beers of the same beer style. Split a 12 ounce bottle between friends, formulate your own opinion and then share with each other. Each of you may have found something different within the confines of the glass container. Really try to enjoy the beer for the style that it is and appreciate the effort that the brewer put into making the beer. Trying to be unbiased will give the best overall chance and rating for a beer. Enjoy!</p>
		';*/
		
		// get the information ready for display
		$arr_load = array(
			'pages' => array(				
				'headerFrontEnd' => true
				, 'formMast' => true
				, 'navigation' => true
				, 'srm' => true
				, 'footerFrontEnd' => true
			)
			, 'data' => $this->_data
		);					
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function swaps() {
        // get the login boolean
        $logged = checkLogin();
        
		if($logged === true) {
             // user info for logged in user
             $userInfo = $this->session->userdata('userInfo');
        
            // create login mast text
            $this->_data['formMast'] = createHeader($logged, $userInfo);
            
			// load the beer model
			$this->load->model('BeerModel', '', true);
			// load the swap model
			$this->load->model('SwapModel', '', true);
			// load the beer library
			$this->load->library('beers');
			
			// set the page seo information
			$this->_data['seo'] = getSEO();
			
			// get the type of swap
			$type = $this->uri->segment(3);
			// get the beer id
			$beerID = $this->uri->segment(4);
			
			// holder for the output
			$output = '';
			
			// make sure both of these are set
			if($type == false || $beerID == false) {
				// create the heading
				$this->_data['leftCol'] = '<h2 class="brown">Beer Swap List</h2>';
				// the output
				$output = '<p>There was a problem processing the request.</p>';
			} else {
				// create the heading
				$this->_data['leftCol'] = '<h2 class="brown">Beer Swap ' . ucfirst(strtolower($type)) . ' List</h2>';
				// go get the output
				$output = $this->beers->swap($beerID, $type);
			}
			
			// store the output for the page
			$this->_data['leftCol'] .= $output;
            
            // right column output
            $this->_data['rightCol'] = '
                <h4><span>More...</span></h4>
                <ul>
                    <li>Beer swapping is done at your own risk.</li>
                </ul>
            ';
            
            // get the information ready for display
            $arr_load = array(
                'pages' => array(                
                    'headerFrontEnd' => true
                    , 'formMast' => true
                    , 'navigation' => true
                    , 'swaps' => true
                    , 'footerFrontEnd' => true
                )
                , 'data' => $this->_data
            );                    
            // load all parts for the view
            $this->doLoad($arr_load);
            
		} else {
			// they are not logged in
			header('Location: ' . base_url() . 'user/login');
			exit;
		}		
	}
	
	public function validMysqlDate($date) {
		// set the error message
		$error = 'The %s is not valid. YYYY-MM-DD is the correct form of dates.';
		// check if there are hyphens
		if(!strstr($date, '-')) {
			$this->form_validation->set_message('validMysqlDate', $error);
			return false;
		}
		// split apart the date by hyphen
		$parts = explode('-', $date);
		// check that there is two hyphens
		if(count($parts) != 3) {
			$this->form_validation->set_message('validMysqlDate', $error);
			return false;
		}
		// check that each part is numeric
		foreach($parts as $val) {
			if(!is_numeric($val)) {
				$this->form_validation->set_message('validMysqlDate', $error);
				return false;
			}
		}
		// make it here, the date is fine
		return true;
	}
	
	public function filterWords($comments) {
		// holder variable
		$boolean = true;
		// set the error message
		$error = 'The %s has some potentially bad words in it.  Please choose your words wisely and appropriately.';
		// get an array of badwords
		$badWords = file('/home/twobeerdudes/www/www/list/badWords.txt', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
		// iterate through the array, checking for bad words
		foreach($badWords as $badWord) {
			if(preg_match('/\b' . $badWord . '\b/i', $comments)) {
				$boolean = false;
				$this->form_validation->set_message('filterWords', $error);
			}			
		}
		// return the result
		return $boolean;
	}
	
	public function decimalNumber($decimal) {
		// set the error message
		$error = 'The %s is not a valid price.';
		// check if there is a period
		if(!strstr($decimal, '.')) {$error .= 1;
			$this->form_validation->set_message('decimalNumber', $error);
			return false;
		}
		// split apart the price by decimal
		$parts = explode('.', $decimal);
		// check that there is only one decimal
		if(count($parts) != 2) {$error .= 2;
			$this->form_validation->set_message('decimalNumber', $error);
			return false;
		}
		// check that before and after the decimal is numeric
		for($i = 0; $i < count($parts); $i++) {
			if(!is_numeric($parts[$i])) {$error .= 3;
				$this->form_validation->set_message('decimalNumber', $error);
				return false;
			}
			// check that there are two digits after the decimal
			if($i == 1 && strlen($parts[$i]) != 2) {$error .= 4;
				$this->form_validation->set_message('decimalNumber', $error);
				return false;
			}
		}
		// make it here, the value is fine
		return true;
	}
	
	public function integerBetween($integer) {
		// holder variable
		$boolean = true;
		// set the error message
		$error = '% has to be an integer between 1 and 10';
		// holder array of potential values
		$array = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
		// iterate through the array, checking for bad words
		if(!in_array($integer, $array)) { 
			$boolean = false;
			$this->form_validation->set_message('integerBetween', $error);
		}
		// return the result
		return $boolean;
	}
	
	public function addBeer() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		if($logged === true) {
			// load the beer model
			$this->load->model('BeerModel', '', true);
			// load the brewery model
			$this->load->model('BreweriesModel', '', true);
			// load the establishment model
			$this->load->model('EstablishmentModel', '', true);
			// load the rating model
			$this->load->model('RatingModel', '', true);
			// load the style model
			$this->load->model('StyleModel', '', true);
			// load the beer library
			$this->load->library('beers');
			// user session info
			$userInfo = $this->session->userdata('userInfo');
			
			// get the number of reviews
			$reviewCount = $this->BeerModel->getBeerReviewCount($userInfo['id']);
			
			// get the establishment id
			$establishmentID = $this->uri->segment(3);
			// get the establishment information
			$est = $this->EstablishmentModel->getEstablishmentExistAndHasBeer($establishmentID);
			// check that the place exists and can brew beer
			if($est === false || !in_array($est['categoryID'], array(1, 4, 6))) {
				// the establishment doesn't brew beer, so can't add one
				// temporary establishment, if they picked one that exists but
				// doesn't do brewing
				$tmp = $est === false ? ' ' : ' (<a href="' . base_url() . 'establishment/info/rating/' . $est['id'] . '">' . $est['name'] . '</a>) ';
				// get the information for the screen
				$this->_data['leftCol'] = '
					<h2 class="brown">Add A Beer</h2>
					<p class="marginTop_8">According to our records, the establishment' . $tmp . 'you have chosen doesn&#39;t brew/sell their own beer.</p>
				';
				// set the page seo information
				$this->_data['seo'] = getSEO();
				// get the information ready for display
				$arr_load = array(
					'pages' => array(				
						'headerFrontEnd' => true
						, 'formMast' => true
						, 'navigation' => true
						, 'addBeer' => true
						, 'footerFrontEnd' => true
					)
					, 'data' => $this->_data
				);				
				// load all parts for the view
				$this->doLoad($arr_load);				
			} else if($reviewCount >= MIN_REVIEW_COUNT) { // check to see if they have reviewed enough beers								
				// load the form validation library
				$this->load->library('form_validation');
				// load the helpers
				$this->load->helper(array('js', 'form'));							
				
				// run the validation and return the result
				if($this->form_validation->run('addBeer') == false) {				
					// get the information for the particular beer
					$info = $this->beers->showAddBeer(false, $est);
					//echo '<pre>'; print_r($info); echo '</pre>'; exit;
					// set the output for the screen
					$this->_data['leftCol'] = $info['leftCol'];
					// set the right column output
					$this->_data['rightCol'] = $info['rightCol'];
					
					// set the page seo information
					$this->_data['seo'] = getSEO();
					// get the information ready for display
					$arr_load = array(
						'pages' => array(				
							'headerFrontEnd' => true
							, 'formMast' => true
							, 'navigation' => true
							, 'addBeer' => true
							, 'footerFrontEnd' => true
						)
						, 'data' => $this->_data
					);				
					// load all parts for the view
					$this->doLoad($arr_load);
				} else {
					// successfull information, so store it
					// get the user id who inserted the beer
					$userInfo = $this->session->userdata('userInfo');
					// create the data array to store the information
					$data = array(
						'establishmentID' => $est['id']
						, 'beerName' => $_POST['txt_beer']
						, 'styleID' => $_POST['slt_style']
						, 'alcoholContent' => $_POST['txt_abv']
                        , 'beerNotes' => $_POST['ttr_beerNotes']
						, 'malts' => $_POST['txt_malts']
						, 'hops' => $_POST['txt_hops']
						, 'yeast' => $_POST['txt_yeast']
						, 'gravity' => $_POST['txt_gravity']
						, 'ibu' => $_POST['txt_ibu']
						, 'food' => $_POST['txt_food']
						, 'glassware' => $_POST['txt_glassware']
						, 'seasonal' => $_POST['slt_seasonal']
						, 'seasonalPeriod' => $_POST['txt_seasonalPeriod']
						, 'userID' => $userInfo['id']
					);
					// insert the information into the database
					$id = $this->BeerModel->createBeer($data);					
					// check if creation notice is required
					if(SEND_NEWBEER_NOTICE === true) {
						// include the mail helper
						$this->load->helper('email');
						// create the configuration array
						$beerInfo = array(
							'action' => 'newBeer'
							, 'beerID' => $id
							, 'userID' => $userInfo['id']
							, 'data' => $data
							, 'subject' => 'New Beer Addition'
						);
						// send out an email to the admins
						sendFormMail($beerInfo);
					}
						
					// take them to the page to create a review
					//header('Location: ' . base_url() . 'beer/createReview/' . $id);
                    header('Location: ' . base_url() . 'beer/review/' . $id);
					exit;
				}	
			} else {
				// set the output for the screen
				$this->_data['leftCol'] = '<p style="clear: both;">You haven\'t reviewed enough beers (you need ' . MIN_REVIEW_COUNT . ' reviews) to add a new beer.</p>';
				
				// set the page seo information
				$this->_data['seo'] = getSEO();
				// get the information ready for display
				$arr_load = array(
					'pages' => array(				
						'headerFrontEnd' => true
						, 'formMast' => true
						, 'navigation' => true
						, 'addBeer' => true
						, 'footerFrontEnd' => true
					)
					, 'data' => $this->_data
				);			
				// load all parts for the view
				$this->doLoad($arr_load);
			}
		} else {
			$array = array(
				'uri' => substr($this->uri->uri_string(), 0)
				, 'search' => array('_')
				, 'replace' => '/'
			);
			$args = swapOutURI($array);
			//echo $args;exit;
			//header('Location: ' . base_url() . $args);
			header('Location: ' . base_url() . 'user/login/' . $args);
		}
	}
	
	public function ratings() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// get the type of ratings to show
		// should be either high or low
		$type = $this->uri->segment(3);
		// holder for the type of values
		$arr_type = array('high', 'low');
		// make sure this value is what we expect
		if(!in_array($type, $arr_type)) {
			$type = 'high';
		}
		// set the config value
		$config['type'] = $type;
		
		// check if the sort is set
		// will be an integer representing a
		// style id
		$styleID = $this->uri->segment(4);
		// check if it is set
		$styleID = $styleID == false ? '' : $config['styleID'] = $styleID;
		
		// load the beer model
		$this->load->model('BeerModel', '', true);
		// load the beers model
		$this->load->library('beers');
		
		// get the information about the beers
		$array = $this->beers->getBestWorstRatings($config);
		//echo '<pre>'; print_r($this->_data);exit;
	
		// set the page seo information
		$this->_data['seo'] = getSEO();
		// get the information ready for display
		$arr_load = array(
			'pages' => array(				
				'headerFrontEnd' => true
				, 'formMast' => true
				, 'navigation' => true
				, 'ratings' => true
				, 'footerFrontEnd' => true
			)
			, 'data' => $this->_data
		);			
		// load all parts for the view
		$this->doLoad($arr_load);
	} 
	
	public function breweryExists($breweryID) {
		// load the user model
		$this->load->model('BreweriesModel', '', true);
		// get the brewery information
		$rs = $this->BreweriesModel->getBreweryByID($breweryID);
		// check if it really exists
		$boolean = count($rs) > 0 ? true : false;
		// check the boolean
		if($boolean === false) {
			$this->form_validation->set_message('breweryExists', 'The %s you have chosen doesn\'t exists.  Please choose another.');
		}
		return $boolean;
	}
	
	public function styleExists($styleID) {
		// load the user model
		$this->load->model('StyleModel', '', true);
		// get the brewery information
		$rs = $this->StyleModel->getStyleByID($styleID);
		// check if it really exists
		$boolean = count($rs) > 0 ? true : false;
		// check the boolean
		if($boolean === false) {
			$this->form_validation->set_message('styleExists', 'The %s you have chosen doesn\'t exists.  Please choose another.');
		}
		return $boolean;
	}
	
	public function seasonalExists($seasonalID) {
		// possible ids of seasonal - there are only two
		$array = array(0, 1);
		// check if it really exists
		$boolean = in_array($seasonalID, $array) ? true : false;
		// check the boolean
		if($boolean === false) {
			$this->form_validation->set_message('seasonalExists', 'The %s you have chosen doesn\'t exists.  Please choose another.');
		}
		return $boolean;
	}
	
	public function addHTMLEntities($str) {
		return htmlentities(($str));
	}
}
?>