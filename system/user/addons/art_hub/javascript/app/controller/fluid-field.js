(function(w, ee) {
    "use_strict";

    ee.app.controller('fluidFieldController', ['$scope', '$rootScope', '$http', '$timeout', 'bsModal', 'dataMod', 'EE', 'fileField', 'request', function($scope, $rootScope, $http, $timeout, bsModal, dataMod, EE, fileField, request) {
        
        // $scope.field = {
        //     name: null,
        //     children: [],
        //     settings: {
        //         child_fields: {}
        //     }
        // };

        $scope.$watch('_field', function(data) { $scope.field = dataMod.fromString(data); });
        $scope._selected_child_field = null;

        $scope.addField = function(ft) {
            let new_field_name = $scope.field.children.length+1;
            
            let new_field = {
                label: ft.label,
                instructions: ft.instructions,
                type: ft.type,
                value: '',
                name: 'field_id_' + ft.id.toString(),
                row_name: 'new_field_' + new_field_name.toString(),
                required: ft.required,
            };

            if(ft.hasOwnProperty('meta')) {
                new_field['meta'] = ft.meta;
            }

            if(new_field.type == 'file') {
                new_field['file'] = {
                    src: null,
                    title: null
                };
            }
            
            $scope.field.children.push(new_field);
        };


        $scope._getModalView = function(modalView, _meta) {

            let data = {
                ACT: EE.actionId('get_view'),
                csrf_token : EE.item('CSRF_TOKEN'),
                meta : _meta,
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

        $scope.selectFieldFile = function(file) {
            // $scope.field.value = file.value;
            // $scope.file.src = file.src;
            // $scope.file.title = file.title;
            $scope._selected_child_field.value = file.value;
            $scope._selected_child_field.file.src = file.src;
            $scope._selected_child_field.file.title = file.title;
            console.log(file);
            $scope._selected_child_field = null;
            bsModal.hideActiveModal();
            bsModal.removeAction('upload_file');
            delete $rootScope.selectFile;
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
                        $scope._selected_child_field.value = data.field.value;
                        $scope._selected_child_field.file.src = data.file.src;
                        $scope._selected_child_field.file.title = data.file.title;
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

        $scope.chooseExisting = function(ft) {
            console.log(ft);
            $scope._selected_child_field = ft;
            $rootScope.selectFile = $scope.selectFieldFile;
            bsModal.setAction('upload_file', function(){
                bsModal.hideActiveModal();
                bsModal.removeAction('upload_file');
                delete $rootScope.selectFile;
                $scope.uploadNew(ft);
            });
            // bsModal.setAction('upload_file', $scope.uploadNew);
            $scope._getModalView('choose_existing_file', ft.meta);
        };

        $scope.uploadNew = function(ft) {
            console.log(ft);
            $scope._selected_child_field = ft;
            bsModal.setAction('upload_file', $scope._uploadFile);
            $scope._getModalView('upload_file', ft.meta);
        };

        $scope.removeChildField = function(ft) {
            for(var i = 0; i < $scope.field.children.length; i++) {
                let child = $scope.field.children[i];
                console.log(child);
                if(ft.row_name == child.row_name) {
                    $scope.field.children.splice(i, 1);
                    break;
                }
            }
        };


    }]);


})(window, EE);