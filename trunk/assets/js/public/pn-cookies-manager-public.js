(function($) {
	'use strict';

	function pn_cookies_manager_timer(step) {
		var step_timer = $('.pn-cookies-manager-player-step[data-pn-cookies-manager-step="' + step + '"] .pn-cookies-manager-player-timer');
		var step_icon = $('.pn-cookies-manager-player-step[data-pn-cookies-manager-step="' + step + '"] .pn-cookies-manager-player-timer-icon');
		
		if (!step_timer.hasClass('timing')) {
			step_timer.addClass('timing');

      setInterval(function() {
      	step_icon.fadeOut('fast').fadeIn('slow').fadeOut('fast').fadeIn('slow');
      }, 5000);

      setInterval(function() {
      	step_timer.text(Math.max(0, parseInt(step_timer.text()) - 1)).fadeOut('fast').fadeIn('slow').fadeOut('fast').fadeIn('slow');
      }, 60000);
		}
	}

	$(document).on('click', '.pn-cookies-manager-popup-player-btn', function(e){
  	pn_cookies_manager_timer(1);
	});

	$('.pn-cookies-manager-carousel-main-images .owl-carousel').owlCarousel({
    margin: 10,
    center: true,
    nav: false, 
    autoplay: true, 
    autoplayTimeout: 5000, 
    autoplaySpeed: 2000, 
    pagination: true, 
    responsive:{
      0:{
        items: 2,
      },
      600:{
        items: 3,
      },
      1000:{
        items: 4,
      }
    }, 
  });
})(jQuery);
