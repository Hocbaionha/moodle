$(document).ready(function(){     
//menu
  $('.navbar-toggler').click(function() {
     var checkClass = $('.collapse').hasClass('menu-show');
     var checkClassMenu = $('.menu-user').hasClass('show');
     if ( checkClassMenu == true) {
      $('.menu-user').removeClass('show');
      if (checkClass==true) {
         $('.collapse').removeClass('menu-show');
       }else{
        $('.collapse').addClass('menu-show');
       }
     }
    if (checkClass==true){
         $('.collapse').removeClass('menu-show');
       }else{
        $('.collapse').addClass('menu-show');
    }
  })
  //menu user
$('.avt-user').click(function(){
   var checkClass = $('.menu-user').hasClass('show');
     if (checkClass == true) {
         $('.menu-user').removeClass('show');
     }else{
       $('.menu-user').addClass('show');
     }
})
//mobile
$('.mobile_menu_user').click(function(){
   var checkClass = $('.menu-user').hasClass('show');
   var checkClassMenu = $('.collapse').hasClass('menu-show');
     if ( checkClassMenu == true) {
      $('.collapse').removeClass('menu-show');
      if (checkClass==true) {
         $('.menu-user').removeClass('show');
       }else{
        $('.menu-user').addClass('show');
       }
     }
    if (checkClass==true) {
         $('.menu-user').removeClass('show');
       }else{
        $('.menu-user').addClass('show');
    }
})
     // Cache selectors
    $('#bslider_video div[class="disabled"]').removeClass('disabled');
    var lastId,
    topMenu = $("#top-menu"),
    topMenuHeight = topMenu.outerHeight(),
    // All list items
    menuItems = topMenu.find("a"),
    // Anchors corresponding to menu items
    scrollItems = menuItems.map(function(){
      var item = $($(this).attr("href"));
      if (item.length) { return item; }
    }); 

    // menuItems.click(function(e){
    //   var href = $(this).attr("href"),
    //       offsetTop = href === "#" ? 0 : $(href).offset().top - 20;
    //   $('html, body').stop().animate({ 
    //       scrollTop: offsetTop
    //   }, 300);
    //   e.preventDefault();
    // });

    // Bind to scroll
    // $(window).scroll(function(){
    //    // Get container scroll position
    //    var fromTop = $(this).scrollTop()+topMenuHeight;
       
    //    // Get id of current scroll item
    //    var cur = scrollItems.map(function(){
    //      if ($(this).offset().top < fromTop)
    //        return this;
    //    });
    //    // Get the id of the current element
    //    cur = cur[cur.length-1];
    //    var id = cur && cur.length ? cur[0].id : "";
       
    //    if (lastId !== id) {
    //        lastId = id;
    //        // Set/remove active class
    //        menuItems
    //          .parent().removeClass("active")
    //          .end().filter("[href='#"+id+"']").parent().addClass("active");
    //    }                   
    // });

    var viewport = $(window).width();
    if (viewport < 768) {
        menuItems.click(function(e){
          var href = $(this).attr("href"),
              offsetTop = href === "#" ? 0 : $(href).offset().top - 130;
          $('html, body').stop().animate({ 
              scrollTop: offsetTop
          }, 300);
          e.preventDefault();
        });
        $(window).scroll(function(){
           // Get container scroll position
           var fromTop = $(this).scrollTop()+topMenuHeight;
           
           // Get id of current scroll item
           var cur = scrollItems.map(function(){
             if ($(this).offset().top < fromTop)
               return this;
           });
           // Get the id of the current element
           cur = cur[cur.length-0];
           var id = cur && cur.length ? cur[0].id : "";
           
           if (lastId !== id) {
               lastId = id;
               // Set/remove active class
               menuItems
                 .parent().removeClass("active")
                 .end().filter("[href='#"+id+"']").parent().addClass("active");
           }                   
        });

    }else {
        menuItems.click(function(e){
          var href = $(this).attr("href"),
              offsetTop = href === "#" ? 0 : $(href).offset().top - 80;
          $('html, body').stop().animate({  
              scrollTop: offsetTop
          }, 300);
          e.preventDefault();
        });
        $(window).scroll(function(){
           // Get container scroll position
           var fromTop = $(this).scrollTop()+topMenuHeight;
           
           // Get id of current scroll item
           var cur = scrollItems.map(function(){
             if ($(this).offset().top < fromTop)
               return this;
           });
           // Get the id of the current element
           cur = cur[cur.length-1];
           var id = cur && cur.length ? cur[0].id : "";
           
           if (lastId !== id) {
               lastId = id;
               // Set/remove active class
               menuItems
                 .parent().removeClass("active")
                 .end().filter("[href='#"+id+"']").parent().addClass("active");
           }                   
        });
    }  
// SLIDE
    $('#bslider_four').owlCarousel({
        loop:true,  
        autoplayTimeout:3500,
        autoplay: true, 
        responsive:{
            0:{
                items:1,
                nav: false,
                margin: 30,
            },
            576 : {
                items:2,
                nav: false,
                margin: 30,
            },
            768 : {
                items:3,
                nav: false,
                margin:30,
            },
            1190:{
                items:4,
                nav:false,
                margin:30,
            }
        }
    })
// slide video
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
            1190:{
                items:4,
                nav:true,
                margin:30,
            }
        }
    })

    $('._video #bslider_video').owlCarousel({
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
            }
        }
    })
