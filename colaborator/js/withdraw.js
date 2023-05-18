jQuery(document).ready(function ($) {
    $(document).on('keypress keyup paste input', "input[name='withdraw']", function(){
        var amount = parseInt($(this).val());
        var max = parseInt($('input[name="max"]').val());
        var formatter = new Intl.NumberFormat('vi', {
            style: 'currency',
            currency: 'VND',
        });

        if (amount > max) {
            amount = max;
            $(this).val(amount);
        }
        if (isNaN(amount)) {
            $('.confirm_request .amount').html('');
        } else {
            $('.confirm_request .amount').html(formatter.format(amount));
        }
    });
});