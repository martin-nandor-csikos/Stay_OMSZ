$(document).ready(function() {
    function updateReportCostAndServices() {
        let checkedServices = $('.report-service-checkbox:checked').map(function () {
            return this.value;
        }).get().join(', ');

        $('#services').val(checkedServices || '');

        if ($('#free_treatment').is(':checked')) {
            $('#cost').val(0);
            return;
        }

        let totalCost = 0;

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
    }

    $('.report-service-checkbox, #free_treatment').on('change click', updateReportCostAndServices);
    updateReportCostAndServices();
});
