// main.js
(function(w, t, ee) {
    'use_strict';

    let renderDataItems = function() {
        t('[data-bg-color]').each(function() {
            let elem = this;
            let bg_color = t(elem).attr('data-bg-color');
            if (bg_color != '' && bg_color != null && typeof bg_color != 'undefined') {
                $(elem).css('background-color', bg_color);
            }
        });
    };


    // let sidebar = $('.sidebar-toc').first().get(0);

    // window.onscroll = function() {
    //     var y = window.scrollY
    //     if(y > 106) {
    //         var rect = sidebar.getBoundingClientRect();
    //         var rect_top = rect.top;
    //         var adjust = 65;
    //         if(rect_top < 0) {
    //             rect_top = rect_top *-1;
    //             adjust = adjust+rect_top;
    //         } else {
    //             adjust = adjust-rect_top;
    //         }
    //         $('.sidebar-toc').css('top', adjust.toString() + "px");
    //         console.log("Top: ", rect_top);
    //         console.log("Adjust: ", adjust);
    //     }
    //     console.log("Y: ", y);
    // };

    t(w).on('DOMContentLoaded', function() {
        console.log(ee);
        renderDataItems();
        $('#categoriesSidebar').each(function(){
            var bsCollapse = new bootstrap.Collapse(this);
            bsCollapse.show();
        });

        $('.show-on-load').each(function(){
            $(this).removeClass('show-on-load');
        });
    });

})(window, jQuery, EE);