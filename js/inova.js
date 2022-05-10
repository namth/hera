function activateModal() {
    // initialize modal element
    var modalEl = document.getElementById('create_card_form').cloneNode(true);
    modalEl.style.backgroundColor = '#fff';
    modalEl.style.display = 'block';
    modalEl.style.float = 'inherit';

    // show modal
    mui.overlay('on', modalEl);
    setTimeout(function() { document.getElementById("group_input").focus(); }, 1000);
}

function editguest(guestid) {
    // var data = 
    var modalEl = document.getElementById('create_card_form').cloneNode(true);
    modalEl.style.backgroundColor = '#fff';
    modalEl.style.display = 'block';
    modalEl.style.float = 'inherit';
    
    modalEl.firstElementChild[0].value = "Name " + guestid;
    modalEl.firstElementChild[1].value = "Called " + guestid;
    modalEl.firstElementChild[2].value = "Phone " + guestid;
    console.log(modalEl.firstElementChild);
    mui.overlay('on', modalEl);
    

}

jQuery(document).ready(function ($) {
    $(".edit_guest").click(function(){
        var groupid = $("input[name=groupid]").val();
        var guestid = $(this).data('guest');

        $.ajax({
            type: "POST",
            url: AJAX.ajax_url,
            data: {
              action: "edit_guest",
              groupid: groupid,
              guestid: guestid,
            },
            error: function (xhr, ajaxOptions, thrownError) {
              console.log(xhr.status);
              console.log(xhr.responseText);
              console.log(thrownError);
            },
            success: function (resp) {
                var obj = JSON.parse(resp);
              
                var modalEl = document.getElementById('create_card_form').cloneNode(true);
                modalEl.style.backgroundColor = '#fff';
                modalEl.style.display = 'block';
                modalEl.style.float = 'inherit';
                
                modalEl.firstElementChild[0].value = obj['name'];
                modalEl.firstElementChild[1].value = obj['guest_attach'];
                modalEl.firstElementChild[2].value = obj['mine'];
                modalEl.firstElementChild[3].value = obj['your'];
                modalEl.firstElementChild[4].value = obj['phone'];
                modalEl.firstElementChild[5].value = '1';
                console.log(modalEl.firstElementChild);
                mui.overlay('on', modalEl);
            },
        });

        return false;
    });
});