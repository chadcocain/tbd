<?php
$nav = '
	<div id="contents_right">
		<ul id="navigation">
			<li class="navTitle">Breweries</li>
			<li class="navLink"><a href="' . base_url() . 'admin/edit/brewery">Edit</a></li>
			<li class="navLink"><a href="' . base_url() . 'admin/add/brewery">Add</a></li>
			<li class="navTitle">Beers</li>
			<li class="navLink"><a href="' . base_url() . 'admin/edit/beer">Edit</a></li>
			<li class="navLink"><a href="' . base_url() . 'admin/add/beer">Add</a></li>
			<li class="navTitle">Ratings</li>
			<li class="navLink"><a href="' . base_url() . 'admin/edit/rating">Edit</a></li>
			<li class="navLink"><a href="' . base_url() . 'admin/add/rating">Add</a></li>
			<li class="navLink"><a href="' . base_url() . 'admin/view/myRatings">View My Ratings</a></li>
		</ul>
	</div>
	<br class="both" />
</div>
';
echo $nav;
?>
	
		
	