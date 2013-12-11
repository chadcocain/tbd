
	<div id="wrapper">

<?php
if(isset($head)) {
	echo $head;
}
?>
	<div id="gallery" class="content">
		<div id="controls" class="controls"></div>
		<div id="loading" class="loader"></div>
		<div id="slideshow" class="slideshow"></div>
		<div id="caption" class="embox"></div>
	</div>
	<div id="thumbs" class="navigation">
		<ul class="thumbs noscript">
			<?php echo $events; ?>
		</ul>
	</div>


<script type="text/javascript">
// We only want these styles applied when javascript is enabled
$('div.navigation').css({'width' : '300px', 'float' : 'left'});
$('div.content').css('display', 'block');

// Initially set opacity on thumbs and add
// additional styling for hover effect on thumbs
var onMouseOutOpacity = 0.67;
$('#thumbs ul.thumbs li').css('opacity', onMouseOutOpacity)
	.hover(
		function () {
			$(this).not('.selected').fadeTo('fast', 1.0);
		}, 
		function () {
			$(this).not('.selected').fadeTo('fast', onMouseOutOpacity);
		}
	);

$(document).ready(function() {
	// Initialize Advanced Galleriffic Gallery
	var galleryAdv = $('#gallery').galleriffic('#thumbs', {
		delay:                  2000,
		numThumbs:              12,
		preloadAhead:           10,
		enableTopPager:         true,
		enableBottomPager:      true,
		imageContainerSel:      '#slideshow',
		controlsContainerSel:   '#controls',
		captionContainerSel:    '#caption',
		loadingContainerSel:    '#loading',
		renderSSControls:       true,
		renderNavControls:      true,
		playLinkText:           'Play Slideshow',
		pauseLinkText:          'Pause Slideshow',
		prevLinkText:           '&lsaquo; Previous Photo',
		nextLinkText:           'Next Photo &rsaquo;',
		nextPageLinkText:       'Next &rsaquo;',
		prevPageLinkText:       '&lsaquo; Prev',
		enableHistory:          true,
		autoStart:              false,
		onChange:               function(prevIndex, nextIndex) {
			$('#thumbs ul.thumbs').children()
				.eq(prevIndex).fadeTo('fast', onMouseOutOpacity).end()
				.eq(nextIndex).fadeTo('fast', 1.0);
		},
		onTransitionOut:        function(callback) {
			$('#caption').fadeTo('fast', 0.0);
			$('#slideshow').fadeTo('fast', 0.0, callback);
		},
		onTransitionIn:         function() {
			$('#slideshow').fadeTo('fast', 1.0);
			$('#caption').fadeTo('fast', 1.0);
		},
		onPageTransitionOut:    function(callback) {
			$('#thumbs ul.thumbs').fadeTo('fast', 0.0, callback);
		},
		onPageTransitionIn:     function() {
			$('#thumbs ul.thumbs').fadeTo('fast', 1.0);
		}
	});
});
</script>

		<br class="both" />
	</div>
