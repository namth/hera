function activateModal(data) {
    // tạo popup
    var modalEl = document.getElementById('create_card_form').cloneNode(true);
    modalEl.style.backgroundColor = '#fff';
    modalEl.style.display = 'block';
    modalEl.style.float = 'inherit';

    // hiển thị popup
    mui.overlay('on', modalEl);
    if (data) {
      /* data để sử dụng phân loại category nhóm Nhà Trai hoặc Nhà Gái */
      modalEl.firstElementChild[1].value = data;
    }
    setTimeout(function() { modalEl.firstElementChild[0].focus(); }, 100);
}
function clearAllInterval(){
  // Get a reference to the last interval + 1
  const interval_id = window.setInterval(function(){}, Number.MAX_SAFE_INTEGER);

  // Clear any timeout/interval up to that id
  for (let i = 1; i < interval_id; i++) {
      window.clearInterval(i);
  }
}

jQuery(document).ready(function ($) {
    
    /* Set chiều cao cho sidebar */
    $("#hera_sidebar").height($(".mui-container-fluid").height());

    /* Hàm gọi ajax để update title */
    function update_title() {
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
    }
  
    /* Edit nhóm khách hàng trực tiếp khi click vào chữ trên tiêu đề */
    $(document.body).on('blur', '.breadcrumb .title', function(){
      update_title();
    });

    $(document.body).on('keypress keyup paste input', '.breadcrumb .title', function(e){
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13) { 
        e.preventDefault();
        $(".breadcrumb .title").blur();
        return false;
      }
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
                console.log(obj);
              
                var modalEl = document.getElementById('create_card_form').cloneNode(true);
                modalEl.style.backgroundColor = '#fff';
                modalEl.style.display = 'block';
                modalEl.style.float = 'inherit';
                
                modalEl.firstElementChild[1].value = obj['name'];
                /* if guest_attach is not empty */
                if(obj['guest_attach']){
                  modalEl.firstElementChild[4].value = "";
                  modalEl.firstElementChild[4].checked = true;
                  modalEl.firstElementChild[5].value = obj['guest_attach'];
                }                
                modalEl.firstElementChild[6].value = obj['mine'];
                modalEl.firstElementChild[7].value = obj['your'];
                modalEl.firstElementChild[8].value = obj['phone'];
                modalEl.firstElementChild[9].value = '1';
                modalEl.firstElementChild[10].value = obj['id'];
                console.log(modalEl.firstElementChild);
                mui.overlay('on', modalEl);
            },
        });

        return false;
    });

    /*
    * Setup view to display detail card when customer click to each card.
    */ 
    $(document.body).on('click', '.viewcard', function(event){
        event.preventDefault();

        var parent = $(this).parents().eq(2);
        var next = parent.next().find('.viewcard');
        var prev = parent.prev().find('.viewcard');

        var old_path = window.location.href;
        var new_path = $(this).data('cardlink');
        var options = {
          'keyboard': true, // teardown when <esc> key is pressed (default: true)
          'static': false, // maintain overlay when clicked (default: false)
          'onclose': function() {
            window.history.pushState({}, null, old_path);
          } // quay lai link truoc khi dong thiep 
        };

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
              nextid: next.data('cardid'),
              previd: prev.data('cardid')
            },
            beforeSend: function() {
                detailcard.style.backgroundColor = '#fff';
                detailcard.style.display = 'flex';
                detailcard.style.float = 'inherit';
                // detailcard.innerHTML = resp;
                mui.overlay('on', options, detailcard);
                // set next and prev
            },
            error: function (xhr, ajaxOptions, thrownError) {
              console.log(xhr.status);
              console.log(xhr.responseText);
              console.log(thrownError);
            },
            success: function (resp) {
              window.history.pushState({}, null, new_path);
              detailcard.innerHTML = resp;
            },
        });
        
        return false;
    });

    /* Click next and previous button on popup detailCard */
    $(document.body).on('click', '#direction .arrow', function(event){
      event.preventDefault();
      event.stopPropagation();

      mui.overlay('off');
      var cardelm = $(this).data('card');
      $(cardelm).trigger('click');
      return false;
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
    * Khi bấm mobile menu show menu
    */
    $(document).on('click', ".mobile_menu_icon", function(){
      $(this).parent().find('.menu').show();
    });
    $(document).on('click', ".mobile_menu .overlay, .close_mobile_menu_button", function(){
      $(this).parents().eq(2).find('.menu').hide();
    });

    /* 
    * Khi bấm like cho card, ... 
    */
    $(document).on('click', ".like", function(){
      var cardid = $(this).data('card');
      $(this).find('i').toggleClass('fa-heart-o').toggleClass('fa-heart');
      if($(this).attr('id') == 'like'){
        $('.heracard-' + cardid).toggleClass('fa-heart-o').toggleClass('fa-heart');
      }
      
      console.log(cardid);

      /* $.ajax({
        type: "POST",
        url: AJAX.ajax_url,
        data: {
          action: "pressLikeButton",
          card: cardid
        },
        beforeSend: function () {},
        error: function (xhr, ajaxOptions, thrownError) {
          console.log(xhr.status);
          console.log(xhr.responseText);
          console.log(thrownError);
        },
        success: function (resp) {
          console.log(resp);
        },
      }); */
      return false;
    });

    /* 
    * File: link-to-partner.php
    * Find user by username
    * Return avatar and displayname
    */
    $(document).on('click', '.link_partner_form .see_pass', function(){
      var username = $('input[name="username"]').val();
      $.ajax({
        type: "POST",
        url: AJAX.ajax_url,
        data: {
          action: "findUser",
          username: username
        },
        beforeSend: function () {
          $('#partner_username').next().html('<img src="' + AJAX.loading + '" style="width: 20px; height: 20px;"/>');
        },
        error: function (xhr, ajaxOptions, thrownError) {
          console.log(xhr.status);
          console.log(xhr.responseText);
          console.log(thrownError);
        },
        success: function (resp) {
          var obj = JSON.parse(resp);
          $('#userFinded').html(obj.result);
          if(obj.flag){
            $('button[type="submit"]').removeAttr('disabled');
          } else {
            $('button[type="submit"]').attr('disabled', 'disabled');
          }
        },
      });
    });
});