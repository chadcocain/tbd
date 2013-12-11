<?php
class User extends Controller {
	public function __construct() {
		parent::Controller();
		$this->load->helper(array('url', 'users', 'admin', 'js', 'form'));
		$this->load->library('session');
	}
	
	private function doLoad($config) {
		$array = array(
			'header' => 'inc/normalHeader.inc.php'
			, 'headerFrontEnd' => 'inc/header_frontend.inc.php'
			, 'formMast' => 'inc/formMast.inc.php'
			, 'navigation' => 'inc/navigation.inc.php'
			, 'login' => 'user/login'
			, 'logout' => 'user/logout'
			, 'profile' => 'user/profile'
			, 'updateProfile' => 'user/updateProfile'
			, 'createAccount' => 'user/createAccount'
			, 'activateAccount' => 'user/activateAccount'
			, 'pms' => 'user/pms'
			, 'buddy' => 'user/buddylist'
			, 'swaplist' => 'user/swaplist'
			, 'reset' => 'user/reset'
			, 'updatePass' => 'user/updatePass'
			, 'formSuccess' => 'user/formSuccess'
			, 'masthead' => 'admin/masthead'
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
	
	public function login() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// create the right column information
		$this->_data['rightCol'] = '
			<h4><span>Not A Member?</span></h4>
			<ul>
				<li><a href="' . base_url() . 'user/createAccount">Create Account</a></li>
			</ul>	
			<h4><span>Agreements</span></h4>
			<ul>
				<li><a href="' . base_url() . 'page/agreement">User Agreement</a></li>
				<li><a href="' . base_url() . 'page/privacy">Terms and Conditions</a></li>
			</ul>			
		';
		
		if($logged === true) {
			header('Location: ' . base_url());
			exit;
		} else {
			// set the page information
			$this->_data['seo'] = getSEO();
			
			// load the libraries
			$this->load->library('form_validation');		
			
			// run the validation and return the result
			if($this->form_validation->run('login') == false) {			
				// create the form
				$form = createLoginForm();
				// set the introduction text
				$this->_data['leftCol'] = '
					<h2 class="brown">Member Login</h2>
					<p class="marginTop_8">Login to your account using your email address and password you supplied while creating your account.</p>
					' . $form
				;	
				
				// get the information ready for display
				$arr_load = array(
					'pages' => array(				
						'headerFrontEnd' => true
						, 'formMast' => true
						, 'navigation' => true
						, 'login' => true
						, 'footerFrontEnd' => true
					)
					, 'data' => $this->_data
				);			
				// load all parts for the view
				$this->doLoad($arr_load);
			} else { 
				// load the models
				$this->load->model('UserModel', '', true);
				// now try and get the user information based on the email and password
				$boolean = $this->UserModel->login(array('email' => trim($_POST['email']), 'password' => trim($_POST['password'])));
				// check if this was successful
				if($boolean === false) {
					// there was a problem logging in
					// create the error text
					$error = '<div class="formError">The email address and password you provided is incorrect.</div>';
					// create the form
					$form = createLoginForm($error);
					// set the introduction text
					$this->_data['leftCol'] = '
						<h2 class="brown">Member Login</h2>
						<p class="marginTop_8">Login to your account using your email address and password you supplied while creating your account.</p>
						' . $form
					;
						
					// get the information ready for display
					$arr_load = array(
						'pages' => array(				
							'headerFrontEnd' => true
							, 'formMast' => true
							, 'navigation' => true
							, 'login' => true
							, 'footerFrontEnd' => true
						)
						, 'data' => $this->_data
					);				
					// load all parts for the view
					$this->doLoad($arr_load);
				} else {
					// login was successful
					// store the user model information
					$userInfo = array(
						'id' => $this->UserModel->getID()
						, 'username' => $this->UserModel->getUserName()
						, 'firstname' => $this->UserModel->getFirstName()
						, 'lastname' => $this->UserModel->getLastName()
						, 'email' => $this->UserModel->getEmail()
						, 'birthdate' => $this->UserModel->getBirthDate()
						, 'city' => $this->UserModel->getCity()
						, 'state' => $this->UserModel->getState()
						, 'avatar' => $this->UserModel->getAvatar()
						, 'avatarImage' => $this->UserModel->getAvatarImage()
						, 'usertype_id' => $this->UserModel->getUserTypeID()
						, 'usertype' => $this->UserModel->getUserType()
						, 'lastlogin' => $this->UserModel->getLastLogin()
						, 'joindate' => $this->UserModel->getJoinDate()
						, 'formatLastLogin' => $this->UserModel->getFormatLastLogin()
						, 'uploadImage' => $this->UserModel->getUploadImage()
						, 'stateID' => $this->UserModel->getStateID()
					);
					$this->session->set_userdata(array('userInfo' => $userInfo));
					
					// update the last login time
					$this->UserModel->updateLastLogin();
					// get the uri information
					$uri = manipURIPassback();
					
					$array = array(
						'uri' => $uri
						, 'search' => array('_', '.', '-')
						, 'replace' => '/'
					);
					$args = swapOutURI($array);					
					// holder for location url
					$url = base_url();
					if(empty($args)) {
						$url .= 'user/profile';
					} else {
						$url .= $args;
					}
					header('Location: ' . $url);
					exit;
				}				
			}	
		}		
	}
	
	/**
	 * reset the password for a user who is not logged in
	 */
	public function reset() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// set the page information
		$this->_data['seo'] = getSEO();
		//echo '<pre>'; print_r($this->_data['seo']); exit;
		
		// create the right side text
		$this->_data['rightCol'] = '
			<h4><span>Steps to Resetting:</span></h4>
			<p class="bold">Security is of the utmost importance to us, therefore we do not store your passwords in plain text.  A new one has to be created and a process followed to insure the security.</p>
			<ol style="margin-left: 1.4em;">
				<li>Enter your email address for our account and the validation code.</li>
				<li>An email will be sent out to the supplied email address, if it exists*, with instructions on how to change your password.  You will only have four hours to act on this email.</li>
				<li>Follow the instructions to get your new password.</li>
				<li>Login into the system with your new password, you can change it once logged in.</li>
			</ol>
			<p class="marginTop_8">*If the account doesn&#39;t exist, account email changed, or the account is banned, there will be no email sent out.</p>
		';
		// start the output string
		$this->_data['leftCol'] = '<h2 class="brown">Reset Password</h2>';
		// check if the user is logged in or not
		if($logged === true) {
			// they are, no reason to reset their password
			$str .= '<p>You are already logged in.  If you want to change your password, do it via the user profile by clicking on your name in the upper left.</p>';
		} else {
			// get the third uri segement to check if this is 
			// a revisit to change their password.
			$ac = $this->uri->segment(3);
			
			if($ac === false) {
				// all about actually reseting the password
				// load the libraries
				$this->load->library('form_validation');		
				
				// run the validation and return the result
				if($this->form_validation->run('resetPassword') == false) {
					// set the introduction text
					$this->_data['leftCol'] .= '
						<p class="marginTop_8">Request an email with instructions on how to change your password.</p>
						' . createResetPassword()
					;
					// get the information ready for display
					$arr_load = array(
						'pages' => array(				
							'headerFrontEnd' => true
							, 'formMast' => true
							, 'navigation' => true
							, 'reset' => true
							, 'footerFrontEnd' => true
						)
						, 'data' => $this->_data
					);			
					// load all parts for the view
					$this->doLoad($arr_load);
				} else {
					// load the email helper
					$this->load->helper('email');
					// load the user model
					$this->load->model('UserModel', '', true);
					// check if the user exists based on email address
					$ui = $this->UserModel->getInfoIfEmailExists($_POST['email']);
					// check that there was a match
					if(!empty($ui)) {
						// send out the reset password email
						sendFormMail($ui + array('action' => 'resetPassword'));
						
						// get the string data into the page
						$this->_data['leftCol'] .= '
							<p class="marginTop_8">An email has been sent the requested address.  This email is valid for four hours.  
							Please follow the instructions within the email.  Make sure your email client is setup to accept emails from
							twobeerdudes.com</p>';
					} else {
						// get the string data into the page
						$this->_data['leftCol'] .= '
							<p class="marginTop_8">The system had difficulty processing your request based on the supplied email address.</p>';
					}
					// get the information ready for display
					$arr_load = array(
						'pages' => array(				
							'headerFrontEnd' => true
							, 'formMast' => true
							, 'navigation' => true
							, 'reset' => true
							, 'footerFrontEnd' => true
						)
						, 'data' => $this->_data
					);			
					// load all parts for the view
					$this->doLoad($arr_load);
				}
			} else {
				// time change the password
				// holder to determine if this is successful or not
				$boolean = false;
				// split apart the activation code
				$parts = explode('_', $ac);
				// check that parts is an array
				if(is_array($parts) && count($parts) == 2) {
					// load the email helper
					$this->load->helper('email');
					// load the user model
					$this->load->model('UserModel', '', true);
					// get the actual activation code
					$activationCode = $parts[0];
					// get the user id
					$userID = $parts[1];
					
					// query the db and make sure these values match what is stored
					$check = $this->UserModel->validatePasswordCode(array('activationCode' => $activationCode, 'userID' => $userID));
					// check that this actually happened
					if($check !== false) {
						// get the string data into the page
						$this->_data['leftCol'] .= '
							<p class="marginTop_8">An email has been sent with your updated password.</p>
						';
						$this->_data['rightCol'] = '
							<h4><span>What Next?</span></h4>
							<ul>
								<li>Check your email for the new password</li>
								<li>You can change your password to something easier to remember once you have logged in.</li>
							</ul>
						';		
						// send out the reset password email
						sendFormMail($check + array('action' => 'changedPassword'));			
						// success
						$boolean = true;
					}
				}
				
				if($boolean === false) {
					// get the string data into the page
					$this->_data['leftCol'] .= '
						<p class="marginTop_8">The system experiened a problem trying to create an email for you. Perhaps it is passed the four
						hour grace period to create a new password.  Try <a href="' . base_url() . 'user/reset">resetting your password</a> again.</p>
					';
					$this->_data['rightCol'] = '
						<h4><span>Reason for Problems:</span></h4>
						<ul>
							<li>The URL sent to your email address is only good for four hours.  Once that time has passed you will have to request another <a href="' . base_url() . 'user/reset">reset</a>.</li>
							<li>Maybe the information sent to you was incorrect because of a system error, in that case request another <a href="' . base_url() . 'user/reset">reset</a>.</li>
						</ul>
					';
				}
					
				// get the information ready for display
				$arr_load = array(
					'pages' => array(				
						'headerFrontEnd' => true
						, 'formMast' => true
						, 'navigation' => true
						, 'reset' => true
						, 'footerFrontEnd' => true
					)
					, 'data' => $this->_data
				);			
				// load all parts for the view
				$this->doLoad($arr_load);
			}
		}
	}
	
