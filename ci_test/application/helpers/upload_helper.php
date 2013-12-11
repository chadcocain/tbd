<?php	
function tryUpload($array) {
	//$config['upload_path'] = '/www/www/images/beers/';
	$config['upload_path'] = $array['path'];
	$config['allowed_types'] = 'gif|jpg|png';
	$config['max_size']	= '200';
	$config['max_width']  = '600';
	$config['max_height']  = '1050';
	$config['overwrite'] = true;
	
	$this->load->library('upload', $config);

	if(!$this->upload->do_upload($array['inputName'])) {
		return array('error' => $this->upload->display_errors());		
		//$this->load->view('upload_form', $error);
	} else {
		return array('upload_data' => $this->upload->data());		
		//$this->load->view('upload_success', $data);
	}
}

function nameEstablishmentImage($config) {
	$ci =& get_instance();
	$parts_brewery = explode(' ', $config['brewery']);
	$name = '';
	foreach($parts_brewery as $part) {
		$part = preg_replace('/[^a-zA-Z0-9]/', '', $part);
		if(!empty($part)) {
			$name .= $part . '_';
		}
	}
	$name = strtolower(substr($name, 0, (strlen($name) - 1)));
	return $name;
}

function nameBeerImage($config) {
	$ci =& get_instance();
	//$brewery = $ci->BreweriesModel->getBreweryByID($config['id'])
	$parts_brewery = explode(' ', str_replace(array('/', '.'), '', $config['brewery']));
	$parts_beer = explode(' ', str_replace('/', '', $config['beer']));
	$name = '';
	foreach($parts_brewery as $part) {
		$name .= $part . '_';
	}
	foreach($parts_beer as $part) {
		$name .= $part . '_';
	}
	$name = strtolower(substr($name, 0, (strlen($name) - 1)));
	return $name;
}

function changeImageName($config) {
	// temporary holder for success
	$success = false;
	// check if the file exists
	if(file_exists($config['oldPath'] . $config['oldName'])) {
		// check if a new path is set for the file
		$newPath = key_exists('newPath', $config) ? $config['newPath'] : $config['oldPath'];
		// try to rename the file
		if(rename($config['oldPath'] . $config['oldName'], $newPath . $config['newName'])) {
			// successful rename
			$success = true;
		}
	} 
	return $success;
}

/**
 * Take an array of information about an image and save it
 * to an active folder from a temporary folder
 *
 * @param array $arr
 */
function resample_image($arr) {
	// check the image type
	if($arr['image_type'] == 'jpg') {
		$arr['image_type'] = 'jpeg';
	}
	// create the image resource
	$imagecreatefrom = 'imagecreatefrom' . $arr['image_type'];
	$orig_image = $imagecreatefrom($arr['src_path'] . $arr['src_image']);
	// create a new true color image
	$new_image = imagecreatetruecolor($arr['target_w'], $arr['target_h']);
	// copy and resize part of an image with resampling
	imagecopyresampled($new_image, $orig_image, 0, 0, $arr['coord_x'], $arr['coord_y'], $arr['target_w'], $arr['target_h'], $arr['coord_w'], $arr['coord_h']);
	// send the output to a file
	$image = 'image' . $arr['image_type'];
	$image($new_image, $arr['save_path'] . $arr['new_image'], $arr['quality']);
	// free up some memory
	imagedestroy($new_image);
	imagedestroy($orig_image);
	// remove the original image
	unlink($arr['src_path'] . $arr['src_image']);       
}

function getMimeType($config) {
	$finfo = finfo_open(FILEINFO_MIME);
	$mimeType = finfo_file($finfo, $config['path'] . $config['fileName']);
	finfo_close($finfo);
	return $mimeType;
}

function removeImage($config) {
	// remove the file
	unlink($config['path'] . $config['fileName']);
}

function checkImageExists($config) {
	// get the file extenstions the files can be
	$fileExt = $config['fileExt'];
	// set the return value
	$boolean = false;
	// holder for the image name
	$fileName = '';
	// iterate through the file extensions
	foreach($fileExt as $ext) {
		// if boolean is true, don't change it
		$boolean = $boolean === true ? true : file_exists($config['path'] . $config['file'] . '.' . $ext);
		// create the file name and hold it
		if($boolean === true && empty($fileName)) {
			$fileName = $config['file'] . '.' . $ext;
		}
	}
	// return the value
	return array('bool' => $boolean, 'fileName' => $fileName);
}

function resizeImageOnFly($config) {
	// get the url to the image
	$url = $config['path'] . $config['image'];
	// get the pathinfo about the image
	$pathInfo = pathinfo($url);
	// determine the type of image	
	$type = $pathInfo['extension'] == 'jpg' ? 'jpeg' : $pathInfo['extension'];	
	// set the header information
	header('Content-type: image/' . $type);
	
	// create the image string for the type of resource to create
	$imagecreate = 'imagecreatefrom' . $type;
	// create the method name of the type of image to output
	$im = 'image' . $type;	
	
	// create the image resource
	$pic = $imagecreate(urldecode($url)) or die('error 1');
	// check if the resource was created
	if($pic) {
		// get the current width of the image
		$width = imagesx($pic);
		// get the current height of the image
		$height = imagesy($pic);
		// set the new width of the image based on the multiplier
		$newWidth = $config['widthMultiplier'] * $width;
		// set the new height of the image based on the multiplier
		$newHeight = $config['heightMultiplier'] * $height;
		// create the new image resource
		$thumb = imagecreatetruecolor($newWidth, $newHeight) or die('error 2');
		// create the resized image resource
		imagecopyresampled($thumb, $pic, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height) or die('error 3');
		// check the type as they are handled differently
		if($type == 'jpeg') {
			$im($thumb, null, 100);			
		} else {
			$im($thumb);
		}
		// cleanup, destroy the resource
		imagedestroy($pic);
	}
}
?>