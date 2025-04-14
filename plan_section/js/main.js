(function ($) {
    "use strict";

    $(window).on('load', function(){
        //===== Prealoder
        $("#preloader").delay(1800).fadeOut("slow");

    });

    $(document).ready(function () {

        //03. Smooth Scroll Initialize
        function smoothScroolInit() {
            $('a').smoothScroll({
                speed: 1000
            });
        }
        smoothScroolInit();

        //05. sticky header
        function sticky_header(){
            var wind = $(window);
            var sticky = $('header');
            wind.on('scroll', function () {
                var scroll = wind.scrollTop();
                if (scroll < 20) {
                    sticky.removeClass('sticky');
                } else {
                    sticky.addClass('sticky');
                }
            });
        }
        sticky_header();
        //===== Back to top

        // Show or hide the sticky footer button
        $(window).on('scroll', function (event) {
            if ($(this).scrollTop() > 600) {
                $('.back-to-top').fadeIn(200);
            } else {
                $('.back-to-top').fadeOut(200);
            }
        });

        //Animate the scroll to yop
        $('.back-to-top').on('click', function (event) {
            event.preventDefault();

            $('html, body').animate({
                scrollTop: 0,
            }, 1500);
        });

        // Hamburger-menu
        $('.hamburger-menu, #menu li a').on('click', function () {
            $('.hamburger-menu .line-top').toggleClass('current');
            $('.hamburger-menu .line-center').toggleClass('current');
            $('.hamburger-menu .line-bottom').toggleClass('current');
            $('#menu').toggleClass('open');
        });


        //07. testimonial Slider Initialize
        function company_carouselInit() {
            $('.owl-carousel.company_carousel').owlCarousel({
                dots: false,
                loop: true,
                margin: 30,
                stagePadding: 2,
                autoplay: false,
                autoplayTimeout: 1500,
                autoplayHoverPause: true,
                responsive: {
                    0: {
                        items: 1
                    },
                    768: {
                        items: 2,
                    },
                    992: {
                        items: 5
                    }
                }
            });
        }
        company_carouselInit();



    });

})(jQuery);