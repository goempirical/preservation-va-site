
    jQuery( document ).ready(function( $ ) {
        
        var owl_dots = $('.owl-two');
        var owl_next = $('.owl-one');
        
        /* TODO: Refactor for dynamic detect between dots or nav */
       
        var stand_obj = {
            loop:true,
            autoplay:true,
            autoplayTimeout:5000,
            nav: false,
            dots: false,
            autoplayHoverPause:true,
            margin:0,
            autoHeight:false,
            smartSpeed: 1000
        };
        
        owl_dots.owlCarousel({
            items:1,
            loop:true,
            autoplay:true,
            autoplayTimeout:5000,
            dots:true,
            autoplayHoverPause:true,
            margin:0,
            autoHeight:false,
            smartSpeed: 1000
        });
        
         owl_next.owlCarousel({
            item:3,
            loop:true,
            autoplay:true,
            autoplayTimeout:5000,
            nav: true,
            dots: false,
            autoplayHoverPause:true,
            margin:0,
            autoHeight:false,
            smartSpeed: 1000
        }); 
        

     /*    $.each(owl, function( index, value  ) {
            
            var aux_cont = $(value);
            var aux_per = {};

                if ( aux_cont.hasClass( 'next' ) ) {

                  aux_per = $.extend( { item:3 }, stand_obj )
                  aux_per.nav = true; 
                    

                } else {

                  aux_per = $.extend( { item:1}, stand_obj );
                  aux_per.dots = true;

                }
            
                console.log(aux_per);
            aux_cont.owlCarousel(aux_per);
        }); */

    });
