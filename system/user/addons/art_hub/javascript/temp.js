(function(w) {
    "use_strict";
    w.addEventListener("DOMContentLoaded", function() {
        console.log(w.EE);
    });

    $('form').on('interact', function() {
        console.log('// I see typing!')
    });

})(window)