<?php
class Page extends Controller {
	private $upload = array(
		'avatars' => array(
			'width' => 100
			, 'height' => 100
		)
		, 'beers' => array(
			'width' => 150
			, 'height' => 350
		)
		, 'establishments' => array(
			'width' => 240
			, 'height' => 160
		)
	);	
	
	public function __construct() {
		parent::Controller();
		$this->load->helper(array('users', 'admin', 'js', 'email', 'form'));
		$this->load->library('session');
		// helper to get the quote for the footer - in users_helper.php
		getFooterQuote();
	}
	
	private function doLoad($config) {
		$array = array(
			'header' => 'inc/normalHeader.inc.php'			
			, 'notlogged' => 'page/notlogged.php'		
			, 'footer' => 'inc/footer.inc.php'
			, 'gallery' => 'page/gallery.php'
			, 'noproto' => 'inc/headerNoPrototype.inc.php'			
			, 'index2' => 'page/index2.php'
			, 'search' => 'page/search.php'
			, 'upload' => 'page/upload.php'
			, 'aboutUs' => 'page/aboutUs.php'
			, 'contactUs' => 'page/contactUs.php'
			, 'updateInfo' => 'page/updateInfo.php'
			, 'cropImage' => 'page/cropImage.php'	
            , 'privacy' => 'page/privacy.php'
            , 'agreement' => 'page/agreement.php'		
			, 'headerFrontEnd' => 'inc/header_frontend.inc.php'
			, 'formMast' => 'inc/formMast.inc.php'
			, 'navigation' => 'inc/navigation.inc.php'
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
	
	public function index2() {
		// check if the user is logged in or not
		if(checkLogin() == true) {
			$userInfo = $this->session->userdata('userInfo');
		}
		// load the quote model
		$this->load->model('QuoteModel', '', true);
		// get a random quote
		$this->_data['quote'] = $this->QuoteModel->getRandom();
		
		// load the beer model
		$this->load->model('RatingModel', '', true);
		// load the beer library
		$this->load->library('rating');
		// get the three newest ratings
		$this->_data['displayData'] = $this->rating->getRatingsLimit(3);
		
		// load the brewery model
		$this->load->model('BreweriesModel', '', true);
		// load the brewery library
		$this->load->library('breweries');
		// get the information for the particular brewery hop
		$str = $this->breweries->showAllBreweryHops();
		// set the output for the screen
		$this->_data['output'] = $str;
		
		$this->load->view('page/index', $this->_data);
	}
	
	public function index() {
		// get the login boolean
		$logged = checkLogin();

		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');
		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// load the beer model
		$this->load->model('RatingModel', '', true);
		// load the beer library
		$this->load->library('rating');
		// get the three newest ratings
		$this->_data['beerReviews'] = $this->rating->getRatingsLimit();
		
		// load the brewery model
		$this->load->model('BreweriesModel', '', true);
		// load the brewery library
		$this->load->library('breweries');
		// get the information for the particular brewery hop
		$this->_data['breweryHop'] = $this->breweries->showBreweryHopFrontPage();

		// load the season model
		$this->load->model('SeasonModel', '', true);
		// load the season library
		$this->load->library('seasons');
		// get the information for the current season
		$this->_data['season'] = $this->seasons->showSeasonFrontPage();		
		
		// set the page information
		$this->_data['seo'] = getSEO();
				
		// array of views and data
		$arr_load = array(
			'pages' => array(
				'headerFrontEnd' => true
				, 'formMast' => true
				, 'navigation' => true
				, 'index2' => true
				, 'footerFrontEnd' => true
			)
			, 'data' => $this->_data
		);
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function search() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// load the search model
		$this->load->model('SearchModel', '', true);
		// load the page library
		$this->load->library('pages');
		
		// start the left column output
		$str = '<h2 class="brown">Search Results</h2>';
		
		// holder for the final search string
		$finalSearchString = array();
		// get filter words
		$badWords = file('./list/noisewords.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		//echo '<pre>'; print_r($badWords); exit;
		// original search string
		$oSearchString = $_POST['txt_search'];
		// get the passed search string
		$searchString = explode(' ', $oSearchString);
		// iterate through the array, if it is an array
		if(!empty($searchString) && is_array($searchString)) {
			// iterate through the array
			foreach($searchString as $string) {
				// get rid of any words less than three letters
				if(strlen($string) > 2  && !in_array($string, $badWords)) {
					// add the string to the holder array
					$finalSearchString[] = $string;
				}
			}
		}
		
		// check that the fixed array has words
		if(empty($finalSearchString)) {
			// nothing to search for
			$str .= '<p class="marginTop_8">No results were found for fitting the search criteria.</p>';
		} else {
			// search for the words in array
			// the type of search to do
			$type = $_POST['slt_searchType'];
			// get the right table
			switch($type) {				
				case 'establishment':
					$type = 'establishment';
					break;
				case 'user':
					$type = 'user';
					break;
				case 'beer':
				default:
					$type = 'beer';
					break;
			}		
			// seach by exact original string
			$search['original'] = $oSearchString;
			// search for each word with wild cards
			$search['wildCards'] = $finalSearchString;
			
			// do the search
			$searchRS = $this->SearchModel->doSearch($search, $type);
			//echo '<pre>'; print_r($searchRS); exit;
			
			$str .= '
				<p class="searchString marginTop_8">Search Term: <span class="bold">' . $oSearchString . '</span> in <span class="bold">' . $type . '</span></p>
				<p>Words actually searched: <span class="bold">' . implode(' ', $finalSearchString) . '</span></p>
			';
			
			// check if there are results
			if($searchRS != false) {
				// begin the list
				$str .= '<ul class="marginTop_8">';
				// counter for declaring background color
				$cnt = 0;
				// iterate through the results
				foreach($searchRS as $item) {
					// get the background color
					$bgColor = ($cnt % 2 == 0) ? ' class="padAll bg2"' : ' class="padAll"';
					switch($type) {
						case 'user':
							$str .= '
								<li' . $bgColor . '>
									<p class="bold"><a href="' . base_url() . 'user/profile/' . $item['id'] . '">' . $item['username'] . '</a></p>
								</li>
							';
							break;
						case 'establishment':
							$str .= '
								<li' . $bgColor . '>
									<p class="bold"><a href="' . base_url() . 'establishment/info/rating/' . $item['id'] . '">' . $item['name'] . '</a></p>
									<p><a href="' . base_url() . 'establishment/city/' . $item['stateID'] . '/' . urlencode($item['city']) . '">' . $item['city'] . '</a>, <a href="' . base_url() . 'establishment/state/' . $item['stateID'] . '">' . $item['stateFull'] . '</a></p>
								</li>
							';
							break;
						case 'beer':
						default:
							// check if retired
							$retired = $item['retired'] == '1' ? ' <span class="retired">(Retired, no longer in production.)</span>' : '';
							$str .= '
								<li' . $bgColor . '>
									<p class="bold"><a href="' . base_url() . 'beer/review/' . $item['id'] . '">' . $item['beerName'] . '</a>' . $retired . '</p>
									<p><a href="' . base_url() . 'brewery/info/' . $item['establishmentID'] . '">' . $item['name'] . '</a> - <a href="' . base_url() . 'establishment/city/' . $item['stateID'] . '/' . urlencode($item['city']) . '">' . $item['city'] . '</a>, <a href="' . base_url() . 'establishment/state/' . $item['stateID'] . '">' . $item['stateFull'] . '</a></p>
								</li>
							';
							break;
					}
					// increment the counter
					$cnt++;
				}
				// finish the list
				$str .= '</ul>';
			} else {
				$str .= '<p class="marginTop_8 bold">No results were found for fitting the search criteria.</p>';
			}			
		} 
		
		// left column output
		$this->_data['leftCol'] = $str;
		
		// right column help
		$this->_data['rightCol'] = '
			<h4><span>Search Pointers</span></h4>
			<ul>
				<li>Certain words are discarded and not searched as they are considered &#34;common.&#34;</li>
				<li>Make sure you are spelling words correctly</li>
				<li></li>
			</ul>
		';
		
		//echo '<pre>'; print_r($_POST); echo '<?pre>'; exit;
		
		// set the page information
		$this->_data['seo'] = getSEO();
				
		// array of views and data
		$arr_load = array(
			'pages' => array(
				'headerFrontEnd' => true
				, 'formMast' => true
				, 'navigation' => true
				, 'search' => true
				, 'footerFrontEnd' => true
			)
			, 'data' => $this->_data
		);
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function uploadImage() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);		
		
		// the value of the main view to load
		$view = '';		
		
		// check if the user is logged in or not
		if($logged == true) {
			// get session information
			$userInfo = $this->session->userdata('userInfo');
			
			// load the pages libary 
			$this->load->library('pages');
			
			// determine the type of image to upload
			$type = $this->uri->segment(3);
			// get the id of the user, beer, or brewery
			$id = $this->uri->segment(4);
			
			// make sure both values are entered
			if($type === false || $id === false) {
				// shouldn't be at this page
				header('Location: ' . base_url());
				exit;
			} else if($type == 'avatars' && $userInfo['id'] != $id) {
				// they are not allowed to upload
				$this->_data['leftCol'] = '
					<h2 class="brown">Upload Image</h2>
					<p>You are not allowed to upload an avatar for someone else.</p>
				';
				// the view to use
				$view = 'upload';
			} else if($userInfo['uploadImage'] == "0") {
				// they are not allowed to upload
				$this->_data['leftcol'] = '
					<h2 class="brown">Upload Image</h2>
					<p>You are banned from uploading images.</p>
				';
				// the view to use
				$view = 'upload';
			} else {	
				// get information about the item in question
				$itemInfo = $this->getImageUploadInfo($id, $type);
				// get the form	
				$this->_data['leftCol'] = 
					$itemInfo . '
					<p class="marginTop_8">File uploads are easy and have two steps: one, select a file to upload from 
					your computer and, two, crop the image to show exactly the part of it you want.</p>' .
					$this->pages->uploadImageForm(array('type' => $type, 'id' => $id))
				;
				//echo '<pre>'; print_r($this->_data['displayData']); echo '</pre>'; exit;
				// create the page header wording
				//$this->_data['pageHeader'] = 'Upload Image: ' . $array_img['beerName'] . ' by ' . $array_img['name'];
				// session information
				//$this->_data['session'] = $this->session->userdata;
				
				//$this->_data['displayData'] = $this->beers->uploadImage($array_img + array('id' => $id));
				$view = 'upload';
			}
		} else {			
			// set the heading
			$this->_data['heading'] = 'Upload Image';
			// not logged in
			// set the view
			$view = 'notlogged';
		}
		
		// get the seo for the page
		$this->_data['seo'] = getSEO(3);
		
		// the right side text
		$this->_data['rightCol'] = '
			<h4><span>Upload Hints</span></h4>
			<ul>
				<li>Only jpg, png, and gif images can be uploaded.  These file extensions are all associated with image files.</li>
				<li>Memory is not unlimited on our server, so...</li>
				<li>Max <span class="bold">file size</span> is <span class="bold">200 megabytes</span>.</li>
				<li>Max <span class="bold">image width</span> is <span class="bold">600 pixels</span>.</li>
				<li>Max <span class="bold">image height</span> is <span class="bold">1050 pixels</span>.</li>
				<li><span class="bold">note:</span> gimp is an open source, free image processing software if you already don&#39;t have this type of software.  This software will help you manipulate your images to fit the upload criteria.</li>
			</ul>
			
			<h4><span>Final Image Sizes</span></h4>
			<ul>
				<li><span class="bold">avatars:</span> 100 pixels by 100 pixels</li>
				<li><span class="bold">beers:</span> 150 pixels by 350 pixels</li>
				<li><span class="bold">establishments:</span> 240 pixels by 160 pixels</li>
				<li><span class="bold">note:</span> images you upload do not need to be this size, the second step, cropping will help accomplish this.</li>
			</ul>
		';
				
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
		/*$arr_load = array(
			'pages' => array('header' => true, $view => true, 'footerFrontEnd' => true)
			, 'data' => $this->_data
		);*/
		// get the information ready for display
		$arr_load = array(
			'pages' => array(				
				'headerFrontEnd' => true
				, 'formMast' => true
				, 'navigation' => true
				, $view => true
				, 'footerFrontEnd' => true
			)
			, 'data' => $this->_data
		);
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	private function getImageUploadInfo($id, $type, $word = 'Upload') {
		// holder for the information about the item that
		// an image it tyring to be uploaded for
		$str = '';
		// check the type of the information that is needed
		switch($type) {
			case 'avatars':	
				// user info for logged in user
				$userInfo = $this->session->userdata('userInfo');
				// create the right header
				$str = '<h2 class="brown">' . $word . ' Image: New Avatar for ' . $userInfo['username'] . '</h2>';
				// get out of here			
				break;
			case 'beers':
				// load the beer model
				$this->load->model('BeerModel', '', true);
				// get the info for this beer
				$info = $this->BeerModel->getBeerByID($id);
				// create the right header
				$str = '<h2 class="brown">' . $word . ' Image: ' . $info['beerName'] . ' by ' . $info['name'] . '</h2>';
				// get out of here
				break;
			case 'establishments':
				// load the establishment model
				$this->load->model('EstablishmentModel', '', true);
				// get the info for this establishment
				$info = $this->EstablishmentModel->getEstablishmentByID($id);
				// create the right header
				$str = '<h2 class="brown">' . $word . ' Image: ' . $info['name'] . '</h2>';
				// get out of here
				break;
		}
		// send back the information
		return $str;
	}
	
	public function cropImage() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// check if the user is logged in or not
		if($logged == true) {
			// load the page library
			$this->load->library('pages');
			// load the upload helper
			$this->load->helper('upload');
			
			// determine the type of image to upload
			$type = $this->uri->segment(3);
			// get the id of the user, beer, or brewery
			$id = $this->uri->segment(4);
			
			// get information about user
			$userInfo = $this->session->userdata('userInfo');
			
			// make sure both values are entered
			if($type === false || $id === false) {
				// shouldn't be at this page
				header('Location: ' . base_url());
				exit;
			} else if($userInfo['uploadImage'] == "0") {
				// they are not allowed to upload
				$this->_data['leftCol'] = '
					<h2 class="brown">Crop Image</h2>
					<p class="marginTop_8>You are banned from uploading images.</p>
				';
				// the view to use
				$view = 'upload';
			} else {			
				if(key_exists('btn_submit', $_POST)) {
					$picture = $_POST['hdn_picture'];
					
					$array = array(
						'fileName' => $picture
						, 'id' => $id
						, 'type' => $type
						, 'width' => $this->upload[$type]['width']
						, 'height' => $this->upload[$type]['height']
					);
					
					// get information about the item in question
					$itemInfo = $this->getImageUploadInfo($id, $type, 'Crop');
					
					$this->_data['leftCol'] = 
						$itemInfo . '
						<p class="marginTop_8">Based on the type of image the selected area shows the ratio of the final
						image size.  The selected area can be dragged and resized before hitting the cropping button
						to finish the process.</p>' .
						$this->pages->cropImage($array)
					;
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
					$config['source_image'] = '/home/twobeerdudes/www/www/images/' . $type . '/tmp/' . $fileName;
					$config['quality'] = 90;
					$config['x_axis'] = $x1;
					$config['y_axis'] = $y1;
					$config['width'] = $width;
					$config['height'] = $height;

					$newItemName = '';
					$location = '';
					switch($type) {
						case 'avatars':		
							$location = 'user/profile/' . $id;
							$newItemName = $userInfo['username'] . '_' . $userInfo['id'];
							break;
						case 'beers':
							$location = 'beer/review/' . $id;
							// load the beer model to get information about the beer
							$this->load->model('BeerModel', '', true);
							$beer = $this->BeerModel->getBeerByID($id);
							// create the config information for creating the new file name
							$array = array(
								'brewery' => $beer['name']
								, 'beer' => $beer['beerName']
							);
							$newItemName = nameBeerImage($array);
							break;
						case 'establishments':
							$location = 'establishment/info/rating/' . $id;
							// load the establishment model to get information about the establishment
							$this->load->model('EstablishmentModel', '', true);
							$brewery = $this->EstablishmentModel->getEstablishmentByID($id);
							// create the config information for creating the new file name
							$array = array(
								'brewery' => $brewery['name']
							);
							$newItemName = nameEstablishmentImage($array);
							break;
					}
					
					// create config information for getting file extension
					$array = array(
						'path' => '/home/twobeerdudes/www/www/images/' . $type . '/tmp/'
						, 'fileName' => $fileName
					);
					// get the mime type of the image
					$path_parts = pathinfo($array['path'] . $array['fileName']);
					$extension = $path_parts['extension'];
					
					// create config information for sizing the image
					$array = array(
						'image_type' => $extension
						, 'src_path' => '/home/twobeerdudes/www/www/images/' . $type . '/tmp/'
						, 'src_image' => $fileName
						, 'target_w' => $this->upload[$type]['width']
						, 'target_h' => $this->upload[$type]['height']
						, 'coord_x' => $x1
						, 'coord_y' => $y1
						, 'coord_w' => $width
						, 'coord_h' => $height
						, 'save_path' => '/home/twobeerdudes/www/www/images/' . $type . '/'
						, 'new_image' => $newItemName . '.' . $extension
						, 'quality' => 100
					);		//echo '<pre>'; print_r($array);exit;
					resample_image($array);
					
					$msg = '';
					switch($type) {
						case 'avatars':		
							// load the user model
							$this->load->model('UserModel', '', true);
							// update the avatar value
							$update = $this->UserModel->updateAvatar($id, $newItemName . '.' . $extension);
							//echo $update . '<br />';
							
							// update the avatar session info
							$userInfo['avatar'] = 1;
							$this->session->set_userdata(array('userInfo' => $userInfo));
							
							// email message	
							$msg = 'A new image was uploaded, please check if okay!' . "\r\n";		
							$msg .= 'ID: ' . $id . "\r\n";
							$msg .= 'URL: ' . base_url() . 'user/profile/' . $id . "\r\n";			
							break;
						case 'beers':
							// save the name of the image file to the db
							$this->BeerModel->updateImageByID($id, urlencode($newItemName) . '.' . $extension);
							// email message	
							$msg = 'A new image was uploaded, please check if okay!' . "\r\n";		
							$msg .= 'ID: ' . $id . "\r\n";
							$msg .= 'URL: ' . base_url() . 'beer/review/' . $id . "\r\n";
							break;
						case 'establishments':
							// save the name of the image file to the db
							$this->EstablishmentModel->updateImageByID($id, urlencode($newItemName) . '.' . $extension);
							// email message	
							$msg = 'A new image was uploaded, please check if okay!' . "\r\n";		
							$msg .= 'ID: ' . $id . "\r\n";
							$msg .= 'URL: ' . base_url() . 'brewery/info/' . $id . "\r\n";
							break;
					}
					
					$array = array(
						'to' => 'scot@twobeerdudes.com, rich@twobeerdudes.com'
						, 'subject' => 'New Image Upload'
						, 'message' => $msg
						, 'header' => 'From: webmaster@twobeerdudes.com' . "\r\n"
					);
					// send an email to notify about upload
					sendEmail($array);
					
					// move to an appropriate page
					header('Location: ' . base_url() . $location);
					exit;
				}
			}
		} else {
			// not logged in
			header('Location: ' . base_url() . 'admin/edit/beer');
			exit;
		}
		/*// load the beer model
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
			//$this->load->library('image_lib', $config); 
			
			//$this->image_lib->resize();
			
			//echo $this->image_lib->display_errors();			
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
			

			
			// move the
			header('Location: ' . base_url() . 'admin/edit/beer');
			exit;
		}
		
		// title of the page
		$this->_data['pageTitle'] = 'Two Beer Dudes - Admin Crop Image';
		// create the page header wording
		$this->_data['pageHeader'] = 'Crop Image: ' . $beerName . ' by ' . $brewery;
		// session information
		$this->_data['session'] = $this->session->userdata;*/
		
		
		//$this->_data['displayData'] = $this->beers->uploadImage($array_img + array('id' => $id));
	
		// the right side text
		$this->_data['rightCol'] = '
			<h4><span>Cropping Hints</span></h4>
			<ul>
				<li>The &#34;box&#34; on your image represents the final size of the image.  Dragging it and resizing it is fine, your final image will be adjusted automatically.</li>
				<li>The file sizes below represent the size the image will be stored on the system as.</li>
				<li>Beer pictures are meant to be a full bottle shot.</li>
				<li>Try to crop out as much of the background as possible, the goal is a good quality picture of the subject.</li>
			</ul>
			
			<h4><span>Final Image Sizes</span></h4>
			<ul>
				<li><span class="bold">avatars:</span> 100 pixels by 100 pixels</li>
				<li><span class="bold">beers:</span> 150 pixels by 350 pixels</li>
				<li><span class="bold">establishments:</span> 200 pixels by 250 pixels</li>
			</ul>
		';
		
		
		// array of views and data
		/*$arr_load = array(
			'pages' => array('header' => true, 'cropImage' => true, 'footerFrontEnd' => true)
			, 'data' => $this->_data
		);*/
		// get the information ready for display
		$arr_load = array(
			'pages' => array(				
				'headerFrontEnd' => true
				, 'formMast' => true
				, 'navigation' => true
				, 'cropImage' => true
				, 'footerFrontEnd' => true
			)
			, 'data' => $this->_data
		);
		// load all parts for the view
		$this->doLoad($arr_load);
	}	
	
	public function createImage() {
		// load the uplod helper
		$this->load->helper('upload');
		
		// get the id from url
		$id = $this->uri->segment(3);
		// get the image type
		$type = $this->uri->segment(4);
				
		// make sure all are set
		if($id != false && $type != false) {
			// holder for setup for sizing image
			$array = array();
			// check to see which type of image is being manipulated
			switch($type) {
				case 'beer':
					// load the beer model
					$this->load->model('BeerModel', '', true);
					// get the name of the image
					$arr_image = $this->BeerModel->getImageByID($id);
					// holder for the name of the image
					$image = '';
					//echo '<pre>'; print_r($arr_image); echo '</pre>';
					// check if there is an image
					//if(empty($arr_image)) {
					if(!key_exists('picture', $arr_image) || empty($arr_image['picture'])) {
						$image = 'bottle.gif';
					} else {
						$image = $arr_image['picture'];
					}
					
					// check if the uri contains resize info
					$type = $this->uri->segment(5);
					switch($type) {
						case 'mini':
							$widthMultiplier = .2;
							$heightMultiplier = .2;
							break;
						default: 
							$widthMultiplier = .5;
							$heightMultiplier = .5;
							break;
					}
					
					$array = array(
						'path' => './images/beers/'
						, 'image' => $image
						, 'alt' => $arr_image['beerName'] . ' by ' . $arr_image['name']
						, 'widthMultiplier' => $widthMultiplier
						, 'heightMultiplier' => $heightMultiplier
					);
					break;
			}
			resizeImageOnFly($array);
		} else {
			
		}
	}
	
	public function gallery() {
		// get the login boolean
		$logged = checkLogin();

		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');
		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// load the event model
		$this->load->model('EventModel', '', true);
		// load the events library
		$this->load->library('events');
		
		// get the event id 
		$eventID = $this->uri->segment(3);
		
		// check if the segment is empty
		if($eventID == false) {
			$eventID = 1;
		}
		
		$info = $this->events->showGallery($eventID);
		//echo '<pre>'; print_r($array); echo '</pre>'; exit;
		$this->_data['events'] = $info['str'];
		$this->_data['head'] = $info['head'];
		
		// set the page seo information
		$this->_data['seo'] = array_slice($info, 0, 3);		
		
		// array of views and data
		$arr_load = array(
			'pages' => array('noproto' => true, 'gallery' => true, 'footerFrontEnd' => true)
			, 'data' => $this->_data
		);
		$arr_load = array(
			'pages' => array(
				'headerFrontEnd' => true
				, 'formMast' => true
				, 'navigation' => true
				, 'gallery' => true
				, 'footerFrontEnd' => true
			)
			, 'data' => $this->_data
		);
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function aboutUs() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// left column text
		$this->_data['leftCol'] = '
			<h2 class="brown">About The Two Beer Dudes</h2>
			<h3 class="marginTop_8 green">The 2BDs &#39;What &#38; Why&#39; Statement</h3>
			<p class="marginTop_4">twobeerdudes.com is a website devoted to the appreciation of American Craft Beers and brewers. The two &#34;dudes&#34; behind the website live in the Chicago area, and would say that &#34;creating and working on the site was a way to justify all this beer &#34;activity&#34; to our wives.&#34; Seriously though, we see this explosion of consumer interest in craft beers and breweries, mirroring our own growing involvement with beer. And, the website provides us with a way to intertwine our interest in the craft brewing world with our professional backgrounds of websites, internet, and social media. We&#39;ll admit to being a little Midwest centric, but that&#39;s just because we are lucky enough to live in one the of U.S. hotspots for craft breweries.</p>
			<p class="marginTop_8">The website is primarily designed as a place where a user can log and review their various experiences with the thousands of American Craft Brews.  It&#39;s a work in progress and it&#39;s been &#39;live&#39; for less than a year.</p>
			<p class="marginTop_8">We have a number of ideas on how to improve and expand the site, and are totally open to input. Hopefully, you&#39;ll witness the growth of the site over time. There&#39;s really no commercial aspect of the site, it&#39;s all about being huge fans of American Craft Beer. Prost!</p>			
			
			<h3 class="marginTop_8 green">Who da 2B Dudes</h3>
			<p class="marginTop_4 bold">Little Richie&#39;s 5 Defining Q&#38;A&#39;s</p>
			<dl>
				<dt>Where did this interest in beer come from?</dt>
				<dd>First generation German on mother&#39;s side is probably a good start. My recollections as a kid of family and friends gatherings were all about food and drink. And my father traveled a bit for his job, and passed along his habit of diving into the local cuisines and beers. I remember him hauling home cases of beer in the late 60&#39;s from a little old fashion brewery in Chippewa Falls, WI.</dd>
				<dt>Favorite Beer Styles?</dt>
				<dd>Still defining my fav&#39;s. First style I became aware of was Porters found on camping trips in Ontario, Canada during my college days. Then probably Oatmeal Stout from Samuel Smith, before craft brewing really got going in the mid -80&#39;s. Always had a taste for Vienna lagers, and really right through the whole Germany style catalogue. And, today it is hard to limit fav&#39;s to a short list, though Imperial IPA&#39;s really are fun. Plus, I can&#39;t forget some cool fruit beers coming out of the Midwest&#39;s access to local fruit harvest.</dd>
				<dt>Where and How do you buy beer?</dt>
				<dd>I&#39;m lucky to live in a large metro area that has some great beers stores.  Every couple of weeks I stop by one large store near my workplace and cruise the beer aisles. Doing research on the net&#39;s good to develop a target list, but great new brews are always emerging. Cool trend developing is the wide variety of seasonal and limited releases. I like the 22oz bombers for tasting sessions with a few friends. But, buying 3 - 4 six packs every couple weeks and saving a few really starts to build a virtual taste library. Buying excursions into nearby, but different distribution networks results in an expanded beer palate.</dd>
				<dt>Beverage Industry Experience?</dt>
				<dd>None. I did apply for a sales job at Stroh&#39;s in Detroit, right after I graduated from college. I&#39;m still around, Stroh&#39;s isn&#39;t...so that worked out OK for me. My great Aunt and Uncle owned a bar for a number of years, and most big family gatherings were held there (the Club Chevelle in Warren, MI). And, I had a good friend who worked for a big Budweiser Distributor as a route driver/salesman, and I rode with him a couple of times. Currently I&#39;m in a marketing job for a big consumer goods company.</dd>
				<dt>Best Brewery Road Tour?</dt>
				<dd>I have not done this tour yet, but it&#39;s on my short to-do list once I refine the route and have the time. Start at Three Floyd&#39;s - Munster, IN right across the Indiana state line from Chicago...then to The Livery - Benton Harbor, MI in SW Michigan&#39;s Harbor Country...then over to Bell&#39;s Eccentric Caf&#233; - Kalamzoo, MI enough said, and final stop for the day at Founders - Grand Rapids, MI and overnight lodging (a very nice JW Marriott on the river). Next morning the blast up north to Traverse City, MI will take about 3-4 hours, but in town there&#39;s North Peak, Right Brain, AND Jolly Pumpkin...then up around the east side of Traverse Bay to Shorts Brewing - Bellaire, MI and overnight in the area (lot&#39;s of cool choices).  Next day, follow the Lake Michigan coast back to Chicago, with optional stops at New Holland Brewing-Holland, MI and Round Barn Brewery - Baroda, MI.</dd>
			</dl>
			<p class="marginTop_4 bold">Big Scot&#39;s 5 Defining Q&#38;A&#39;s</p>
			<dl>
				<dt>Why do you homebrew?</dt>
				<dd>I started it so that I could learn more about beer and the brewing process.  I wanted to better understand how flavors, aromas, appearance, etc were imparted in the beers I was drinking.  Hops, water, malt, and yeast all play a very important and distinct roll in manipulating the profile of the beer.  There are also many other factors that play a part with a large part being chemistry of the main.  I enjoy the entire process, especially learning about the brewing process.  I have come a long way in a short period of time but there is so much more to learn.</dd>
                <dt>Why American craft beer?</dt>
                <dd>Very little is American these days.  It seems that everything is going overseas.  American craft beer is top notch with the artistry, style, and American ingenuity coming through.  I relish the ability to talk to fellow home brewers or craft beer brewers about the process, taking chances, and the pride they take in what they do and produce.  Every time a friend or family member mentions the fact that they have converted to craft beer and fancy the chance to share their new found hobby with friends, I get chills up and down my spine: the fever is spreading.</dd>
                <dt>Favorite Beer Style(s)?</dt>
                <dd>At one time this was such an easy question but time has complicated it for me, especially as my appreciation for American craft beer has developed.  Once upon a time, the lighter the beer the better, yes, even macro.  I quickly decided this beer style wasn&#39;t for me.  Being a college student, I didn&#39;t have many funds, so I was stuck, with decreased consumption being the only obvious option.  Summer time was bliss as I had cash from working, Sam Adams and Sierra Nevada were the beers of choice: still light, but not macros.  As more and more craft beers came out, I was headed on a collusion course with big, bold stouts and the like.  I have once again changed: I appreciate any American craft beer that has been tediously created.  The time spent to get down a solid pilsner could be more difficult than a big stout, as a stout has the ability to hide flaws.  Any which way you look at, I now look for solid beers in just about any style and appreciate them for what they are; this has allowed me to take my beer enjoyment to another level.</dd>
			</dl>
		';
		
		// right column text
		$this->_data['rightCol'] = '
			<h4><span>It&#39;s Alive</span></h4>
			<ul>
				<li>Finally, something on this page but, as everything here, it is still a work in progress.</li>
			</ul>
			
			<h4><span>2BDs Code of Conduct</span></h4>
			<p class="weight700">The two Beer vow the following:</p>
			<ul>
				<li>To support, advocate, defend, and patronize the Craft Brewers of America</li>
				<li>Continue to learn and refine our knowledge of brews and brewing</li>
				<li>Strive to always remain Beer Geeks at our core and never go to the dark side (Beer Experts or worse...Beer Critics)</li>
				<li>Be vigilant in our openness to new brews, brewers, and styles</li>
				<li>Craft Brewing is about adventure, independence, and the entrepreneurial spirit of America</li>
				<li>Will NOT drink and drive</li>
				<li>If fine wine is like classic music, craft beer is punk rock</li>
				<li>And remember, &#34;in heaven there is no beer that&#39;s why we drink it here&#34;</li>
			</ul>
		'; 
		
		// set the page seo information
		$this->_data['seo'] = getSeo();
		
		$arr_load = array(
			'pages' => array(
				'headerFrontEnd' => true
				, 'formMast' => true
				, 'navigation' => true
				, 'aboutUs' => true
				, 'footerFrontEnd' => true
			)
			, 'data' => $this->_data
		);
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function contactUs() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// load the form validation library
		$this->load->library('form_validation');
		
		// left column text
		$this->_data['leftCol'] = '
			<h2 class="brown">Contact The Two Beer Dudes</h2>
		';
		
		// run the validation and return the result
		if($this->form_validation->run('contactUs') == false) {		
			// left column text
			$this->_data['leftCol'] .= form_contactUs(array());
		} else {
			// the form was filled out completely
			// get the information together and send out the
			// appropriate email 
			
			// array of information to send to the email helper
			$data = array(
				'action' => 'contactUs'
				, 'to' => EMAIL_CONTACT_US
				, 'name' => $_POST['txt_name']
				, 'email' => $_POST['txt_email']
				, 'comments' => $_POST['ttr_comments']
			);
			// send out the email
			sendFormMail($data);
			
			// show something nice for the page
			$this->_data['leftCol'] .= '<p class="marginTop_8">Your information has been sent to Rich and Scot at Two Beer Dudes.  They will try and get back to you in a timely fashion.  In the meantime go rate some beers.  Enjoy!</p>';			
		}
		
		// right column text
		$this->_data['rightCol'] = '
			<h4><span>Drop Two Beer Dudes A Line</span></h4>
			<ul>
				<li>We are always interested to get feedback from our visitors.</li>
				<li>Please offer up some suggestions for improvements to the site.  There is no guarantee we will make the improvement or when we will finish the changes.  We just like to know there are people out there thinking about us.</li>
			</ul>
		'; 
	
		// set the page seo information
		$this->_data['seo'] = getSeo();
		
		$arr_load = array(
			'pages' => array(
				'headerFrontEnd' => true
				, 'formMast' => true
				, 'navigation' => true
				, 'contactUs' => true
				, 'footerFrontEnd' => true
			)
			, 'data' => $this->_data
		);
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function updateInfo() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// check if they are logged in
		if($logged == false) {
			// no, make them go to the login page
			header('Location: ' . base_url() . 'user/login');
			exit;
		} else {	
			// get the type of update to do
			$updateType = $this->uri->segment(3);
			// get the id of the item
			$id = $this->uri->segment(4);
			
			// check if both are not false
			if($updateType == false || $id == false) {
				// the page was loaded incorrectly
				// left column text
				$this->_data['leftCol'] = '
					<h2 class="brown">Update Information</h2>
					<p class="marginTop_8">There was a problem trying to process your request.</p>
				';
			} else {
				// holder for record set
				$itemInfo = '';
				// check that both things exist
				switch($updateType) {
					case 'beer':
						// load the beer model
						$this->load->model('BeerModel', '', true);
						// get the beer by the id
						$itemInfo = $this->BeerModel->getBeerByID($id);
						break;
					case 'establishment':
						// load the beer model
						$this->load->model('EstablishmentModel', '', true);
						// get the beer by the id
						$itemInfo = $this->EstablishmentModel->getEstablishmentByID($id);
						break;
				}
				
				// check that we have a match based on passed criteria
				if(empty($itemInfo)) {
					// couldn't be found
					// left column text
					$this->_data['leftCol'] = '
						<h2 class="brown">Update Information</h2>
						<p class="marginTop_8">There was a problem trying to process your request.</p>
					';
				} else {			
					// load the form validation library
					$this->load->library('form_validation');
					// get the anme of the item being changed
					$name = array_key_exists('beerName', $itemInfo) != false ? $itemInfo['beerName'] : $itemInfo['name'];
					
					// left column text
					$this->_data['leftCol'] = '
						<h2 class="brown">Update Info for: ' . $name . '</h2>
					';
					
					// load the change type model
					$this->load->model('ChangetypeModel', '', true);
					// get the change types for the dropdown
					$ct = $this->ChangetypeModel->selectForDropdown();
					
					// run the validation and return the result
					if($this->form_validation->run('updateInfo') == false) {	
						// an array of information to pass to the form
						$arr_form = array(
							'ct' => $ct
							, 'type' => $updateType
							, 'id' => $id
						);	
						// left column text
						$this->_data['leftCol'] .= form_updateInfo($arr_form);
					} else {
						// the form was filled out completely
						// get the information together and send out the
						// appropriate email 
						
						// array of information to send to the email helper
						$data = array(
							'action' => 'updateInfo'
							, 'to' => EMAIL_CONTACT_US
							, 'itemInfo' => $itemInfo
							, 'userInfo' => $userInfo
							, 'change' => $this->ChangetypeModel->getByID($_POST['slt_change'])
							, 'comments' => $_POST['ttr_comments']
						);
						// send out the email
						sendFormMail($data);
						
						// show something nice for the page
						$this->_data['leftCol'] .= '<p class="marginTop_8">Your information has been sent to Rich and Scot at Two Beer Dudes.  We will review the submitted informaton and go from there.  Thank you for your input.  Enjoy!</p>';			
					}
				}
			}
			
			// right column text
			$this->_data['rightCol'] = '
				<h4><span>Keep the Information Fresh</span></h4>
				<ul>
					<li>We will do our best to make updates in a timely fashion but we have to make sure the update is accurate, which could take some time.</li>
					<li>Don&#39;t submit an update because you don&#39;t agree with something on the site.  Research, double checking to make sure you have a valid reason for the update.</li>
				</ul>
			'; 
		
			// set the page seo information
			$this->_data['seo'] = getSeo();
			
			$arr_load = array(
				'pages' => array(
					'headerFrontEnd' => true
					, 'formMast' => true
					, 'navigation' => true
					, 'updateInfo' => true
					, 'footerFrontEnd' => true
				)
				, 'data' => $this->_data
			);
			// load all parts for the view
			$this->doLoad($arr_load);
		}
	}
	
	public function changeTypeExists($changeTypeID) {
		// load the user model
		//$this->load->model('ChangetypeModel', '', true);
		// check if the type exists
		$boolean = $this->ChangetypeModel->checkExistsByID($changeTypeID);
		// check the boolean
		if($boolean === false) {
			$this->form_validation->set_message('changeTypeExists', 'The %s you have chosen doesn\'t exists.  Please choose another.');
		}
		return $boolean;
	}
    
    public function privacy() {
        // get the login boolean
        $logged = checkLogin();
        // user info for logged in user
        $userInfo = $this->session->userdata('userInfo');        
        // create login mast text
        $this->_data['formMast'] = createHeader($logged, $userInfo);
        
        // right column
        $this->_data['rightCol'] = '
            <h4><span>Download Privacy Policy</span></h4>
            <ul>
                <li><a href="' . base_url() . 'files/privacy-statement.txt">plain text</a></li>
                <li><a href="' . base_url() . 'files/privacy-statement.pdf">pdf</a></li>
            </ul>
        ';
        
        $arr_load = array(
            'pages' => array(
                'headerFrontEnd' => true
                , 'formMast' => true
                , 'navigation' => true
                , 'privacy' => true
                , 'footerFrontEnd' => true
            )
            , 'data' => $this->_data
        );
        // load all parts for the view
        $this->doLoad($arr_load);
    }
    
    public function agreement() {
        // get the login boolean
        $logged = checkLogin();
        // user info for logged in user
        $userInfo = $this->session->userdata('userInfo');        
        // create login mast text
        $this->_data['formMast'] = createHeader($logged, $userInfo);
        
        // right column
        $this->_data['rightCol'] = '
            <h4><span>Download User Agreement</span></h4>
            <ul>
                <li><a href="' . base_url() . 'files/user-agreement.txt">plain text</a></li>
                <li><a href="' . base_url() . 'files/user-agreement.pdf">pdf</a></li>
            </ul>
        ';
        
        $arr_load = array(
            'pages' => array(
                'headerFrontEnd' => true
                , 'formMast' => true
                , 'navigation' => true
                , 'agreement' => true
                , 'footerFrontEnd' => true
            )
            , 'data' => $this->_data
        );
        // load all parts for the view
        $this->doLoad($arr_load);
    }
}
?>