// /theme/hbon_app/amd/src/countdowntimer.js
define(['jquery', 'jqueryui', 'theme_hbon_app/slick'], function ($, jqui, Slick) {
    return {
        hbon_app_homepage: function ($params) {
            console.log("Run hbon_app homepage JS");
            function hbon_app_init_carousel($elem) {
                $elem.not('.slick-initialized').each(function () {
                    var _this = $(this),
                            _config = [];

                    _config.responsive = true;
                    _config.slidesToScroll = 1;
                    _config.autoplay = true;
                    _config.autoplaySpeed = 3000;
                    _config.dots = true;
                    _config.arrows = false;
                    _this.slick(_config);
                });
            }
            if ($('.owl-slick').length) {
                $('.owl-slick').each(function () {
                    hbon_app_init_carousel($(this));
                });
            }
            //furgan-testimonial
            if ($('.furgan-testimonial').length) {
                $('.furgan-testimonial').not('.slick-initialized').each(function () {
                    var _config = [];
                    _config.responsive = true;
                    _config.slidesToScroll = 1;
                    _config.autoplay = true;
                    _config.autoplaySpeed = 2000;
                    _config.dots = true;
                    _config.arrows = false;
                    $(this).slick(_config)

                });
            }

            function furgan_header_sticky($elem) {
                var $this = $elem;
                $this.on('furgan_header_sticky', function () {
                    $this.each(function () {
                        var previousScroll = 0,
                                header = $(this).closest('.header'),
                                header_wrap_stick = $(this),
                                header_position = $(this).find('.header-position'),
                                header_logo_nav = $(this).find('.header-logo-nav'),
                                header_logo2 = $(this).find('.header-logo2'),
                                headerOrgOffset = header_position.offset().top;
                        header_wrap_stick.css('height', header_wrap_stick.outerHeight());
                        $(document).on('scroll', function (ev) {
                            var currentScroll = $(this).scrollTop();
                            if (currentScroll > headerOrgOffset) {
                                header_position.addClass('fixed');
                                header_logo2.addClass('show');
                                header_logo_nav.addClass('show');
                                header.addClass('fixed');
                            } else {
                                header_position.removeClass('fixed');
                                header_logo2.removeClass('show');
                                header_logo_nav.removeClass('show');
                                header.removeClass('fixed');
                            }
                            previousScroll = currentScroll;
                        });

                    })
                }).trigger('furgan_header_sticky');
                $(window).on('resize', function () {
                    $this.trigger('furgan_header_sticky');
                });
            }
            if ($('.header-sticky .header-wrap-stick').length) {
                furgan_header_sticky($('.header-sticky .header-wrap-stick'));
            }


            /*--------------------------------------------------*/
            /*  Ripple Effect
             /*--------------------------------------------------*/
            $('.ripple-effect, .ripple-effect-dark').on('click', function (e) {

                var rippleDiv = $('<span class="ripple-overlay">'),
                        rippleOffset = $(this).offset(),
                        rippleY = e.pageY - rippleOffset.top,
                        rippleX = e.pageX - rippleOffset.left;

                rippleDiv.css({
                    top: rippleY - (rippleDiv.height() / 2),
                    left: rippleX - (rippleDiv.width() / 2),
                }).appendTo($(this));

                window.setTimeout(function () {
                    rippleDiv.remove();
                }, 800);
            });

            /* ## Theme Popup */
            function handlePopup() {
                var popup = $('.modal-window');
                // Activate popup
                popup.css('display', 'block');
                popup.find('.btn-loading-disabled').addClass('btn-loading');
                setTimeout(function () {
                    popup.addClass('open');
                }, 30);
            }
            $('body').on('click keydown', '.modal-window .close, .modal-window .subscribe-nothanks-btn', function (e) {
                e.preventDefault(e);
                var popup = $(this).closest('.modal-window');
                closePopup(popup);
            });

            function closePopup(popup) {
                // Close button
                popup.removeClass('open');
                setTimeout(function () {
                    popup.css('display', 'none');
                    popup.find('.modal-content').empty();
                    popup.find('.modal-content').removeClass().addClass('modal-content');
                }, 400);

            }

            // function handleVideoPopup() {
            //     $(document).on('click', '.video-module', function (event) {
            //         event.preventDefault();
            //         handlePopup();
            //         var popupInner = $('.modal-content').addClass('video-popup');
            //         popupInner.siblings('.btn-loading-disabled').removeClass('btn-loading');
            //         // Append video
            //         popupInner.append($(document.createElement("iframe")).attr({
            //             'src': $(this).attr('data-video-module') + "?autoplay=0",
            //             'allowfullscreen': 'true',
            //             'frameborder': '0'
            //         }));
            //     });
            // }
            // handleVideoPopup();

//            $('.course-group-slick').slick({
//                infinite: true,
//                slidesToShow: 3,
//                slidesToScroll: 3
//            });
//            $("#slicktest").slick();
            if ($('.course-group-slick').length) {
                $('.course-group-slick').not('.slick-initialized').each(function () {
                    var _config = [];
                    _config.responsive = true;
                    _config.slidesToScroll = 2;
                    _config.slidesToShow = 4;
                    _config.autoplay = true;
                    _config.mobileFirst = true;
                    _config.autoplaySpeed = 2000;
                    _config.dots = false;
                    _config.arrows = false;
                    _config.responsive = [{
                        breakpoint: 1366,
                        settings: {
                          slidesToShow: 4,
                          slidesToScroll: 2,
                          infinite: true,
                          dots: false
                        }
                      },{
                        breakpoint: 1024,
                        settings: {
                          slidesToShow: 3,
                          slidesToScroll: 3,
                          infinite: true,
                          dots: true
                        }
                      },
                      {
                        breakpoint: 600,
                        settings: {
                          slidesToShow: 2,
                          slidesToScroll: 2
                        }
                      },
                      {
                        breakpoint: 315,
                        settings: {
                          slidesToShow: 1,
                          slidesToScroll: 1
                        }
                      }]
                    
                    $(this).slick(_config)

                });
            }
            // if ($('.owl-carousel').length) {
            //     $('.owl-carousel').not('.slick-initialized').each(function () {
            //         var _config = [];
            //         _config.responsive = true;
            //         _config.slidesToScroll = 2;
            //         _config.slidesToShow = 4;
            //         _config.autoplay = true;
            //         _config.autoplaySpeed = 2000;
            //         _config.dots = false;
            //         _config.arrows = false;
            //         _config.responsive = [{
            //             breakpoint: 1024,
            //             settings: {
            //               slidesToShow: 3,
            //               slidesToScroll: 3,
            //               infinite: true,
            //               dots: true
            //             }
            //           },
            //           {
            //             breakpoint: 600,
            //             settings: {
            //               slidesToShow: 2,
            //               slidesToScroll: 2
            //             }
            //           },
            //           {
            //             breakpoint: 480,
            //             settings: {
            //               slidesToShow: 1,
            //               slidesToScroll: 1
            //             }
            //           }]
                    
            //         $(this).slick(_config)

            //     });
            // }

        }
    };
});