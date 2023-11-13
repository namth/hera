<?php 
    $history_link   = $_SERVER['HTTP_REFERER'];
    $current_user_id = get_current_user_id();
    $where_update = 'user_' . $current_user_id;
    
    $commission_withdraw = get_field('commission_withdraw', $where_update);
    $partner_name   = get_field('partner_name', $where_update); # ten cty doi tac
    $bank_number    = get_field('bank_number', $where_update);
    $bank           = get_field('bank', $where_update);
    $bank_name      = get_field('bank_account', $where_update);
    
    if ( isset( $_GET['t'] ) && ($_GET['t'] != "") ) {
        $type = $_GET['t'];
        if ($type == 'edit') {
            $label = 'Sửa';
        } else $label = 'Thêm';
    } else if ($bank_number && $bank) {
        $label = 'Sửa';
    } else $label = 'Thêm';
    

    if ($bank_number && $bank) {
        $has_bank_acc = true;
    } else $has_bank_acc = false;

    if ( isset($_POST['post_nonce_field']) &&
    wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce') ) {
        $bank = strip_tags($_POST['bank']);
        $bank_number = strip_tags($_POST['bank_number']);
        $bank_name = strip_tags($_POST['bank_name']);
        $history_link = strip_tags($_POST['history_link']);

        #update bank
        if ($bank_number) {
            update_field('field_63eb4b43d2cb4', $bank_number, $where_update);
            update_field('field_63eb4b57d2cb5', $bank, $where_update);
            update_field('field_63eb4b68d2cb6', $bank_name, $where_update);

            wp_redirect($history_link);
            exit;
        }
    }
?>
<div class="mui-row" id="withdraw">
    <div class="mui-col-md-8">
        <div class="mui-panel" id="withdraw_form"> 
            <h3><?php echo $label; ?> thông tin nhận tiền</h3>
            <form class="mui-form" method="POST" enctype="multipart/form-data">
                <div class="commission_request mui-textfield">
                    <label for="bank">Tên ngân hàng</label>
                    <input type="text" name="bank" value="<?php echo $bank; ?>">
                </div>
                <div class="commission_request mui-textfield">
                    <label for="bank_number">Số tài khoản</label>
                    <input type="text" name="bank_number" value="<?php echo $bank_number; ?>">
                </div>
                <div class="commission_request mui-textfield">
                    <label for="bank_name">Người sở hữu</label>
                    <input type="text" name="bank_name" value="<?php echo $bank_name; ?>">
                </div>
                <?php
                echo '<input type="hidden" name="history_link" value="' . $history_link . '">';
                wp_nonce_field('post_nonce', 'post_nonce_field');
                ?>
                <div class="confirm_request">
                    <button type="submit" class="mui-btn hera-btn"><?php echo $label; ?> thông tin</button>
                </div>
            </form>
        </div>
    </div>
    <div class="mui-col-md-4" id="commission">
        <div class="mui-panel" id="withdraw_info">
            <div class="title">
                <h3>Thông tin nhận tiền</h3>
            </div>
            <div class="bank_info">
                
                <?php 
                    if ($partner_name) {
                        echo '<span class="partner_name">' . $partner_name . '</span>';
                    }

                    if ($has_bank_acc) {
                        echo '<span class="bank"><i class="fa fa-university" aria-hidden="true"></i> ' . $bank . '</span>
                            <span class="name"><i class="fa fa-user-circle-o" aria-hidden="true"></i> ' . $bank_name . '</span>
                            <span class="bank_number"><i class="fa fa-credit-card" aria-hidden="true"></i> ' . $bank_number . '</span>';
                    } else {
                        echo '<a href="' . get_permalink() . '?direct=bank" class="add_new"><i class="fa fa-plus" aria-hidden="true"></i> Thêm thông tin nhận tiền</a>';
                    }
                    
                ?>
            </div>
        </div>
    </div>
</div>