<?php	
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

function createAbuseForm($config = array()) {
    $report = (key_exists('ttr_report', $config) && empty($_POST)) ? $config['ttr_report'] : set_value('ttr_report');
		
	// get the code igniter instance
	$ci =& get_instance();
	
	$form = '
		<form action="' . base_url() . ltrim($config['uri'], '/') . '" method="post" id="modal_form">
			<div class="formBlock">
	';
	if(form_error('ttr_report')) {
		$form .= '<div class="formError">' . form_error('ttr_report') . '</div>';
	}
	$form .= '
				<label for="ttr_report"><span class="required">*</span> Justification:</label>
				<textarea id="ttr_report" name="ttr_report">' . $report . '</textarea>
			</div>
			
		    <input type="submit" id="btn_submit" name="submit" value="Report Abuse" />
		</form>
	';
	
	return $form;
}
?>