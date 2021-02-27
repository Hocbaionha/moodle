// /theme/hbon_app/amd/src/countdowntimer.js
define(['jquery', 'theme_hbon_app/jquery.countdown'  ], function($, c,) {
    return {
        initialise: function ($params) {
            console.log ("Count down");
            $('#clock').countdown('2020/10/10', function(event) {
                $(this).html(event.strftime('%D days %H:%M:%S'));
            });


            $('#clock').css ('color','red');



        }
    };
});