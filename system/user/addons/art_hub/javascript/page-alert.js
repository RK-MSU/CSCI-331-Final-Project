// page alert
(function(w) {
    'use_strict';

    let removePageAlerts = function() {
        $('#mainPageBody > .page-alert').each(function() {
            $(this).remove();
        });
    };

    w.addEventListener('DOMContentLoaded', function() {
        setTimeout(removePageAlerts, 2000);
    });

})(window);
