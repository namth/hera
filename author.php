<?php 
get_header();
get_template_part('header', 'topbar');

/* $userID = get_current_user_id();
$link_avatar = get_avatar_url($userID, array('size' => '2000'));
echo "Link avatar: ";
print_r($link_avatar);
$current_attachment_id = attachment_url_to_postid($link_avatar);
echo "<br>Attach ID: ";
print_r($current_attachment_id);
wp_delete_attachment($current_attachment_id); */

?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-2">
            <?php
            get_sidebar();
            ?>
        </div>
        <div class="mui-col-md-8">
            <!-- <div class="breadcrumb">
                <a href="<?php echo get_bloginfo('url'); ?>"><i class="fa fa-home" aria-hidden="true"></i></a>
                
            </div> -->
            <div class="mui-panel">
                <h3 class="title_general">Thông tin tài khoản</h3>
                <div class="user_infomation">
                <?php 
                    $user = wp_get_current_user();
                    $link_avatar = get_avatar_url($user->ID);
                    $phone = get_field('phone', 'user_' . $user->ID);
                    $address = get_field('address', 'user_' . $user->ID);
                    echo '<div class="avatar">
                            <img src="' . $link_avatar . '">
                            <a href="" class="upload"><i class="fa fa-camera-retro" aria-hidden="true"></i></a>
                        </div>';
                ?>
                <div class="uploadform" id="uploadform">
                    <form class="box" method="post" action="" enctype="multipart/form-data">
                        <div class="box__input">
                            <input class="box__file" type="file" name="files[]" id="file" data-multiple-caption="{count} files selected" multiple />
                            <label for="file" style="display: block;">
                                <figure>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="34" viewBox="0 0 20 17">
                                        <path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/>
                                    </svg>
                                </figure> 
                                <strong>Tải tệp lên</strong><br>
                                <span class="box__dragndrop">hoặc kéo ảnh vào đây</span>.
                            </label>
                        </div>
                        <div class="box__uploading">Uploading…</div>
                        <div class="box__success">Done!</div>
                        <div class="box__error">Error! <span></span>.</div>
                        
                    </form>
                </div>
                <?php
                    echo '<table>
                            <tr>
                                <td>Tên đăng nhập:</td>
                                <td>' . $user->user_login . '</td>
                            </tr>
                            <tr>
                                <td>Họ và tên:</td>
                                <td>' . $user->display_name . '</td>
                            </tr>
                            <tr>
                                <td>Email:</td>
                                <td>' . $user->user_email . '</td>
                            </tr>';
                    if ($phone) {
                        echo '<tr>
                                <td>Điện thoại:</td>
                                <td>' . $phone . '</td>
                            </tr>';
                    }
                    echo '</table>';
                ?>
                </div>
                
                <div class="royal_line">
                    <div class="line"></div>
                    <img src="<?php echo get_template_directory_uri() . '/img/royal_line_1.png'; ?>" alt="">
                </div>
                <div class="update_user_button">
                    <a href="http://" class="mui-btn hera-btn">Sửa thông tin tài khoản</a>
                    <a href="http://" class="mui-btn hera-btn">Đổi mật khẩu</a>
                </div>
            </div>
        </div>
        <div class="mui-col-md-2">
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function ($) {

        $(".avatar .upload").click(function () {
            $(".avatar").hide();
            $("#uploadform").show();
            return false;
        });

        var isAdvancedUpload = function() {
            var div = document.createElement('div');
            return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
        }();
        var $form = $('.box');
        var $input    = $form.find('input[type="file"]'),
            $label    = $form.find('label'),
            showFiles = function(files) {
                $label.text(files.length > 1 ? ($input.attr('data-multiple-caption') || '').replace( '{count}', files.length ) : files[ 0 ].name);
            };

        if (isAdvancedUpload) {
            $form.addClass('has-advanced-upload');

            var droppedFiles = false;

            $form.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
            })
            .on('dragover dragenter', function() {
                $form.addClass('is-dragover');
            })
            .on('dragleave dragend drop', function() {
                $form.removeClass('is-dragover');
            })
            .on('drop', function(e) {
                droppedFiles = e.originalEvent.dataTransfer.files;
                showFiles( droppedFiles );
                $form.trigger('submit');
            });

            $input.on('change', function(e) {
                showFiles(e.target.files);
                $form.trigger('submit');
            });

            $form.on('submit', function(e) {
                if ($form.hasClass('is-uploading')) return false;
                $form.addClass('is-uploading').removeClass('is-error');

                if (isAdvancedUpload) {
                    // ajax for modern browsers
                    e.preventDefault();
                    var ajaxData = new FormData($form[0]);
                    ajaxData.append("action", "uploadAvatar");
                    // console.log(ajaxData);

                    if (droppedFiles) {
                        $.each( droppedFiles, function(i, file) {
                            ajaxData.append( $input.attr('name'), file );
                        });
                    }

                    $.ajax({
                        type: "POST",
                        url: AJAX.ajax_url,
                        data: ajaxData,
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        complete: function() {
                            $form.removeClass('is-uploading');
                        },
                        success: function(data) {
                            console.log(data);
                            if (data) {
                                location.reload();
                            } else {
                                
                            }
                            // $form.addClass( data.success  == true ? 'is-success' : 'is-error' );
                            // if (!data.success) $errorMsg.text(data.error);
                        },
                        error: function() {
                            // Log the error, show an alert, whatever works for you
                        }
                    });
                    
                } else {
                    // ajax for legacy browsers
                    var iframeName  = 'uploadiframe' + new Date().getTime();
                        $iframe   = $('<iframe name="' + iframeName + '" style="display: none;"></iframe>');

                    $('body').append($iframe);
                    $form.attr('target', iframeName);

                    $iframe.one('load', function() {
                        var data = JSON.parse($iframe.contents().find('body' ).text());
                        $form
                            .removeClass('is-uploading')
                            .addClass(data.success == true ? 'is-success' : 'is-error')
                            .removeAttr('target');
                        if (!data.success) $errorMsg.text(data.error);
                        $form.removeAttr('target');
                        $iframe.remove();
                    });
                }
            });
        }

        // $('#file').change(handleFileSelect);
    });
</script>
<?php

get_footer();