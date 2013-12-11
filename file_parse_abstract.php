<?php
    /**
    * File Parse Abstract and Base class for the File Parse family
    * 
    * @author Scot Schlinger
    * @version 0.1
    * @since 2013-10-24
    * @abstract
    */
    abstract class File_Parse_Abstract
    {
        /**
        * Path that the file is located
        * 
        * @access protected
        * @var string
        */
        protected $_file_path = '';
        
        /**
        * Name of the file
        * 
        * @access protected
        * @var string
        */
        protected $_file_name = '';
        
        /**
        * Open file resource
        * 
        * @access protected
        * @var resource
        */
        protected $_file_handle = NULL;
        
        /**
        * Contents of the file
        * 
        * @access protected
        * @var mixed
        */
        protected $_file_contents = '';

        /**
        * Constuctor.  The passed values are matched against class methods and vars, set appropriately.
        * 
        * @access public
        * @param array $config
        * @return File_Parse_Abstract Child
        */
        public function __construct($config = array())
        {
            if (is_array($config) && count($config) > 0)
            {
                $properties = array_keys(get_class_vars(get_class($this)));
                foreach ($config as $key => $value)
                {
                    if (in_array('_' . $key, $properties))
                    {
                        if (method_exists($this, '_set_' . $key))
                        {
                            $this->{'_set_' . $key}($value);
                        }
                    }
                }
            }
        }
        
        /**
        * Process method to be deteremined by each non-abstract child
        * 
        * @access protected
        * @abstract
        */
        abstract protected function _process();
        
        /**
        * Display method to be determined by each non-abstract child
        * 
        * @access public
        * @abstract
        */
        abstract public function display();
        
        /**
        * Opened the file resource
        * 
        * @access protected
        * @return void
        */
        protected function _file_open()
        {
            $this->_file_handle = fopen($this->get_file_path() . $this->get_file_name(), 'r+');
        }
        
        /**
        * Closed the file resources
        * 
        * @access protected
        * @return void
        */
        protected function _file_close()
        {
            fclose($this->_file_handle);
        }
        
        /**
        * Checked that the file exists and is readable.  Throws an exception otherwise.
        * 
        * @access protected
        * @return void
        */
        protected function _file_readable()
        {
            if (!is_readable($this->get_file_path() . $this->get_file_name()))
            {
                $this->_throw_exception(__FILE__, __LINE__, 'File does not exist and/or is not readable: ' . $file);
            }
        }

        /**
        * Set the file path.  Will throw an excpetion if the value is empty
        * 
        * @access protected
        * @param string $str
        * @return void
        */
        protected function _set_file_path($str)
        {
            if (!empty($str))
            {
                $this->_file_path = $str;
            }
            else
            {
                $this->_throw_exception(__FILE__, __LINE__, 'File path is empty');
            }
        }

        /**
        * Return the file path.  Will throw an exception if the value is empty
        * 
        * @access public
        * @return void
        */
        public function get_file_path()
        {
            if (empty($this->_file_path))
            {
                $this->_throw_exception(__FILE__, __LINE__, 'File path is empty');
            }
            return $this->_file_path;
        }

        /**
        * Set the file name.  Will throw and exception if the file_name is empty
        * 
        * @access protected
        * @param string $str
        * @return void
        */
        protected function _set_file_name($str)
        {
            if (!empty($str))
            {
                $this->_file_name = $str;
            }
            else
            {
                $this->_throw_exception(__FILE__, __LINE__, 'File name is empty');
            }
        }

        /**
        * Return the file name.  Will throw an exception if the file_name is empty.
        * 
        * @access public
        * @return string
        */
        public function get_file_name()
        {
            if (empty($this->_file_name))
            {
                $this->_throw_exception(__FILE__, __LINE__, 'File path is empty');
            }
            return $this->_file_name;
        }
        
        /**
        * Throws an exception that will display the file, line number, and error
        * 
        * @access protected
        * @param string $file
        * @param string $line
        * @param string $str_error
        * @return void
        */
        protected function _throw_exception($file, $line, $str_error)
        {
            if (!empty($str_error))
            {
                throw new Exception('file: ' . $file . ', line: ' . $line . ', error: ' . $str_error);
            }
        }
        
        /**
        * put your comment there...
        * 
        * @access protected
        * @param mixed $str
        * @abstract
        */
        abstract protected function _set_file_contents($str);
        
        /**
        * Return the file contents
        * 
        * @access protected
        * @return mixed
        */
        protected function _get_file_contents()
        {
            return $this->_file_contents;
        }
    }
?>