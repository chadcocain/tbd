<?php
if(!function_exists('showJS')) {
	function showJS($config = array()) {
		$ci =& get_instance();
		$controller = $ci->uri->segment(1) == false ? 'page' : $ci->uri->segment(1);
		$method = $ci->uri->segment(2) == false ? 'index' : $ci->uri->segment(2);
		
		$js = '';
		if($controller == 'admin' && ($method == 'edit' || $method == 'add')) {
			$js = '
<script type="text/javascript" src="' . base_url() . 'js/scriptaculous.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/calendarview.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/scripts.js"></script>
<script type="text/javascript">
/*<![CDATA[*/
function showSpinner(id) {
	$(id).update(\'<img src="' . base_url() . 'images/spinner.gif" style="margin: 1.0em auto; display: block; width: 16px; height: 16px;" />\');
}
/*]]>*/
</script>
<link rel="stylesheet" type="text/css" href="' . base_url() . 'css/calendarview.css" />
			';
		}
		
		if($controller == 'admin' && $method == 'uploadImage') {
			$js = '
<script type="text/javascript" src="' . base_url() . 'js/scriptaculous.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/scripts.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/upload.js"></script>
<script type="text/javascript">
/*<![CDATA[*/
function showSpinner(id) {
	$(id).update(\'<img src="' . base_url() . 'images/spinner.gif" style="margin: 1.0em auto; display: block; width: 16px; height: 16px;" />\');
}
/*]]>*/
</script>
			';
		}
		
		if($controller == 'admin' && $method == 'cropImage') {
			$js = '
<script type="text/javascript" src="' . base_url() . 'js/scriptaculous.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/scripts.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/cropper.js"></script>
<link rel="stylesheet" type="text/css" href="' . base_url() . 'css/cropper.css" />
			';
		}
		
		if($controller == 'brewery' && $method == 'hop') {
			$js = '
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/thickbox-compressed.js"></script>			
<link rel="stylesheet" type="text/css" href="' . base_url() . 'css/hops.css" />
<link rel="stylesheet" type="text/css" href="' . base_url() . 'css/thickbox.css" />
			';
		}
        
        if($controller == 'brewery' && $method == 'addEstablishment') {
            $js = '
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
            ';
        }
		
		/*if($controller == 'admin' && $method == 'edit') {
			$js .= '
<script type="text/javascript">
Event.observe(window, \'load\', animateNubbins, false);
function animateNubbins() {
	var els = $(\'contents\').getElementsByClassName(\'list_itemContainer\');
	for(var i = 0; i < els.length; i++) {
		var nub = els[i].getElementsByClassName(\'nubbin\');
		var nubID = nub[0].id;
		els[i].onmouseover = function(nubID) { return function() {
				$(nubID).style.visibility = \'visible\';
			}
		}(nubID);
		
		els[i].onmouseout = function(nubID) { return function() {
				$(nubID).style.visibility = \'hidden\';
			}
		}(nubID);
	}
}
</script>			
			';
		}*/
		
		if($controller == 'admin' && $method == 'view') {
			$js .= '
<script type="text/javascript" src="' . base_url() . 'js/scriptaculous.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/accordion.js"></script>
<script type="text/javascript">
/*<![CDATA[*/
Event.observe(window, \'load\', loadAccordions, false);
function loadAccordions() {
	var myAccordion = new accordion(\'contents_left\', {
		classNames: {
			toggle: \'atoggle\',
			toggleActive: \'atoggle_active\',
			content: \'accordion_content\'
		}
	});
}
/*]]>*/
</script>
<link rel="stylesheet" type="text/css" href="' . base_url() . 'css/accordion.css" />
			';
		}
		
		if($controller == 'review' && $method == 'addReview') {
			$js = '
<script type="text/javascript" src="' . base_url() . 'js/prototype.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/calendarview.js"></script>
<link rel="stylesheet" type="text/css" href="' . base_url() . 'css/calendarview.css" />
			';
		}
		
		if($controller == 'beer' && $method == 'review') {
			/*$js .= '
<script type="text/javascript" src="' . base_url() . 'js/scriptaculous.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/accordion.js"></script>
<script type="text/javascript">
Event.observe(window, \'load\', loadAccordions, false);
function loadAccordions() {
	var myAccordion = new accordion(\'beerReview\', {
		classNames: {
			toggle: \'toggle_beerReview\',
			toggleActive: \'active_beerReview\',
			content: \'content_beerReview\'
		}
	});
}
</script>
<script type="text/javascript">
function showSpinner(id) {
	$(id).update(\'<img src="' . base_url() . 'images/spinner.gif" style="margin: 1.0em auto; display: block; width: 16px; height: 16px;" />\');
}
</script>
<link rel="stylesheet" type="text/css" href="' . base_url() . 'css/accordion.css" />
			';*/
		
//<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js?ver=1.3.2"></script>
//<script type="text/javascript" src="http://jqueryjs.googlecode.com/files/jquery-1.3.2.min.js"></script>		
			$js .= '
<script type="text/javascript" src="' . base_url() . 'js/prototype.js"></script>
<script type="text/javascript">
/*<![CDATA[*/
/*jQuery.noConflict();*/
function showSpinner(id) {
	$(id).update(\'<img src="' . base_url() . 'images/spinner.gif" style="margin: 1.0em auto; display: block; width: 16px; height: 16px;" />\');
}
/*]]>*/
</script>
			';
		}
		
		if($controller == 'page' && $method == 'uploadImage') {
			$js = '
<script type="text/javascript" src="' . base_url() . 'js/prototype.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/scriptaculous.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/upload.js"></script>
<script type="text/javascript">
/*<![CDATA[*/
function showSpinner(id) {
	$(id).update(\'<img src="' . base_url() . 'images/spinner.gif" style="margin: 1.0em auto; display: block; width: 16px; height: 16px;" />\');
}
/*]]>*/
</script>
			';
		}
		
		if($controller == 'page' && $method == 'cropImage') {
			$js = '
<script type="text/javascript" src="' . base_url() . 'js/prototype.js"></script>			
<script type="text/javascript" src="' . base_url() . 'js/scriptaculous.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/cropper.js"></script>
<link rel="stylesheet" type="text/css" href="' . base_url() . 'css/cropper.css" />
			';
		}
		
		if(($controller == 'beer' && $method == 'createReview') || ($controller == 'establishment' && $method == 'createReview')) {
			$js = '
<script type="text/javascript" src="' . base_url() . 'js/prototype.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/scriptaculous.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/calendarview.js"></script>
<link rel="stylesheet" type="text/css" href="' . base_url() . 'css/calendarview.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.0/jquery.min.js"></script>
<script type="text/javascript">
var $j = jQuery.noConflict();
</script>
			';
		}
        
        if($controller == 'brewery' && $method == 'info') {
            $js = '
<script type="text/javascript" src="' . base_url() . 'js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(document).ready(function() { 
    $(\'#activeTable\').tablesorter({
        widgets: [\'zebra\']
        , headers: { 0:{sorter: false}, 5:{sorter: false}, 6:{sorter: false}}
        , sortList: [[1,0]]
    });
    $(\'#retiredTable\').tablesorter({
        widgets: [\'zebra\']
        , headers: { 0:{sorter: false}, 5:{sorter: false}, 6:{sorter: false}}
        , sortList: [[1,0]]
    }); 
}); 
</script>
            ';
        }
		
		if($controller == 'user' && $method == 'swaplist') {
			$js = '
<script type="text/javascript" src="' . base_url() . 'js/prototype.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/swaplist.js"></script>
			';
		}
		
		if($controller == 'user' && $method == 'pms') {
			$js = '
<script type="text/javascript" src="' . base_url() . 'js/prototype.js"></script>			
<script type="text/javascript" src="' . base_url() . 'js/maltedmail.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/jquery.maxlength.js"></script>
<script type="text/javascript">
var $j = jQuery.noConflict();
$j(document).ready(function() {  
    var onEditCallback = function(remaining) {
        $j(this).siblings(\'.ttr_message\').children(\'.charsRemaining\').text(\'(Characters remaining: \' + remaining + \')\');
        
        if(remaining > 0) {
            $j(this).siblings(\'.ttr_message\').children(\'.charsRemaining\').css(\'background-color\', \'#fff\');
        } else {
            $j(this).siblings(\'.ttr_message\').children(\'.charsRemaining\').css(\'background-color\', \'#c00\');
        }
    }
 
    $j(\'textarea[maxlength]\').limitMaxlength( {
        onEdit: onEditCallback
    });
});
</script>
			';
		}
		
		if($controller == 'beer' && $method == 'addBeer') {
			$js = '
<script type="text/javascript" src="' . base_url() . 'js/prototype.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/scripts.js"></script>
			';
		}
		
		if($controller == 'page' && $method == 'gallery') {
			$js = '
<link rel="stylesheet" type="text/css" href="' . base_url() . 'css/galleriffic.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/galleriffic.js"></script>
			';
		}
		
		if($controller == 'page' && $method == 'index') {
			$js = '
<script type="text/javascript" src="' . base_url() . 'js/prototype.js"></script>
<script type="text/javascript">
/*<![CDATA[*/
window.onload = function() {
	new Ajax.Request(
		\'' . base_url() . 'ajax/blogRSS\', {
			asynchronous: true, 
			evalScripts: true, 
			method: \'post\', 
			onLoading: function() {showSpinner(\'blogPosts\');}, 
			onComplete: function(response) {$(\'blogPosts\').update(response.responseText);}
		}
	);
}

function showSpinner(id) {
	$(id).update(\'<img src="' . base_url() . 'images/spinner.gif" style="margin: 1.0em auto; display: block; width: 16px; height: 16px;" />\');
}
/*]]>*/
</script>			
			';
		}
		
		if($controller == 'user' && $method == 'profile') {
			$js = '
<script type="text/javascript" src="' . base_url() . 'js/prototype.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.0/jquery.min.js"></script>
<script type="text/javascript">
var $j = jQuery.noConflict();
</script>
<script type="text/javascript" src="'. base_url() . 'js/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="' . base_url() . 'css/fancybox.css" />
<script type="text/javascript" src="' . base_url() . 'js/swaplist.js"></script>
			';
		}
		
		if($controller == 'report') {
			$js = '
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.0/jquery.min.js"></script>
			';
		}
		
	if($controller == 'establishment' && $method == 'googleMapss') {
			$js = '
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=' . GOOGLEAPI . '&sensor=false" type="text/javascript"></script>
<script type="text/javascript" src="http://www.google.com/jsapi?key=' . GOOGLEAPI . '"></script>
<script type="text/javascript">
/*<![CDATA[*/
function initialize() {
	if(GBrowserIsCompatible()) {
		var map = new GMap2(document.getElementById(\'map\'));
		map.setCenter(new GLatLng(' . $config['latitude'] . ', ' . $config['longitude'] . '), 13);
		map.setUIToDefault();
		//map.openInfoWindow(map.getCenter(), document.createTextNode("Hello, world"));
		//var point = new GLatLng(' . $config['latitude'] . ', ' . $config['longitude'] . ');
		//map.addOverlay(new GMarker(point));
		
		  var pos = new google.maps.LatLng(' . $config['latitude'] . ', ' . $config['longitude'] . ');
		  var myMarker = new google.maps.Marker({
		      position: companyPos,
		      map: map,
		      title: "Some title"
		  });
		
		//var companyPos = new google.maps.LatLng(' . $config['latitude'] . ', ' . $config['longitude'] . ');
		/*var companyMarker = new google.maps.Marker({
			position: new GLatLng(' . $config['latitude'] . ', ' . $config['longitude'] . '),
			map: map,
		})*/;
		
		var contentString = \'<div id="content">\'+
    \'<div id="siteNotice">\'+
    \'</div>\'+
    \'<h1 id="firstHeading" class="firstHeading">H�genhaug</h1>\'+
    \'<div id="bodyContent">\'+
    \'<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\'+
    \'</div>\'+
    \'</div>\';
 
var infowindow = new google.maps.InfoWindow({
    content: contentString
});

google.maps.event.addListener(point, \'click\', function() {
  infowindow.open(map,point);
});

	}
}
google.setOnLoadCallback(initialize);
/*]]>*/
</script>
			';
		}
		
		if($controller == 'establishment' && $method == 'googleMaps') {
			$js = '
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="http://www.google.com/jsapi?key=' . GOOGLEAPI . '"></script>
<script type="text/javascript">
function initialize() {
	var latlng = new google.maps.LatLng(' . $config['latitude'] . ', ' . $config['longitude'] . ');
	var settings = {
		zoom: 10,
		center: latlng,
		mapTypeControl: true,
		mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
		navigationControl: true,
		navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
		scaleControl: true,
		mapTypeId: google.maps.MapTypeId.ROADMAP};
	var map = new google.maps.Map(document.getElementById(\'map\'), settings);
	var contentString = \'<div id="content">\'+
		\'<div id="siteNotice">\'+
		\'</div>\'+
		\'<h2 class="brown"><a href="' . base_url() . '/establishment/info/rating/' . $config['establishment']['establishmentID'] . '" class="brown">' . htmlspecialchars($config['establishment']['name'], ENT_QUOTES) . '</a></h2>\'+
		\'<div id="bodyContent">\'+
		\'<p>' . $config['establishment']['address'] . '<br /><a href="' . base_url() . 'establishment/city/' . $config['establishment']['stateID'] . '/' . urlencode($config['establishment']['city']) . '">' . htmlspecialchars($config['establishment']['city'], ENT_QUOTES) . '</a>, <a href="' . base_url() . 'establishment/state/' . $config['establishment']['stateID'] . '">' . $config['establishment']['stateAbbr'] . '</a> ' . $config['establishment']['zip'] . '<br />' . formatPhone($config['establishment']['phone']) . '<br /><a href="' . $config['establishment']['url'] . '" target="_blank">' . $config['establishment']['url'] . '</a></p>\'+
		\'</div>\'+
		\'</div>\';
	var infowindow = new google.maps.InfoWindow({
		content: contentString
	});
	
	var companyImage = new google.maps.MarkerImage(\'' . base_url() . 'images/beer_pointer.gif\',
		new google.maps.Size(25,40),
		new google.maps.Point(0,0),
		new google.maps.Point(25,40)
	);

	var companyPos = new google.maps.LatLng(' . $config['latitude'] . ', ' . $config['longitude'] . ');
	
	var companyMarker = new google.maps.Marker({
		position: companyPos,
		map: map,
		icon: companyImage,
		title: \'' . htmlspecialchars($config['establishment']['name'], ENT_QUOTES) . '\',
		zIndex: 3
	});

	google.maps.event.addListener(companyMarker, \'click\', function() {
		infowindow.open(map,companyMarker);
	});
}
google.setOnLoadCallback(initialize);
</script>
			';
		}
		
		if($controller == 'user' && $method == 'updateProfile') {
			$js = '
<script type="text/javascript" src="' . base_url() . 'js/prototype.js"></script>
<script type="text/javascript" src="' . base_url() . 'js/scripts.js"></script>
<script type="text/javascript">
/*<![CDATA[*/
Event.observe(window, \'load\', function() {
	var ta = $$(\'textarea\');
	for(var i = 0; i < ta.length; i++) {
		ta[i].onkeyup = ta[i].onchange = checkLength;
		ta[i].onkeyup();
	}	
});
/*]]>*/
</script>
			';
		}
		
		if($controller == 'user') {
			$js .= '
<script type="text/javascript" src="' . base_url() . 'js/dudelist.js"></script>
<script type="text/javascript">
/*<![CDATA[*/
function showSpinner(id) {
	$(id).update(\'<img src="' . base_url() . 'images/spinner.gif" style="margin: 1.0em auto; display: block; width: 16px; height: 16px;" />\');
}
/*]]>*/
</script>
			';
		}
                
                if($controller == 'forum') {
                    $js .= '
<link rel="stylesheet" type="text/css" href="' . base_url() . 'css/forums.css" />                       
                    ';
                }
	
		
/*<script type="text/javascript">
Event.observe(window, \'load\', formField);

function formField() {
	var arr = $$(\'div.formBlock\');
	for(var i = 0; i < arr.length; i++) {
		var str = arr[i].id;
		arr[i].onmouseover = function(str) { return function() {
				$(str).style.backgroundColor = \'' . MOUSEOVER_FORMFIELD . '\';
			}
		}(str);
		arr[i].onmouseout = function(str) { return function() {
				$(str).style.backgroundColor = \'' . DEFAULT_FORMFIELD . '\';
			}
		}(str);
	}
}
</script>
			';
		}*/
		
		return $js;
	}
}
?>