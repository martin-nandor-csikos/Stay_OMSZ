// When the checkboxes are checked, update the value of price and diagnosis

$(document).ready(function() {
    $('input[type=checkbox]').click(function () {
        let price = parseFloat($('#price').val()) || 0;
        let checkedValues = $('input[type=checkbox]:checked').map(function () {
            return this.value;
        }).get().join(', ');

        $('#diagnosis').val(checkedValues || '');

        let checkboxPrices = {
            'vizs': 20000,
            'kot': 30000,
            'gip': 35000,
            'gyogy': 30000,
            'kav': 20000,
            'as': 30000,
            'ss': 35000,
            'emb': 20000,
            'th': 150000
        };

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