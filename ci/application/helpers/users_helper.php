<?php	
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

function createLoginForm($error = '') {
	$email = set_value('email');
	$password = set_value('password');
	
	// get the code igniter instance
	$ci =& get_instance();
	// check if there was a page to return to
	$uri = manipURIPassback();
	
	$form = '
		<form action="' . base_url() . 'user/login/' . $uri . '" method="post">
	';
	if(!empty($error)) {
		$form .= $error;
	}
	$form .= '
			<div class="formBlock">
	';
	if(form_error('email')) {
		$form .= '<div class="formError">' . form_error('email') . '</div>';
	}
	$form .= '
				<label for="txt_email"><span class="required">*</span> Email Address:</label>
				<input type="text" id="txt_email" name="email" value="' . $email . '" />
			</div>
			
			<div class="formBlock">
	';
	if(form_error('password')) {
		$form .= '<div class="formError">' . form_error('password') . '</div>';
	}
	$form .= '
				<label for="pwd_password"><span class="required">*</span> Password:</label>
				<input type="password" id="pwd_password" name="password" value="' . $password . '" />
				<div class="explanation"><a href="' . base_url() . 'user/reset">Forgot your password?</a></div>
			</div>
				
			<input type="submit" id="btn_submit" name="submit" value="Login" />
		</form>
	';
	
	return $form;
}

function createResetPassword($error = '') {
	$email = set_value('email');
	
	// get the code igniter instance
	$ci =& get_instance();
	
	// load the captcha plugin
	$ci->load->helper('captcha_pi');
	
	$form = '
		<form action="' . base_url() . 'user/reset/" method="post">
	';
	if(!empty($error)) {
		$form .= $error;
	}
	$form .= '
			<div class="formBlock">
	';
	if(form_error('email')) {
		$form .= '<div class="formError">' . form_error('email') . '</div>';
	}
	$form .= '
				<label for="txt_email"><span class="required">*</span> Email Address:</label>
				<input type="text" id="txt_email" name="email" value="' . $email . '" />
			</div>
			
			<div class="formBlock">
	';
	// captcha
	$vals = array(
		'img_path' => './captcha/'
		, 'img_url' => base_url() . 'captcha/'
		, 'font_path'	 => './font/verdana.ttf',
	);	
	$cap = create_captcha($vals);

	$data = array(
		'captcha_id' => '',
		'captcha_time' => $cap['time'],
		'ip_address' => $ci->input->ip_address(),
		'word' => $cap['word']
	);
	// load the captcha model
	$ci->load->model('CaptchaModel', '', true);
	// run the captcha query
	$ci->CaptchaModel->insertCaptcha($data);	
		
	if(form_error('captcha')) {
		$form .= '<div class="formError">' . form_error('captcha') . '</div>';
	}
	$form .= '
				<label for="txt_captcha"><span class="required">*</span> Security Code:</label>
				' . $cap['image'] . '
				<input type="text" id="txt_captcha" name="captcha" value="" />
				<div class="explanation">Helps us to keep out bots and other unwanted bits and bytes.</div>
			</div>
				
			<input type="submit" id="btn_submit" name="submit" value="Reset Password" />
		</form>
	';
	
	return $form;
}

function form_updatePassword($error = '') {
	$password1 = set_value('password1');
	$password2 = set_value('password2');
	
	// get the code igniter instance
	$ci =& get_instance();
	
	$sessionInfo = $ci->session->userdata('userInfo');
	
	$form = '
		<form action="' . base_url() . 'user/updatePass/' . $sessionInfo['id'] . '" method="post">
	';
	if(!empty($error)) {
		$form .= $error;
	}
	$form .= '
			<div class="formBlock">
	';
	if(form_error('password1')) {
		$form .= '<div class="formError">' . form_error('password1') . '</div>';
	}
	$form .= '
				<label for="pwd_password1"><span class="required">*</span> Password:</label>
				<input type="password" id="pwd_password1" name="password1" value="' . $password1 . '" />
				<div class="explanation">Your password needs to be at least six characters in length but not exceed 12.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('password2')) {
		$form .= '<div class="formError">' . form_error('password2') . '</div>';
	}
	$form .= '
				<label for="pwd_password2"><span class="required">*</span> Verify Password:</label>
				<input type="password" id="pwd_password2" name="password2" value="' . $password2 . '" />
				<div class="explanation">Verification of your password from above.</div>
			</div>
			
			<input type="submit" id="btn_submit" name="submit" value="Update Password" />
		</form>
	';
	
	return $form;
}

