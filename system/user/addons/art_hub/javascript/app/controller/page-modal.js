(function(w, ee) {
    "use_strict";

    ee.app.controller('pageModal', ['$scope', '$element', '$compile', 'bsModal', function($scope, $element, $compile, bsModal) {

        bsModal.compile = function() {
            $compile($element.contents())($scope);
            $('[data-bs-toggle="tooltip"]', $element).each(function() {
                new bootstrap.Tooltip(this);
            });
        };

        bsModal.setTitle = function(val) {
            $('.modal-title', $element).html(val);
        };

        bsModal.setBody = function(val) {
            $('.modal-body', $element).html(val);
        };

        bsModal.setFooter = function(val) {
            $('.modal-footer', $element).html(val);
        };

        $scope.modalAction = function(action) {
            var act = bsModal.actions[action];
            act();
        };

    }]);

})(window, EE);