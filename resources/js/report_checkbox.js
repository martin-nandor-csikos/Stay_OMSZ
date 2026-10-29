$(document).ready(function() {
    function updateReportCostAndServices() {
        const freeTreatment = $('#free_treatment');
        const npcCall = $('#npc_call');

        npcCall.prop('disabled', freeTreatment.is(':checked'));
        freeTreatment.prop('disabled', npcCall.is(':checked'));
        freeTreatment.closest('.report-treatment-option').toggleClass('is-disabled', freeTreatment.is(':disabled'));
        npcCall.closest('.report-treatment-option').toggleClass('is-disabled', npcCall.is(':disabled'));

        let checkedServices = $('.report-service-checkbox:checked').map(function () {
            return this.value;
        }).get().join(', ');

        $('#services').val(checkedServices || '');

        if (freeTreatment.is(':checked') || npcCall.is(':checked')) {
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

    $('.report-service-checkbox, #free_treatment, #npc_call').on('change click', updateReportCostAndServices);
    updateReportCostAndServices();
});
