<?php
class Admin extends Controller {
	public function __construct() {
		parent::Controller();
		// load the admin helper
		$this->load->helper(array('admin', 'js', 'phone'));
	}
	
	private function doLoad($config) {
		$array = array(
			'header' => 'inc/header.inc.php'
			, 'login' => 'admin/login'
			, 'loginHandle' => 'admin/loginHandle'
			, 'welcome' => 'admin/welcome'
			, 'edit' => 'admin/edit'
			, 'add' => 'admin/add'
			, 'view' => 'admin/view'
			, 'upload' => 'admin/upload'
			, 'cropImage' => 'admin/cropImage'
			, 'logout' => 'admin/logout'
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
	
	public function login() {
		// title of the page
		$this->_data['pageTitle'] = 'Two Beer Dudes - Admin';
		
		$arr_load = array(
			'pages' => array('header' => true, 'login' => true, 'footer' => false)
			, 'data' => $this->_data
		);
		$this->doLoad($arr_load);
	}
	
	public function handleLogin() {
		// title of the page
		$this->_data['pageTitle'] = 'Two Beer Dudes - Processing Login';
		
		$username = $_POST['user'];
		$password = $_POST['pass'];
		
		$arr_clean = array();
		$arr_error = array();
		
		if(empty($username)) {
			$arr_error['username'] = 'You must enter a username to login.  <span>(This will be the email address you used during signup.)</span>';
			$arr_clean['username'] = '';
		} else {
			$arr_clean['username'] = $username;
		}
		
		if(empty($password)) {
			$arr_error['password'] = 'You must enter a password to login.';
			$arr_clean['password'] = '';
		} else {
			$arr_clean['password'] = $password;
		}
		
		if(empty($arr_error)) {
			// load the user model to handle the login
			$this->load->model('UserModel', '', true);
			// check the login to see if it is succesfull
			$login = $this->UserModel->login(array('email' => $arr_clean['username'], 'password' => $arr_clean['password'], 'type' => 'admin'));
			
			// check if there was an error with the login
			if($login === true) {
				// successful login
				// set all the session values
				$array = array(
					'id' => $this->UserModel->getID()
					, 'firstName' => $this->UserModel->getFirstName()
					, 'lastName' => $this->UserModel->getLastName()
					, 'email' => $this->UserModel->getEmail()
					, 'userTypeID' => $this->UserModel->getUserTypeID()
					, 'userType' => $this->UserModel->getUserType()
					, 'lastLogin' => $this->UserModel->getLastLogin()
					, 'joinDate' => $this->UserModel->getJoinDate()
					, 'formatLastLogin' => $this->UserModel->getFormatLastLogin()
				);
				$this->session->set_userdata($array);
				
				header('Location: ' . base_url() . 'admin/welcome');
			} else {
				// problem w/ username/password combo
				$this->_data['errors']['login'] = 'There was a problem processing your login.';
				// array of views and data
				$arr_load = array(
					'pages' => array('header' => true, 'login' => true, 'footer' => false)
					, 'data' => $this->_data
				);
			}
		} else {
			// go back to login w/ errors
			$this->_data['errors'] = $arr_error;
			
			if(!empty($arr_clean['user'])) {
				$this->_data['user'] = $arr_clean['user'];
			}
			
			// array of views and data
			$arr_load = array(
				'pages' => array('header' => true, 'login' => true, 'footer' => false)
				, 'data' => $this->_data
			);
		}		
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function logout() {
		// see if the user is logged in
		$this->checkLogin();
		
		// title of the page
		$this->_data['pageTitle'] = 'Two Beer Dudes - Admin Logout';
		
		// destroy the session
		$this->session->sess_destroy();	
		
		// array of views and data
		$arr_load = array(
			'pages' => array('header' => true, 'logout' => true, 'footer' => false)
			, 'data' => $this->_data
		);
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	private function checkLogin() {
		$id = $this->session->userdata('id');
		if(empty($id)) {
			header('Location: ' . base_url() . 'admin/login');
		}
	}
	
	public function welcome() { 
		// see if the user is logged in
		$this->checkLogin();
		
		// title of the page
		$this->_data['pageTitle'] = 'Two Beer Dudes - Admin Welcome';
		// session information
		$this->_data['session'] = $this->session->userdata;		
		
		// array of views and data
		$arr_load = array(
			'pages' => array('header' => true, 'masthead' => true, 'welcome' => true, 'navigation' => true, 'footer' => false)
			, 'data' => $this->_data
		);
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function edit() {
		// see if the user is logged in
		$this->checkLogin();
		
		// get the type of page
		$type = $this->uri->segment(3);
		
		switch($type) {
			case 'brewery':
				$this->load->model('BreweriesModel', '', true);
				$this->load->library('breweries');
				$title = $this->breweries->getTitle();
				$pageHeader = 'Edit Brewery';
				$this->_data['displayData'] = $this->breweries->getAllBreweries();
				break;
			case 'beer':
				$this->load->model('BeerModel', '', true);
				$this->load->library('beers');
				$title = $this->beers->getTitle();
				$pageHeader = 'Edit Beers';
				$this->_data['displayData'] = $this->beers->getAllBeers();
				break;
			case 'rating':
				$this->load->model('RatingModel', '', true);
				$this->load->library('rating');
				$title = $this->rating->getTitle();
				$pageHeader = 'Edit Ratingss';
				$this->_data['displayData'] = $this->rating->getAllRatings();
				break;
		}
		
		// title of the page
		$this->_data['pageTitle'] = 'Two Beer Dudes - Admin ' . $title;
		// create the page header wording
		$this->_data['pageHeader'] = $pageHeader;
		// session information
		$this->_data['session'] = $this->session->userdata;
		
		// array of views and data
		$arr_load = array(
			'pages' => array('header' => true, 'masthead' => true, 'edit' => true, 'navigation' => true, 'footer' => true)
			, 'data' => $this->_data
		);
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function add() {
		// see if the user is logged in
		$this->checkLogin();
		
		// get the type of page
		$type = $this->uri->segment(3);
		
		switch($type) {
			case 'brewery':
				$this->load->model('BreweriesModel', '', true);
				// load the state model
				$this->load->model('StateModel', '', true);
				// load the brewery library
				$this->load->library('breweries');
				// make and array of configuration values
				$array = array(
					'states' => $this->StateModel->getAllForDropDown()
				);
				// create the output for the screen (form)
				$this->_data['displayData'] = $this->breweries->createForm($array);
				$title = $this->breweries->getTitle();
				// create the page header wording
				$pageHeader = 'Add Brewery';
				break;
			case 'beer':
				// load the beer model
				$this->load->model('BeerModel', '', true);
				// load the breweries model
				$this->load->model('BreweriesModel', '', true);	
				// load the style model
				$this->load->model('StyleModel', '', true);				
				// load the beer library
				$this->load->library('beers');
				// make and array of configuration values
				$array = array(
					'breweries' => $this->BreweriesModel->getAllForDropDown()
					, 'styles' => $this->StyleModel->getAllForDropDown()
				);
				// create the output for the screen (form)
				$this->_data['displayData'] = $this->beers->createForm($array);
				$title = $this->beers->getTitle();
				// create the page header wording
				$pageHeader = 'Add Beer';
				break;
			case 'rating':
				// load the rating model
				$this->load->model('RatingModel', '', true);
				// load the rating library
				$this->load->library('rating');
				// check if there are any beers they didn't rate
				$array['beer'] = $this->RatingModel->getNonRatedBeersForDropDown();
				// create the output for the screen (form)
				$this->_data['displayData'] = $this->rating->createRating($array);
				$title = $this->rating->getTitle();
				// create the page header wording
				$pageHeader = 'Add Rating';
				break;
		}
		
		// title of the page
		$this->_data['pageTitle'] = 'Two Beer Dudes - Admin ' . $title;
		// create the page header wording
		$this->_data['pageHeader'] = $pageHeader;
		// session information
		$this->_data['session'] = $this->session->userdata;
		
		// array of views and data
		$arr_load = array(
			'pages' => array('header' => true, 'masthead' => true, 'add' => true, 'navigation' => true, 'footer' => true)
			, 'data' => $this->_data
		);
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function view() {
		// see if the user is logged in
		$this->checkLogin();
		
		// get the type of page
		$type = $this->uri->segment(3);
		
		switch($type) {
			case 'myRatings':
				$this->load->model('RatingModel', '', true);
				$this->load->library('rating');
				$title = $this->rating->getTitle();
				$pageHeader = 'View My Ratings';
				$this->_data['displayData'] = $this->rating->getRatingsByUserID($this->session->userdata('id'));
				break;
		}
		
		// title of the page
		$this->_data['pageTitle'] = 'Two Beer Dudes - Admin ' . $title;
		// create the page header wording
		$this->_data['pageHeader'] = $pageHeader;
		// session information
		$this->_data['session'] = $this->session->userdata;
				
		// array of views and data
		$arr_load = array(
			'pages' => array('header' => true, 'masthead' => true, 'view' => true, 'navigation' => true, 'footer' => true)
			, 'data' => $this->_data
		);
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function uploadImage() {
		// see if the user is logged in
		$this->checkLogin();
		// load the beer model
		$this->load->model('BeerModel', '', true);
		// load the beer library
		$this->load->library('beers');
		// load the admin helper
		$this->load->helper(array('upload'));
		
		// get the beer id
		$id = $this->uri->segment(3);
		
		// get the current information about the image
		$array_img = $this->BeerModel->getImageByID($id);
		
		// title of the page
		$this->_data['pageTitle'] = 'Two Beer Dudes - Admin Upload Image';
		// create the page header wording
		$this->_data['pageHeader'] = 'Upload Image: ' . $array_img['beerName'] . ' by ' . $array_img['name'];
		// session information
		$this->_data['session'] = $this->session->userdata;
		
		$this->_data['displayData'] = $this->beers->uploadImage($array_img + array('id' => $id));
		
		// get the current information about the image
		/*$array_img = $this->BeerModel->getImageByID($id);
		// name of the image
		$imageName = $array_img['picture'];
		// check if the name of the image is empty
		if(empty($imageName)) {
			// image name has to be created
			// create the name
			$imageName = nameBeerImage(array('brewery' => $array_img['name'], 'beer' => $array_img['beerName']));
		}*/
		/*
				// check that the image exists
				if(imageExists(array('path' => $path, 'fileName' => $imageName))) {
					// delete the image
					unlink($path . $imageName);					
					// remove the image from the database
					$this->BeerModel->removeImageByID($id);
					// load the beer library
					$this->load->library('beers');
					// get the formatted beer by id
					$output = $this->beers->getBeerByID($id);
				} else {
					$output = 'file does not exist';
				}*/
		// array of views and data
		$arr_load = array(
			'pages' => array('header' => true, 'masthead' => true, 'upload' => true, 'navigation' => true, 'footer' => true)
			, 'data' => $this->_data
		);
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function cropImage() {
		// see if the user is logged in
		$this->checkLogin();
		// load the beer model
		$this->load->model('BeerModel', '', true);
		// load the beer library
		$this->load->library('beers');
		// load the admin helper
		$this->load->helper(array('upload'));
		
		//echo '<pre>'; print_r($_POST); echo '</pre>'; exit;
		
		$brewery = '';
		$beerName = '';
		$picture = '';
		$id = $this->uri->segment(3);
		
		if(key_exists('btn_submit', $_POST)) {
			// original arrival at page
			$brewery = $_POST['hdn_brewery'];
			$beerName = $_POST['hdn_beer'];
			$picture = $_POST['hdn_picture'];
			
			$array = array(
				'fileName' => $picture
				, 'id' => $id
			);
			$this->_data['displayData'] = $this->beers->cropImage($array);
		} else if(key_exists('btn_crop', $_POST)) {
			$x1 = (int) $_POST['x1'];
			$x2 = (int) $_POST['x2'];
			$y1 = (int) $_POST['y1'];
			$y2 = (int) $_POST['y2'];
			$width = (int) $_POST['width'];
			$height = (int) $_POST['height'];
			$fileName = $_POST['hdn_fileName'];			
			
			// resize the image
			// create the config information for changing the file size
			$config['image_library'] = 'gd2';
			$config['source_image'] = '/home/twobeerdudes/www/www/images/beers/tmp/' . $fileName;
			$config['quality'] = 90;
			$config['x_axis'] = $x1;
			$config['y_axis'] = $y1;
			$config['width'] = $width;
			$config['height'] = $height;
			// load the library
			/*$this->load->library('image_lib', $config); 
			
			$this->image_lib->resize();
			
			echo $this->image_lib->display_errors();*/
			
			// get the beer informaiton
			$beer = $this->BeerModel->getBeerByID($id);
			// create the config information for creating the new file name
			$array = array(
				'brewery' => $beer['name']
				, 'beer' => $beer['beerName']
			);
			$newImageName = nameBeerImage($array);
			
			// create config information for getting file extension
			$array = array(
				'path' => '/home/twobeerdudes/www/www/images/beers/tmp/'
				, 'fileName' => $fileName
			);
			// get the mime type of the image
			$path_parts = pathinfo($array['path'] . $array['fileName']);
			$extension = $path_parts['extension'];
			
			// create config information for sizing the image
			$array = array(
				'image_type' => $extension
				, 'src_path' => '/home/twobeerdudes/www/www/images/beers/tmp/'
				, 'src_image' => $fileName
				, 'target_w' => 150
				, 'target_h' => 350
				, 'coord_x' => $x1
				, 'coord_y' => $y1
				, 'coord_w' => $width
				, 'coord_h' => $height
				, 'save_path' => '/home/twobeerdudes/www/www/images/beers/'
				, 'new_image' => $newImageName . '.' . $extension
				, 'quality' => 100
			);		//echo '<pre>'; print_r($array);exit;
			resample_image($array);
					
			// save the name of the image file to the db
			$this->BeerModel->updateImageByID($id, urlencode($newImageName) . '.' . $extension);
			
			
			// create the config information for changing the file name
			/*$array = array(
				'oldName' => $fileName
				, 'oldPath' => '/home/twobeerdudes/www/www/images/beers/tmp/'
				, 'newName' => $newImageName
				, 'newPath' => '/home/twobeerdudes/www/www/images/beers/'
			);
			$boolean = changeImageName($array);*/
			
			// move the
			header('Location: ' . base_url() . 'admin/edit/beer');
			exit;
		}
		
		// title of the page
		$this->_data['pageTitle'] = 'Two Beer Dudes - Admin Crop Image';
		// create the page header wording
		$this->_data['pageHeader'] = 'Crop Image: ' . $beerName . ' by ' . $brewery;
		// session information
		$this->_data['session'] = $this->session->userdata;
		
		
		//$this->_data['displayData'] = $this->beers->uploadImage($array_img + array('id' => $id));
	
		// array of views and data
		$arr_load = array(
			'pages' => array('header' => true, 'masthead' => true, 'cropImage' => true, 'navigation' => true, 'footer' => true)
			, 'data' => $this->_data
		);
		// load all parts for the view
		$this->doLoad($arr_load);
	}
}