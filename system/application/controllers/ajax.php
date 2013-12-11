<?php
/**
 * Controller that takes care of all the ajax
 * calls from the website
 *
 */
class Ajax extends Controller {
	/**
	 * constructor
	 *
	 */
	public function __construct() {
		// use the parent contructor
		parent::Controller();
		// load the admin helper
		$this->load->helper(array('admin', 'phone', 'js', 'users'));
	}
	
	/**
	 * Creates an update form to be shown on the admin 
	 * update portion
	 *
	 */
	public function edit() {
		// get the type of page
		$type = $this->uri->segment(3);
		// set the output holder
		$output = '';
		// determine the type of page 
		switch($type) {
			case 'brewery':
				// load the breweries model
				$this->load->model('BreweriesModel', '', true);
				// load the state model
				$this->load->model('StateModel', '', true);
				// load the brewery library
				$this->load->library('breweries');
				// get the id from the uri segment
				$id = $this->uri->segment(4);
				// make and array of configuration values
				$array = array(
					'id' => $id
					, 'states' => $this->StateModel->getAllForDropDown()
				);
				// create the output for the screen (form)
				$output = $this->breweries->createForm($array);
				// bust out of the switch statement
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
				// get the id from the uri segment
				$id = $this->uri->segment(4);
				// make and array of configuration values
				$array = array(
					'id' => $id
					, 'breweries' => $this->BreweriesModel->getAllForDropDown()
					, 'styles' => $this->StyleModel->getAllForDropDown()
				);
				// create the output for the screen (form)
				$output = $this->beers->createForm($array);
				// bust out of the switch statement
				break;
			case 'rating':
				// load the beer model
				$this->load->model('RatingModel', '', true);
				// load the packaging model
				$this->load->model('PackageModel', '', true);
				// load the beer library
				$this->load->library('rating');
				// get the id from the uri segment
				$id = $this->uri->segment(4);
				// make and array of configuration values
				$array = array(
					'id' => $id
					, 'packages' => $this->PackageModel->getAllForDropDown()
				);
				// create the output for the screen (form)
				$output = $this->rating->createForm($array);
				// bust out of the switch statement
				break;
		}
		// print out the data for screen display
		echo $output;
	}
	
	/**
	 * Saves the data that has been edited on the admin
	 * portion of the site
	 *
	 */
	public function editData() { 
		// get the type of page
		$type = $this->uri->segment(3);
		// set the output holder
		$output = '';
		// determine the type of page 
		switch($type) {
			case 'brewery':
				// load the breweries model
				$this->load->model('BreweriesModel', '', true);
				// load the brewery library
				$this->load->library('breweries');
				// get the id from the uri segment				
				$id = $this->uri->segment(4);				
				// an array of all the form field values
				$data = array(
					'id' => $id
					, 'categoryID' => 1
					, 'name' => $_POST['txt_name']
					, 'address' => $_POST['txt_address']
					, 'city' => $_POST['txt_city']
					, 'stateID' => $_POST['slt_state']
					, 'zip' => $_POST['txt_zip']
					, 'phone' => $_POST['txt_phone']
					, 'url' => $_POST['txt_url']
				);
				// update the information held in the database
				$this->BreweriesModel->updateBreweryByID($data);
				// create the output for the screen
				$output = $this->breweries->getBreweryByID($id);
				// bust out of the switch statement
				break;
			case 'beer':
				// load the breweries model
				$this->load->model('BreweriesModel', '', true);
				// load the beer model
				$this->load->model('BeerModel', '', true);
				// load the brewery library
				$this->load->library('beers');
				// get the id from the uri segment				
				$id = $this->uri->segment(4);	
				// an array of all the form field values
				$data = array(
					'id' => $id
					, 'beerName' => $_POST['txt_beer']
					, 'establishmentID' => $_POST['slt_brewery']
					, 'styleID' => $_POST['slt_style']
					, 'alcoholContent' => $_POST['txt_abv']
					, 'malts' => $_POST['txt_malts']
					, 'hops' => $_POST['txt_hops']
					, 'yeast' => $_POST['txt_yeast']
					, 'gravity' => $_POST['txt_gravity']
					, 'ibu' => $_POST['txt_ibu']
					, 'food' => $_POST['txt_food']
					, 'glassware' => $_POST['txt_glassware']
					, 'seasonal' => $_POST['slt_seasonal']
					, 'seasonalPeriod' => $_POST['txt_seasonalPeriod']
				);
				// update the information held in the database
				$this->BeerModel->updateBeerByID($data);
				// create the output for the screen
				$output = $this->beers->getBeerByID($id);
				// bust out of the switch statement
				break;
			case 'rating':
				// load the breweries model
				$this->load->model('RatingModel', '', true);
				// load the brewery library
				$this->load->library('rating');
				// get the id from the uri segment				
				$id = $this->uri->segment(4);	
				// an array of all the form field values
				$data = array(
					'id' => $id
					, 'packageID' => $_POST['slt_package']
					, 'rating' => $_POST['slt_rating']
					, 'dateTasted' => $_POST['txt_dateTasted']
					, 'color' => $_POST['txt_color']
					, 'comments' => $_POST['ttr_comments']
					, 'price' => $_POST['txt_price']
					, 'haveAnother' => $_POST['slt_haveAnother']
				);
				// update the information held in the database
				$this->RatingModel->updateRatingByID($data);
				// create the output for the screen
				$output = $this->rating->getRatingByID($id);
				// bust out of the switch statement
				break;
		}
		// print out the data for screen display
		echo $output;
	}
	
