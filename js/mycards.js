jQuery(document).ready(function ($) {
    $('.invitation').click(function(){
        var group = $('input[name="group"]').val();
        var invitee = $('input[name="invitee"]').val();
        var answer = $(this).data('answer');
        $.ajax({
            type: "POST",
            url: AJAX.ajax_url,
            data: {
                action: "acceptInvite",
                invitee: invitee,
                group: group,
                answer: answer,
            },
            beforeSend: function() {
                $('.notification').hide(); 
                // $('#function_action').append('<button class="mui-btn mui-btn--raised mui-btn--primary fullwidth loading" disabled><img src="' + loading + '" style="margin: 0 auto;"/></button>');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            success: function (resp) {
                $('#function_action').append(resp);
                console.log(resp);
            },
        });
        return false;
    });
});