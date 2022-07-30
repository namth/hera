function activateModal(data) {
    // tạo popup
    var modalEl = document.getElementById('create_card_form').cloneNode(true);
    modalEl.style.backgroundColor = '#fff';
    modalEl.style.display = 'block';
    modalEl.style.float = 'inherit';

    // hiển thị popup
    mui.overlay('on', modalEl);
    modalEl.firstElementChild[1].value = data;
    setTimeout(function() { modalEl.firstElementChild[0].focus(); }, 100);
}

jQuery(document).ready(function ($) {


  
    /* Edit nhóm khách hàng trực tiếp khi click vào chữ trên tiêu đề */
    $(document.body).on('blur', '.breadcrumb .title', function(){
      var content = $('.breadcrumb .title').text();
      var guestid = $('.breadcrumb .title').data('guestid');
      
      $.ajax({
        type: "POST",
        url: AJAX.ajax_url,
        data: {
          action: "updateCustomerGroup",
          content: content,
          guestid: guestid,
        },
        beforeSend: function() {
          $('.breadcrumb .loader').css('opacity', 1);
        },
        error: function (xhr, ajaxOptions, thrownError) {
          console.log(xhr.status);
          console.log(xhr.responseText);
          console.log(thrownError);
        },
        success: function (resp) {
          $('.breadcrumb .loader').css('opacity', 0);
        },
      });
    });

    /* 
    * Xử lý ajax khi bấm nút xoá một khách mời trong một group
    */
    $(".del_customer").click(function(){
      var selector = $(this).parents().eq(1);
      var del_data = $(this).data('del');
      $.ajax({
        type: "POST",
        url: AJAX.ajax_url,
        data: {
          action: "deleteCustomer",
          content: del_data,
        },
        beforeSend: function() {
          selector.addClass('delete');
          console.log(selector);
        },
        error: function (xhr, ajaxOptions, thrownError) {
          console.log(xhr.status);
          console.log(xhr.responseText);
          console.log(thrownError);
        },
        success: function (resp) {
          var selector = $(".delete");
          selector.remove();
        },
      });

      return false;
    });
    
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
    $(document.body).on('click', '.viewcard', function(){
        var cardid = $(this).data('cardid');
        var detailcard = document.getElementById('detail_card').cloneNode(true);
        var groupid;
        if($('input[name="groupid"]').val()){
          groupid = $('input[name="groupid"]').val();
        } else {
          groupid = 0;
        }
        $.ajax({
            type: "POST",
            url: AJAX.ajax_url,
            data: {
              action: "viewDetailCard",
              cardid: cardid,
              groupid: groupid,
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
                detailcard.innerHTML = resp;
            },
        });
    });
    
    $(document.body).on('click', '#select_card', function(){
      var groupid = $('#select_card').data('groupid');
      var loading = $('#select_card').data('loading');
      if(groupid){
        var cardid = $('input[name="cardid"]').val();
        var thumbnail = $('input[name="thumbnail"]').val();
        $.ajax({
          type: "POST",
          url: AJAX.ajax_url,
          data: {
            action: "addCardToSelectedGroup",
            cardid: cardid,
            groupid: groupid,
            thumbnail: thumbnail,
          },
          beforeSend: function() {
              $('#select_card').hide(); 
              $('#detail_data_box').append('<button class="mui-btn mui-btn--raised mui-btn--primary fullwidth" disabled><img src="' + loading + '" style="margin: 0 auto;"/></button>');
          },
          error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr.status);
            console.log(xhr.responseText);
            console.log(thrownError);
          },
          success: function (resp) {
            window.location.replace(resp);
          },
        });
      } else {
        $('.use_card form').toggle(200);
      }
    });
    $(document.body).on('click', '#close_select_card', function(){
      $('.use_card form').hide(200);
      return false;
    });
    /* 
    * Khi click chọn vào các group khách mời và ấn chọn thiệp này thì sẽ bổ sung mẫu thiệp vào cho group khách mời
    */
    $(document.body).on("click", ".use_card form input[type='submit']", function(){
      var $data = $(".use_card form").serialize();
      $.ajax({
        type: "POST",
        url: AJAX.ajax_url,
        data: {
          action: "addCardToCustomerGroup",
          data: $data,
        },
        beforeSend: function () 
        {
          $(".use_card form").fadeOut(200);
        },
        error: function (xhr, ajaxOptions, thrownError) {
          console.log(xhr.status);
          console.log(xhr.responseText);
          console.log(thrownError);
        },
        success: function (resp) {
          firstDiv = '<div id="notification">';
          endDiv = '</div>';
          $('.use_card').append(firstDiv + resp + endDiv).show(200);
          setTimeout(function(){
            if ($('#notification').length > 0) {
              $('#notification').remove();
            }
          }, 5000)
        },
      });
      return false;
    });

    /* 
    * Khi bấm like cho card, ... 
    */
    $(document).on('click', "#like", function(){
      alert('alo');
    });
});