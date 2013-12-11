<?php
class Establishment extends Controller {
	public function __construct() {
		parent::Controller();
		$this->load->helper(array('url', 'phone', 'users', 'admin', 'js', 'form'));
		// helper to get the quote for the footer - in users_helper.php
		getFooterQuote();
	}
	
	private function doLoad($config) {
		$array = array(
			'header' => 'inc/normalHeader.inc.php'
			, 'headerFrontEnd' => 'inc/header_frontend.inc.php'
			, 'formMast' => 'inc/formMast.inc.php'
			, 'navigation_front' => 'inc/navigation.inc.php'
			, 'info' => 'brewery/info.php'
			, 'infoGeneric' => 'brewery/infoGeneric.php'
			, 'hop' => 'brewery/hop.php'
			, 'hopGeneric' => 'brewery/hopGeneric.php'
			, 'city' => 'establishment/city.php'
			, 'state' => 'establishment/state.php'
			, 'googleMaps' => 'establishment/googleMaps.php'
			, 'createReview' => 'establishment/createReview.php'
			, 'addEstablishment' => 'brewery/add.php'
			, 'navigation' => 'admin/navigation.php'
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
	
	public function info() {
		// get the login boolean
		$logged = checkLogin();		
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// load the establishment model
		$this->load->model('EstablishmentModel', '', true);
		// load the state model
		$this->load->model('StateModel', '', true);
		// load the rating model
		$this->load->model('BreweriesModel', '', true);
		// load the establishment library
		$this->load->library('establishments');
		
		// the length of the uri string
		$numSegments = $this->uri->total_segments();
		// third segment should be the action
		$action = $this->uri->segment(3);
		// check the third segment
		switch($action) {
			case 'category':
				// fourth segment should be the type of action
				$type = $this->uri->segment(4);
				// holder array
				$array = array();
				
				// get the state id
				$stateID = $this->uri->segment(5);

				if($stateID === false || $type === false) {
					header('Location: ' . base_url() . 'brewery/info');
					// finish the script
					exit;
				} else {
					// get the information
					$array = $this->establishments->showCategoryInfo($stateID, $type, $logged);						
					// set the output for the screen
					$this->_data['leftCol'] = $array['leftCol'];
					// get the right side screen display
					$this->_data['rightCol'] = $array['rightCol'];
				}
							
				// set the page seo information
				$this->_data['seo'] = array_slice($array, 0, 3);
				// get the information ready for display
				$arr_load = array(
					'pages' => array(				
						'headerFrontEnd' => true
						, 'formMast' => true
						, 'navigation_front' => true
						, 'info' => true
						, 'footerFrontEnd' => true
					)
					, 'data' => $this->_data
				);			
				// load all parts for the view
				$this->doLoad($arr_load);
				// finish the case
				break;
			case 'rating':
				// fourth segment should be the type of action
				$establishmentID = $this->uri->segment(4);
                
                // load helper
                $this->load->helper('social_media');
				
				if($establishmentID === false) {
					header('Location: ' . base_url() . 'brewery/info');
					// finish the script
					exit;
				} else {
					// load the breweries model
					$this->load->model('BreweriesModel', '', true);
					// get the information
					$array = $this->establishments->showEstablishmentRatingsByID($establishmentID, $logged);						
					// set the output for the screen
					$this->_data['leftCol'] = $array['leftCol'];
					// get the right side screen display
					$this->_data['rightCol'] = $array['rightCol'];
				}
							
				// set the page seo information
				$this->_data['seo'] = array_slice($array, 0, 3);
				// get the information ready for display
				$arr_load = array(
					'pages' => array(				
						'headerFrontEnd' => true
						, 'formMast' => true
						, 'navigation_front' => true
						, 'info' => true
						, 'footerFrontEnd' => true
					)
					, 'data' => $this->_data
				);			
				// load all parts for the view
				$this->doLoad($arr_load);				
				// finish the case
				break;
			case false:
			default:
				// they are going to a whacked url
				// they might be looking for...
				header('Location: ' . base_url() . 'brewery/info');
				// finish the script
				exit;
				// finish the case
				break;
		}
	}
	
	public function city() {
		// get the login boolean
		$logged = checkLogin();
		
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');
		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// load the brewery model
		$this->load->model('EstablishmentModel', '', true);
		// load the state model
		$this->load->model('StateModel', '', true);
		// load the beer library
		$this->load->library('establishments');
		
		// get the id of the state passed
		$state = $this->uri->segment(3);
		// get the name of the city passed
		$city = $this->uri->segment(4);
		
		// check if the city is empty
		// this should not happen
		if($state === false || $city === false) {
			// set the output for the screen
			$this->_data['leftCol'] = '
				<h2>Establishments</h2>
				<p>There are no records for the city and state requested.</p>			
			';			
			// set the page information
			$this->_data['seo'] = getSEO();
		} else {		
			
			// get the information for the particular brewery hop
			/*$breweryCities = $this->breweries->showEstablishmentsCity($state, $city);
			// get the output for the screen
			$this->_data['output'] = $breweryCities['str'];
			
			// set the page seo information
			$this->_data['seo'] = array_slice($breweryCities, 0, 3);*/
		
			// replace values in the city
			$city = str_replace('_', ' ', $city);
			// get the information for the particular state
			$establishmentInfo = $this->establishments->showEstablishmentsCity($state, $city);			
			//echo '<pre>'; print_r($establishmentInfo); echo '</pre>'; exit;
			// set the page seo information
			if(is_array($establishmentInfo)) {
				$this->_data['seo'] = array_slice($establishmentInfo, 0, 3);
				// set the output for the screen
				$this->_data['leftCol'] = $establishmentInfo['str'];
			}
		}
		// get the information ready for display
		/*$arr_load = array(
			'pages' => array('header' => true, 'city' => true, 'footerFrontEnd' => true)
			, 'data' => $this->_data
		);*/	
		$arr_load = array(
			'pages' => array(				
				'headerFrontEnd' => true
				, 'formMast' => true
				, 'navigation_front' => true
				, 'city' => true
				, 'footerFrontEnd' => true
			)
			, 'data' => $this->_data
		);			
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function state() {
		// get the login boolean
		$logged = checkLogin();
		
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');
		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// load the brewery model
		$this->load->model('EstablishmentModel', '', true);
		// load the state model
		$this->load->model('StateModel', '', true);
		// load the beer library
		$this->load->library('establishments');
		
		// get the id of the state passed
		$stateID = $this->uri->segment(3);
		
		// check if the city is empty
		// this should not happen
		if($stateID === false) {
			// set the output for the screen
			$this->_data['leftCol'] = '
				<h2>Establishments</h2>
				<p>There are no records for the state requested.</p>			
			';			
			// set the page information
			$this->_data['seo'] = getSEO();
		} else {		
			// get the information for the particular state
			$establishmentInfo = $this->establishments->showEstablishmentState($stateID);			
			//echo '<pre>'; print_r($establishmentInfo); echo '</pre>'; exit;
			// set the page seo information
			if(is_array($establishmentInfo)) {
				$this->_data['seo'] = array_slice($establishmentInfo, 0, 3);
				// set the output for the screen
				$this->_data['leftCol'] = $establishmentInfo['str'];
			} /*else {
				// set the page information
				$this->_data['seo'] = getSEO();
				// load the state model
				$this->load->model('StateModel', '', true);
				// get the name of the state
				$array = $this->StateModel->getStateByID($stateID);
				// set the output for the screen
				$this->_data['leftCol'] = '<p>There are currently no establishments found in ' . $array['stateFull'] . '.</p>';
			}*/
		}
		
		// get the information ready for display
		/*$arr_load = array(
			'pages' => array('header' => true, 'state' => true, 'footerFrontEnd' => true)
			, 'data' => $this->_data
		);	*/
		// get the information ready for display
		$arr_load = array(
			'pages' => array(				
				'headerFrontEnd' => true
				, 'formMast' => true
				, 'navigation_front' => true
				, 'state' => true
				, 'footerFrontEnd' => true
			)
			, 'data' => $this->_data
		);				
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function addEstablishment() {
		if(checkLogin() === true) {
			// load the brewery model
			$this->load->model('BreweriesModel', '', true);
			// load the beer model
			$this->load->model('BeerModel', '', true);
			// load the state model
			$this->load->model('StateModel', '', true);
			// load the beer library
			$this->load->library('breweries');
			// user session info
			$userInfo = $this->session->userdata('userInfo');
			
			// get the number of reviews
			$reviewCount = $this->BeerModel->getBeerReviewCount($userInfo['id']);
			
			// check to see if they have reviewed enough beers			
			if($reviewCount >= MIN_REVIEW_COUNT_FOR_ESTABLISHMENT) {								
				// load the form validation library
				$this->load->library('form_validation');
				// load the helpers
				$this->load->helper(array('js', 'form'));							
				
				// run the validation and return the result
				if($this->form_validation->run('addEstablishment') == false) {				
					// get the information for the particular beer
					$info = $this->breweries->showAddEstablishment();
					//echo '<pre>'; print_r($info); echo '</pre>'; exit;
					// set the output for the screen
					$this->_data['output'] = $info;
					
					// set the page seo information
					$this->_data['seo'] = getSEO();
					// get the information ready for display
					$arr_load = array(
						'pages' => array('header' => true, 'addEstablishment' => true, 'footerFrontEnd' => true)
						, 'data' => $this->_data
					);				
					// load all parts for the view
					$this->doLoad($arr_load);
				} else {
					// successfull information, so store it
					// create the data array to store the information
					$data = array(
						'categoryID' => $_POST['slt_category']
						, 'name' => $_POST['txt_name']
						, 'address' => $_POST['txt_address']
						, 'city' => $_POST['txt_city']
						, 'state' => $_POST['slt_state']
						, 'zip' => $_POST['txt_zip']
						, 'phone' => $_POST['txt_phone']
						, 'url' => $_POST['txt_url']
					);
					// insert the information into the database
					$id = $this->BreweriesModel->createEstablishment($data);
					// get the user id who inserted the beer
					$userInfo = $this->session->userdata('userInfo');
					// check if creation notice is required
					if(SEND_NEWBREWERY_NOTICE === true) {
						// include the mail helper
						$this->load->helper('email');
						// create the configuration array
						$breweryInfo = array(
							'action' => 'newEstablishment'
							, 'establishmentID' => $id
							, 'userID' => $userInfo['id']
							, 'data' => $data
							, 'subject' => 'New Establishment Addition'
						);
						// send out an email to the admins
						sendFormMail($breweryInfo);
					}
						
					// take them to the page to create a review
					header('Location: ' . base_url() . 'brewery/info/' . $id);
					exit;
				}	
			} else {
				// set the output for the screen
				$this->_data['output'] = '<p style="clear: both;">You haven\'t reviewed enough beers (you need ' . MIN_REVIEW_COUNT_FOR_ESTABLISHMENT . ' reviews) to add a new beer.</p>';
				
				// set the page seo information
				$this->_data['seo'] = getSEO();
				// get the information ready for display
				$arr_load = array(
					'pages' => array('header' => true, 'addEstablishment' => true, 'footerFrontEnd' => true)
					, 'data' => $this->_data
				);				
				// load all parts for the view
				$this->doLoad($arr_load);
			}
		} else {
			$array = array(
				'uri' => substr($this->uri->uri_string(), 1)
				, 'search' => array('_')
				, 'replace' => '/'
			);
			$args = swapOutURI($array);
			//echo $args;exit;
			//header('Location: ' . base_url() . $args);
			header('Location: ' . base_url() . 'user/login/' . $args);
		}
	}
	
	public function createReview() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// checked if they are logged in
		if($logged === true) {
			// load the establishment model
			$this->load->model('EstablishmentModel', '', true);
			// load the rating model
			$this->load->model('RatingModel', '', true);
			// load the beer library
			$this->load->library('establishments');
			// user session info
			$userInfo = $this->session->userdata('userInfo');
			
			// get the id that is passed
			// this is the id of the beer that will be reviewed
			$id = $this->uri->segment(3);
            
            // create the right column information that will
            // appear no matter if the situation
            $this->_data['rightCol'] = '
                <h4><span>Tips on Reviewing</span></h4>
                <ul>
                    <li>10s should be few and far between: the establishmet is perfect and needs no improvement.</li>
                    <li>Reviews should be recent and, preferrably, from handwritten notes as it is sometimes difficult to remember the caveats of an establishment.</li>
                    <li>Try to review an establishment with an appreciation of what the owners are trying to accomplish.</li>
                    <li>Have fun with your review (within reason) let your personality show through.</li>
                    <li>This is not a race to see who can review the most beers in a day, week, month, year, etc.  Like your beer, enjoy the experience.</li>
                </ul>
            
                <h4><span>Rules of Reviewing</span></h4>
                <ul>
                    <li>Only <span class="bold">American establishments</span> should be reviewed here.</li>
                    <li>Use <span class="bold">English</span>!</li>
                    <li>Some people are offended by off language, keep it to a minimum.</li>
                    <li>Don&#39;t slam another user for their review.  This is suppose to be fun, keep it that way.</li>
                </ul>
            ';   
			
			// get the number of reviews
			//$reviewCount = $this->EstablishmentModel->getEstablishmentReviewCount($userInfo['id']);
			
			// check to see if they have reviewed enough beers			
			//if($reviewCount >= MIN_REVIEW_COUNT_FOR_ESTABLISHMENT) {								
				// load the form validation library
				$this->load->library('form_validation');
				// load the helpers
				$this->load->helper(array('js', 'form'));							
				
				// run the validation and return the result
				if($this->form_validation->run('addReviewEstablishment') == false) {
					// get the information for the particular beer
					$info = $this->establishments->showCreateReview($id);
					//echo '<pre>'; print_r($info); echo '</pre>'; exit;
					// set the output for the screen
					$this->_data['leftCol'] = $info['leftCol'];
					//$this->_data['leftCol'] = form_estblishmentReview(array('id' => $id));
					
					// set the page seo information
					$this->_data['seo'] = array_slice($info, 0, 3);				
			
					// get the information ready for display
					$arr_load = array(
						'pages' => array(				
							'headerFrontEnd' => true
							, 'formMast' => true
							, 'navigation_front' => true
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
					$establishment = $this->EstablishmentModel->getEstablishmentByID($id);
					// create the data array to store the information
					$array = array(
						'establishmentID' => $id
						, 'userID' => $userInfo['id']
						, 'dateVisited' => trim($_POST['txt_dateVisited'])
						, 'drink' => trim($_POST['drink'])
                        , 'service' => trim($_POST['service'])
                        , 'atmosphere' => trim($_POST['atmosphere'])
                        , 'pricing' => trim($_POST['pricing'])
                        , 'accessibility' => trim($_POST['accessibility'])
						, 'comments' => trim($_POST['ttr_comments'])
						, 'visitAgain' => trim($_POST['slt_visitAgain'])
					);
					
					// query the establishment table
					$rating = $this->EstablishmentModel->checkForRatingByUserIDEstablishmentID($userInfo['id'], $id);
					// holder for the rating id
					$ratingID = '';
					// store that information
					if(empty($rating)) {
						// create a new rating
						$ratingID = $this->EstablishmentModel->createRating($array);
					} else {
						// add to the array of values
						$array['id'] = $rating['id'];
						// update a rating
						$this->EstablishmentModel->updateRatingByID($array);
						// rating id
						$ratingID = $rating['id'];
					}
					
					// check if creation notice is required
					if(SEND_CREATION_NOTICE === true) {
						// include the mail helper
						$this->load->helper('email');
						// get the information about the beer rating
						$ratingInfo = $this->EstablishmentModel->getEstblishmentRatingsByRatingsID($ratingID);
                        $ratingInfo = $ratingInfo[0] + $array;
                        // create the configuration array
						$ratingInfo += array('action' => 'establishmentReview', 'userID' => $userInfo['id'], 'username' => $userInfo['username'], 'subject' => 'Establishment Review Addition');
                        // send out an email to the admins
						sendFormMail($ratingInfo);
					}
						
					// take them to the page for the beer
					header('Location: ' . base_url() . 'establishment/info/rating/' . $id);
					exit;
				}
			/*} else {
				// they haven't reviewed enough to create a 
				// get the information for the particular establishment
				$info = $this->establishments->showCreateReview($id, true);
				
				// set the output for the screen
				$this->_data['leftCol'] = 
					$info['str'] . '
					<p style="clear: both;">You haven\'t reviewed enough establishments (you need ' . MIN_REVIEW_COUNT_FOR_ESTABLISHMENT . ' reviews) to add a new beer.</p>
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
			// they are not logged in
			header('Location: ' . base_url() . 'user/login');
			exit;
		}
	}
	
	public function categoryExists($categoryID) {
		// load the user model
		$this->load->model('BreweriesModel', '', true);
		// get the brewery information
		$rs = $this->BreweriesModel->getCategoryCheck($categoryID);
		// check if it really exists
		$boolean = count($rs) > 0 ? true : false;
		// check the boolean
		if($boolean === false) {
			$this->form_validation->set_message('categoryExists', 'The %s you have chosen doesn\'t exists.  Please choose another.');
		}
		return $boolean;
	}
	
	public function alphaNumericSpace($str) {
		$boolean = (!preg_match("/^([a-z0-9\s])+$/i", $str)) ? false : true;
		
		// check the boolean
		if($boolean === false) {
			$this->form_validation->set_message('alphaNumericSpace', '%s should only contain alpha numerical information and spaces.');
		}
		return $boolean;
	}
	
	public function alphaNumericSpaceAndOthers($str) {
		$boolean = (!preg_match("/^([a-z0-9\s/./\])+$/i", $str)) ? false : true;
		
		// check the boolean
		if($boolean === false) {
			$this->form_validation->set_message('alphaNumericSpaceAndOthers', '%s should only contain alpha numerical information and spaces.');
		}
		return $boolean;
	}
	
	public function stateExists($stateID) {
		// load the user model
		$this->load->model('StateModel', '', true);
		// get the brewery information
		$rs = $this->StateModel->getStateCheck($stateID);
		// check if it really exists
		$boolean = count($rs) > 0 ? true : false;
		// check the boolean
		if($boolean === false) {
			$this->form_validation->set_message('stateExists', 'The %s you have chosen doesn\'t exists.  Please choose another.');
		}
		return $boolean;
	}
	
	public function googleMaps() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// get the value of the id passed
		$establishmentID = $this->uri->segment(3);
		// make sure this is set
		if($establishmentID == false) {
			// no id was present
			header('Location: ' . base_url());
			exit;
		} else {
			// load the establishment model
			$this->load->model('EstablishmentModel', '', true);
			// get the information about the establishment
			$est = $this->EstablishmentModel->getEstablishmentByID($establishmentID);
			// check for the latitude and longitude
			//echo '<pre>'; print_r($est); echo '</pre>';exit;
			if(empty($est['latitude']) || empty($est['longitude'])) {
				// determine that information
				// load the google library
				$this->load->library('google');
				// set the array for initialization
				$array = array(
					'id' => $establishmentID
					, 'address' => $est['address'] . ', ' . $est['city'] . ', ' . $est['stateFull']
				);
				//echo '<pre>'; print_r($array); exit;
				// initialize
				$this->google->init($array);
				// get the latitude and longitude based on google api
				$this->google->determineLatitudeAndLongitudeViaGoogle();
				// set the current latitude
				$est['latitude'] = $this->google->getLatitude();
				// set the curernt longitude
				$est['longitude'] = $this->google->getLongitude();
			}
			// create an array of parameters to pass to the js code
			$array_js = array(
				'latitude' => $est['latitude']
				, 'longitude' => $est['longitude']
				, 'establishment' => $est
			);   echo '<pre>'; print_r($array_js); exit;
			// set the javascript for this page
			$this->_data['array_showJS'] = $array_js;
			// prepare the left column text
			$this->_data['leftCol'] = '
				<h2 class="brown">Map for ' . $est['name'] . '</h2>
				<div id="map" style="margin: 1.0em 1.0em 1.0em 0; clear: both; width: 640px; height: 350px;"></div>
			';
			
			// get the places that are within RADIUS_SEARCH
			$closeBy = $this->EstablishmentModel->determineDistance($array_js + array('id' => $establishmentID));
			// holder for the right column
			$str = '';
			// check for results
			if($closeBy != false) {
				$str = '<h4><span>Establishments Within ' . RADIUS_SEARCH . ' Miles</span></h4><ul>';
				foreach($closeBy as $store) {
					$distance = number_format(round($store['distance'], 2), 2);
					$miles = $distance == 1.00 ? ' mile' : ' miles';
					$str .= '
						<li>
							<p><a href="' . base_url() . 'brewery/info/' . $store['id'] . '">' . $store['name'] . '</a> - <span class="bold">' . $distance . '</span> ' . $miles . '</p>
							<p class="rightBreweryLink">' . $store['address'] . ', <a href="' . base_url() . 'establishment/city/' . $store['stateID'] . '/' . urlencode($store['city']) . '">' . $store['city'] . '</a>, <a href="' . base_url() . 'establishment/state/' . $store['stateID'] . '">' . $store['stateAbbr'] . '</a> ' . $store['zip'] . '</p>
						</li>
					';
				}
				$str .= '</ul>';
			}
			// store the info for the view
			$this->_data['rightCol'] = $str;
						
			// get the information ready for display
			$arr_load = array(
				'pages' => array(				
					'headerFrontEnd' => true
					, 'formMast' => true
					, 'navigation_front' => true
					, 'googleMaps' => true
					, 'footerFrontEnd' => true
				)
				, 'data' => $this->_data
			);		
			// load all parts for the view
			$this->doLoad($arr_load);
		}
	}
}
?>