
<div id="container">
<?php
	$str = '
	<div id="loginMast" style="background-image: url(' . base_url() . 'images/header_' . rand(1, HEADER_IMAGE_COUNT) . '.jpg);">
	';
	// check if they are logged in
	if ($logged)
	{
		// holder for private message info
		$pms = '';		
		// check to see if they user has a new message
		if(get_number_malted_mail_unread() > 0)
		{
			$pms = '
        <span>
            <a href="' . base_url() . 'user/pms"><img src="' . base_url() . 'images/chat_bubble.gif" alt="new private message(s)" /></a>
        </span>
            ';
		}
		$str .= '
		<div id="loginInfo">
			<p>' . $pms . 'Welcome, <a href="' . base_url() . 'user/profile/' . $user_info['id'] . '">' . $user_info['username'] . '</a> 
                <span style="font-size: 74%;">(if you are finished, <a href="' . base_url() . 'user/logout">logout</a>)</span>
            </p>
		</div>
		';
	}
	else
	{
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
	echo $str;
?>
