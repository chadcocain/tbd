<?php
class Page extends CI_Controller
{
	private $upload = array(
		'avatars' => array(
			'width' => 100,
			'height' => 100
		),
		'beers' => array(
			'width' => 150,
			'height' => 350
		),
		'establishments' => array(
			'width' => 240,
			'height' => 160
		)
	);	
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('users', 'admin', 'js', 'email', 'form'));
		$this->load->library(array('session', 'layout'));
		// helper to get the quote for the footer - in users_helper.php
		getFooterQuote();
	}
	
	private function _do_load($config)
	{
		$array = array(
			'header' => 'inc/normalHeader.inc.php',			
			'notlogged' => 'page/notlogged.php',
			'footer' => 'inc/footer.inc.php',
			'gallery' => 'page/gallery.php',
			'noproto' => 'inc/headerNoPrototype.inc.php',
			'index2' => 'page/index2.php',
			'search' => 'page/search.php',
			'upload' => 'page/upload.php',
			'aboutUs' => 'page/aboutUs.php',
			'contactUs' => 'page/contactUs.php',
			'updateInfo' => 'page/updateInfo.php',
			'cropImage' => 'page/cropImage.php',
            'privacy' => 'page/privacy.php',
            'agreement' => 'page/agreement.php',
			'headerFrontEnd' => 'inc/header_frontend.inc.php',
			'form_mast' => 'inc/form_mast.php',
			'navigation' => 'inc/navigation.inc.php',
			'footerFrontEnd' => 'inc/footer_frontend.inc.php'
		);
		
		foreach ($config['pages'] as $page => $data)
		{
			if ($data === TRUE)
			{
				$this->load->view($array[$page], $config['data']);
			}
			else
			{
				$this->load->view($array[$page]);
			}
		}
	}
	
	public function index()
	{
		// get the login boolean (user helper)
		$this->_data['logged'] = checkLogin();
        
        // user login information
        $this->_data['user_info'] = $this->session->userdata('userInfo');
		
		$this->load->model('RatingModel', '', TRUE);
		// get the three newest ratings
		$this->_data['beer_reviews'] = $this->RatingModel->getAll(BEER_REVIEW_HOME);
		
		$this->load->model('BreweriesModel', '', TRUE);
		// get the information for the particular brewery hop
		$this->_data['brewery_hop'] = $this->BreweriesModel->getAllBrewreyHops(1);
		
		$this->load->model('SeasonModel', '', TRUE);
        $this->load->helper('date');
		// get the information for the current season
		$this->_data['season'] = $this->SeasonModel->getSeasonalForFrontPage();	
		
		// set the page information
		$this->_data['seo'] = getSEO();
        
        $this->layout->render('page/index2.php', $this->_data, 'two_column.php');
	}
	
	public function search()
    {
		// get the login boolean
        $this->_data['logged'] = checkLogin();
		
		// load the search model
		$this->load->model('SearchModel', '', TRUE);
		// load the page library
		$this->load->library('pages');
		
		// start the left column output
		$str = '';
		
		// holder for the final search string
		$final_search_string = array();
		// get filter words
		//$badWords = file('./list/noisewords.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		//echo '<pre>'; print_r($badWords); exit;
		// original search string
		$original_search_string = trim($_POST['txt_search']);
		// get the passed search string
		$search_string = explode(' ', $original_search_string);
		// iterate through the array, if it is an array
		if(!empty($search_string) && is_array($search_string)) {
			// iterate through the array
			foreach($search_string as $string) {
				// get rid of any words less than three letters
				//if(strlen($string) > 2  && !in_array($string, $badWords)) {
					// add the string to the holder array
					$final_search_string[] = $string;
				//}
			}
		}
		
		// check that the fixed array has words
		if (!empty($final_search_string))
        {
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
            $this->_data['type'] = $type;		
			// seach by exact original string
			$search['original'] = $this->_data['original_search_string'] = $original_search_string;
			// search for each word with wild cards
			$search['wildCards'] = $this->_data['final_search_string'] = $final_search_string;
			
			// do the search
			$this->_data['searchRS'] = $this->SearchModel->doSearch($search, $type);
            //$searchRS = $this->SearchModel->doSearch($oSearchString, $type);
        }
		
		// set the page information
		$this->_data['seo'] = getSEO();
        
        $this->layout->render('page/search.php', $this->_data, 'two_column.php');
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
	
	/**
    * About us section of the main site.  This is static information that can be changed wihtin the aboutUs view
    * 
    * @access public
    * @return void
    */
    public function aboutUs()
    {
        // get the login boolean
        $this->_data['logged'] = checkLogin();
        // user info for logged in user
        $this->_data['user_info'] = $this->session->userdata('userInfo');        

        // set the page seo information
        $this->_data['seo'] = getSeo();
        
        $this->layout->render('page/aboutUs.php', $this->_data, 'two_column.php');
    }
	
    /**
    * Contact us form.  A user can send a comment to the team.  Captcha on the page is deter bots.
    * 
    * @access public
    * @return void
    */
	public function contactUs()
    {
		// get the login boolean
        $this->_data['logged'] = checkLogin();
        // user info for logged in user
        $this->_data['user_info'] = $this->session->userdata('userInfo');
		
		// load the form validation library
		$this->load->library('form_validation');
		
		// run the validation and return the result
		if ($this->form_validation->run('contactUs') == FALSE)
        {		
			// display the form and any errors, if necessary
            $this->_data['show_form'] = TRUE;
		}
        else
        {
			// the form was filled out completely
			// get the information together and send out the
			// appropriate email 
			
			// array of information to send to the email helper
			$data = array(
				'action' => 'contactUs',
				'to' => EMAIL_CONTACT_US,
				'name' => $_POST['txt_name'],
				'email' => $_POST['txt_email'],
				'comments' => $_POST['ttr_comments']
			);
			// send out the email
			sendFormMail($data);
			
            // show the result of the form send
            $this->_data['show_form'] = FALSE;
		} 
	
		// set the page seo information
        $this->_data['seo'] = getSeo();
        
        $this->layout->render('page/contactUs.php', $this->_data, 'two_column.php');
    }
	
	public function updateInfo()
    {
		// get the login boolean
        $this->_data['logged'] = checkLogin();
        // user info for logged in user
        $this->_data['user_info'] = $this->session->userdata('userInfo');
		
		// check if they are logged in
		if (!$this->_data['logged'])
        {
			// no, make them go to the login page
			header('Location: ' . base_url() . 'user/login');
			exit;
		}
        else
        {	
			// get the type of update to do
			$updateType = $this->uri->segment(3);
			// get the id of the item
			$id = $this->uri->segment(4);
			
			// check if both are not false
			if(!$updateType || !$id)
            {
				// the page was loaded incorrectly
                $this->_data['problem'] = TRUE;
            }
            else
            {
				// holder for record set
				$itemInfo = '';
				// check that both things exist
				switch($updateType)
                {
					case 'beer':
						// load the beer model
						$this->load->model('BeerModel', '', TRUE);
						// get the beer by the id
						$itemInfo = $this->BeerModel->getBeerByID($id);
						break;
					case 'establishment':
						// load the beer model
						$this->load->model('EstablishmentModel', '', TRUE);
						// get the beer by the id
						$itemInfo = $this->EstablishmentModel->getEstablishmentByID($id);
						break;
				}
				
				// check that we have a match based on passed criteria
				if (empty($itemInfo))
                {
					// couldn't be found
					$this->_data['problem'] = TRUE;
				}
                else
                {			
					// load the form validation library
					$this->load->library('form_validation');
					// get the anme of the item being changed
					$this->_data['name'] = array_key_exists('beerName', $itemInfo) ? $itemInfo['beerName'] : $itemInfo['name'];
										
					// load the change type model
					$this->load->model('ChangetypeModel', '', TRUE);
					// get the change types for the dropdown
					$ct = $this->ChangetypeModel->selectForDropdown();
					
					// run the validation and return the result
					if (!$this->form_validation->run('updateInfo'))
                    {	
						// an array of information to pass to the form
						$arr_form = array(
							'ct' => $ct
							, 'type' => $updateType
							, 'id' => $id
						);	
						// left column text
						$this->_data['display'] = form_updateInfo($arr_form);
					}
                    else
                    {
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
						$this->_data['display'] = 1;			
					}
				}
			}
            
            $this->layout->render('page/updateInfo.php', $this->_data, 'two_column.php');
		}
	}
	
	public function changeTypeExists($changeTypeID)
    {
		// load the user model
		//$this->load->model('ChangetypeModel', '', true);
		// check if the type exists
		$boolean = $this->ChangetypeModel->checkExistsByID($changeTypeID);
		// check the boolean
		if ($boolean === FALSE)
        {
			$this->form_validation->set_message('changeTypeExists', 'The %s you have chosen doesn\'t exists.  Please choose another.');
		}
		return $boolean;
	}
    
    /**
    * Privacy policy for the website.  Includes downloads in plain text and pdf style.
    * 
    * @access public
    * @return void
    */
    public function privacy()
    {
        // get the login boolean
        $this->_data['logged'] = checkLogin();
        // user info for logged in user
        $this->_data['user_info'] = $this->session->userdata('userInfo');
        
        $this->layout->render('page/privacy.php', $this->_data, 'two_column.php');
    }
    
    /**
    * End user agreement.  Includes downloads in plain text and pdf style.
    * 
    * @access public
    * @return void
    */
    public function agreement()
    {
        // get the login boolean
        $this->_data['logged'] = checkLogin();
        // user info for logged in user
        $this->_data['user_info'] = $this->session->userdata('userInfo');
        
        $this->layout->render('page/agreement.php', $this->_data, 'two_column.php');
    }
}
?>
