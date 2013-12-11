<?php
    require_once 'file_parse.php';
    
    /**
    * File Parse TSV class that extends File_Parse
    * 
    * Example:
    * * Example:
    * $config = array
    * (
    *   'file_path' => './',
    *    file_name' => 'Week11.txt'        
    * );
    * $file_parse = new File_Parse($config);
    * echo $file_parse->display();
    * 
    * @author Scot Schlinger
    * @version 0.1
    * @since 2013-10-24
    */
    class File_Parse_TSV extends File_Parse
    {
        /**
        * The total number of hits for all browsers
        * 
        * @access private
        * @var integer
        */
        private $_total = 0;
        
        /**
        * The contents of the file that is to processed
        * 
        * @access protected
        * @var array
        */
        protected $_file_contents = array();
        
        /**
        * Constructor
        * 
        * @access public
        * @param array $config
        * @return File_Parse_TSV
        */
        public function __construct($config)
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
            $this->_iterate_file();
            $this->_file_close();
        }
        
        /**
        * Iterate over the file contents.  Two things are accomplished: 
        * 1. Raw data for display for each browser
        * 2. Total hits is continually calculated
        * 
        * @access private
        * @return void
        */
        private function _iterate_file()
        {
            while (!feof($this->_file_handle))
            {    
                $parts = str_getcsv(fgets($this->_file_handle), "\t");
                
                if (count($parts) >= 3)
                {
                    $hits = $this->_convert_to_integer($parts[2]);
                    $this->_set_total($hits);
                    $this->_set_file_contents(array($parts[0], $parts[1], $hits));
                }
            }
        }
        
        /**
        * Display the contents of the file, including totalling the number of hits to display the overall percentage
        * that each browser was used.
        * 
        * @access public
        * @return string
        */
        public function display()
        {
            $this->_process();
                        
            $display = 'Results for the file (' . $this->get_file_name() . ') could not be tabulated';
            if (!empty($this->_file_contents))
            {
                $display = '
                    <table>
                        <caption>Results for ' . $this->get_file_name() . '</caption>
                        <tr>
                            <th width="50">&nbsp;</th>
                            <th width="240">Browswer</th>
                            <th width="70">% Usage</th>
                        </tr>
                ';
                foreach($this->_get_file_contents() as $key)
                {
                    $display .= '
                        <tr>
                            <td>' . $key[0] . '</td>
                            <td>' . $key[1] . '</td>
                            <td>' . ($this->get_total() == 0 ? 0 : number_format(($key[2] / $this->get_total()) * 100, 2)) . '%</td>
                        </tr>
                    ';
                }
                $display .= '
                    </table>
                ';
            }
            return $display;
        }
        
        /**
        * Converted the value in the file from a string, potentially with commas, into an integer
        * 
        * @access private
        * @param string $value
        * @return integer
        */
        private function _convert_to_integer($value)
        {
            return (integer) str_replace(',', '', $value);
        }
        
        /**
        * Add the current passed value to the overall total
        * 
        * @access private
        * @param integer $integer
        */
        private function _set_total($integer)
        {
            $this->_total += $integer;
        }
        
        /**
        * Return the total hits
        * 
        * @access public
        * @return integer
        */
        public function get_total()
        {
            return $this->_total;
        }
        
        /**
        * Set the value of a file contents
        * 
        * @access protected
        * @param array $array
        * @return void
        */
        protected function _set_file_contents($array)
        {
            $this->_file_contents[] = $array;
        }
    }
?>