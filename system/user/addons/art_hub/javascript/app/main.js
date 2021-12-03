(function(w, ee) {
    "use_strict";

    ee.app = angular.module("myApp", []);

    ee.app.run(['$rootScope', '$compile', 'bsModal', function($rootScope, $compile, bsModal) {

        $rootScope.ee = {
            csrf_token: ee.CSRF_TOKEN
        };

        $rootScope.member = {
            username: ee.username,
            id: ee.member_id,
        };
        
    }]);
    
})(window, EE);