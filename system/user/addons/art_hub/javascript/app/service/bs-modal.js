(function(w, ee) {
    "use_strict";

    ee.app.service('bsModal', ['$rootScope', '$compile', function($rootScope, $compile) {
        let inst = this;
        let defaultOptions = {
            backdrop: true, // Includes a modal-backdrop element. Alternatively, specify static for a backdrop which doesn't close the modal on click.
            keyboard: true, // Closes the modal when escape key is pressed
            focus: true // Puts the focus on the modal when initialized.
        };
        let pageModalCon = document.getElementById('pageModalContainer');
        let active_modal = null;
        let actions = {};

        this.setAction = function(name, fnc) {
            actions[name] = fnc;
        };

        this.removeAction = function(name) {
            if(actions.hasOwnProperty(name)) {
                delete actions[name];
                return true;
            }
            return false;
        };

        $rootScope.modalAction = function(name) {
            if(!actions.hasOwnProperty(name)) {
                console.error("bsModal does not have action: " + name);
                console.error(actions);
                return;
            }
            let fnc = actions[name];
            return fnc();
        }

        this.hideActiveModal = function(){
            if(active_modal != null) {
                active_modal.hide();
            }
        };

        this.getActiveModal = function() {
            if(active_modal != null) {
                return active_modal;
            }
            return null;
        };
        
        this.compileModalHTML = function(elem = null) {
            elem = (elem == null) ? pageModalCon : elem;
            $compile($(elem).contents())($rootScope);
            EE.Tooltips.enableAll();
        };

        this.newModal = function(modal_html) {
            $(pageModalCon).append(modal_html);
            var modalElem = $(pageModalCon).children().last().get(0);
            inst.compileModalHTML(modalElem);

            //var bsModal = bootstrap.Modal.getOrCreateInstance(modalElem, inst.defaultOptions);

            var modalObj = {};

            // elem
            Object.defineProperty(modalObj, "elem", {
                writable : false,      // Property value can be changed
                enumerable : false,    // Property can be enumerated
                configurable : false,  // Property can be reconfigured
                value : modalElem
            });
            Object.defineProperty(modalObj, "modal", {
                writable : false,
                enumerable : false,
                configurable : false,
                value : new bootstrap.Modal(modalElem, inst.defaultOptions)
            });
            Object.defineProperty(modalObj, "shown", {
                writable : true,
                enumerable : false,
                configurable : false,
                value : false
            });
            Object.defineProperty(modalObj, "show", {
                // writable : false,
                enumerable : false,
                configurable : false,
                value : function() {
                    this.modal.show();
                }
            });
            Object.defineProperty(modalObj, "hide", {
                // writable : false,
                enumerable : false,
                configurable : false,
                value : function() {
                    this.modal.hide();
                }
            });
            // Object.defineProperty(modalObj, "remove", {
            //     // writable : false,
            //     enumerable : false,
            //     configurable : false,
            //     value : function() {
            //         var m = this;
            //         var del = function() {
            //             m.modal.dispose();
            //             $(m.elem).remove();
            //         };
            //         if(this.shown == false) {
            //             del();
            //         } else {
            //             this.elem.addEventListener('hidden.bs.modal', function (event) {
            //                 del();
            //             });
            //             this.modal.hide();
            //         }
                    
            //     }
            // });

            modalObj.elem.addEventListener('show.bs.modal', function (event) {
                modalObj.shown = true;
                active_modal = modalObj;
                console.log(active_modal);
            });
            modalObj.elem.addEventListener('hide.bs.modal', function (event) {
                active_modal = null;
            });
            modalObj.elem.addEventListener('hidden.bs.modal', function (event) {
                modalObj.shown = false;
                modalObj.modal.dispose();
                // $(modalObj.elem).remove();
            });

            return modalObj;
        };

    }]);
    
})(window, EE);