<?php
$images = array(
	//'fakepic.png'
	'login.gif'
);

$path = 'images/';

foreach($images as $image) {
	$url = $path . $image;
	$pathInfo = pathinfo($url);
	
	//echo '<img src="' . $url . '" /><br />';
	
	$type = $pathInfo['extension'] == 'jpg' ? 'jpeg' : $pathInfo['extension'];	
	
	header('Content-type: image/' . $type);
	
	$imagecreate = 'imagecreatefrom' . $type;
	$im = 'image' . $type;	
	
	//echo $imagecreate . '<br />' . $im;exit;
	
	$pic = $imagecreate($url) or die('error 1');
	if($pic) {
		$width = imagesx($pic);
		$height = imagesy($pic);
		
		$newWidth = .4 * $width;
		$newHeight = .4 * $height;
		
		$thumb = imagecreatetruecolor($newWidth, $newHeight) or die('error 2');
		imagecopyresampled($thumb, $pic, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height) or die('error 3');
		
		if($type == 'jpeg') {
			$im($thumb, null, 100);			
		} else {
			$im($thumb);
		}
		
		imagedestroy($pic);
	}
	//exit;
	//echo '<img src="' . $path . '" /><br />';
}
?>