<?php
function formatPhone($num) {
	$phone = 'N/A';
	if(!empty($num)) {
		$phone = '(' . substr($num, 0, 3) . ') ' . substr($num, 3, 3) . '-' . substr($num, 6, 4);
	} /*else {
		$phone = 'N/A';
	}*/
	return $phone;	
}
?>