(function(w, ee) {
    'use_strict';

    ee.jQueryUIDatePicker = {};

    let getInputDate = function (elem) {
        let input_value = $(elem).val();
        if(input_value.toString().length == 0) {
            return null;
        }
        // unix timestamp
        try {
            let unix_timestamp = null;
            unix_timestamp = parseInt(input_value);
            if(!isNaN(unix_timestamp) && unix_timestamp.toString().length == 10) {
                return new Date(unix_timestamp * 1000);
            }
        } catch (error) {
            console.error(error);
        }
        // string date
        let match = /([0-9]+\/[0-9]+\/[0-9]+)/i.exec(input_value);
        if(match != null) {
            let date_str = match[0];
            return new Date(match[0]);
        }
        return null;
    }

    let renderDateInputs = function() {
        $('input.datepicker').each(function() {
            let elem = this;
            let datePicker = $(elem).datepicker({
                dateFormat: "mm/dd/yy",
                // showOn: "both",
            });
            let input_date = getInputDate(elem);
            if(input_date != null) {
                datePicker.datepicker("setDate", input_date);
            }

        });
    };

    ee.jQueryUIDatePicker.render = renderDateInputs;

    w.addEventListener('DOMContentLoaded', function() {
        ee.jQueryUIDatePicker.render();
    });

})(window, EE);