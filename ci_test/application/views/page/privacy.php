<?php
    if (isset($header))
    {
        $this->load->view($header);
    }
    
    if (isset($form_mast))
    {
        $this->load->view($form_mast);
    }
    
    if (isset($navigation))
    {
        $this->load->view($navigation);
    }
?>

	<div id="wrapper">
		<div id="container_left">
			<div id="contents">
                <div id="agreement">
                    <h2 class="brown">Two Beer Dudes Website Privacy Statement</h2>
                    <p>This document was last updated on 2010-08-12.</p>
                    <ol class="agreement">
                        <li>
                            <p class="bold">ACKNOWLEDGEMENT AND ACCEPTANCE OF TERMS</p>
                            <p>Two Beer Dudes is committed to protecting your privacy. This Privacy Statement sets forth our current privacy practices with regard to the information we collect when you or your computer interact with our website.   By accessing www.twobeerdudes.com, you acknowledge and fully understand Two Beer Dudes&#39;s Privacy Statement and freely consent to the information collection and use practices described in this Website Privacy Statement.</p>
                        </li>
                        <li>
                            <p class="bold">PARTICIPATING MERCHANT POLICIES</p>
                            <p>Related services and offerings with links from this website, including vendor sites, have their own privacy statements that can be viewed by clicking on the corresponding links within each respective website. Online merchants and others who participate in Two Beer Dudes services are encouraged to participate in industry privacy initiatives and to take a responsible attitude towards consumer privacy.  However, since we do not have direct control over the policies or practices of participating merchants and other third parties, we are not responsible for the privacy practices or contents of those sites.  We recommend and encourage that you always review the privacy policies of merchants and other third parties before you provide any personal information or complete any transaction with such parties.</p>
                        </li>
                        <li>
                            <p class="bold">INFORMATION WE COLLECT AND HOW WE USE IT</p>
                            <p>Two Beer Dudes collects certain information from and about its users three ways: directly from our Web Server logs, the user, and with Cookies.</p>
                            <ol>
                                <li>
                                    <p class="bold"> Web Server Logs.</p>
                                    <p>When you visit our Website, we may track information to administer the site and analyze its usage. Examples of information we may track include:</p>
                                    <ul>
                                        <li>Your Internet protocol address.</li>
                                        <li>The kind of browser or computer you use.</li>
                                        <li>Number of links you click within the site.</li>
                                        <li>State or country from which you accessed the site.</li>
                                        <li>Date and time of your visit.</li>
                                        <li>Name of your Internet service provider.</li>
                                        <li>Web page you linked to our site from.</li>
                                        <li>Pages you viewed on the site.</li>
                                    </ul>
                                </li>
                                <li>
                                    <p class="bold">Use of Cookies</p>
                                    <p>Two Beer Dudes may use cookies to personalize or enhance your user experience.  A cookie is a small text file that is placed on your hard disk by a Web page server.  Cookies cannot be used to run programs or deliver viruses to your computer.  Cookies are uniquely assigned to you, and can only be read by a Web Server in the domain that issued the cookie to you.</p>
                                    <p>One of the primary purposes of cookies is to provide a convenience feature to save you time.  For example, if you personalize a web page, or navigate within a site, a cookie helps the site to recall your specific information on subsequent visits.  Hence, this simplifies the process of delivering relevant content and eases site navigation by providing and saving your preferences and login information as well as providing personalized functionality. </p>
                                    <p>You have the ability to accept or decline cookies.  Most Web browsers automatically accept cookies, but you can usually modify your browser setting to decline cookies. If you reject cookies by changing your browser settings then be aware that this may disable some of the functionality on our Website.</p>
                                </li>
                            </ol>
                        </li>
                        <li>
                            <p class="bold">ACCESSING WEB ACCOUNT INFORMATION</p>
                            <p>We will provide you with the means to ensure that personally identifiable information in your web account file is correct and current via the web site itself</p>
                        </li>
                        <li>
                            <p class="bold">CHANGES TO THIS STATEMENT</p>
                            <p>Two Beer Dudes has the discretion to occasionally update this privacy statement.  When we do, we will also revise the &#34;updated&#34; date at the top of this Privacy page.  We encourage you to periodically review this privacy statement to stay informed about how we are helping to protect the personal information we collect.  Your continued use of the service constitutes your agreement to this privacy statement and any updates.</p>
                        </li>
                        <li>
                            <p class="bold">CONTACTING US</p>
                            <p>If you have questions regarding our Privacy Statement, its implementation, failure to adhere to this Privacy Statement and/or our general practices, please contact us: <script type="text/javascript">document.write('web' + 'master'); document.write('@' + 'two'); document.write('beerdudes' + '.' + 'com');</script>.  Two Beer Dudes will use commercially reasonable efforts to promptly respond and resolve any problem or question.
                            </p>
                        </li>
                    </ol>            
                </div>
			</div>
		</div>
		<div id="container_right">
			<div class="sideInfo">
                <h4><span>Download Privacy Policy</span></h4>
                <ul>
                    <li><a href="<?php echo base_url(); ?>files/privacy-statement.txt">plain text</a></li>
                    <li><a href="<?php echo base_url(); ?>files/privacy-statement.pdf">pdf</a></li>
                </ul>
			</div>
		</div>
		<br class="both" />
	</div>
    
<?php
    if (isset($footer))
    {
        $this->load->view($footer);
    }
?>
