
	<div id="wrapper">
		<div id="container_left">
			<div id="contents">
<?php
if (isset($problem))
{
    echo '
                <h2 class="brown">Update Information</h2>
				<p class="marginTop_8">There was a problem trying to process your request.</p>
    ';
}
else
{
    echo '
                <h2 class="brown">Update Info for: ' . $name . '</h2>    
    ';
    
    if ($display == 1)
    {
        echo '
                <p class="marginTop_8">Your information has been sent to Rich and Scot at Two Beer Dudes.  We will review the submitted informaton and go from there.  Thank you for your input.  Enjoy!</p>
        ';
    }
    else
    {
        echo $display;        
    }
}
?>
			</div>
		</div>
		<div id="container_right">
			<div class="sideInfo">
                <h4><span>Keep the Information Fresh</span></h4>
				<ul>
					<li>We will do our best to make updates in a timely fashion but we have to make sure the update is accurate, which could take some time.</li>
					<li>Don&#39;t submit an update because you don&#39;t agree with something on the site.  Research, double checking to make sure you have a valid reason for the update.</li>
				</ul>
			</div>
		</div>
		<br class="both" />
	</div>
	