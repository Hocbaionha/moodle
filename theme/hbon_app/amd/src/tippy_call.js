define(['jquery', 'theme_hbon_app/tippyall'], function ($, tippy) {
    console.log("Goi tippy");

    function initManage() {
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
    }

    return {
        init_tippy: function () {
            initManage();
        }
    };
});

