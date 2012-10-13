$(document).ready(function(){
		
	// Header Drupal Search Box
	$('#header [name=search_block_form]').val('Search this site...');
	$('#header [name=search_block_form]').focus(function () {
	$('#header [name=search_block_form]').val('');
	});
	
	// Hide border for image links
	$('a:has(img)').css('border', 'none');
	
	// Update CSS classes based on window load
	$(window).load(function() {
		var width = $(window).width();
		
		if ((width >= 751) && (width < 963)) {
		$('.two-sidebars #sidebar-first').removeClass('span3');
		$('.two-sidebars #sidebar-first').addClass('span4');
		$('.two-sidebars #sidebar-second').removeClass('span3');
		$('.two-sidebars #sidebar-second').addClass('span12');
		$('.two-sidebars #content').removeClass('span6');
		$('.two-sidebars #content').addClass('span8');
		$('.two-sidebars .region-sidebar-second .block').addClass('span4');
    	$('.sidebar-first #sidebar-first').removeClass('span3');
		$('.sidebar-first #sidebar-first').addClass('span4');
		$('.sidebar-first #content').removeClass('span9');
		$('.sidebar-first #content').addClass('span8');
		$('.sidebar-second #sidebar-second').removeClass('span3');
		$('.sidebar-second #sidebar-second').addClass('span12');
		$('.sidebar-second #content').removeClass('span9');
		$('.sidebar-second #content').addClass('span12');
		$('.sidebar-second .region-sidebar-second .block').addClass('span4');
		}

		else {
		$('.two-sidebars #sidebar-first').removeClass('span4');
		$('.two-sidebars #sidebar-first').addClass('span3');
		$('.two-sidebars #sidebar-second').removeClass('span12');
		$('.two-sidebars #sidebar-second').addClass('span3');
		$('.two-sidebars #content').removeClass('span8');
		$('.two-sidebars #content').addClass('span6');
		$('.two-sidebars .region-sidebar-second .block').removeClass('span4');
    	$('.sidebar-first #sidebar-first').removeClass('span4');
		$('.sidebar-first #sidebar-first').addClass('span3');
		$('.sidebar-first #content').removeClass('span8');
		$('.sidebar-first #content').addClass('span9');
		$('.sidebar-second #sidebar-second').removeClass('span12');
		$('.sidebar-second #sidebar-second').addClass('span3');
		$('.sidebar-second #content').removeClass('span12');
		$('.sidebar-second #content').addClass('span9');
		$('.sidebar-second .region-sidebar-second .block').removeClass('span4');
		}
	});
	
	// Update CSS classes based on window resize
	$(window).resize(function() {
		var width = $(window).width();
		
		if ((width >= 751) && (width < 963)) {
		$('.two-sidebars #sidebar-first').removeClass('span3');
		$('.two-sidebars #sidebar-first').addClass('span4');
		$('.two-sidebars #sidebar-second').removeClass('span3');
		$('.two-sidebars #sidebar-second').addClass('span12');
		$('.two-sidebars #content').removeClass('span6');
		$('.two-sidebars #content').addClass('span8');
		$('.two-sidebars .region-sidebar-second .block').addClass('span4');
    	$('.sidebar-first #sidebar-first').removeClass('span3');
		$('.sidebar-first #sidebar-first').addClass('span4');
		$('.sidebar-first #content').removeClass('span9');
		$('.sidebar-first #content').addClass('span8');
		$('.sidebar-second #sidebar-second').removeClass('span3');
		$('.sidebar-second #sidebar-second').addClass('span12');
		$('.sidebar-second #content').removeClass('span9');
		$('.sidebar-second #content').addClass('span12');
		$('.sidebar-second .region-sidebar-second .block').addClass('span4');
		}

		else {
		$('.two-sidebars #sidebar-first').removeClass('span4');
		$('.two-sidebars #sidebar-first').addClass('span3');
		$('.two-sidebars #sidebar-second').removeClass('span12');
		$('.two-sidebars #sidebar-second').addClass('span3');
		$('.two-sidebars #content').removeClass('span8');
		$('.two-sidebars #content').addClass('span6');
		$('.two-sidebars .region-sidebar-second .block').removeClass('span4');
    	$('.sidebar-first #sidebar-first').removeClass('span4');
		$('.sidebar-first #sidebar-first').addClass('span3');
		$('.sidebar-first #content').removeClass('span8');
		$('.sidebar-first #content').addClass('span9');
		$('.sidebar-second #sidebar-second').removeClass('span12');
		$('.sidebar-second #sidebar-second').addClass('span3');
		$('.sidebar-second #content').removeClass('span12');
		$('.sidebar-second #content').addClass('span9');
		$('.sidebar-second .region-sidebar-second .block').removeClass('span4');
		}
	});
});

// Hide Address Bar in Mobile View
addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
	function hideURLbar(){
		if (window.pageYOffset < 1) {
		window.scrollTo(0, 1);
	}
}


