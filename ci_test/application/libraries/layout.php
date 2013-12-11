<?php
    if(!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }
    
    class Layout
    {
        private $_ci;
        
        public function __construct()
        {
            $this->_ci =& get_instance();
        }
        
        public function render($view, $data, $full_layout)
        {
            if (!empty($view) && !empty($full_layout))
            {
                $rendered = $this->_ci->load->view($view, $data, TRUE);
                return $this->_ci->load->view('full/' . $full_layout, array('page_content' => $rendered, 'data' => $data));
            }
        }
    }
?>