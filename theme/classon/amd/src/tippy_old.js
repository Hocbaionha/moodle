define(['jquery','theme_classon/tippyall'], function($,a) {

    return {
        tippyinit: function () {
            $(document).ready(function(){
            tippy('[data-tippy-placement]', {
                delay: 100,
                arrow: true,
                arrowType: 'sharp',
                size: 'regular',
                duration: 200,
                animation: 'shift-away',
                animateFill: true,
                theme: 'dark',
                distance: 10,
            });
            });
        }
    };
});