function manipURIPassback() {
	// get the code igniter instance
	$ci =& get_instance();
	
	// total number of uri segments
	$totalSegs = $ci->uri->total_segments();
	// holder for uri
	$uri = '';
	// check the number
	if($totalSegs > 3) {
		$arr = $ci->uri->segment_array();
		for($i = 3; $i <= count($arr); $i++) {
			$uri .= empty($uri) ? $arr[$i] : '/' . $arr[$i];
		}
	} else if($ci->uri->segment(3) !== false){
		$uri = $ci->uri->segment(3);
	}
	// return the uri
	return $uri;
}

function form_updateProfile($config) {
	$firstName = key_exists('firstname', $config) ? $config['firstname'] : set_value('txt_firstname');
	$lastName = key_exists('lastname', $config) ? $config['lastname'] : set_value('txt_lastname');//set_value('txt_firstname');
	$city = key_exists('city', $config) ? $config['city'] : set_value('txt_city');//set_value('city');
	$state = key_exists('state', $config) ? $config['state'] : set_value('slt_state');
	$notes = key_exists('notes', $config) ? $config['notes'] : set_value('ttr_notes');
	
	//$aroma = key_exists('rating', $config) ? $config['rating']['aroma'] : set_value('aroma');
	
	// get the code igniter instance
	$ci =& get_instance();
	
	$config = array(
		'data' => $ci->StateModel->getAllForDropDown()
		, 'id' => 'slt_state'
		, 'name' => 'slt_state'
		, 'selected' => $state
	);
	$stateDropDown = createDropDown($config);
	
	$form = '
		<form action="' . base_url() . 'user/updateProfile" method="post">
			<div class="formBlock">
	';
	if(form_error('txt_firstname')) {
		$form .= '<div class="formError">' . form_error('txt_firstname') . '</div>';
	}
	$form .= '
				<label for="txt_firstname">Frist Name:</label>
				<input type="text" id="txt_firstname" name="txt_firstname" value="' . $firstName . '" />
				<div class="explanation">Simple: your first name.</div>
			</div>

			<div class="formBlock">
	';	
	if(form_error('txt_lastname')) {
		$form .= '<div class="formError">' . form_error('txt_lastname') . '</div>';
	}
	$form .= '
				<label for="txt_lastname">Last Name:</label>
				<input type="text" id="txt_lastname" name="txt_lastname" value="' . $lastName . '" />
				<div class="explanation">Simple: your last name.</div>
			</div>

			<div class="formBlock">
	';	
	if(form_error('txt_city')) {
		$form .= '<div class="formError">' . form_error('txt_city') . '</div>';
	}
	$form .= '
				<label for="txt_city">City:</label>
				<input type="text" id="txt_city" name="txt_city" value="' . $city . '" />
			</div>

			<div class="formBlock">
	';
	if(form_error('slt_state')) {
		$form .= '<div class="formError">' . form_error('slt_state') . '</div>';
	}
	$form .= '
				<label for="slt_state"><span class="required">*</span> State:</label>
				' . $stateDropDown . '
			</div>
			<div class="formBlock">
	';	
	if(form_error('ttr_notes')) {
		$form .= '<div class="formError">' . form_error('ttr_notes') . '</div>';
	}
	$form .= '
				<label for="ttr_notes">Notes <span id="thoughtCount"></span>:</label>
				<textarea id="ttr_notes" name="ttr_notes">' . $notes . '</textarea>
				<div class="explanation">Ideas: favorite <a href="' . base_url() . 'beer/style">style(s)</a>, can\'t miss <a href="' . base_url() . 'brewery/hop">brewery hops</a>, generic beer info, etc. (be creative)</div>
			</div>
	';
	$form .= '
			<input type="submit" id="btn_submit" name="submit" value="Update Profile" class="marginTop_8" />
		</form>
	';
	// send back the form
	return $form;
}

