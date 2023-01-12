function setClipboard(value) {
    var tempInput = document.createElement("input");
    tempInput.style = "position: absolute; left: -1000px; top: -1000px";
    tempInput.value = value;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);

    return false;
}
jQuery(document).ready(function ($) {
    /* copy_link */
    $('.copy_link').click(function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var copylink = $(this);
        setClipboard(url);
        copylink.html('<i class="fa fa-check-circle" aria-hidden="true"></i> Đã copy');
        copylink.addClass('success_copied');

        /* Restore link */
        setTimeout(function(){
            copylink.html('Copy link');
            copylink.removeClass('success_copied');
        }, 4000);
    });

    /* Bấm vào checkbox đã mời và cập nhật vào database */
    $('.sent_friend').change(function() {
        var field = $(this).data('field');
        var ischecked= $(this).is(':checked')?1:0;
        var row_index = $(this).data('index');
        var groupid = $('input[name="groupid"]').val();

        $.ajax({
            type: "POST",
            url: AJAX.ajax_url,
            data: {
                row: row_index,
                action: "updateSentFriend",
                field: field,
                ischecked: ischecked,
                groupid: groupid,
            },
            beforeSend: function() {
                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            success: function (resp) {
                // console.log(resp);
            },
        });
    });
});