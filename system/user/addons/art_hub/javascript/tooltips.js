// tooltips
// Bootstrap: Enable tooltips everywhere - https://getbootstrap.com/docs/5.1/components/tooltips/#example-enable-tooltips-everywhere
(function(w, ee) {
    'use_strict';

    ee.Tooltips = {
        tooltipList: null
    };

    ee.Tooltips.enableAll = function() {
        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        this.tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    };

    w.addEventListener('DOMContentLoaded', function() {
        ee.Tooltips.enableAll();
    });


})(window, EE);
