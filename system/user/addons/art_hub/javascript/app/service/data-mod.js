(function(w, ee) {
    "use_strict";

    ee.app.service('dataMod', function() {
        this.fromString = function (val) {
            try {
                return JSON.parse(atob(val));
            } catch (err) {
                console.error(err);
            }
            return null;
        }
    });
    
})(window, EE);