<?php
$upperRight = '
		<ul>
			<li>Welcome back ' . $session['firstName'] . '</li>
			<li>Last visit: ' . $session['formatLastLogin'] . '</li>
			<li><a href="' . base_url() . 'admin/logout">logout</a></li>
		</ul>
';

$str_pageHeader = !empty($pageHeader) ? '<div id="pageHeader"><h2>' . $pageHeader . '</h2></div>' : '';
?>
<div id="container">
	<div id="header">
		<h2>Two Beer Dudes</h2>
		<?php echo $upperRight; ?>
		<br class="both" />
	</div>

	<div id="contents_left">
		<?php echo $str_pageHeader; ?>
		<div id="contents">