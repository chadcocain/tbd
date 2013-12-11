<?php
class Demo {
    private $_clean_date;
    private $_current_year;
    private $_arr_json_contents = array();
    private $_arr_years = array();
    private $_arr_links = array();
    
    private $_links = '';
    private $_options = '';
    private $_html = '';
    
    const _json_file_to_read = './sample.json';

    public function __construct($date = NULL) {
        $this->_set_clean_date($date);
    }

    public function process_Demo() {
        if($this->_read_json_Demo() === TRUE) {
            if(array_key_exists('comm_archive', $this->_get_arr_json_contents()) && !empty($this->_arr_json_contents['comm_archive'])) {
                $arr_years = array();
                $arr_link = array();
                foreach($this->_arr_json_contents['comm_archive'] as $index) {
                    $unix_time = strtotime($index['publish_date']); 
                    $year = date('Y', $unix_time);
                    $this->_set_arr_years($year);

                    $arr_link[$year][$unix_time] = '<h4><a href="/this_is_a_Test/archive.php?cdate=' . date('m-d-Y', $unix_time) . '">' . date('M Y', $unix_time) . ' (' . $index['counter1'] . ' entries)</a></h4>';
                }
                $this->_prepare_options();
                $this->_prepare_links($arr_link);
            }
        }
    }
    
    private function _prepare_links($links = NULL) {
        if(!empty($links)) {
            $keys = array_keys($links);
            foreach($this->_get_arr_years() as $year) {
                if(!in_array($year, $keys)) {
                    $links[$year][] = '';
                }
            }
            
            arsort($links, SORT_NUMERIC);
            foreach($links as $year => $uts) {
                arsort($uts);
                $links[$year] = $uts;
            }
            
            $arr_of_years = array();
            $html = '';
            foreach($links as $year => $uts) {
                $show = $this->_get_current_year() == $year ? ' show' : ' hide';
                $show = '';
                $css = $this->_get_current_year() == $year ? '' : ' style="display: none;"';
                $html .= '<div class="bddiv' . $show . '" id="d_' . $year . '"' . $css . '>';
                if(!empty($uts)) {
                    foreach($uts as $ts => $xhtml) {
                        $html .= !empty($xhtml) ? $xhtml : '<h4><a href="#">' . $year . ' - No entries available</a></h4>';
                    }
                }
                $html .= '</div>';
            }
            $this->_set_html($html);
        }
    }
    
    private function _prepare_options() {                       
        $this->_set_current_year(substr($this->_get_clean_date(), -4, 4));
        if(!in_array($this->_get_current_year(), $this->_get_arr_years())) {
            $this->_set_arr_years($this->_get_current_year());
        }
        
        if(!empty($this->_arr_years)) {
            arsort($this->_arr_years, SORT_NUMERIC);
            foreach($this->_get_arr_years() as $year) {
                $selected = $this->_get_current_year() == $year ? ' selected="selected"' : '';
                $this->_set_options('<option value="' . $year . '"' . $selected . '>' . $year . '</option>' . "\n");
            }
        }
    }

    private function _read_json_Demo() {
        if(file_exists(self::_json_file_to_read) && is_readable(self::_json_file_to_read)) {
            $file = file_get_contents(self::_json_file_to_read);
            if($file !== FALSE) {
                $json = json_decode($file, TRUE);
                if($json == true) {
                    $this->_set_arr_json_contents($json);
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

    private function _verify_date($date = NULL) {
        if(!empty($date) && strlen($date) == 10) {
            $pattern = '/^(?:0?[1-9]|1[012])-(?:0?[1-9]|[12][0-9]|3[01])-(?:20|19)[0-9]{2}$/';
            if(preg_match($pattern, $date)) {
                $this->_clean_date = $date;
            } else {
                $this->_clean_date = date('m-d-Y');
            }
        } else {
            $this->_clean_date = date('m-d-Y');
        }
    }

    private function _set_clean_date($date = NULL) {
        if(empty($date)) {
            $this->_clean_date = date('m-d-Y');
        } else {
            $this->_verify_date($date);
        }
    }
    
    private function _get_clean_date() {
        return $this->_clean_date;
    }

    private function _set_arr_json_contents($array = array()) {
        $this->_arr_json_contents = $array;
    }
    
    private function _get_arr_json_contents() {
        return $this->_arr_json_contents;
    }

    private function _get_Demo_file() {
        return $this->_Demo_file;
    }
    
    private function _set_current_year($year = NULL) {
        $this->_current_year = $year;
    }
    
    private function _get_current_year() {
        return $this->_current_year;
    }
    
    private function _set_arr_years($year = NULL) {
        if(!empty($year) && !in_array($year, $this->_get_arr_years())) {
            $this->_arr_years[] = $year;
        }
    }
    
    private function _get_arr_years() {
        return $this->_arr_years;
    }
    
    private function _set_options($string = NULL) {
        $this->_options .= $string;
    }
    
    public function get_options() {
        return $this->_options;
    }
    
    private function _set_arr_links($array = array()) {
        $this->_arr_links = $array;
    }
    
    public function get_arr_links() {
        return $this->_arr_links;
    }
    
    private function _set_html($html) {
        $this->_html = $html;
    }
    
    public function get_html() {
        return $this->_html;
    }
}
$cdate = isset($_GET['cdate']) ? $_GET['cdate'] : NULL;
$Demo = new Demo($cdate);
$Demo->process_Demo();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html version="-//W3C//DTD XHTML 1.1//EN" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.w3.org/1999/xhtml http://www.w3.org/MarkUp/SCHEMA/xhtml11.xsd">
<head>
<title>Zacks Test</title>
<link rel="stylesheet" type="text/css" media="all" href="./style.css" />
<script type="text/javascript">
/*<![CDemo[*//*---->*/
window.onload = function() {
    document.getElementById('optionCdate').onchange = toggle;
}

function toggle() {
    var year = this.options[this.selectedIndex].value;
    var divs = document.getElementsByTagName('DIV');    
    for(var i = 0; i < divs.length; i++) {
        if(divs[i].id.indexOf('d_') != -1) {
            if(divs[i].id == 'd_' + year) {
                divs[i].style.display = 'block';
            } else {
                divs[i].style.display = 'none';
            }
        }
    }
}
/*--*//*]]>*/
</script>
</head>
<body>

<div class="unit size1of1 lastUnit">
    <div class="mod trademod z_mod_turnaround">
        <div class="hd">
            <h2>Archive</h2>
        </div>
    
        <div class="bddiv">
            <p>
                Select Year :
                <select name="optionCdate" id="optionCdate">
                    <?php echo $Demo->get_options(); ?>
                </select>
            </p>
        </div>
        <?php echo $Demo->get_html(); ?>
    </div>
</div>
</body>
</html>