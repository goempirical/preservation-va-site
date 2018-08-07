// also contains historic site donation scripts

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

function convertToSlug(Text) {
    return Text
        .toLowerCase()
        .replace(/[^\w ]+/g,'')
        .replace(/ +/g,'-')
        ;
}

jQuery( document ).ready(function( $ ) {

    if(getParameterByName('hsd')){
        var hsd = getParameterByName('hsd')
        console.log(hsd);

        $("#ffm-historic_site > option").each(function() {
            var slugVal = convertToSlug(this.value);

            if (slugVal === hsd) {
                $("#ffm-historic_site").val(hsd);
            }
        });
    }

    
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


