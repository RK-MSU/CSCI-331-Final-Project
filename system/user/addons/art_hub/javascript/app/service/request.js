(function(w, ee) {
    "use_strict";

    ee.app.service('request', ['bsModal', function(bsModal) {

        let print_response = true;

        this.data = function(values) {

        };

        // success
        this.success = function(response) {

            if(print_response) {
                console.log(response.data);
            }

            var data = response.data;
            // check type
            if(typeof data != 'object') {
                alert('Successful Response is not type: object\nSee console.');
                console.error(data);
                return;
            }
            // ensure status is recieved
            if(!data.hasOwnProperty('status')) {
                alert('Response does not have status.\nSee console.');
                console.error(data);
                return;
            }

            // success status
            if(data.status != 'success') {
                var status = data.status;
                alert(`Response status: ${status}.\nSee console.`);
                console.error(data);
                return;
            }

            if(data.hasOwnProperty('modal')) {
                var modal_html = data.modal;
                var modal = bsModal.newModal(modal_html);
                // modal.elem.addEventListener('hidden.bs.modal', function (event) {
                //     delete modal;
                // });
                modal.show();
                // console.log(modal_html);
            }
        };

        // error
        this.error = function(response) {
            console.error(response.statusText);
            console.error(response.data);
            if(typeof response.data == 'object') {
                alert(JSON.stringify(response.data));
            } else {
                alert(response.data);
            }
        };

    }]);

})(window, EE);