(function(w, ee) {
    "use_strict";

    ee.app.controller('memberRegistration', ['$scope', function($scope) {
        $scope.screen_name_same_as = true;
        $scope.$watch('screen_name_same_as', function(newVal, oldValue) {
            $scope.screen_name = '';
        });
    }]);
    
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation');
        
    // Loop over them and prevent submission
    Array.prototype.slice.call(forms).forEach(function (form) {
      form.addEventListener('submit', function (event) {
          if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
          }

          form.classList.add('was-validated');
        }, false);
    });

})(window, EE);