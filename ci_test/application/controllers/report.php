<?php
class Report extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper(array('url', 'users', 'admin', 'js'));
	}
	
	private function doLoad($config) {
		$array = array(
			'header_modal' => 'inc/header_modal.inc.php'
            , 'footer_modal' => 'inc/footer_modal.inc.php'
			, 'report' => 'report/abuse'
		);
		
		foreach($config['pages'] as $page => $data) {
			if($data === true) {
				$this->load->view($array[$page], $config['data']);
			} else {
				$this->load->view($array[$page]);
			}
		}
	}
	
	public function abuse() {
        /*echo '<html><head>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
        <script type="text/javascript">
        $(document).ready(function() {
            setTimeout(\'shitHole()\', 3000);
            //$(this).parent().fancybox.close();    
            
        });
        function shitHole() {
            parent.$j.fancybox.close();
        }
        </script>
        </head><body>' . $this->uri->segment(3) . '</body></html>';*/
        
        $this->_data = array();
        
        // get the login boolean
		$logged = checkLogin();
		
		// check if the user is logged in
		if($logged === true) {
			// load the form validation library
			$this->load->library('form_validation');
			// load the helpers
			$this->load->helper(array('report', 'form'));
			
			// get the uri segment to determine the type of form and
			// appropriate way to validate
			$type = $this->uri->segment(2);
            
            // holder for the form
            $form = '';
            // holder for the type of abuse reported
            $abuse_type = '';
            // holder for information about the malicious information
            $info = array();
			
			// determine the type of feedback
			switch($type) {
				case 'feedback':
				default:
					// run the validation and return the result
					if($this->form_validation->run('reportFeedback') == false) {
                        // get confiration values ready
                        $config = array(
                            'uri' => $this->uri->uri_string()
                        );
					    $form = createAbuseForm($config);
                        // load the swap model
                        $this->load->model('SwapModel', '', true);
                        // get the information about the swap feedback
                        $swap = $this->SwapModel->getSwapFeedbackByFeedbackID($this->uri->segment(3));
                        // get it nicely packaged
                        $info = array(
                            'username' => $swap->username
                            , 'text' => $swap->feedback
                            , 'date' => $swap->feedbackDate
                        );
                        // abuse type
                        $abuse_type = 'Beer Swapping Feedback';
					} else {
						$this->_data['js_special'] = '
		<script type="text/javascript">
        $(document).ready(function() {
            //setTimeout(\'shitHole()\', 3000);
            //$(this).parent().fancybox.close();    
            
        });
        function shitHole() {
            parent.$j.fancybox.close();
        }
        </script>
        				';	
					}
					break;
			}        
		}
		
		// right column information
        $this->_data['leftCol'] = '
            <h3 class="brown">Malicious Content Report</h3>
            <p>We take the reporting of malicious content seriously, so should you.  Make sure your abuse report is viable as <span class="bold">' . ABUSE_TOLERATION_LEVEL . '</span> incidents will cause a person to lose their membership!</p>
            <p>This is a report for the following incident for <span class="bold">' . $abuse_type . '</span>:</p>
            <div id="abuseInformation">
                <ul>
                    <li>' . $info['username'] . '</li>
                    <li>' . $info['text'] . '</li>
                    <li>' . $info['date'] . '</li>
                </ul>
            </div>
            ' . $form
        ;
        
        // array of views and data
		$arr_load = array(
			'pages' => array('header_modal' => true, 'report' => true, 'footer_modal' => true)
			, 'data' => $this->_data
		);
		// load all parts for the view
		$this->doLoad($arr_load);
    }
}
?>