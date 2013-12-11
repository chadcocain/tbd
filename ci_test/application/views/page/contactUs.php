
	<div id="wrapper">
		<div id="container_left">
			<div id="contents">
                <h2 class="brown">Contact The Two Beer Dudes</h2>
<?php
if ($show_form)
{
    echo form_contactUs(array());
}
else
{
    echo '<p class="marginTop_8">Your information has been sent to Rich and Scot at Two Beer Dudes.  
        They will try and get back to you in a timely fashion.  In the meantime go 
        <a href="' . base_url() . 'beer/review">rate</a> some beers.  Enjoy!</p>
    ';
}
?>                
			</div>
		</div>
		<div id="container_right">
			<div class="sideInfo">
                <h4><span>Drop Two Beer Dudes A Line</span></h4>
                <ul>
                    <li>We are always interested to get feedback from our visitors.</li>
                    <li>Please offer up some suggestions for improvements to the site.  There is no guarantee we will make the improvement or when we will finish the changes.  We just like to know there are people out there thinking about us.</li>
                </ul>
			</div>
		</div>
		<br class="both" />
	</div>