function createAccountForm() {
	$username = set_value('username');
	$password1 = set_value('password1');
	$password2 = set_value('password2');
	$email = set_value('email');
	$city = set_value('city');
	$state = set_value('state');
	$captcha = set_value('captcha');
	
	// get the code igniter instance
	$ci =& get_instance();
	
	// load the captcha plugin
	$ci->load->helper('captcha_pi');
	
	$config = array(
		'data' => $ci->StateModel->getAllForDropDown()
		, 'id' => 'slt_state'
		, 'name' => 'state'
		, 'selected' => $state
	);
	$stateDropDown = createDropDown($config);
	
	$form = '
		<form action="' . base_url() . 'user/createAccount" method="post">
			<div class="formBlock">
	';
	if(form_error('username')) {
		$form .= '<div class="formError">' . form_error('username') . '</div>';
	}
	$form .= '
				<label for="txt_username"><span class="required">*</span> User Name:</label>
				<input type="text" id="txt_username" name="username" value="' . $username . '" />
				<div class="explanation">This will be your name of association on the site.  This is permanent.</div>
			</div>

			<div class="formBlock">
	';
	if(form_error('email')) {
		$form .= '<div class="formError">' . form_error('email') . '</div>';
	}
	$form .= '
				<label for="txt_email"><span class="required">*</span> Email Address:</label>
				<input type="text" id="txt_email" name="email" value="' . $email . '" />
				<div class="explanation">Your email address will be used to verify your account, login and use your account.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('password1')) {
		$form .= '<div class="formError">' . form_error('password1') . '</div>';
	}
	$form .= '
				<label for="pwd_password1"><span class="required">*</span> Password:</label>
				<input type="password" id="pwd_password1" name="password1" value="' . $password1 . '" />
				<div class="explanation">Your password needs to be at least six characters in length but not exceed 12.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('password2')) {
		$form .= '<div class="formError">' . form_error('password2') . '</div>';
	}
	$form .= '
				<label for="pwd_password2"><span class="required">*</span> Verify Password:</label>
				<input type="password" id="pwd_password2" name="password2" value="' . $password2 . '" />
				<div class="explanation">Verification of your password from above.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('state')) {
		$form .= '<div class="formError">' . form_error('state') . '</div>';
	}
	$form .= '
				<label for="slt_state"><span class="required">*</span> State:</label>
				' . $stateDropDown . '
			</div>
			
			<div class="formBlock">
	';
	if(form_error('city')) {
		$form .= '<div class="formError">' . form_error('city') . '</div>';
	}
	$form .= '
				<label for="txt_city"><span class="required">*</span> City:</label>
				<input type="text" id="txt_city" name="city" value="' . $city . '" />
			</div>
			<div class="formBlock">
	';
	
	// captcha
	$vals = array(
		'img_path' => './captcha/'
		, 'img_url' => base_url() . 'captcha/'
		, 'font_path'	 => './font/verdana.ttf',
	);	
	$cap = create_captcha($vals);

	$data = array(
		'captcha_id' => '',
		'captcha_time' => $cap['time'],
		'ip_address' => $ci->input->ip_address(),
		'word' => $cap['word']
	);
	// load the captcha model
	$ci->load->model('CaptchaModel', '', true);
	// run the captcha query
	$ci->CaptchaModel->insertCaptcha($data);	
		
	if(form_error('captcha')) {
		$form .= '<div class="formError">' . form_error('captcha') . '</div>';
	}
	$form .= '
				<label for="txt_captcha"><span class="required">*</span> Security Code:</label>
				' . $cap['image'] . '
				<input type="text" id="txt_captcha" name="captcha" value="" />
				<div class="explanation">Helps us to keep out bots and other unwanted bits and bytes.</div>
			</div>
			
			<input type="submit" id="btn_submit" name="submit" value="Create Account" class="marginTop_8" />
		</form>
	';
	
	return $form;
}

