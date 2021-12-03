// clickable-post.js
(function(w, t, ee) {
    'use_strict';

    let renderClickableElems = function() {
        t('.clickable').each(function() {
            let elem = this;
            // add hover class to card
            t(elem).mouseenter(function() { t(this).addClass('hover'); });
            // remove hover class
            t(elem).mouseleave(function() { t(this).removeClass('hover'); });
            let href_attr = t(elem).attr('data-nav-href');
            if (href_attr != '' && href_attr != null && typeof href_attr != 'undefined') {
                t(elem).on('click', function(){
                    w.location.assign(href_attr);
                });
            }
        });
    };

    t(w).on('DOMContentLoaded', function() {
        renderClickableElems();
    });

})(window, jQuery, EE);