	public function logout() {
		if(checkLogin() === true) {
			// they are logged in
			// destroy the session information
			$this->session->sess_destroy();
			// bring them to front page
			header('Location: ' . base_url());
			exit;
		} else {
			// not logged in, boot 'em
			header('Location: ' . base_url());
			exit;
		}
	}
	
	public function createAccount() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// create the right column information
		$this->_data['rightCol'] = '
			<h4><span>Why Two Beer Dudes?</span></h4>
			<ul>
				<li>Learn how to truly enjoy beer</li>
				<li>Keep track of your beer history</li>
				<li>Enjoy American craft beer</li>
				<li>Midwest centric but not completely</li>
				<li>Trying to get the little guys on the map</li>
				<li>Share your thoughts with the community</li>
			</ul>	
			<h4><span>Agreements</span></h4>
			<ul>
				<li><a href="' . base_url() . 'page/agreement">User Agreement</a></li>
                <li><a href="' . base_url() . 'page/privacy">Terms and Conditions</a></li>
			</ul>
			<h4><span>Already A Member?</span></h4>
			<ul>
				<li><a href="' . base_url() . 'user/login">Login</a></li>
			</ul>
		';
		
		if($logged === true) {
			// set the page information
			$this->_data['seo'] = getSEO();
			// set the text for the top
			$this->_data['leftCol'] = '
				<h2 class="brown">Create User Account</h2>
				<p class="marginTop_8">You are currently logged and obviously have an account, so you don&#39;t need another.  Stop saucing so much so you realize what you are doing.</p>
			';
			
			// get the information ready for display
			$arr_load = array(
				'pages' => array(				
					'headerFrontEnd' => true
					, 'formMast' => true
					, 'navigation' => true
					, 'createAccount' => true
					, 'footerFrontEnd' => true
				)
				, 'data' => $this->_data
			);			
			// load all parts for the view
			$this->doLoad($arr_load);
		} else {		
			// load the models
			$this->load->model('StateModel', '', true);
			$this->load->model('UserModel', '', true);
			// load the libraries
			$this->load->library('form_validation');		
			
			// run the validation and return the result
			if($this->form_validation->run('createAccount') == false) {
				// set the page information
				$this->_data['seo'] = getSEO();
				// create the form
				$form = createAccountForm();
				// set the introduction text
				$this->_data['leftCol'] = '
					<h2 class="brown">Create User Account</h2>
					<p class="marginTop_8">Creating a Two Beer Dudes account is free.  Become a member and share your thoughts, reviews, and ratings of American craft beer with other enthusists.</p>
					<p class="marginTop_8">Be sure that we will never share your information with any third party providers.  We dislike that crap also!</p>
					' . $form
				;	
				
				// get the information ready for display
				$arr_load = array(
					'pages' => array(				
						'headerFrontEnd' => true
						, 'formMast' => true
						, 'navigation' => true
						, 'createAccount' => true
						, 'footerFrontEnd' => true
					)
					, 'data' => $this->_data
				);				
				// load all parts for the view
				$this->doLoad($arr_load);
			} else { 
				// load the state model
				$this->load->model('StateModel', '', true);
				// get the abbreviation for the state
				$stateAbbr = $this->StateModel->getStateByID($_POST['state']);
				// set the state the abbreviation
				$_POST['state'] = $stateAbbr['stateAbbr'];
				// save the information to the database	and get the id of the user	
				$userID = $this->UserModel->createAccount($_POST + array('usertype' => 2, 'ip' => $_SERVER['REMOTE_ADDR']));
				// get the activation code and id
				$activationCode = $this->UserModel->getActivationCode($userID);
				// send out the activation email
				$array = array(
					'activationCode' => $activationCode
					, 'membername' => trim($_POST['username'])
					, 'email' => trim($_POST['email'])
				);
				sendActivationMail($array);
				
				header('Location: ' . base_url() . 'user/formSuccess/createAccount');
			}	
		}	
	}
	
	public function activateAccount() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// set the page seo information
		$this->_data['seo'] = getSEO();
		
		// get the activation code
		$activationCode = $this->uri->segment(3);
		
		if(!empty($activationCode)) {
			// load the user model
			$this->load->model('UserModel', '', true);
			// try to activate the account
			$int = $this->UserModel->activateAccount(array('activationCode' => $activationCode));
			// start the output
			$this->_data['leftCol'] = '<h2 class="brown marginTop_10">Account Activation</h2>';
			// check if there was one affected row
			if($int == 1) {
				// successful account activation
				$this->_data['leftCol'] .= '<p class="marginTop_8">Your membership is now active and you have access to everything that Two Beer Dudes has to offer.  You need to <a href="' . base_url() . 'user/login">login</a> to access your account profile or start making your own ratings.</p>';
				$this->_data['rightCol'] = '
					<h4><span>Thank You For Joining</span></h4>
					<ul>
						<li>Enjoy the site and we look forward to you becoming a dude.</li>
						<li>Now that you have joined we would like to ask you to report any problems you have with the site by sending an email to <script type="text/javascript">document.write(\'webmaster\' + \'@\' + \'twobeerdudes.com\');</script> with the url of the page and problem you expereinced.</li>
					</ul>	
				';
			} else {
				// something was wrong
				$this->_data['leftCol'] .= '<p class="marginTop_8">The system experienced a problem trying to activate your membership.  
					Please try again.  If the problem persists please try creating a new account as activation is only 
					allowed within 48 hours of account creation.  Also, try logging in as your account may already
					be active.</p>';
			}
		} else {
			// they shouldn't be at this page
			header('Location: ' . base_url());
			exit;
		}		
		
		// get the information ready for display
		$arr_load = array(
			'pages' => array(				
				'headerFrontEnd' => true
				, 'formMast' => true
				, 'navigation' => true
				, 'activateAccount' => true
				, 'footerFrontEnd' => true
			)
			, 'data' => $this->_data
		);				
		// load all parts for the view
		$this->doLoad($arr_load);
	}
	
	public function formSuccess() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// set the page seo information
		$this->_data['seo'] = getSEO();
		
		// get the type of success
		$type = $this->uri->segment(3);
		// get the correct text based on the page
		switch($type) {
			case 'createAccount':
				$this->_data['leftCol'] = '
					<h2 class="brown">Account Created, Now Activate</h2>
					<p class="marginTop_8">Thank you for your interest in Two Beer Dudes.  You will be receiving an email shortly with instructions on how to activate your account (please check your spam mail also).  The link in the email will only be valid for 48 hours.</p>
				';
				$this->_data['rightCol'] = '
					<h4><span>Almost There, One More Step</span></h4>
					<ul>
						<li>We require that each new account be activated via an email notice that is sent to the provided email for the account.  This is a security measure for all involved.</li>
					</ul>	
				';
				break;
		}
		
		$arr_load = array(
			'pages' => array('header' => true, 'formSuccess' => true, 'footer' => true)
			, 'data' => $this->_data
		);		
		// get the information ready for display
		$arr_load = array(
			'pages' => array(				
				'headerFrontEnd' => true
				, 'formMast' => true
				, 'navigation' => true
				, 'formSuccess' => true
				, 'footerFrontEnd' => true
			)
			, 'data' => $this->_data
		);				
		// load all parts for the view
		$this->doLoad($arr_load);		
	}
	
	public function emailExists($email) {
		// load the user model
		$this->load->model('UserModel', '', true);
		// check if the email address is already associated w/ an account
		$boolean = $this->UserModel->emailCheck($email);
		// check the boolean
		if($boolean === false) {
			$this->form_validation->set_message('emailExists', 'The %s address you have chosen already has an account.');
		}
		return $boolean;
	}
	
	public function emailCheckMatch($email) {
		// load the user model
		$this->load->model('UserModel', '', true);
		// check if the email address is already associated w/ an account
		$boolean = $this->UserModel->emailCheckMatch($email);
		// check the boolean
		if($boolean === false) {
			$this->form_validation->set_message('emailCheckMatch', 'The %s address you have chosen does not exist in our records.');
		}
		return $boolean;
	}
	
	public function usernameExists($username) {
		// load the user model
		$this->load->model('UserModel', '', true);
		// check if the email address is already associated w/ an account
		$boolean = $this->UserModel->usernameCheckCreateAccount($username);
		// check the boolean
		if($boolean === false) {
			$this->form_validation->set_message('usernameExists', 'The %s you have chosen already exists, please choose another');
		}
		return $boolean;
	}
	
	public function userExists($to) {
		// load the user model
		$this->load->model('UserModel', '', true);
		// check if the email address is already associated w/ an account
		$boolean = $this->UserModel->usernameCheck($to);
		// check the boolean
		if($boolean === false) {
			$this->form_validation->set_message('userExists', 'The %s recipient does not exist.');
		}
		return $boolean;
	}
	
	public function userBlocked($to) {
		// load the user model
		$this->load->model('UserModel', '', true);
		// get the session info
		$userInfo = $this->session->userdata('userInfo');
		// check if the email address is already associated w/ an account
		$boolean = $this->UserModel->checkBlockUsername($to, $userInfo['id']);
		// check the boolean
		if($boolean === false) {
			$this->form_validation->set_message('userBlocked', 'The %s recipient has blocked Malt Mail from you.');
		}
		return $boolean;
	}
	
	public function mailLength($message) {
		// holder for the boolean
		$boolean = true;
		// get the length of the string
		$strlen = strlen($message);
		// check the length
		if($strlen > PRIVATE_MESSAGE_MAX_LENGTH) {
			$boolean = false;
		}
		// check the boolean
		if($boolean === false) {
			$this->form_validation->set_message('mailLength', 'The %s is too long (' . $strlen . ').  There is a ' . PRIVATE_MESSAGE_MAX_LENGTH . ' character limit.');
		}
		return $boolean;
	}
	
	public function alphaNumericSpace($str) {
		return (!preg_match("/^([a-z0-9\s])+$/i", $str)) ? false : true;
	}
	
	public function validateCaptcha($str) {
		
	}
	
    public function profile() {
        // get the login boolean
        $logged = checkLogin();
        // user info for logged in user
        $userInfo = $this->session->userdata('userInfo');		
        // create login mast text
        $this->_data['formMast'] = createHeader($logged, $userInfo);

        if($logged === true) {
            // set the page seo information
            $this->_data['seo'] = getSEO();

            // get the user id of the user information to show
            $id = $this->uri->segment(3);

            // check the id is populated
            if($id == false) {
                // throw them to the front page, damn little bitches
                header('Location:' . base_url());
                exit;
            }

            // load the use model
            $this->load->model('UserModel', '', true);
            // load the swap model
            $this->load->model('SwapModel', '', true);

            // holder for the user information from the model
            $array = array();			
            // check that the user exists
            if($this->UserModel->idCheck($id) === false) {
                // the id doesn't exist in the user table
                $this->_data['leftCol'] = '
                <h2 class="brown">User Profile</h2>
                <p class="marginTop_8">The user you are looking for does not exist.<p>
                ';
                $this->_data['rightCol'] = '
                <h4><span>Problem Finding User Account</span></h4>
                <ul>
                    <li>If the account is yours and it isn&#39;t working please contact the <script type="text/javascript">document.write(\'webmaster\' + \'@\' + \'twobeerdudes.com\');</script> with your account information.</li>
                    <li>Otherwise the account might not exist, might have been inactivated, banned, etc.</li>
                </ul>
                ';
            } else {	
                // load the javascript helper
                $this->load->helper(array('js'));
                // get the user information
                $this->load->library('userslib');
                // load the rating model
                $this->load->model('RatingModel', '', true);
                // get the information for the profile
                $array = $this->userslib->showProfile($id);
                // set the output for the screen
                $this->_data['leftCol'] = $array['str'];

                // user info for logged in user
                $userInfo = $this->session->userdata('userInfo');
                // create the right side output
                $this->_data['rightCol'] = $this->profileRightLinks(array('userInfo' => $userInfo, 'id' => $id));
            }			

            // set the page seo information
            $this->_data['seo'] = array_slice($array, 0, 3);
            // get the information ready for display
            $arr_load = array(
                'pages' => array(				
                    'headerFrontEnd' => true
                    , 'formMast' => true
                    , 'navigation' => true
                    , 'profile' => true
                    , 'footerFrontEnd' => true
                )
                , 'data' => $this->_data
            );
            // load all parts for the view
            $this->doLoad($arr_load);
        } else {
            // set the output for the screen
            header('Location:' . base_url() . 'user/login');
            exit;
        }		
    }
	
	public function updateProfile() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		// set the page seo information
		$this->_data['seo'] = getSEO();
		
		if($logged === true) {
			// load the state model
			$this->load->model('StateModel', '', true);
			// load the libraries
			$this->load->library('form_validation');
			
			// run the validation and return the result
			if($this->form_validation->run('updateProfile') == false) {				
				// holder for configuration values to send to form
				$config = array();
				// see if the post array is set
				if(empty($_POST)) {
					// get the notes
					$ui = $this->UserModel->getUserProfile($userInfo['id']);
					$config['notes'] = $ui['notes'];
					$config['state'] = $userInfo['stateID'];
					$config['firstname'] = $userInfo['firstname'];
					$config['lastname'] = $userInfo['lastname'];
					$config['city'] = $userInfo['city'];
				}
				// set the introduction text
				$this->_data['leftCol'] = '
					<h2 class="brown">Update Profile: ' . $userInfo['username'] . '</h2>' . 
					form_updateProfile($config)
				;	
			} else { 
				// everything seems to be in good order
				// get the state abbreviation
				$stateInfo = $this->StateModel->getStateByID($_POST['slt_state']);
				// package it up to be populated into the db
				$config = array(
					'username' => $userInfo['username']
					, 'firstname' => $_POST['txt_firstname']
					, 'lastname' => $_POST['txt_lastname']
					, 'email' => $userInfo['email']
					, 'birthdate' => $userInfo['birthdate']
					, 'city' => $_POST['txt_city']
					, 'state' => $stateInfo['stateAbbr']
					, 'notes' => $_POST['ttr_notes']
				);
				// run the query
				$this->UserModel->updateProfileByID($userInfo['id'], $config);
				
				// now store those value into the session
				$this->session->set_userdata('firstname', $_POST['txt_firstname']);
				$this->session->set_userdata('lastname', $_POST['txt_lastname']);
				$this->session->set_userdata('city', $_POST['txt_city']);
				$this->session->set_userdata('state', $stateInfo['stateAbbr']);
				$this->session->set_userdata('stateID', $_POST['slt_state']);
				
				// all done, go to the profile page for this user
				header('Location: ' . base_url() . 'user/profile/' . $userInfo['id']);
				exit;
			}
			// create the right side output
			$this->_data['rightCol'] = '
				<h4><span>Personal Information Privacy</span></h4>
				<ul>
					<li>Nothing here is shared without outside vendors.</li>
					<li>The information below is only shown to other members on the site when they visit your profile page.</a>
					<li>Don\'t want anything to display, leave it blank.</li>
					<li>State is the only required piece of information.</li>
				</ul>	
			';
			$this->_data['rightCol'] .= $this->profileRightLinks(array('userInfo' => $userInfo, 'id' => 0));			
		} else {
			// user is not logged in
			$this->_data['leftCol'] = '
				<h2 class="brown">Update Profile</h2>
				<p class="marginTop_8">You do not have access to this portion of the site without being 
				<a href="' . base_url() . 'user/login">logged in</a>.  If are not a member, 
				<a href="' . base_url() . 'user/createAccount">create</a> a free membership.</p>';
		}

		// get the information ready for display
		$arr_load = array(
			'pages' => array(				
				'headerFrontEnd' => true
				, 'formMast' => true
				, 'navigation' => true
				, 'updateProfile' => true
				, 'footerFrontEnd' => true
			)
			, 'data' => $this->_data
		);
		// load all parts for the view
		$this->doLoad($arr_load);	
	}
	
	public function pms() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// set the page seo information
		$this->_data['seo'] = getSEO();
		
		if($logged === true) {
			// user is logged in
			// load the user model
			$this->load->model('UserModel', '', true);
			// load the user library
			$this->load->library('userslib');
			// load the libraries
			$this->load->library('form_validation');	
			// load the form helper
			$this->load->helper(array('form'));	
			// need the logged in user id
			$userInfo = $this->session->userdata('userInfo');
			// get the action to take
			$action = $this->uri->segment(3);
			// create the right side output
			$this->_data['rightCol'] = $this->profileRightLinks(array('userInfo' => $userInfo, 'id' => 0));
			// check which action to take
			switch($action) {
				case 'view':
				default:
					$this->_data['leftCol'] = '
						<h2 class="brown">Malted Mail Inbox</h2>' . 
						$this->userslib->showMessages($userInfo, false, false)
					;
					break;
				case 'showMessage':
					// get the message id
					$messageID = $this->uri->segment(4);
					// make sure the id was there
					if($messageID == false) {
						// no message id
						$this->_data['leftCol'] = '
							<h2 class="brown">Malted Mail Display Message</h2>
							<p>No message found matching requested information.</p>
						';
					} else {
						// message id
						$this->_data['leftCol'] = '
							<h2 class="brown">Malted Mail Display Message</h2>' . 
							$this->userslib->showMessageByID($messageID, $userInfo)
						;
					}
					break;
				case 'create':					
					// run the validation and return the result
					if($this->form_validation->run('sendMaltedMail') == false) {
						// set the page information
						$this->_data['seo'] = getSEO();
						// holder for configuration array
						$config = array();
						// get a user id, if it is a person already to send to
						$sendToID = $this->uri->segment(4);
						// check if send to id is set
						if($sendToID != false) {
							// get the name of the user
							$nameToSendTo = $this->UserModel->getUsernameByID($sendToID);
							// add to the configuration array
							$config['nameToSendTo'] = $nameToSendTo;
						}

						// create the form
						$this->_data['form'] = form_createMessage($config + array('formType' => 'create'));
						// set the introduction text
						$this->_data['leftCol'] = '
							<h2 class="brown">Create Malt Mail</h2>' . 
							form_createMessage($config)
						;	
					} else { 
						// get the name of the user
						$toUserID = $this->UserModel->getIDByUsername($_POST['txt_to']);
						// create the config array to pass to model
						$array = array(
							'fromID' => $userInfo['id']
							, 'toID' => $toUserID
							, 'subject' => $_POST['txt_subject']
							, 'message' => $_POST['ttr_message']
						);						
						// save the information to the database	and get the id of the user	
						$this->UserModel->insertPM($array);
						// display congratulations message
						$this->_data['leftCol'] = '
							<h2 class="brown">Create Malt Mail</h2>
							<p class="marginTop_8">Your message has been sent to ' . $_POST['txt_to'] . '</p>
						';
						//header('Location: ' . base_url() . 'user/formSuccess/createAccount');
					}
					// get the form information
					//$this->_data['pms'] = $this->userslib->createMessage($userInfo);
					break;
				case 'reply':
					// run the validation and return the result
					if($this->form_validation->run('sendMaltedMail') == false) {
						// set the page information
						$this->_data['seo'] = getSEO();
						// holder for configuration array
						$config = array();
						// get a malted mail id
						$messageID = $this->uri->segment(4);
						// check if malted mail id is set
						if($messageID == false) {
							// it is not, send them back to mail 
							header('Location: ' . base_url() . 'user/pms');
							exit;
						} else {			
							// holder for the configuration array
							$config = array();				
							// get the information about the message
							$array = $this->UserModel->getMessageInfoByMessageID($messageID);
							// check that a name was returned
							if($array !== false) {
								// add to the configuration array
								$config['nameToSendTo'] = $array['username'];
								
								$arr = array(
									'subject' => $array['subject']
									, 'add' => 'RE: '
								);
								$subject = adjustMailSubject($arr);
								$config['subjectText'] = $subject;
								$config['messageText'] = markupPreviousMessage($array);
							}
							// create the form
							//$this->_data['form'] = form_createMessage($config);
							// set the introduction text
							$this->_data['leftCol'] = '
								<h2 class="brown">Reply To Malt Mail</h2>' . 
								form_createMessage($config + array('formType' => 'reply/' . $messageID))
							;
						}							
					} else { 
						// get the name of the user
						$toUserID = $this->UserModel->getIDByUsername($_POST['txt_to']);
						// create the config array to pass to model
						$array = array(
							'fromID' => $userInfo['id']
							, 'toID' => $toUserID
							, 'subject' => $_POST['txt_subject']
							, 'message' => $_POST['ttr_message']
						);						
						// save the information to the database	and get the id of the user	
						$this->UserModel->insertPM($array);
						// display congratulations message
						$this->_data['leftCol'] = '
							<h2 class="brown">Reply To Malt Mail</h2>
							<p class="marginTop_8">Your message has been sent to ' . $_POST['txt_to'] . '</p>
						';
					}
					break;
				case 'forward':
					// run the validation and return the result
					if($this->form_validation->run('sendMaltedMail') == false) {
						// set the page information
						$this->_data['seo'] = getSEO();
						// holder for configuration array
						$config = array();
						// get a malted mail id
						$messageID = $this->uri->segment(4);
						// check if malted mail id is set
						if($messageID == false) {
							// it is not, send them back to mail 
							header('Location: ' . base_url() . 'user/pms');
							exit;
						} else {			
							// holder for the configuration array
							$config = array();				
							// get the information about the message
							$array = $this->UserModel->getMessageInfoByMessageID($messageID);
							// check that a name was returned
							if($array !== false) {
								// add to the configuration array
								$arr = array(
									'subject' => $array['subject']
									, 'add' => 'FWD: '
								);
								$subject = adjustMailSubject($arr);
								$config['subjectText'] = $subject;
								$config['messageText'] = markupPreviousMessage($array);
							}
							// set the introduction text
							$this->_data['leftCol'] = '
								<h2 class="brown">Forward Malt Mail</h2>' . 
								form_createMessage($config + array('formType' => 'forward/' . $messageID))
							;
						}							
					} else { 
						// get the name of the user
						$toUserID = $this->UserModel->getIDByUsername($_POST['txt_to']);
						// create the config array to pass to model
						$array = array(
							'fromID' => $userInfo['id']
							, 'toID' => $toUserID
							, 'subject' => $_POST['txt_subject']
							, 'message' => $_POST['ttr_message']
						);						
						// save the information to the database	and get the id of the user	
						$this->UserModel->insertPM($array);
						// display congratulations message
						$this->_data['leftCol'] = '
							<h2 class="brown">Forward Malt Mail</h2>
							<p class="marginTop_8">Your message has been sent to ' . $_POST['txt_to'] . '</p>';
					}
					break;
			}
		} else {
			// user is not logged in
			$this->_data['leftCol'] = '
				<h2 class="brown">Malted Mail</h2>
				<p class="marginTop_8">You do not have access to this portion of the site without being 
				<a href="' . base_url() . 'user/login">logged in</a>.  If are not a member, 
				<a href="' . base_url() . 'user/createAccount">create</a> a free membership.</p>';
		}

		// get the information ready for display
		$arr_load = array(
			'pages' => array(				
				'headerFrontEnd' => true
				, 'formMast' => true
				, 'navigation' => true
				, 'pms' => true
				, 'footerFrontEnd' => true
			)
			, 'data' => $this->_data
		);
		// load all parts for the view
		$this->doLoad($arr_load);		
	}
	
	private function profileRightLinks($config = array()) {
		// create the links for all user profile
		// and similar pages
		$str = '';
	 	if($config['id'] > 0 && $config['id'] != $config['userInfo']['id']) {
	 		$str = '
	 		<h4><span>User Actions</span></h4>
			<ul>
	 			<li><img src="' . base_url() . 'images/person_icon.png" alt="two beer dudes add dude ' . $config['id'] . '" /> <a href="#" onclick="new Ajax.Request(\'' . base_url() . 'ajax/addDude/' . $config['id'] . '\', {asynchronous: true, evalScripts: true, method: \'get\', onLoading: function() {showSpinner(\'dudeHolder\');}, onComplete: function(response) {alterDudeList(response.responseText);}}); return false;">Add to dude list</a></li>
	 			<li><img src="' . base_url() . 'images/email_icon.jpg" alt="send two beer dudes malted mail to ' . $config['id'] . '" /> <a href="' . base_url() . 'user/pms/create/' . urlencode($config['id']) . '">Send malted mail</a></li>
	 		</ul>
	 		';
	 	}		
		
		$str .= '
			<h4><span>My Profile</span></h4>
			<ul>
				<li><a href="' . base_url() . 'user/profile/' . $config['userInfo']['id'] . '">View Profile</a></li>
				<li><a href="' . base_url() . 'user/updateProfile">Update Profile</a></li>
				<li><a href="' . base_url() . 'user/updatePass/' . $config['userInfo']['id'] . '">Update Password</a></li>
			</ul>
			<h4><span>Malted Mail</span></h4>
			<ul>
				<li><a href="' . base_url() . 'user/pms">List Malted Mail</a></li>
				<li><a href="' . base_url() . 'user/pms/create">Create Malted Mail</a></li>
			</ul>
			<h4><span>Beer Swapping</span></h4>
			<ul>
				<li><a href="' . base_url() . 'user/swaplist/ins">Swap Ins</a></li>
				<li><a href="' . base_url() . 'user/swaplist/outs">Swap Outs</a></li>
			</ul>
		';
        $str .= showDudeList($config['userInfo']['id']);
		// return the links
		return $str;
	}
	
	public function buddylist() {
		// set the page seo information
		$this->_data['seo'] = getSEO();
		
		if(checkLogin() === true) {
			// user is logged in
			// load the user model
			$this->load->model('UserModel', '', true);
			// load the user library
			$this->load->library('userslib');
			// need the logged in user id
			$userInfo = $this->session->userdata('userInfo');
			// get the action to take
			$action = $this->uri->segment(3);
			// check which action to take
			switch($action) {
				case 'buddy':
				default:
					$this->_data['buddylist'] = $this->userslib->showBuddyList($userInfo);
					break;
				case 'showMessage':
					// get the message id
					$messageID = $this->uri->segment(4);
					// make sure the id was there
					if($messageID == false) {
						// no message id
						$this->_data['buddylist'] = '<p>No message found matching requested information.</p>';
					} else {
						// message id
						$this->_data['buddylist'] = $this->userslib->showMessageByID($messageID, $userInfo);
					}
					break;
				case 'block':
					break;
			}
			
			$arr_load = array(
				'pages' => array('header' => true, 'buddy' => true, 'footer' => true)
				, 'data' => $this->_data
			);				
			// load all parts for the view
			$this->doLoad($arr_load);
		} else {
			// set the output for the screen
			header('Location:' . base_url() . 'user/login');
			exit;
		}
	}
	
    public function swaplist() {
        // get the login boolean
        $logged = checkLogin();
        // user info for logged in user
        $userInfo = $this->session->userdata('userInfo');
        // create login mast text
        $this->_data['formMast'] = createHeader($logged, $userInfo);

        if($logged === true) {
            // get the swap type
            $type = $this->uri->segment(3);
            // get the user information
            $userInfo = $this->session->userdata('userInfo');
            // check that the type and session are set
            if($type == false || $userInfo == false || empty($userInfo['id'])) {
                // set the output for the screen
                header('Location:' . base_url() . 'user/login');
                exit;
            } else {
                // type and session are set
                // load the swap model
                $this->load->model('SwapModel', '', true);
                // load the beer model
                $this->load->model('BeerModel', '', true);
                // the user lib library
                $this->load->library('userslib');
                // load the admin helper
                $this->load->helper(array('js'));
                // check if the user id is being passed
                $user_id = $this->uri->segment(4);
                $logged_in_user = false;
                if($user_id == false) {
                    $user_id = $userInfo['id'];
                    $logged_in_user = true;
                }
                // so determine what to show
                switch($type) {
                    case 'outs':
                        $this->_data['leftCol'] = $this->userslib->showSwaplistOuts($user_id, false, false, $logged_in_user);
                        // create the right side output
                        $this->_data['rightCol'] = $this->profileRightLinks(array('userInfo' => $userInfo, 'id' => 0));
                        break;
                    case 'ins':
                    default:
                        $this->_data['leftCol'] = $this->userslib->showSwaplistIns($user_id, false, false, $logged_in_user);
                        // create the right side output
                        $this->_data['rightCol'] = $this->profileRightLinks(array('userInfo' => $userInfo, 'id' => 0));
                        break;
                }

                // set the page information
                $this->_data['seo'] = getSEO();

                // get the information ready for display
                $arr_load = array(
                    'pages' => array(				
                        'headerFrontEnd' => true
                        , 'formMast' => true
                        , 'navigation' => true
                        , 'swaplist' => true
                        , 'footerFrontEnd' => true
                    )
                    , 'data' => $this->_data
                );
                // load all parts for the view
                $this->doLoad($arr_load);
            }
        } else {
            // set the output for the screen
            header('Location:' . base_url() . 'user/login');
            exit;
        }	
    }
	
	public function updatePass() {
		// get the login boolean
		$logged = checkLogin();
		// user info for logged in user
		$userInfo = $this->session->userdata('userInfo');		
		// create login mast text
		$this->_data['formMast'] = createHeader($logged, $userInfo);
		
		// set the page seo information
		$this->_data['seo'] = getSEO();
		
		// start the output
		$this->_data['leftCol'] = '
			<h2 class="brown">Update Password</h2>
		';
		if($logged === true) {
			// get passed in user id
			$userID = $this->uri->segment(3);
			// check that it is set and matches the session id
			if($userID != false && $userID == $userInfo['id']) {
				// go ahead and allow them to change it
				// load the libraries
				$this->load->library('form_validation');		
				
				// run the validation and return the result
				if($this->form_validation->run('updatePassword') == false) {
					// set the introduction text
					$this->_data['leftCol'] .= '
						<p class="marginTop_8">Update your password to a string of letters, characters, and numbers 6 to 12 in length.</p>
						' . form_updatePassword()
					;
					$this->_data['rightCol'] = '
						<h4><span>Password Change</span></h4>
						<ul>
							<li>This password will be encrypted upon creation for security purposes</li>
							<li>Please store your password in a safe place as it will not be emailed to you or recoverable.</li>
						</ul>
					';
					// create the right side output
					$this->_data['rightCol'] .= $this->profileRightLinks(array('userInfo' => $userInfo, 'id' => $userID));
				} else {
					// update the password
					// load the user model
					$this->load->model('UserModel', '', true);
					// update the database
					$this->UserModel->setPassword(array('userID' => $userID, 'newPassword' => $_POST['password1']));
					// create the thank you text
					$this->_data['leftCol'] .= '
						<p class="marginTop_8">Your password has been updated.  The next time you login you will be required to use it.</p>
					';
					// create the right side output
					$this->_data['rightCol'] = $this->profileRightLinks(array('userInfo' => $userInfo, 'id' => $userID));
				}
			} else {
				$this->_data['leftCol'] .= '<p class="marginTop_8">You are only allowed to change the password for your account.</p>';
			}			
			
			// get the information ready for display
			$arr_load = array(
				'pages' => array(				
					'headerFrontEnd' => true
					, 'formMast' => true
					, 'navigation' => true
					, 'updatePass' => true
					, 'footerFrontEnd' => true
				)
				, 'data' => $this->_data
			);
			// load all parts for the view
			$this->doLoad($arr_load);		
		} else {
			header('Location: ' . base_url());
			exit;
		}			
	}
}
?>