	/**
	 * Cancels out of updating/editing on the admin
	 * portion of the site
	 *
	 */
	public function cancel() {
		// get the type of page
		$type = $this->uri->segment(3);
		// set the output holder
		$output = '';
		// determine the type of page 
		switch($type) {
			case 'brewery':
				// load the breweries model
				$this->load->model('BreweriesModel', '', true);
				// load the brewery library
				$this->load->library('breweries');
				// get the id from the uri segment						
				$id = $this->uri->segment(4);								
				$output = $this->breweries->getBreweryByID($id);
				// bust out of the switch statement
				break;
			case 'beer':
				// load the beer model
				$this->load->model('BeerModel', '', true);
				// load the beer library
				$this->load->library('beers');
				// get the id from the uri segment
				$id = $this->uri->segment(4);
				// create the output for the screen (form)
				$output = $this->beers->getBeerByID($id);
				// bust out of the switch statement
				break;
			case 'rating':
				// load the beer model
				$this->load->model('RatingModel', '', true);
				// load the beer library
				$this->load->library('rating');
				// get the id from the uri segment
				$id = $this->uri->segment(4);
				// create the output for the screen (form)
				$output = $this->rating->getRatingByID($id);
				// bust out of the switch statement
				break;
		}
		// print out the data for screen display
		echo $output;
	}
	
	
	/**
	 * Saves the data that has been edited on the admin
	 * portion of the site
	 *
	 */
	public function addData() {
		// get the type of page
		$type = $this->uri->segment(3);
		// set the output holder
		$output = '';
		// user session info
		$userInfo = $this->session->userdata('userInfo');
		// check if set
		$userInfoID = !empty($userInfo) ? $userInfo['id'] : $this->session->userdata('id');
		// determine the type of page
		switch($type) {
			case 'brewery':
				// load the breweries model
				$this->load->model('BreweriesModel', '', true);
				// load the brewery library
				$this->load->library('breweries');
				// an array of all the form field values
				$data = array(
					'name' => $_POST['txt_name']
					, 'categoryID' => 1
					, 'address' => $_POST['txt_address']
					, 'city' => $_POST['txt_city']
					, 'stateID' => $_POST['slt_state']
					, 'zip' => $_POST['txt_zip']
					, 'phone' => $_POST['txt_phone']
					, 'url' => $_POST['txt_url']
					, 'userID' => $userInfoID
				);
				// update the information held in the database
				$id = $this->BreweriesModel->createBrewery($data);
				
				// check if the brewery has been added via the rating area
				if(key_exists('hdn_step', $_POST) && $_POST['hdn_step'] == 'beer') {
					// load the beer model
					$this->load->model('BeerModel', '', true);
					// load the beer library
					$this->load->library('beers');
					// load the rating library
					$this->load->library('rating');
					// make and array of configuration values
					$array = array(
						'beers' => $this->BeerModel->getAllForDropDownByBrewery($id)
					);
					// create the output for the screen (form)
					$output = $this->rating->showBeersForBreweryForm($array, $id, false);
				} else {
					// create the output for the screen
					$output = $this->breweries->getBreweryByID($id);
				}				
				// bust out of the switch statement
				break;
			case 'beer':
				// load the breweries model
				$this->load->model('BeerModel', '', true);
				// load the brewery library
				$this->load->library('beers');
				
				// an array of all the form field values
				$data = array(
					'beerName' => $_POST['txt_beer']
					, 'styleID' => $_POST['slt_style']
					, 'alcoholContent' => $_POST['txt_abv']
					, 'malts' => $_POST['txt_malts']
					, 'hops' => $_POST['txt_hops']
					, 'yeast' => $_POST['txt_yeast']
					, 'gravity' => $_POST['txt_gravity']
					, 'ibu' => $_POST['txt_ibu']
					, 'food' => $_POST['txt_food']
					, 'glassware' => $_POST['txt_glassware']
					, 'seasonal' => $_POST['slt_seasonal']
					, 'seasonalPeriod' => $_POST['txt_seasonalPeriod']
					, 'userID' => $userInfoID
				);
				
				// check to see which way the brewery id is being passed
				if(key_exists('slt_brewery', $_POST)) {
					$data['establishmentID'] = $_POST['slt_brewery'];
				} else {
					$data['establishmentID'] = $this->uri->segment(4);
				}
				
				// update the information held in the database
				$id = $this->BeerModel->createBeer($data);
				
				// check if the brewery has been added via the rating area
				if(key_exists('hdn_step', $_POST) && $_POST['hdn_step'] == 'rate') {
					// load the style model
					$this->load->model('StyleModel', '', true);	
					// load the package model
					$this->load->model('PackageModel', '', true);
					// load the brewery library
					$this->load->library('rating');
					// make and array of configuration values
					$array = array(
						'beerID' => $id
						, 'beer' => $this->BeerModel->getBeerByID($id)
						, 'styles' => $this->StyleModel->getAllForDropDown()
						, 'packages' => $this->PackageModel->getAllForDropDown()
					);
					// create the output for the screen (form)
					$output = $this->rating->createForm($array);
				} else {
					// create the output for the screen
					$output = $this->beers->getBeerByID($id);
				}
				// bust out of the switch statement
				break;
			case 'rating':
				// load the rating model
				$this->load->model('RatingModel', '', true);
				// load the beer model
				$this->load->model('BeerModel', '', true);
				// load the breweries model
				$this->load->model('BreweriesModel', '', true);
				// load the style model
				$this->load->model('StyleModel', '', true);	
				// load the package model
				$this->load->model('PackageModel', '', true);
				// load the state model
				$this->load->model('StateModel', '', true);
				// load the brewery library
				$this->load->library('rating');
				
				// check which step we are on
				$step = !empty($_POST['hdn_step']) ? $_POST['hdn_step'] : $this->uri->segment(4);
				switch($step) {
					case 'begin_rating':
						// need to get information for the form
						$beerID = $_POST['slt_beer'];
						// make and array of configuration values
						$array = array(
							'beerID' => $beerID
							, 'beer' => $this->BeerModel->getBeerByID($beerID)
							, 'styles' => $this->StyleModel->getAllForDropDown()
							, 'packages' => $this->PackageModel->getAllForDropDown()
						);
						// create the output for the screen (form)
						$output = $this->rating->createForm($array);
						break;
					case 'save_rating':
						// an array of all the form field values
						$data = array(
							'beerID' => $_POST['hdn_beerID']
							, 'establishmentID' => 1
							, 'userID' => $this->session->userdata('id')
							, 'packageID' => $_POST['slt_package']
							, 'dateTasted' => $_POST['txt_dateTasted']
							, 'color' => $_POST['txt_color']
							, 'rating' => $_POST['slt_rating']
							, 'comments' => $_POST['ttr_comments']
							, 'haveAnother' => $_POST['slt_haveAnother']	
							, 'price' => $_POST['txt_price']						
						);
						// update the information held in the database
						$id = $this->RatingModel->createRating($data);
						// create the output for the screen
						$output = $this->rating->getRatingByID($id);
						// bust out of the switch statement
						break;
					case 'brewery':
						// make an array of configuration values
						$array = array(
							'breweries' => $this->BreweriesModel->getAllForDropDown()
						);
						// create the output for the screen (form)
						$output = $this->rating->showBreweries($array);
						// bust out of the switch statement
						break;
					case 'brewery_form':
						// load the brewery library
						$this->load->library('breweries');
						// make and array of configuration values
						$array = array(
							'states' => $this->StateModel->getAllForDropDown()
							, 'hidden' => 'rating'
						);
						// create the output for the screen (form)
						$output = $this->breweries->createForm($array);
						// bust out of the switch statement
						break;
					case 'beer_form':
						// load the beer library
						$this->load->library('beers');
						// get the brewery id
						$establishmentID = $this->uri->segment(5);
						// make and array of configuration values
						$array = array(
							'styles' => $this->StyleModel->getAllForDropDown()
							, 'hidden' => 'rating'
							, 'establishmentID' => $establishmentID
						);
						// create the output for the screen (form)
						$output = $this->beers->createForm($array);
						// bust out of the switch statement
						break;
					case 'beer':
						// load the beer library
						$this->load->library('beers');
						// get the brewery id
						$establishmentID = $_POST['slt_brewery'];
						// make and array of configuration values
						$array = array(
							'beers' => $this->BeerModel->getAllForDropDownByBrewery($establishmentID)
						);
						// create the output for the screen (form)
						$output = $this->rating->showBeersForBreweryForm($array, $establishmentID);
						// bust out of the switch statement
						break;
					case 'rate':
						
						// bust out of the switch statement
						break;
				}
				break;
		}
		// print out the data for screen display
		echo $output;
	}
	