// customer
 $('#bslider_customer').owlCarousel({
        loop:true,  
        autoplayTimeout:2500,
        // autoplay: true, 
        nav: true,
        // pagination:false,
        dots:true,
        dotsClass:true,
        // navigation:true,
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
            1190:{
                items:4,
                nav:true,
                margin:30,
            }
        }
    })
 //team
 $('#bslider_team1').owlCarousel({
        loop:($(".owl-carousel .item").length > 4) ? true: false, 
        autoplayTimeout:2500,
        // autoplay: true, 
        nav: true,
        // pagination:false,
        dots:true,
        dotsClass:true,
        // navigation:true,
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
            1190:{
                items:4,
                nav:true,
                margin:30,
            }
        }
    })
 $('#bslider_team2').owlCarousel({
        loop:($(".owl-carousel .item").length > 4) ? true: false,  
        // autoplayTimeout:2500,
        // autoplay: true, 
        nav: true,
        // pagination:false,
        dots:true,
        dotsClass:true,
        // navigation:true,
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
  $('#bslider_team3').owlCarousel({
        loop:($(".owl-carousel .item").length > 4) ? true: false,  
        // autoplayTimeout:2500,
        // autoplay: true, 
        nav: true,
        // pagination:false,
        dots:true,
        dotsClass:true,
        // navigation:true,
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
            1190:{
                items:4,
                nav:true,
                margin:30,
            }
        }
    })
  $('#bslider_team4').owlCarousel({
        loop:($(".owl-carousel .item").length > 4) ? true: false,  
        // autoplayTimeout:2500,
        // autoplay: true, 
        nav: true,
        // pagination:false,
        dots:true,
        dotsClass:true,
        navigation:true,
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
    $('#bslider_team5').owlCarousel({
        loop:($(".owl-carousel .item").length > 4) ? true: false,  
        // autoplayTimeout:2500,
        // autoplay: true, 
        nav: true,
        // pagination:false,
        dots:true,
        dotsClass:true,
        // navigation:true,
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


    var viewport = $(window).width();
    if (viewport < 768) {  
        //viewport height    
        $(window).scroll(function () {
            if ($(this).scrollTop() > 0) {  
                $('.b-content__left').addClass("content-fixed");   
            } else { 
                $('.b-content__left').removeClass("content-fixed");    
            }
        });  
        $('.b-content__left').addClass ('b-content__left-mb');  
    }       

    var viewport = $(window).width();
    if (viewport < 768) {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 0) {
                $('.b-header').addClass("top-fixed");   
            } else {
                $('.b-header').removeClass("top-fixed");  
            }
        });
    }else {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 0) {
                $('.b-header').addClass("top-fixed");   
            } else {
                $('.b-header').removeClass("top-fixed");  
            }
        });
    }

    $(window).scroll(function(){
      if($(this).scrollTop() > 50){
        $('.b-content__left').addClass("b-fixed");
      } else {
     $('.b-content__left').removeClass("b-fixed");
      }
    });     

    $(".navbar-toggler").click(function(e){
        $(".nav-toggle__mb").toggle();
         e.stopPropagation();
    });
    
    $(".nav-toggle__mb").click(function(e){
        e.stopPropagation();
    }); 

    $(document).on('touchstart', function() {
        $(".nav-toggle__mb").hide();
    });  
});
jQuery(document).ready(function($) {
  var owl = $('.owl-carousel');
  owl.owlCarousel({
    loop:true,
    margin:10,
    // nav:false,
    // nav: true,
    dotsClass:true,
    items: 1,
    autoplay: true,
    autoplayTimeout: 4000,
    // stagPadding: true,
     // dots: true,
    // nav: true,
    // dots: false,
    // navigation:true
  });
  
  // Custom Button
  $('.customNextBtn').click(function() {
    owl.trigger('next.owl.carousel');
  });
  $('.customPreviousBtn').click(function() {
    owl.trigger('prev.owl.carousel');
  });

  // select course
  $('#select-course').change(function(){
    var selectedCourse = $(this).children("option:selected").val();
    if (selectedCourse!=0) {
      $(".content .item").addClass("hide");
      $(".cl"+selectedCourse).removeClass("hide");
    }
    if (selectedCourse==0) {
      $(".content .item").removeClass("hide");
    }
  })

  $('#bslider_two').owlCarousel({
        loop:false,  
        autoplayHoverPause:true,
        autoplay: true,
        responsive:{
            0:{
                items:2,
                nav: false,
                margin: 20,
            },
            576 : {
                items:3,
                nav: false,
                margin: 20,
            },
            768 : {
                items:3,
                nav: false,
                margin:20,
            },
            1190:{
                items:3,
                nav:true,
                margin:20,
            }
        }
    })
  
});





  