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

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

/* Dự đoán cách xưng hô và điền giúp người dùng */
function checkName(guestinput) {
    /* Đọc dữ liệu 2 element cần thiết */
    var category    = guestinput.previousElementSibling.value;
    var parentNext  = guestinput.parentElement.nextElementSibling;
    var vaive       = parentNext.nextElementSibling;
    var xungho      = vaive.nextElementSibling;
    var updateMode  = xungho.nextElementSibling.nextElementSibling.value;

    /* Nếu không ở updatemode thì xử lý dự đoán */
    if(updateMode == 0){    
        /* Xử lý dữ liệu đã nhận */
        guestName = guestinput.value;
        const first = capitalizeFirstLetter(guestName.split(' ')[0].toLowerCase());

        var list_vaive      = ["Anh", "Chị", "Em", "Cô", "Chú", "Cháu", "Bạn"];
        var list_xungho_nam = ["Em", "Em", "", "Cháu", "Cháu", "", "Tôi"];

        /* Set vai vế và xưng hô theo dự đoán */
        let find_vaive = list_vaive.findIndex(x => x === first)

        if(find_vaive !== -1){
            vaive.firstChild.nextElementSibling.value = first;
            xungho.firstChild.nextElementSibling.value = list_xungho_nam[find_vaive];
        }
    }

    console.log(updateMode);
}

/* Xử lý khi bấm vào radio check tự nhập người mời kèm */
/* function handleChange(checkbox) {
    var guest_attach = document.getElementById("guest_attach");
    console.log(checkbox.checked);
    if(checkbox.checked == true){
        guest_attach.style.display = "block";
    } else {
        guest_attach.style.display = "none";
   }
} */


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