	public function uploadFile() {
		// get the type of page
		$type = $this->uri->segment(3);
		// set the output holder
		$output = '';
		// determine the type of upload to do
		switch($type) {
			case 'beerPic':
				// check if there was an image entered
				if(!empty($_FILES['beerImage'])) {		 			
					// configuration values for upload library
					$config = array(
						'upload_path' => './images/beers/tmp/'
						, 'allowed_types' => 'gif|jpg|png'
						, 'max_size' => '2500'
						, 'max_width'  => '1200'
						, 'max_height' => '2800'
						, 'overwrite' => true
					);
					// load the upload library
					$this->load->library('upload', $config);
					// try to upload the image
					if(!$this->upload->do_upload('beerImage')) {
						$output = $this->upload->display_errors();
					} else {
						// the picture was successfully uploaded
						// get the data array of information
						$pic = $this->upload->data();
						// load the beers library
						$this->load->library('beers');
						// call to make the appropriate html for the screen
						//$output = $this->beers->cropImage(array('fileName' => $pic['orig_name']));
						$output = $pic['orig_name'] . ' was uploaded successfully';
						//$output = $pic['orig_name'];
					}
				} else {
					$output = 'Nothing to process';
				}
				// bust out of switch statement
				break;
			case 'avatars':
				// check if there was an image entered
				if(!empty($_FILES['imageToUpload'])) {		 			
					// configuration values for upload library
					$config = array(
						'upload_path' => './images/avatars/tmp/'
						, 'allowed_types' => 'gif|jpg|png'
						, 'max_size' => '200'
						, 'max_width'  => '600'
						, 'max_height' => '600'
						, 'overwrite' => true
					);
					// load the upload library
					$this->load->library('upload', $config);
					// try to upload the image
					if(!$this->upload->do_upload('imageToUpload')) {
						$output = $this->upload->display_errors();
					} else {
						// the picture was successfully uploaded
						// get the data array of information
						$pic = $this->upload->data();
						// call to make the appropriate html for the screen
						$output = $pic['orig_name'] . ' was uploaded successfully';
					}
				} else {
					$output = 'Nothing to process';
				}
				// bust out of switch statement
				break;
			case 'beers':
				// check if there was an image entered
				if(!empty($_FILES['imageToUpload'])) {		 			
					// configuration values for upload library
					$config = array(
						'upload_path' => './images/beers/tmp/'
						, 'allowed_types' => 'gif|jpg|png'
						, 'max_size' => '250'
						, 'max_width'  => '600'
						, 'max_height' => '1400'
						, 'overwrite' => true
					);
					// load the upload library
					$this->load->library('upload', $config);
					// try to upload the image
					if(!$this->upload->do_upload('imageToUpload')) {
						$output = $this->upload->display_errors();
					} else {
						// the picture was successfully uploaded
						// get the data array of information
						$pic = $this->upload->data();
						// call to make the appropriate html for the screen
						$output = $pic['orig_name'] . ' was uploaded successfully';
					}
				} else {
					$output = 'Nothing to process';
				}
				// bust out of switch statement
				break;
			case 'establishments':
				// check if there was an image entered
				if(!empty($_FILES['imageToUpload'])) {		 			
					// configuration values for upload library
					$config = array(
						'upload_path' => './images/establishments/tmp/'
						, 'allowed_types' => 'gif|jpg|png'
						, 'max_size' => '250'
						, 'max_width'  => '600'
						, 'max_height' => '750'
						, 'overwrite' => true
					);
					// load the upload library
					$this->load->library('upload', $config);
					// try to upload the image
					if(!$this->upload->do_upload('imageToUpload')) {
						$output = $this->upload->display_errors();
					} else {
						// the picture was successfully uploaded
						// get the data array of information
						$pic = $this->upload->data();
						// call to make the appropriate html for the screen
						$output = $pic['orig_name'] . ' was uploaded successfully';
					}
				} else {
					$output = 'Nothing to process';
				}
				// bust out of switch statement
				break;
			default:
				$output = 'There was an error processing the image';
				break;
		}
		echo $output;		
	}
	
