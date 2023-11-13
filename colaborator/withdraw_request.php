<?php 
    $current_user_id = get_current_user_id();
    $where_update = 'user_' . $current_user_id;

    $commission_withdraw = get_field('commission_withdraw', $where_update);
    $partner_name   = get_field('partner_name', $where_update); # ten cty doi tac
    $bank_number    = get_field('bank_number', $where_update);
    $bank           = get_field('bank', $where_update);
    $bank_account   = get_field('bank_account', $where_update);

    if ($bank_account && $bank_number && $bank) {
        $has_bank_acc = true;
    } else $has_bank_acc = false;

    if ( isset($_POST['post_nonce_field']) &&
    wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce') ) {
        $withdraw = strip_tags($_POST['withdraw']);

        #update bank
        if ($withdraw >= '50000') {
            $args = array(
                'post_title'    => $current_user_id . ' - ' . $partner_name . ' rút ' . number_format($withdraw) . 'đ',
                'post_status'   => 'publish',
                'post_type'     => 'invoice',
            );
        
            $inserted = wp_insert_post($args, $error);
        
            if ($inserted) {
                update_field('field_644127d11b108', "Mới", $inserted); # Payment Status
                update_field('field_644125621a5bf', $commission_withdraw, $inserted); # withdrawn_amount
                update_field('field_644126381a5c0', $withdraw, $inserted); # request_amount
                
                $thongbao = '<span class="success_notification">Đã gửi yêu cầu thành công, chúng tôi sẽ xử lý trong thời gian sớm nhất.</span>';
            }
        } else $thongbao = '<span class="error_notification">Hiện tại hệ thống không thể xử lý yêu cầu này, hãy liên hệ với chúng tôi qua hotline để được hỗ trợ.</span>';
    }
?>
<h3>Yêu cầu rút tiền</h3>
<div class="mui-row" id="withdraw">
    <div class="mui-col-md-8">
        <div class="mui-panel" id="withdraw_form"> 
            <?php 
                if ($thongbao) {
                    echo $thongbao;
                }
            ?>
            <h3>Số tiền có thể rút</h3>
            <span class="commission"><?php echo number_format($commission_withdraw); ?>đ</span>

            <h4>Nhập thông tin rút tiền</h4>
            <form class="mui-form" method="POST" enctype="multipart/form-data">
                <div class="commission_request mui-textfield">
                    <label for="withdraw">Số tiền rút tối thiểu 50,000đ</label>
                    <input type="number" name="withdraw" value="<?php echo $total; ?>">
                    <input type="hidden" name="max" value="<?php echo $commission_withdraw; ?>">
                </div>
                <?php
                wp_nonce_field('post_nonce', 'post_nonce_field');
                ?>
                <div class="confirm_request">
                    <button type="submit" class="mui-btn hera-btn">Yêu cầu rút</button>
                    <span class="amount"></span>
                </div>
            </form>
        </div>
    </div>
    <div class="mui-col-md-4" id="commission">
        <div class="mui-panel" id="withdraw_info">
            <div class="title">
                <h3>Thông tin nhận tiền</h3>
                <?php 
                    if ($has_bank_acc) {
                        echo '<a href="' . get_permalink() . '?direct=bank&t=edit' . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
                    }
                ?>
            </div>
            <div class="bank_info">
                
                <?php 
                    if ($partner_name) {
                        echo '<span class="partner_name">' . $partner_name . '</span>';
                    }

                    if ($has_bank_acc) {
                        echo '<span class="bank"><i class="fa fa-university" aria-hidden="true"></i> ' . $bank . '</span>
                            <span class="name"><i class="fa fa-user-circle-o" aria-hidden="true"></i> ' . $bank_account . '</span>
                            <span class="bank_number"><i class="fa fa-credit-card" aria-hidden="true"></i> ' . $bank_number . '</span>';
                    } else {
                        echo '<a href="' . get_permalink() . '?direct=bank" class="add_new"><i class="fa fa-plus" aria-hidden="true"></i> Thêm thông tin nhận tiền</a>';
                    }
                    
                ?>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/colaborator/js/withdraw.js"></script>