<?php
function createDropDown($config) {
	$class = key_exists('class', $config) ? ' class="' . $config['class'] . '"' : '';
	$onchange = key_exists('onchange', $config) ? ' ' . $config['onchange'] : '';
	$str = '
		<select id="' . $config['id'] . '" name="' . $config['name'] . '"' . $class . $onchange . '>
	';
	
	foreach($config['data'] as $data) {
		$select = $config['selected'] == $data['id'] ? ' selected="selected"' : '';
		$name = (key_exists('upperCase', $config) && $config['upperCase'] === true) ? ucwords($data['name']) : $data['name'];
		$str .= '
			<option value="' . $data['id'] . '"' . $select . '>' . $name . '</option>
		';
	}
	
	$str .= '
		</select>
	';
	return $str;
}

function createDropDownNoKeys($config) {
	$class = key_exists('class', $config) ? ' class="' . $config['class'] . '"' : '';
	$str = '
		<select id="' . $config['id'] . '" name="' . $config['name'] . '"' . $class . '>
	';
	
	foreach($config['data'] as $data) {
		$select = $config['selected'] == $data ? ' selected="selected"' : '';
		$str .= '
			<option value="' . $data . '"' . $select . '>' . $data . '</option>
		';
	}
	
	$str .= '
		</select>
	';
	return $str;
}

function createDropDownStyles($config) {
	$class = key_exists('class', $config) ? ' class="' . $config['class'] . '"' : '';
	$onchange = key_exists('onchange', $config) ? ' ' . $config['onchange'] : '';
	$str = '
		<select id="' . $config['id'] . '" name="' . $config['name'] . '"' . $class . $onchange . '>
	';
	///echo '<pre>'; print_r($config['data']); echo '</pre>'; exit;
	//$arr_major = array();
	$arr_styles = array();
	//$i = 0;
	$j = 0;
	foreach($config['data'] as $data) {
		if(!in_array($data['origin'] . '_' . $data['styleType'], $arr_styles)) {
			$str .= $j > 0 ? '</optgroup>' : '';
		}
		/*if(!in_array($data['styleType'], $arr_major)) {
			$str .= $i > 0 ? '</optgroup>' : '';
			$arr_major[] = $data['styleType'];
			$str .= '
			<optgroup label="' . $data['styleType'] . '">
			';
		}*/
		if(!in_array($data['origin'] . '_' . $data['styleType'], $arr_styles)) {
			//$str .= $j > 0 ? '</optgroup>' : '';
			$arr_styles[] = $data['origin'] . '_' . $data['styleType'];
			$str .= '
			<optgroup label="' . $data['origin'] . ' ' . $data['styleType'] . '">
			';
		}
		$select = $config['selected'] == $data['id'] ? ' selected="selected"' : '';
		$name = (key_exists('upperCase', $config) && $config['upperCase'] === true) ? ucwords($data['name']) : $data['name'];
		$str .= '
				<option value="' . $data['id'] . '"' . $select . '>' . $name . '</option>
		';
		//$i++;
		$j++;
	}
	
	$str .= '
		</select>
	';
	return $str;
}

function checkForImage($config, $edit = true, $wrap = true) {
	$img = '';
	$nub = '';
	
	// see if width is set as a config value		
	$width = key_exists('width', $config) ? ' width="' . $config['width'] . '"' : '';
	// see if height is set as a config value
	$height = key_exists('height', $config) ? ' height="' . $config['height'] . '"' : '';
	if(!empty($config['picture'])) {		
		// see if alternate text is set as a config value
		$alt = key_exists('alt', $config) ? ' title="' . $config['alt'] . '" alt="' . $config['alt'] . '"' : '';
		// see if standard wrap text is to be used
		if($wrap === true) {		
			$img = '<div class="admin_beerPic"><img src="' . base_url() . 'images/beers/' . $config['picture'] . '"' . $alt . $width . $height . ' /></div>';
		} else {
			$img = '<img src="' . base_url() . 'images/beers/' . $config['picture'] . '"' . $alt . $width . $height . ' />';
		}
		$nub = '<li class="delete"><a href="#" onclick="if(confirm(\'Are you sure you want to delete this image?\')) {new Ajax.Request(\'' . base_url() . 'ajax/deleteImage/beer/' . $config['id'] . '\', {asynchronous: true, evalScripts: true, method: \'get\', onComplete: function(response) {$(\'item_list_container_' . $config['id'] . '\').update(response.responseText);}});}; return false;"><img src="' . base_url() . 'images/nubbin_trash.gif" title="delete image" alt="delete image" /></a></li>';
	} else {
		if($wrap === true) {
			$img = '<div class="admin_beerPic"><img src="' . base_url() . 'images/beers/bottle.gif"' . $width . $height . ' /></div>';
		} else {
			$img = '<img src="' . base_url() . 'images/beers/bottle.gif"' . $width . $height . ' />';
		}
	}
	if($edit === true) {
		$nub = '
			<div id="nubbin_' . $config['id'] . '" class="nubbin">
				<ul>
					' . $nub . '
					<li class="edit"><a href="' . base_url() . 'admin/uploadImage/' . $config['id'] . '"><img src="' . base_url() . 'images/nubbin_editPhoto.jpg" title="edit image" alt="edit image" /></a></li>
				</ul>
			</div>
		';
	} else {
		$nub = '';
	}
	return $nub . $img;
}

function imageExists($config) {
	$boolean = false;
	if(file_exists($config['path'] . $config['fileName'])) {
		$boolean = true;
	}
	return $boolean;
}
?>