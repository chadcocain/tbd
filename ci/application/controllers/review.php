<?php
class Review extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper(array('url', 'users', 'admin', 'js'));
	}
	
	private function doLoad($config) {
		$array = array(
			'header' => 'inc/normalHeader.inc.php'
			, 'review' => 'review/addReview'
			, 'navigation' => 'admin/navigation'
			, 'masthead' => 'admin/masthead'
			, 'footer' => 'inc/footer.inc.php'
		);
		
		foreach($config['pages'] as $page => $data) {
			if($data === true) {
				$this->load->view($array[$page], $config['data']);
			} else {
				$this->load->view($array[$page]);
			}
		}
	}
	
	public function addReview() {
//	echo '<pre>'; print_r($badWords); exit;
		/*if(checkLogin() === true) {
			header('Location: ' . base_url());
			exit;
		}*/
		
		// get the establishment id
		$establishmentID = $this->uri->segment(3);
		// get the type of review to create
		$type = $this->uri->segment(4);
		
		if(empty($establishmentID) || empty($type)) {
			header('Location: ' . base_url());
			exit;
		}
		
		// load the rating model
		$this->load->model('RatingModel', '', true);
		// load the rating library
		$this->load->library('reviews');
		
		// holder for screen display
		$output = '';
		switch($type) {
			case 'beer':
				// get the beer id
				$beerID = $this->uri->segment(5);
				// check if this value is empty
				if(empty($beerID)) {
					header('Location: ' . base_url());
					exit;
				} else {
					// load the beer model
					$this->load->model('BeerModel', '', true);
					// load the beer library
					$this->load->library('beers');
					
					// get the information for the particular beer
					$beer = $this->beers->showBeerRatings($beerID);
					// set the page seo information
					$this->_data['seo'] = array_slice($beer, 0, 3);
					
					// load the libraries
					$this->load->library('form_validation');		
			
					// run the validation and return the result
					if($this->form_validation->run('addReviewBeer') == false) {	
						// show the form					
						// load the packaging model
						$this->load->model('PackageModel', '', true);
						
						// get the user info from the session
						$userInfo = $this->session->userdata('userInfo');
						// create the configuration values for the form
						$config = array(
							'establishmentID' => $establishmentID
							, 'beerID' => $beerID
							, 'userID' => $userInfo['id']
							, 'packages' => $this->PackageModel->getAllForDropDown()
							, 'type' => $type
						);
						// create the form
						$output = $this->reviews->createForm($config);
					} else {
						// get the user info from the session
						$userInfo = $this->session->userdata('userInfo');
						// determine if this is an updated review
						$re = $this->RatingModel->getRatingsByUserIDEstablishmentID($userInfo['id'], $establishmentID, $beerID);
						// if above is empty, then this is an insert
						// otherwise it is an update of a current review
						if(empty($re)) {
							// get an array of information to pass
							$data = array(
								'establishmentID' => $establishmentID
								, 'beerID' => $beerID
								, 'userID' => $userInfo['id']
								, 'packageID' => $_POST['slt_package']
								, 'dateTasted' => $_POST['txt_dateTasted']
								, 'color' => $_POST['txt_color']
								, 'rating' => $_POST['slt_rating']
								, 'comments' => $_POST['ttr_comments']
								, 'haveAnother' => $_POST['slt_haveAnother']
								, 'price' => $_POST['txt_price']
							);
							// save the information
							$this->RatingModel->createRating($data);							
						} else {
							// get an array of information to pass
							$data = array(
								'id' => $re['id']
								, 'packageID' => $_POST['slt_package']
								, 'dateTasted' => $_POST['txt_dateTasted']
								, 'color' => $_POST['txt_color']
								, 'rating' => $_POST['slt_rating']
								, 'comments' => $_POST['ttr_comments']
								, 'haveAnother' => $_POST['slt_haveAnother']
								, 'price' => $_POST['txt_price']
							);
							// update the information
							$this->RatingModel->updateRatingByID($data);
						}	
						header('Location: ' . base_url() . 'beer/review/' . $beerID);					
					}
				}
				break;
		}		
		
		// set the output for the screen
		$this->_data['output'] = $output;		
		
		// get the information ready for display
		$arr_load = array(
			'pages' => array('header' => true, 'review' => true, 'footer' => true)
			, 'data' => $this->_data
		);				
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function createReview() {
		
	}
	
	public function styles() {
		
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
	
	public function addHTMLEntities($str) {
		return htmlentities(($str));
	}
}
?>