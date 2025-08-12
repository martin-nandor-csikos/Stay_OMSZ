// When the checkboxes are checked, update the value of price and diagnosis
$(document).ready(function() {
    $('input[type=checkbox]').click(function () {
        let totalCost = parseFloat($('#cost').val()) || 0;
        let checkedServices = $('input[type=checkbox]:checked').map(function () {
            return this.value;
        }).get().join(', ');

        $('#services').val(checkedServices || '');

        totalCost = 0;

        $.each(services, function (service_name, service_cost) {
            if ($('#' + service_name).is(':checked')) {
                if ((totalCost + service_cost) >= 300000) {
                    totalCost = 300000;
                } else {
                    totalCost += service_cost;
                }
            }
        });

        $('#cost').val(totalCost);
    });
});
