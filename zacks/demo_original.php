<?php
if(!defined('JSON_FILE_TO_READ')) {
    define('JSON_FILE_TO_READ', './sample.json');
}

$file = file_get_contents(JSON_FILE_TO_READ);

$str_options = '';
if($file !== FALSE) {
    $json = json_decode($file, TRUE);
    //echo '<pre>'; print_r($json);
    if($json == true) {
        if(array_key_exists('comm_archive', $json) && !empty($json['comm_archive'])) {
            $arr_years = array();
            foreach($json['comm_archive'] as $index) {
                if(!in_array($index['year'], $arr_years)) {
                    $arr_years[] = $index['year'];
                }
                
                
                
                //echo $index['year'] . '<br />';
            }
            if(!empty($arr_years)) {
                sort($arr_years, SORT_NUMERIC);
                $arr_years = array_reverse($arr_years);
                foreach($arr_years as $year) {
                    $str_options .= '<option value="' . $year . '">' . $year . '</option>';
                }
            }
        }
    } else {
        echo 'json error';
    }
} else {
    echo 'cannot read file';
}
//$array_unique_years = array_unique($array_years, SORT_NUMERIC);
//echo '<pre>'; print_r($array_unique_years);
?>
<html>
<head>
<link rel="stylesheet" type="text/css" media="all" href="./style.css" />
<script type="text/javascript">

</script>
</head>
<body>

<div class="unit size1of1 lastUnit">
    <div class="mod trademod z_mod_turnaround">
        <div class="hd">
            <h2>Archive</h2>
        </div>
    
        <div class="bddiv">
            <p>Select Year :
            <select name="optionCdate" id="optionCdate">
                <!--<option value="2011" selected="selected">2011</option>
                <option value="2010">2010</option>
                <option value="2010">2009</option>-->
                <?php echo $str_options; ?>
            </select>
        </div>
        <div class="bddiv show" id="d_2011">
            <h4><a href="/this_is_a_Test/archive.php?cdate=07-01-2011">Jul 2011 (7 entries)</a></h4>
        </div>
        <div class="bddiv hide" id="d_2010">
            <h4><a href="/this_is_a_Test/archive.php?cdate=12-08-2010">Dec 2010 (16 entries)</a></h4>
        </div>
        <div class="bddiv hide" id="d_2009">
            <h4><a href="/this_is_a_Test/archive.php?cdate=12-08-2009">Dec 2009 (16 entries)</a></h4>
        </div>
    </div>
</div>

</body>
</html>