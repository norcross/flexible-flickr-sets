jQuery(document).ready(function($) {


//********************************************************
// get variables from shortcode
//********************************************************


	$('div.flexiflickr').each(function() {
		// values stored
		var trans_v		= $(this).data('trans');
		var speed_v		= $(this).data('speed');
		var paging_v	= $(this).data('paging');
		var direction_v	= $(this).data('direction');

		// set conditionals
		trans		= (trans_v)		? trans_v		: 'fade';
		speed		= (speed_v)		? speed_v		: 7000;
		paging		= (paging_v)	? paging_v		: false;
		direction	= (direction_v)	? direction_v	: false;

	});

//********************************************************
// call slideshow
//********************************************************

	$('div.flexslider').flexslider({
		animation:		trans,
		animationLoop:	true,
		slideshowSpeed:	speed,
		animationSpeed:	600,
		pauseOnAction:	false,
		pauseOnHover:	true,
		controlNav:		paging,
		directionNav:	direction
	});


//********************************************************
// You're still here? It's over. Go home.
//********************************************************
	

});	// end init
