(function(w, ee) {
    "use_strict";


    ee.app.controller('fileInputField', ['$scope', '$rootScope', '$http', '$timeout', 'EE', 'dataMod', 'bsModal', 'request', function($scope, $rootScope, $http, $timeout, EE, dataMod, bsModal, request) {

        $scope.file = {
            title : null,
            src : null
        };

        $scope.field = {
            value : null,
            meta : null
        };

        $scope._uploading_file = false;

        $scope.$watch('_file', function(newValue, oldValue) {$scope.file = dataMod.fromString(newValue);});
        $scope.$watch('_field', function(newValue, oldValue) {$scope.field = dataMod.fromString(newValue);});

        $scope.selectFieldFile = function(file) {
            $scope.field.value = file.value;
            $scope.file.src = file.src;
            $scope.file.title = file.title;
            bsModal.hideActiveModal();
            bsModal.removeAction('upload_file');
            delete $rootScope.selectFile;
        };

        $scope._getModalView = function(modalView) {

            let data = {
                ACT: EE.actionId('get_view'),
                csrf_token : EE.item('CSRF_TOKEN'),
                meta : $scope.field.meta,
                view : modalView,
            };

            let http_req = {
                method: "POST",
                url: EE.site_url(),
                data: $.param(data),
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            };

            $http(http_req).then(request.success, request.error);
        };

        $scope._uploadFile = function() {
            if($scope._uploading_file == true) {
                alert('Upload inprogress.');
            } else {
                $scope._uploading_file = true;
            }

            let active_modal = bsModal.getActiveModal();
            let modal_elem = active_modal.elem;

            $('.modal-footer button', modal_elem).each(function(){
                this.disabled = true;
            });
            
            let form = $('form', modal_elem).first().get(0);
            let formData = new FormData(form);

            $.ajax({
                type: "POST",
                url: EE.site_url(),
                async: true,
                cache: false,
                contentType: false,
                processData: false,
                timeout: 90000,
                data: formData,
                success: function (data) {
                    $scope._uploading_file = false;
                    $('.modal-footer button', modal_elem).each(function(){
                        this.disabled = false;
                    });
                    if(!data.hasOwnProperty('status')) {
                        console.error('No Status', data);
                        return;
                    }
                    if(data.status != 'success') {
                        console.error('Status: ' + data.status);
                        console.error('Data: ' + data);
                        return;
                    }

                    $timeout(function () {
                        $scope.field.value = data.field.value;
                        $scope.file.src = data.file.src;
                        $scope.file.title = data.file.title;
                    }, 1);
                    bsModal.removeAction('upload_file');
                    bsModal.hideActiveModal();
                },
                error: function (error) {
                    $scope._uploading_file = false;
                    $('.modal-footer button', modal_elem).each(function(){
                        this.disabled = false;
                    });
                    console.error(error);
                    console.error(error.responseText);
                    return;
                    $scope._uploading_file = false;
                    $('.modal-footer button', bsModal._modalElem).each(function(){
                        this.disabled = false;
                    });
                    alert('Error:' + JSON.stringify(error));
                    console.error(error);
                }
            });
        };

        $scope.chooseExisting = function() {
            $rootScope.selectFile = $scope.selectFieldFile;
            bsModal.setAction('upload_file', function(){
                bsModal.hideActiveModal();
                bsModal.removeAction('upload_file');
                delete $rootScope.selectFile;
                $scope.uploadNew();
            });
            // bsModal.setAction('upload_file', $scope.uploadNew);
            $scope._getModalView('choose_existing_file');
        };

        $scope.uploadNew = function() {
            bsModal.setAction('upload_file', $scope._uploadFile);
            $scope._getModalView('upload_file');
        };

    }]);

})(window, EE);