(function(w, ee) {
    "use_strict";

    ee.app.controller('filePicker', ['$scope', 'dataMod', function($scope, dataMod) {
        
        $scope.$watch('_files', function(data) { 
            $scope.files = dataMod.fromString(data);
        });
        
        $scope.$watch('_filters', function(data) { 
            $scope.filters = dataMod.fromString(data);
        });

    }]);

})(window, EE);