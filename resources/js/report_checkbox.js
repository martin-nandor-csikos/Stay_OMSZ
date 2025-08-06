// When the checkboxes are checked, update the value of price and diagnosis

$(document).ready(function() {
    $('input[type=checkbox]').click(function () {
        let price = parseFloat($('#price').val()) || 0;
        let checkedValues = $('input[type=checkbox]:checked').map(function () {
            return this.value;
        }).get().join(', ');

        $('#diagnosis').val(checkedValues || '');

        price = 0;

        $.each(checkboxPrices, function (id, amount) {
            if ($('#' + id).is(':checked')) {
                if ((price + amount) >= 300000) {
                    price = 300000;
                } else {
                    price += amount;
                }
            }
        });

        $('#price').val(price);
    });
});
