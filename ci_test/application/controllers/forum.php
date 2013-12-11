<?php
class Forum extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper(array('url', 'admin', 'users', 'form', 'js', 'forum'));
        // load the forum model
        $this->load->model('ForumModel', '', true);
        // helper to get the quote for the footer - in users_helper.php
        getFooterQuote();
    }
    
    private function doLoad($config) {
        $array = array(
            'headerFrontEnd' => 'inc/header_frontend.inc.php'
            , 'formMast' => 'inc/formMast.inc.php'
            , 'navigation' => 'inc/navigation.inc.php'
            , 'index' => 'forum/index.php'
            , 'sub_topic_index' => 'forum/sub_topic_index.php'
            , 'add_thread' => 'forum/add_thread.php'
            , 'display_thread' => 'forum/display_thread.php'
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
    
    public function index() {
        // get the login boolean
        $logged = checkLogin();
        // user info for logged in user
        $userInfo = $this->session->userdata('userInfo');        
        // create login mast text
        $this->_data['formMast'] = createHeader($logged, $userInfo);
        
        // load the forums library
        $this->load->library('forums');
        
        $data = $this->forums->getAllForums();
        $this->_data['leftCol'] = $data['leftCol'];
        $this->_data['rightCol'] = $data['rightCol'];
        $this->_data['seo'] = $data['seo'];
        
        //echo '<pre>'; print_r($data); echo '</pre>'; exit;
        
        // get the information ready for display
        $arr_load = array(
            'pages' => array(                
                'headerFrontEnd' => true
                , 'formMast' => true
                , 'navigation' => true
                , 'index' => true
                , 'footerFrontEnd' => true
            )
            , 'data' => $this->_data
        );            
        // load all parts for the view
        $this->doLoad($arr_load);
    }
    
    /**
    * Display Sub Topic and threads
    * 
    */
    public function dst() {
        // get the login boolean
        $logged = checkLogin();
        // user info for logged in user
        $userInfo = $this->session->userdata('userInfo');        
        // create login mast text
        $this->_data['formMast'] = createHeader($logged, $userInfo);
        
        // load the forums library
        $this->load->library('forums');
        
        $data = $this->forums->getSubTopic();
        $this->_data['leftCol'] = $data['leftCol'];
        $this->_data['rightCol'] = $data['rightCol'];
        $this->_data['seo'] = $data['seo'];
        
        //echo '<pre>'; print_r($data); echo '</pre>'; exit;
        
        // get the information ready for display
        $arr_load = array(
            'pages' => array(                
                'headerFrontEnd' => true
                , 'formMast' => true
                , 'navigation' => true
                , 'sub_topic_index' => true
                , 'footerFrontEnd' => true
            )
            , 'data' => $this->_data
        );            
        // load all parts for the view
        $this->doLoad($arr_load);    
    }
    
    /**
    * Show thread - show a particular thread
    * 
    */
    public function st() {
        // get the login boolean
        $logged = checkLogin();
        // user info for logged in user
        $userInfo = $this->session->userdata('userInfo');        
        // create login mast text
        $this->_data['formMast'] = createHeader($logged, $userInfo);
        // load the forums library
        $this->load->library('forums');

        $data = $this->forums->getThread();                
        
        $this->_data['leftCol'] = $data['leftCol'];
        $this->_data['seo'] = $data['seo'];
        //$this->_data['rightCol'] = $data['rightCol'];
        //echo '<pre>'; print_r($this->_data); exit;
        // get the information ready for display
        $arr_load = array(
            'pages' => array(                
                'headerFrontEnd' => true
                , 'formMast' => true
                , 'navigation' => true
                , 'display_thread' => true
                , 'footerFrontEnd' => true
            )
            , 'data' => $this->_data
        );            
        // load all parts for the view
        $this->doLoad($arr_load);
    }
    
    /**
    * Add a new thread or reply to thread
    * 
    */
    public function atr() { 
        // get the login boolean
        $logged = checkLogin();
        // user info for logged in user
        $userInfo = $this->session->userdata('userInfo');
        // create login mast text
        $this->_data['formMast'] = createHeader($logged, $userInfo);
        
        // check if user is logged in
        if($logged === true) {
            // load the libraries
            $this->load->library('form_validation');
            
            // get the type of post this will be
            $otype = $this->uri->segment(3);
            $type = '';
            // get the id value of the forum or the post to reply to
            $sub_topic_id = $this->uri->segment(4);
            // get the id of the thread being replied to (no necessarily there)
            $thread_id = $this->uri->segment(5);
            
            // get the query string as an associative array
            $assoc = $this->uri->segment_array();
            $total = $this->uri->total_segments();
            // check if this is a reply with a quote
            $quote = false;
            $quote_id = 0;
            if(in_array('q', $assoc) && array_search('q', $assoc) < $total) {
                $quote = true;
                $quote_id = $assoc[array_search('q', $assoc) + 1];
            }
            
            // holder for info
            $info = array();
            // holder for reply info
            $reply_info = array();
            // holders for closed thread
            $closed = false;
            $closed_text = '';
            // check if either of the above are empty            
            if(empty($otype) || empty($sub_topic_id) || ($otype == 'rp' && $thread_id === false)) {
                header('Location: ' . base_url() . 'forum');
                exit;
            } else {
                // check make sure they exist in the db
                $info = $this->ForumModel->getSubTopicInfoByID($sub_topic_id);
                if(empty($info)) {
                    header('Location: ' . base_url() . 'forum');
                    exit;
                } 
                
                // check if the thread_id exists if this is a reply
                if($otype == 'rp') { 
                    $reply_info = $this->ForumModel->checkThreadExists($thread_id, $sub_topic_id);
                    if(empty($reply_info)) {
                        header('Location: ' . base_url() . 'forum');
                        exit;
                    }
                    // if it does, then check if the quote is needed
                    if($quote === true) {
                        // get the quote from the post
                        $quote = $this->ForumModel->getQuote($quote_id);
                        // check, again, if we got anything
                        if($quote !== false) {
                            $quote = trim(preg_replace('#\[q\](.*?)\[\/q\]#si', '', $quote));
                        }
                    }
                    
                    // check if the thread is closed                    
                    if(!empty($reply_info) && $reply_info['closed'] == 1) {
                        $closed = true;
                        $closed_text = '<p class="green marginTop_8 bold">No more replies accepted as this draft is closed.</p>';
                    }
                }
            }
            
            //echo '<pre>'; print_r($quote); exit;
            
            // checked if thread is closed
            if($closed === true) {
                $this->_data['leftCol'] = $closed_text;    
            } else {            
                switch($otype) {
                    case 'nt':
                        // new thread
                        $type = 'new_thread';
                        break;
                    case 'rp':
                        // reply to a thread
                        $type = 'reply_thread';
                        break;
                }
                
                // run the validation and return the result
                if($this->form_validation->run($type) == false) {
                    // holder for configuration values to send to form
                    $config = array();
                    // see if the post array is set
                    if(!empty($_POST)) {
                        $config['subject'] = ($type == 'new_thread' ? $_POST['txt_subject'] : '');
                        $config['message'] = $_POST['ttr_message'];
                    } else if($quote !== false) {
                        $config['message'] = '[q]' . $quote . '[/q]' . "\n";
                    }
                    $config['otype'] = $otype;
                    $config['type'] = $type;
                    $config['id'] = $sub_topic_id;
                    $config['topic_id'] = $info->id;
                    $config['sub_topic_name'] = $info->sub_topic_name;
                    $config['description'] = $info->sub_topic_desc;
                    $config['topic_name'] = $info->name;
                    $config['thread_id'] = ($thread_id !== false ? $thread_id : '');
                    $config['thread_subject'] = (array_key_exists('subject', $reply_info) ? $reply_info['subject'] : '');
                    //}
                    // set the introduction text
                    $this->_data['leftCol'] = createThreadForm($config);
                } else {
                    $message = trim(strip_tags($_POST['ttr_message']));
                    //$message = $this->formatMessage($_POST['ttr_message'], $quote);
                    // check the message for multiple continuous \n and get rid of
                    /*$msg_parts = explode("\n", $message);
                    // holder for the message
                    $message = '';
                    // now piece back together w/out the extra \n
                    foreach($msg_parts as $msg) {
                        // check that this isn't a quote as it shouldn't need formatting
                        if(substr($msg, 0, 3) != '[q]') {
                            $msg = trim(str_replace("\n", '', $msg));
                        }     
                        if($msg != '' && $msg != "\n") {  
                            if(!empty($message)) {
                                $message .= "\n";
                            }
                            $message .= $msg;
                        }
                    }*/
                    /*if($quote !== false) {
                        $stristr = stristr($message, '[/q]');
                        $msg_tmp = '';
                        if($stristr !== false) {
                            $msg_tmp = substr($stristr, 4);    
                        } else {
                            $msg_tmp = $message;
                        }
                        $message = str_replace("\n\n", "\n", $message);    
                    } else {
                        $messsage = trim($message);
                    } */                   
                                               
                    // create the new thread or add a reply
                    $data = array(
                        'remote_addr' => $_SERVER['REMOTE_ADDR']
                        , 'subject' => ($type == 'new_thread' ? $_POST['txt_subject'] : '')
                        , 'message' => $message
                        , 'sub_topic_id' => $sub_topic_id
                        , 'type' => $type
                        , 'user_id' => $userInfo['id']
                        , 'thread_id' => ($thread_id !== false ? $thread_id : '')
                    );
                    // add the new thread
                    $insert_id = $this->ForumModel->createNewThread($data);
                    
                    // check if this is a reply to send out notices of the reply
                    if($otype == 'rp') {
                        // process the users to send a notice to
                        $this->send_reply_notice($thread_id, $userInfo, $sub_topic_id, $insert_id);
                    }
                    
                    // determine the values for the url based on type of thred
                    $uri = $type == 'new_thread' ? $sub_topic_id . '/' . $insert_id : $sub_topic_id . '/' . $thread_id . '#' . $insert_id;
                    // send them to the thread
                    header('Location: ' . base_url() . 'forum/st/' . $uri);
                }
            }
        } else {
            // they should't be able to get here
            header('Location: ' . base_url() . 'user/login');
            exit;
        }
        
        // get the information ready for display
        $arr_load = array(
            'pages' => array(                
                'headerFrontEnd' => true
                , 'formMast' => true
                , 'navigation' => true
                , 'add_thread' => true
                , 'footerFrontEnd' => true
            )
            , 'data' => $this->_data
        );            
        // load all parts for the view
        $this->doLoad($arr_load);
    }
    
    private function formatMessage($message, $quote = false) {
        $message = trim(strip_tags($message));
        // check the message for multiple continuous \n and get rid of
        /*$msg_parts = explode("\n", $message);
        // holder for the message
        $message = '';
        // now piece back together w/out the extra \n
        foreach($msg_parts as $msg) {
            // check that this isn't a quote as it shouldn't need formatting
            if(substr($msg, 0, 3) != '[q]') {
                $msg = trim(str_replace("\n", '', $msg));
            }     
            if($msg != '' && $msg != "\n") {  
                if(!empty($message)) {
                    $message .= "\n";
                }
                $message .= $msg;
            }
        }*/
        $store = '';
        if($quote !== false) {
            $stristr = stristr($message, '[/q]');
            if($stristr !== false) {
                $store = substr($message, 0, strpos($message, '[/q]'));
                $message = substr($stristr, 4);    
            }
            //$message = str_replace("\n\n", "\n", $message);    
            /*$msg_parts = explode("\n", $message);
            // holder for the message
            $message = '';
            foreach($msg_parts as $msg) {
                // check that this isn't a quote as it shouldn't need formatting
                if(substr($msg, 0, 3) != '[q]') {
                    $msg = trim(str_replace("\n", '', $msg));
                }     
                if($msg != '' && $msg != "\n") {  
                    if(!empty($message)) {
                        $message .= "\n";
                    }
                    $message .= $msg;
                }
            }*/
        }
        
        $message = $store . $this->removeMultipleNewLine($message);
        
        return $message;    
    }
    
    private function removeMultipleNewLine($message) {
        $tmp = str_replace("\n\n", "\n", $message);
        if($tmp == $message) {
            return $message;
        } else {
            return $this->removeMultipleNewLine($tmp);
        }
    }
    
    private function send_reply_notice($thread_id = 0, $user_info = array(), $sub_topic_id, $insert_id) {
        // make sure the id is not 0
        if($thread_id > 0 && !empty($user_info)) {
            // get the email addresses to send the mail to
            $email_list = $this->ForumModel->get_email_for_thread($thread_id, $user_info['id']);
            // check that there is someone to send to
            if(!empty($email_list)) {
                // get the original thread subject    
                $thread_info = $this->ForumModel->checkThreadExists($thread_id, $sub_topic_id);
                // create the message of the email
                $msg = '<p style="margin-top: 1.0em;">' . $user_info['username'] . ' has made a reply to "' . $thread_info['subject'] . '" on Two Beer Dudes.'; 
                $msg .= '  Check out what he/she had to say:</p>';
                $msg .= '<p style="margin-top: 1.0em;"><a href="' . base_url() . 'forum/st/' . $sub_topic_id . '/' . $thread_id . '#' . $insert_id . '">' . base_url() . 'forum/st/' . $sub_topic_id . '/' . $thread_id . '#' . $insert_id . '</a></p>';
                $subject = 'Two Beer Dudes forum reply';
                
                // iterate through the people that need the update
                foreach($email_list as $key) {
                    $msg = '<p>Hi ' . $key['username'] . ',</p>' . $msg;
                    $headers = 'MIME-Version: 1.0' . "\r\n" .
                        'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                        'From: webmaster@twobeerdudes.com' . "\r\n" .
                        'Reply-To: webmaster@twobeerdudes.com' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();
                    mail($key['email'], $subject, $msg, $headers);
                }
            }
        }    
    }
}
?>