	public function deleteImage() {
		// holder value for the output
		$output = '';
		// get the type of image to delete
		$type = $this->uri->segment(3);		
		// get the id of the item being affected
		$id = $this->uri->segment(4);
		
		// determine which methods to invoke
		switch($type) {
			case 'beer':
				// path to the beer images
				$path = '/home/twobeerdudes/www/www/images/beers/';
				// load the beer model
				$this->load->model('BeerModel', '', true);
				// get the information about the picture
				$array_pic = $this->BeerModel->getImageByID($id);
				// get the name of the image to delete
				$imageName = urldecode($array_pic['picture']);
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
				}
				// bust out of here
				break;
		}
		echo $output;
	}
	
	public function swapadd() {
		$logged = checkLogin();
		if($logged === true) {
			// load the swap model
			$this->load->model('SwapModel', '', true);
			// load the beer model
			$this->load->model('BeerModel', '', true);
			// load the user lib
			$this->load->library('userslib');
			// get the user info
			$userInfo = $this->session->userdata('userInfo');
			// get the type of swap to add
			$type = $this->uri->segment(3);
			// get the beer id
			$beerID = is_array($_POST) && key_exists('beerID', $_POST) ? $_POST['beerID'] : '';
			// set the holder if get needs to be used
			$get = false;
			// check if beerID is empty, if so, it has to be get
			if(empty($beerID)) {
				$beerID = $this->uri->segment(4);
				$get = true;
			}
			// so determine what to show
			switch($type) {
				case 'outs':
					// get a record set of swap outs
					$swapouts = $this->SwapModel->getSwapOutsByUserID($userInfo['id']);
					// get the number of swapouts
					$soNum = count($swapouts);
					// only add the swapout if there are less than 10 and the swapout
					// isn't already in the list
					if($soNum < 10) {
						// check if they have already added this beer to either list
						$outs = $this->SwapModel->determineInSwapOuts($beerID, $userInfo['id']);
						$ins = $this->SwapModel->determineInSwapIns($beerID, $userInfo['id']);
						// only add to the list if they don't exist in either
						if($outs == false && $ins == false) {
							// add the beer to the list
							$this->SwapModel->insertSwapOut($userInfo['id'], $beerID);
						}
					}
					// create the output based on boolean
					if($get === true) {			
						// load the beers library
						$this->load->library('beers');
						// get the data for the screen			
						$this->beers->showBeerRatings($beerID, true, $logged);
					} else {
						$this->userslib->showSwaplistOuts($userInfo['id'], true);
					}
					// get out of here
					break;
				case 'ins':
				default:
					// get a record set of swap ins
					$swapins = $this->SwapModel->getSwapInsByUserID($userInfo['id']);		
					// get the number of swapins
					$siNum = count($swapins && !in_array($beerID, $swapins));
					// only add the swapin if there are less than 10
					if($siNum < 10) {
						// check if they have already added this beer to either list
						$outs = $this->SwapModel->determineInSwapOuts($beerID, $userInfo['id']);
						$ins = $this->SwapModel->determineInSwapIns($beerID, $userInfo['id']);
						// only add to the list if they don't exist in either
						if($outs == false && $ins == false) {
							// add the beer to the list
							$this->SwapModel->insertSwapIn($userInfo['id'], $beerID);
						}
					}
					// create the output based on boolean
					if($get === true) {			
						// load the beers library
						$this->load->library('beers');
						// get the data for the screen			
						$this->beers->showBeerRatings($beerID, true, $logged);
					} else {
						$this->userslib->showSwaplistIns($userInfo['id'], true);
					}
					// get out of here
					break;
			}
		}
	}
	
	public function swapremove() {
		if(checkLogin() === true) {
			// load the swap model
			$this->load->model('SwapModel', '', true);
			// load the beer model
			$this->load->model('BeerModel', '', true);
			// load the user lib
			$this->load->library('userslib');
			// get the user info
			$userInfo = $this->session->userdata('userInfo');
			// get the type of swap to add
			$type = $this->uri->segment(3);
			// get the beer id
			$beerID = $this->uri->segment(4);
			// so determine what to show
			switch($type) {
				case 'outs':
					// add the beer to the list
					$this->SwapModel->removeSwapOut($userInfo['id'], $beerID);
					// create the output
					$this->userslib->showSwaplistOuts($userInfo['id'], true, true);
					break;
				case 'ins':
				default:
					// add the beer to the list
					$this->SwapModel->removeSwapIn($userInfo['id'], $beerID);
					// create the output
					$this->userslib->showSwaplistIns($userInfo['id'], true, true);
					break;
			}
		}
	}
	
	public function swapFeedbackAdd() {
		if(checkLogin() === true) {
			// load the swap model
			$this->load->model('SwapModel', '', true);
			// load the user model
			$this->load->model('UserModel', '', true);
			// load the user lib
			$this->load->library('userslib');
			// get the user info
			$userInfo = $this->session->userdata('userInfo');
			
			$str = '';
			// check that the writerID is set and matches the logged in user
			if(empty($_POST['hdn_writerUserID']) || $_POST['hdn_writerUserID'] != $userInfo['id']) {
				$str = false;
			} else if($this->UserModel->idCheck($_POST['hdn_writerUserID']) == false) {
				$str = false;
			}
			
			// check that the feedbackID is set and matches the logged in user
			if(empty($_POST['hdn_feedbackUserID'])) {
				$str = false;
			} else if($this->UserModel->idCheck($_POST['hdn_feedbackUserID']) == false) {
				$str = false;
			}
			
			// check that the feedback wasn't empty
			if(empty($_POST['ttr_swapFeedback'])) {
				$str = false;
			} else if($this->filterWords($_POST['ttr_swapFeedback']) == false) {
				$str = false;
			}
			
			if($str === false) {
				echo 'bad';
			} else {
				// store the feedback
				$this->SwapModel->saveFeedback($_POST);
				// send out an email to two beer dudes
				
				// get the information for the screen
				echo $this->userslib->showProfile($_POST['hdn_feedbackUserID'], true);
			}
		}
	}
	
	public function mailremove() {
		if(checkLogin() === true) {
			// load the user model
			$this->load->model('UserModel', '', true);
			// load the user library
			$this->load->library('userslib');
			// get the user info
			$userInfo = $this->session->userdata('userInfo');
			// get the message to remove
			$messageID = $this->uri->segment(3);
			
			// remove the message
			$this->UserModel->removePM($messageID, $userInfo['id']);
			
			$this->userslib->showMessages($userInfo, true);
		}
	}
	
	public function blogRSS() {
		// holder for output
		$str = '';
		// load the wordpress helper
		$this->load->helper('wp');		
		
		try {
			// set the configuration array
			$config = array(
				'size' => BLOG_RSS_COUNT
			);
			// get the wordpress rss
			$str = parseWPRSS($config);
		} catch(Exception $e) {
			$str = $e->getMessage();
		}
		// show it on the screen
		echo $str;
	}
	
	private function filterWords($str) {
		// holder variable
		$boolean = true;
		// get an array of badwords
		$badWords = file('/home/twobeerdudes/www/www/list/badWords.txt', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
		// iterate through the array, checking for bad words
		foreach($badWords as $badWord) {
			if(preg_match('/\b' . $badWord . '\b/i', $str)) {
				$boolean = false;
			}			
		}
		// return the result
		return $boolean;
	}
	
	public function removeDude() {
		// get the id of the dude friend to remove
		$id = $this->uri->segment(3);
		// check that we have a segment
		if($id != false) {
			// need the logged in user id
			$userInfo = $this->session->userdata('userInfo');
			// load the models
			$this->load->model('UserModel', '', true);
			// delete the dude from the list
			$this->UserModel->removeFromDudeList($userInfo['id'], $id);
			// get the remaining dude list
			$str = showDudeList($userInfo['id'], true);
			// send out the output
			echo $str;
		}		
	}
	
	public function addDude() {
		// get the id of the dude friend to remove
		$id = $this->uri->segment(3);
		// check that we have a segment
		if($id != false) {
			// need the logged in user id
			$userInfo = $this->session->userdata('userInfo');
			// check that these two values aren't the same
			if($id != $userInfo['id']) {
				// load the models
				$this->load->model('UserModel', '', true);
				// check that the user exists that is to be added
				$user = $this->UserModel->getUsernameByID($id);
				// check that this isn't empty
				if(!empty($user)) {
					// delete the dude from the list
					$this->UserModel->addToDudeList($userInfo['id'], $id);						
				}				
			}
		}
		// get the remaining dude list
		$str = showDudeList($userInfo['id'], true);
		// send out the output
		echo $str;
	}
}

