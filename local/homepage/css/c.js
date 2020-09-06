$(document).ready(function(){     

     // Cache selectors
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

    $('#bslider_one').owlCarousel({
        loop:true,  
        autoplayTimeout:1000,
        autoplayHoverPause:true,
        autoplay: false,
        responsive:{
            0:{
                items:1,
                nav: false,
                margin: 20,
            },
            576 : {
                items:2,
                nav: false,
                margin: 20,
            },
            768 : {
                items:3,
                nav: false,
                margin:20,
            },
            1190:{
                items:4,
                nav:true,
                margin:20,
            }
        }
    })

    $('#bslider_two').owlCarousel({
        loop:true,  
        autoplayHoverPause:true,
        autoplay: false,
        responsive:{
            0:{
                items:1,
                nav: false,
                margin: 20,
            },
            576 : {
                items:2,
                nav: false,
                margin: 20,
            },
            768 : {
                items:3,
                nav: false,
                margin:20,
            },
            1190:{
                items:4,
                nav:true,
                margin:20,
            }
        }
    })

    $('#bslider_three').owlCarousel({
        loop:true,  
        autoplayHoverPause:true,
        autoplay: false,
        responsive:{
            0:{
                items:1,
                nav: false,
                margin: 20,
            },
            576 : {
                items:2,
                nav: false,
                margin: 20,
            },
            768 : {
                items:3,
                nav: false,
                margin:20,
            },
              1190:{
                  items:4,
                  nav:true,
                  margin:20,
              }
        }
    })

    $('#bslider_four').owlCarousel({
        loop:true,  
        autoplayTimeout:3000,
        autoplay: false, 
        responsive:{
            0:{
                items:1,
                nav: false,
                margin: 20,
            },
            576 : {
                items:2,
                nav: false,
                margin: 20,
            },
            768 : {
                items:3,
                nav: false,
                margin:20,
            },
            1190:{
                items:4,
                nav:true,
                margin:20,
            }
        }
    })

    $('#bslider_five').owlCarousel({
        loop:true,  
        autoplayTimeout:3000,
        autoplay: false, 
        responsive:{
            0:{
                items:1,
                nav: false,
                margin: 20,
            },
            576 : {
                items:2,
                nav: false,
                margin: 20,
            },
            768 : {
                items:3,
                nav: false,
                margin:20,
            },
            1190:{
                items:4,
                nav:true,
                margin:20,
            }
        }
    })

    $('#bslider_six').owlCarousel({
        loop:true,  
        autoplayTimeout:3000,
        autoplay: false, 
        responsive:{
            0:{
                items:1,
                nav: false,
                margin: 20,
            },
            576 : {
                items:2,
                nav: false,
                margin: 20,
            },
            768 : {
                items:3,
                nav: false,
                margin:20,
            },
            1190:{
                items:4,
                nav:true,
                margin:20,
            }
        }
    })

    $('#bslider_seven').owlCarousel({
        loop:true,  
        autoplayTimeout:3000,
        autoplay: false, 
        responsive:{
            0:{
                items:1,
                nav: false,
                margin: 20,
            },
            576 : {
                items:2,
                nav: false,
                margin: 20,
            },
            768 : {
                items:3,
                nav: false,
                margin:20,
            },
            1190:{
                items:4,
                nav:true,
                margin:20,
            }
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




  