function sendActivationMail($config) {
	$to = $config['email'];
	$subject = 'Two Beer Dudes membership activation';
	$msg = '<html><body><p>Hey ' . $config['membername'] . ',</p>';
	$msg .= '<p>Your request for membership is appreciated and almost complete.  We hope that you enjoy the site.  Please feel free to offer your suggestions as we want to continue to improve on the site</p>';
	$msg .= '<p>Please follow the link below to activate your account:</p>';
	$msg .= '<p><a href="' . base_url() . 'user/activateAccount/' . $config['activationCode'] . '">' . base_url() . 'user/activateAccount/' . $config['activationCode'] . '</a></p>';
	$msg .= '<p>Regards,<br />Two Beer Dudes</p>';
		
	$headers = 'MIME-Version: 1.0' . "\r\n" .
		'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
		'From: webmaster@twobeerdudes.com' . "\r\n" .
		'Reply-To: webmaster@twobeerdudes.com' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
	mail($to, $subject, $msg, $headers);
}

function updateActiveTime($id) {
	// get the code igniter instance
	$ci =& get_instance();
	$ci->UserModel->updateLastLogin($id);
}

function checkLogin() {
	// get the code igniter instance
	$ci =& get_instance();
	// check if the session is set
	$userInfo = $ci->session->userdata('userInfo');
	// boolean holder if user is logged in
	$boolean = false;
	// check if this value existed
	if($userInfo !== false) {
		// they are logged in
		$boolean = true;
	}
	return $boolean;
}

function createHeader($logged, $userInfo) {
	// holder string
	$str = '
	<div id="loginMast" style="background-image: url(' . base_url() . 'images/header_' . rand(1, HEADER_IMAGE_COUNT) . '.jpg);">
	';
	// check if they are logged in
	if($logged === true) {
		// they are logged in
		// get the code igniter instance
		$ci =& get_instance();
		// load the user model
		$ci->load->model('UserModel', '', true);
		// get the number of unread private messages
		$num = $ci->UserModel->numNewMessages($userInfo['id']);
		//echo '<pre>'; print_r($num); echo '</pre>'; exit;
		// holder for private message info
		$pms = '';		
		// check to see if they user has a new message
		if($num > 0) {
			$pms = '<span><a href="' . base_url() . 'user/pms"><img src="' . base_url() . 'images/chat_bubble.gif" alt="new private message(s)" /></a></span>';
		}
		$str .= '
		<div id="loginInfo">
			<p>' . $pms . 'Welcome, <a href="' . base_url() . 'user/profile/' . $userInfo['id'] . '">' . $userInfo['username'] . '</a> <span style="font-size: 74%;">(if you are finished, <a href="' . base_url() . 'user/logout">logout</a>)</span></p>
		</div>
		';
	} else {
		// they are not logged in
		$str .= '
		<div id="loginInfo">
			<a href="' . base_url() . 'user/createAccount" id="joinButton">Join Now!</a>
			<a href="' . base_url() . 'user/login" id="loginButton">Login</a>
			<br class="left" />
		</div>
		';
	}
	// finish the output
	$str .= 
		form_search() . '
		<br class="left" />
		<div id="tagLineWrapper">
			<div id="tagLineWrapperOpacity"></div>
			<p id="tagLine">Two regular dudes who happen to be huge fans of American craft beer.</p>
		</div>
	</div>
	';
	// return the output
	return $str;
}

