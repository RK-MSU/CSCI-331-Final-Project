(function(w, ee) {
    "use_strict";

    ee.app.filter('unsafe', function($sce) {
        return $sce.trustAsHtml;
    });
    
})(window, EE);