/*
// get the image value that was entered
				$image = $_POST['hdn_picture'];
				// set the temporary image name
				$imageName = NULL;
				// set the image processing temporary value
				$processImage = 1;
				// check if there was an image entered
				if(!empty($image)) {
					// create the name for image after upload
					$imageName = nameBeerImage(array('id' => $_POST['slt_brewery'], 'beer' => $_POST['txt_beer']));
					// image processing will need to be done
					$processImage = 2;
				}	
				
				// temporary holder for results of image upload
				$imageUploadSuccess = false;			
				
				// check if there is image processing involved
				if($processImage == 2) {
					// try to upload the image
					$this->load->helper('upload');
					// get an array of results
					$result = $this->upload->do_upload($image);
					// check if the image was uploaded correctly
					if(!key_exist('error', $result)) {
						// allow the image to be processed
						// start by renaming the image
						$imageUploadSuccess = changeBeerImageName(array('oldName' => $result['file_name'], 'newName' => $imageName, 'oldPath' => '/www/www/images/beers/'));
						// check if this was successful
						if($imageUploadSuccess === false) {
							// remove the image as we couldn't rename it
							// this really shouldn't happen
							removeImage(array(array('fileName' => $result['file_name'], 'path' => '/www/www/images/beers/')));
							// bust out of here
							break;
						}						
					}
				} else if($processImage == 3) {
					// change the image size
					
					// create the output for the screen
					$output = $this->beers->getBeerByID($id);
				} 
				
				if($processImage != 3) {
					// an array of all the form field values
					$data = array(
						'id' => $id
						, 'beerName' => $_POST['txt_beer']
						, 'breweryID' => $_POST['slt_brewery']
						, 'styleID' => $_POST['slt_style']
						, 'alcoholContent' => $_POST['txt_abv']
						, 'malts' => $_POST['txt_malts']
						, 'hops' => $_POST['txt_hops']
						, 'yeast' => $_POST['txt_yeast']
						, 'gravity' => $_POST['txt_gravity']
						, 'ibu' => $_POST['txt_ibu']
						, 'food' => $_POST['txt_food']
						, 'glassware' => $_POST['txt_glassware']
						, 'picture' => $imageName
						, 'seasonal' => $_POST['slt_seasonal']
						, 'seasonalPeriod' => $_POST['txt_seasonalPeriod']
					);
					// update the information held in the database
					$this->BeerModel->updateBeerByID($data);
					
					// check to see which information needs to be processed
					// for output
					if($processImage == 2 && $imageUploadSuccess === true) {
						// create the output for the screen
						$output = $this->beers->getBeerByID($id);
					} else {
						// create the output for the screen
						$output = $this->beers->cropImage($data);
					}					
				}
*/
?>