function determineTimeSinceLastActive($secs) {
	$timeConv = array(
		'minutes' => 60
		, 'hours' => 3600
		, 'days' => 86400
		, 'months' => 2592000
		, 'years' => 31536000
	);
	
	$time = '';
	// check the years
	$years = floor(($secs / $timeConv['years']));
	if($years > 0) {
		//$time = $years > 1 ? 'more than ' . $years . ' years' : 'more than ' . $years . ' year';
            $time = $years > 1 ? $years . ' years' : $years . ' year';
	}
	
	// check the months
	if(empty($time)) {		
		$months = floor(($secs / $timeConv['months']));
		if($months > 0) {
			//$time = $months > 1 ? 'more than ' . $months . ' months' : 'more than ' . $months . ' month';
                        $time = $months > 1 ? $months . ' months' : $months . ' month';
		}
	}
	
	// check the days
	if(empty($time)) {
		$days = floor(($secs / $timeConv['days']));
		if($days > 0) {
			//$time = $days > 1 ? 'more than ' . $days . ' days' : 'more than ' . $days . ' day';
                    $time = $days > 1 ? $days . ' days' : $days . ' day';
		}
	}
	
	// check the hours
	if(empty($time)) {
		$hours = floor(($secs / $timeConv['hours']));
		if($hours > 0) {
			//$time = $hours > 1 ? 'more than ' . $hours . ' hours' : 'more than ' . $hours . ' hour';
                    $time = $hours > 1 ? $hours . ' hours' : $hours . ' hour';
		}
	}
	
	// check the minutes
	if(empty($time)) {
		$minutes = floor(($secs / $timeConv['minutes']));
		if($minutes > 0) {
			//$time = $minutes > 1 ? 'more than ' . $minutes . ' minutes' : 'more than ' . $minutes . ' minute';
                    $time = $minutes > 1 ? $minutes . ' minutes' : $minutes . ' minute';
		}
	}
	
	// check for seconds
	if(empty($time)) {
		//$time = $secs > 1 ? 'more than ' . $secs . ' seconds' : 'more than ' . $secs . ' second';
            $time = $secs > 1 ? $secs . ' seconds' : $secs . ' second';
	}
	
	return $time . ' ago';
}

function adjustMailSubject($config) {
	// array of potential values that can be placed
	// at the front of the subject
	$possible = array(
		're:'
		, 'fwd:'
	);
	// subject
	$subject = $config['subject'];
	// the value to add in front
	$add = $config['add'];
	/*echo $subject . '<br />';
	// loop through the possible values
	foreach($possible as $value) {echo $value . '<br />';
		if(stristr($subject, $value) !== false) {
			$subject = trim(stristr($subject, $value));
			echo $subject . '<br />';
		}
	}*/
	$subject = str_ireplace($possible, '', $subject);

	// return back the formatted text
	return $add . trim($subject);
	/*$arr = array(
		'subject' => $array['subject']
		, 'add' => 'RE: '
	);*/
}

