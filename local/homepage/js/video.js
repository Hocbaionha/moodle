jQuery(document).ready(function($) {
	if ($(window).width() > 568 && $(window).width() < 1024  ) {
			$('#bslider_video').owlCarousel({
	        loop:true,  
	        nav: true,
	        dots:true,
	        dotsClass:true,
	        responsive:{
	            0:{
	                items:1,
	                nav: true,
	                margin: 30,
	            },
	            576 : {
	                items:2,
	                nav: true,
	                margin: 30,
	            },
	        }
	    })	
	}else{
		 $('#bslider_video').owlCarousel({
	        loop:true,  
	        nav: true,
	        dots:true,
	        dotsClass:true,
	        responsive:{
	            0:{
	                items:1,
	                nav: true,
	                margin: 30,
	            },
	            576 : {
	                items:2,
	                nav: true,
	                margin: 30,
	            },
	            768 : {
	                items:3,
	                nav: true,
	                margin:30,
	            },
	        }
	    })
	}
	
});