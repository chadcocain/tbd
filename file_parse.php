<?php
    require_once 'file_parse_abstract.php';
    
    /**
    * File Parse class that extends File_Parse
    * 
    * Example:
    * $config = array
    * (
    *   'file_path' => './',
    *    file_name' => 'Week11.txt'        
    * );
    * $file_parse = new File_Parse_TSV($config);
    * echo $file_parse->display();
    * 
    * @author Scot Schlinger
    * @version 0.1
    * @since 2013-10-24
    */
    class File_Parse extends File_Parse_Abstract
	{
        /**
        * Constructor
        * 
        * @access public
        * @param array $config
        * @return File_Parse
        */
		public function __construct($config = array())
		{
			parent::__construct($config);
        }
        
        /**
        * Handles the processing of the file: open, read, and close
        * 
        * @access protected
        * @return void
        */
        protected function _process()
        {
            $this->_file_readable();
            $this->_file_open();
            
            $this->_set_file_contents(fread($this->_file_handle, filesize($this->get_file_path() . $this->get_file_name())));
            
            $this->_file_close();
        }
        
        /**
        * Display the contents of the file just as they appear in the file.
        * 
        * @access public
        * @return string
        */
        public function display()
        {
            $this->_process();
            return $this->_get_file_contents();
        }
        
        /**
        * Set the value of a file contents
        * 
        * @access protected
        * @param string $str
        * @return void
        */
        protected function _set_file_contents($str)
        {
            $this->_file_contents = trim(nl2br($str));
        }
	}
?>