function checkForAnImage($config, $edit = true, $wrap = true, $type = 'beer') {
	$img = '';
	$nub = '';
	//echo '<pre>'; print_r($config); exit;
	$path = $type == 'beer' ? 'beers' : 'establishments';
	
	// see if width is set as a config value		
	$width = key_exists('width', $config) ? ' width="' . $config['width'] . '"' : '';
	// see if height is set as a config value
	$height = key_exists('height', $config) ? ' height="' . $config['height'] . '"' : '';
	if(!empty($config['picture']) || (!empty($config['picture'])&& array_key_exists('approval', $config) && $config['approval'] == 1)) {		
		// see if alternate text is set as a config value
		$alt = key_exists('alt', $config) ? ' title="' . $config['alt'] . '" alt="' . $config['alt'] . '"' : '';
		// see if standard wrap text is to be used
		if($wrap === true) {		
			$img = '<div class="' . $type . 'Pic_normal"><img src="' . base_url() . 'images/' . $path . '/' . $config['picture'] . '"' . $alt . $width . $height . ' /></div>';
		} else {
			$img = '<img src="' . base_url() . 'images/' . $path . '/' . $config['picture'] . '"' . $alt . $width . $height . ' />';
		}
	} else {
		if($wrap === true) {
			if($type == 'beer') {
				$img = '<div class="' . $type . 'Pic_normal"><img src="' . base_url() . 'images/beers/bottle.gif"' . $width . $height . ' /></div>';
			} else if($type == 'establishment') {
				$img = '<div class="' . $type . 'Pic_normal"><img src="' . base_url() . 'images/establishments/brewery.jpg"' . $width . $height . ' /></div>';
			}
		} else {
			if($type == 'beer') {
				$img = '<img src="' . base_url() . 'images/beers/bottle.gif"' . $width . $height . ' />';
			}
			else if($type == 'establishment') {
				$img = '<img src="' . base_url() . 'images/establisments/brewery.jpg"' . $width . $height . ' />';
			}
		}
		if($edit === true) {
			// holder to include the nubin
			$bl = true;
			// check if this is an establishment and the image has been approved
			if(!empty($config['picture']) && array_key_exists('approval', $config) && $config['approval'] == 0) {
				$bl = false;
			}
			// check the boolean
			if($bl === true) {
				// have to style differently for an establishment
				$extraClass = $type == 'establishment' ? ' nubbin_establishment' : '';
				$nub = '
				<div id="nubbin_' . $config['id'] . '" class="nubbin' . $extraClass . '">
					<ul>
						<li class="edit"><a href="' . base_url() . 'page/uploadImage/' . $path . '/' . $config['id'] . '"><img src="' . base_url() . 'images/nubbin_editPhoto.jpg" title="edit image" alt="edit image" /></a></li>
					</ul>
				</div>
				';
			}
		} else {
			$nub = '';
		}
	}
	
	return $nub . $img;
}

function getFooterQuote() {
	// get the code igniter instance
	$ci =& get_instance();
	// load the quote model
	$ci->load->model('QuoteModel', '', true);
	// get a random quote
	$ci->_data['quote'] = $ci->QuoteModel->getRandom();
}

function showDudeList($id, $ajax = false) {
	// holder for the return value
	$str = '';
	// get the code igniter instance
	$ci =& get_instance();
	// check their dude list
    $dudes = $ci->UserModel->selectDudeList($id);
    // check that we have results
    if($dudes !== false) {
    	if($ajax !== true) {
        	$str = '
        <h4><span>Dude List</span></h4>
        <div id="dudeHolder">
        	';
    	}
    	$str .= '
        	<ul id="dudeList">
        ';
        foreach($dudes as $row) {
            $str .= '
	            <li class="dudeListItem">
	            	<div id="dudeListItemContainer_' . $row['id'] . '" onmouseover="$(\'removeDude_' . $row['id'] . '\').toggle();" onmouseout="$(\'removeDude_' . $row['id'] . '\').toggle();">
		            	<a href="' . base_url() . 'user/pms/create/' . urlencode($row['id']) . '"><img src="' . base_url() . 'images/email_icon.jpg" alt="send two beer dudes malted mail to ' . $row['username'] . '" /></a>
		            	<a href="' . base_url() . 'user/profile/' . $row['id']. '">' . $row['username'] . '</a>
		            	<div class="removeDude" id="removeDude_' . $row['id'] . '" style="display: none;"><a href="#" onclick="new Ajax.Request(\'' . base_url() . 'ajax/removeDude/' . $row['id'] . '\', {asynchronous: true, evalScripts: true, method: \'get\', onLoading: function() {showSpinner(\'dudeHolder\');}, onComplete: function(response) {alterDudeList(response.responseText);}}); return false;">remove</a></div>
		            </div>
	            </li>
            ';
        }
        $str .= '    
        	</ul>
        ';
        if($ajax !== true) {
        	$str .= '    
        </div>
        	';
        }    
    }
    // send back the string
    return $str;
}
?>