function hideShowBasedOnAnother(el, elToHOS) {
	var vl = $F(el);
	if(vl == '1') {
		$(elToHOS).show();
	} else {
		$(elToHOS).hide();
	}
}

/** for the update profile form **/
function checkLength() {
	var max = 500;
	var len = this.value.length;
	if(len > max) {
		this.value = this.value.substring(0, max);
		len = this.value.length;
	}
	
	var per = Math.round(len/max*10)/10;
	var clss;
	var arr_class = new Array('long', 'longer', 'longest', 'toolong', 'green');
	switch(per) {
		case .7:
			clss = 'long';
			break;
		case .8:
			clss = 'longer';
			break;
		case .9:
			clss = 'longest';
			break;
		case 1:
			clss = 'toolong';
			break;
		default:
			clss = 'green';
	}
	
	arr_class.each(function(s) {
		if(clss != s && $('thoughtCount').hasClassName(s)) {
			$('thoughtCount').removeClassName(s);
		}
	});
	
	$('thoughtCount').innerHTML = len + '/' + max;	
	$('thoughtCount').addClassName(clss);
}
/** end update profile form **/