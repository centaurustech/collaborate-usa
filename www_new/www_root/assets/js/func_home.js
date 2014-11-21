$(document).ready(function(){
	$('.bxslider').bxSlider({
    auto: true,
    pause: 9000,
    startSlide: 0,
    autoDelay: 100,
	});

    //home portfolio slider
    $(".bxslider2").owlCarousel({
        items :4,
        itemsDesktop: [1199,4],
        itemsDesktopSmall: [979,3],
        itemsTablet : [768,3],
        itemsMobile : [579,2],
        slideSpeed:200,

        //Autoplay
        utoPlay : true,
        stopOnHover : false,
    });
});