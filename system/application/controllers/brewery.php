<?php
class Brewery extends Controller {
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
			, 'city' => 'brewery/city.php'
			, 'state' => 'brewery/state.php'
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
		
		// load the brewery model
		$this->load->model('BreweriesModel', '', true);
		// load the beer library
		$this->load->library('breweries');
                // load the helpers
                $this->load->helper('social_media');
		
		// get the id that is passed
		$id = $this->uri->segment(3);
		
		if($id !== false) {
			// get the information for the particular brewery
			$brewery = $this->breweries->showBreweryInfo($id, $logged);
			// set the output for the screen
			$this->_data['leftCol'] = $brewery['leftCol'];
			// get the right side screen display
			$this->_data['rightCol'] = $brewery['rightCol'];
			
			// set the page seo information
			$this->_data['seo'] = array_slice($brewery, 0, 3);
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
		} else {
			// load the establishment model
			$this->load->model('EstablishmentModel', '', true);
			// load the state model
			$this->load->model('StateModel', '', true);
			
			// get the information for the particular brewery
			$establishment = $this->breweries->showBreweryInfoGeneric($id, $logged);
			// set the output for the screen
			$this->_data['leftCol'] = $establishment['leftCol'];
			// get the right side screen display
			$this->_data['rightCol'] = $establishment['rightCol'];
			
			// set the page information
			$this->_data['seo'] = getSEO();
			
			// get the information ready for display
			$arr_load = array(
				'pages' => array(				
					'headerFrontEnd' => true
					, 'formMast' => true
					, 'navigation_front' => true
					, 'infoGeneric' => true
					, 'footerFrontEnd' => true
				)
				, 'data' => $this->_data
			);				
			// load all parts for the view
			$this->doLoad($arr_load);
		}
	}
	
	public function hop() {
		// get the login boolean
		$logged = checkLogin();		
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// load the brewery model
		$this->load->model('BreweriesModel', '', true);
		// load the beer library
		$this->load->library('breweries');
		
		// get the id that is passed
		$id = $this->uri->segment(3);
		
		// check if the id is empty
		// this will be the case in which to show all brewery hops
		if($id === false) {
			// get the information for the particular brewery hop
			$this->_data['breweryHop'] = $this->breweries->showBreweryHopFrontPage();
			// get the information for the particular brewery hop
			$this->_data['hops'] = $this->breweries->showAllBreweryHops();
			
			// set the page information
			$this->_data['seo'] = getSEO();
			
			// get the information ready for display
			$arr_load = array(
				'pages' => array(				
					'headerFrontEnd' => true
					, 'formMast' => true
					, 'navigation_front' => true
					, 'hopGeneric' => true
					, 'footerFrontEnd' => true
				)
				, 'data' => $this->_data
			);				
			// load all parts for the view
			$this->doLoad($arr_load);
		} else {		
			// get the information for the particular brewery hop
			$breweryhop = $this->breweries->showBreweryHop($id);
			// set the output for the screen
			$this->_data['breweryHop'] = $breweryhop['str'];
			// get the information for the particular brewery hop
			$this->_data['hops'] = $this->breweries->showAllBreweryHops();
			
			// set the page seo information
			$this->_data['seo'] = array_slice($breweryhop, 0, 3);
			
			$arr_load = array(
				'pages' => array(				
					'headerFrontEnd' => true
					, 'formMast' => true
					, 'navigation_front' => true
					, 'hop' => true
					, 'footerFrontEnd' => true
				)
				, 'data' => $this->_data
			);			
			// load all parts for the view
			$this->doLoad($arr_load);
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
		$this->load->model('BreweriesModel', '', true);
		// load the beer library
		$this->load->library('breweries');
		
		// get the id of the state passed
		$state = $this->uri->segment(3);
		// get the name of the city passed
		$city = $this->uri->segment(4);
		
		// check if the city is empty
		// this should not happen
		if($state === false || $city === false) {
			// set the output for the screen
			$this->_data['leftCol'] = '
				<h2 class="brown">Breweries in ' . $city . '</h2>
				<p>There are no records for the city requested.</p>			
			';			
			// set the page information
			$this->_data['seo'] = getSEO();
		} else {		
			// replace values in the city
			$city = str_replace('_', ' ', $city);
			// get the information for the particular brewery hop
			$breweryCities = $this->breweries->showBreweriesCity($state, $city);
			// get the output for the screen
			$this->_data['leftCol'] = $breweryCities['str'];
			
			// set the page seo information
			$this->_data['seo'] = array_slice($breweryCities, 0, 3);
		}
		
		// get the information ready for display
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
		// load the brewery model
		$this->load->model('BreweriesModel', '', true);
		// load the beer library
		$this->load->library('breweries');
		
		// get the id of the state passed
		$state = $this->uri->segment(3);
		
		// check if the city is empty
		// this should not happen
		if($state === false) {
			// set the output for the screen
			$this->_data['output'] = '
				<h2>Breweries</h2>
				<p>There are no records for the state requested.</p>			
			';			
			// set the page information
			$this->_data['seo'] = getSEO();
		} else {		
			// get the information for the particular state
			$breweryStates = $this->breweries->showBreweriesState($state);			
			
			// set the page seo information
			if(is_array($breweryStates)) {
				$this->_data['seo'] = array_slice($breweryStates, 0, 3);
				// set the output for the screen
				$this->_data['output'] = $breweryStates['str'];
			} else {
				// set the page information
				$this->_data['seo'] = getSEO();
				// load the state model
				$this->load->model('StateModel', '', true);
				// get the name of the state
				$array = $this->StateModel->getStateByID($state);
				// set the output for the screen
				$this->_data['output'] = '<p>There are currently no establishments found in ' . $array['stateFull'] . '.</p>';
			}
		}
		
		// get the information ready for display
		$arr_load = array(
			'pages' => array('header' => true, 'state' => true, 'footerFrontEnd' => true)
			, 'data' => $this->_data
		);				
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function addEstablishment() {
		// get the login boolean
		$logged = checkLogin();		
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		if($logged === true) {
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
							, 'navigation_front' => true
							, 'addEstablishment' => true
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
						'categoryID' => $_POST['slt_category']
						, 'name' => $_POST['txt_name']
						, 'address' => $_POST['txt_address']
						, 'city' => $_POST['txt_city']
						, 'state' => $_POST['slt_state']
						, 'zip' => $_POST['txt_zip']
						, 'phone' => $_POST['txt_phone']
						, 'url' => $_POST['txt_url']
						, 'twitter' => $_POST['txt_twitter']
						, 'userID' => $userInfo['id']
					);
					// insert the information into the database
					$id = $this->BreweriesModel->createEstablishment($data);					
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
					header('Location: ' . base_url() . 'establishment/info/rating/' . $id);
					exit;
				}	
			} else {
				// set the output for the screen
				$this->_data['output'] = '<p style="clear: both;">You haven\'t reviewed enough beers (you need ' . MIN_REVIEW_COUNT_FOR_ESTABLISHMENT . ' reviews) to add a new beer.</p>';
				
				// set the page seo information
				$this->_data['seo'] = getSEO();
				// get the information ready for display
				$arr_load = array(
					'pages' => array(				
						'headerFrontEnd' => true
						, 'formMast' => true
						, 'navigation_front' => true
						, 'addEstablishment' => true
						, 'footerFrontEnd' => true
					)
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
		$boolean = (!preg_match("/^([a-z0-9\s\'&])+$/i", $str)) ? false : true;
		
		// check the boolean
		if($boolean === false) {
			$this->form_validation->set_message('alphaNumericSpace', '%s should only contain alpha numerical information and spaces.');
		}
		return $boolean;
	}
    
    public function dropEndSlash($url) {
        // get the lengthe of the string
        $len = strlen($url);
        // determine if the last value of the string is a slash
        $boolean = ($url[($len - 1)] == '/') ? false : true;
        // check the boolean
        if($boolean === false) {
            $this->form_validation->set_message('dropEndSlash', '%s should not end in a slash.');
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
}
?>