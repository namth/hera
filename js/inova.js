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

jQuery(document).ready(function ($) {

    /*
    * Edit guest in each single card group.
    * Get guestid and check it in database, then call ajax to process all the edited parts.
    */ 
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
                modalEl.firstElementChild[6].value = obj['id'];
                console.log(modalEl.firstElementChild);
                mui.overlay('on', modalEl);
            },
        });

        return false;
    });

    /*
    * Setup view to display detail card when customer click to each card.
    */ 
    $(".viewcard").click(function(){
        var cardid = $(this).data('cardid');
        var detailcard = document.getElementById('detail_card').cloneNode(true);

        $.ajax({
            type: "POST",
            url: AJAX.ajax_url,
            data: {
              action: "viewDetailCard",
              cardid: cardid,
            },
            beforeSend: function() {
                detailcard.style.backgroundColor = '#fff';
                detailcard.style.display = 'block';
                detailcard.style.float = 'inherit';
                // detailcard.innerHTML = resp;
                mui.overlay('on', detailcard);
            },
            error: function (xhr, ajaxOptions, thrownError) {
              console.log(xhr.status);
              console.log(xhr.responseText);
              console.log(thrownError);
            },
            success: function (resp) {
                // var detailcard = document.getElementById('detail_card').cloneNode(true);
                
                // detailcard.style.backgroundColor = '#fff';
                // detailcard.style.display = 'block';
                // detailcard.style.float = 'inherit';
                detailcard.innerHTML = resp;
                // mui.overlay('on', detailcard);
            },
        });
    });
    
});