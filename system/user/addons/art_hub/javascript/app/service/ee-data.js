(function(w, ee) {
    "use_strict";

    ee.app.service('EE', function() {

        var inst = this;

        this.getEE = function() {
            if(w.hasOwnProperty('EE')) {
                return w.EE;
            } else {
                return null;
            }
        };

        this.item = function(name) {
            var ee = inst.getEE();
            if(ee.hasOwnProperty(name)) {
                return ee[`${name}`];
            }
            return null;
        }

        this.actionId = function(name) {
            var ee = inst.getEE();
            if(!ee.hasOwnProperty('action_ids')) {
                return null;
            }
            if(!ee.action_ids.hasOwnProperty(name)) {
                return null;
            }
            return ee.action_ids[name];
        };


        this.site_url = function() {
            return this.item('site_url') + this.item('site_index');
        };

    });

})(window, EE);