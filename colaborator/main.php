<?php 
    $current_user_id = get_current_user_id();
    $where_update = 'user_' . $current_user_id;

    $number_of_clicks   = get_field('number_of_clicks', $where_update);
    $number_of_order    = get_field('number_of_order', $where_update);
    $number_of_card     = get_field('number_of_card', $where_update);
    $monthly_sales      = get_field('monthly_sales', $where_update);
    $commission_received = get_field('commission_received', $where_update);
    $commission_withdraw = get_field('commission_withdraw', $where_update);

    $aff_link = get_bloginfo('url') . '/link?u=' . $current_user_id;

    # tính toán mức chiết khấu 
    if ($monthly_sales < '3000000') {
        $discount_amount = '0.15';
    } else if (($monthly_sales >= '3000000') && ($monthly_sales < '6000000')) {
        $discount_amount = '0.2';
    } else if (($monthly_sales >= '6000000') && ($monthly_sales < '9000000')) {
        $discount_amount = '0.25';
    } else {
        $discount_amount = '0.3';
    }
    
?>
<h3 class="title_general mui--divider-bottom">Cộng tác viên</h3>
<div class="mui-row">
    <div class="mui-col-md-9">
        <div class="mui-row" id="affiliate_info">
            <div class="mui-col-md-4 info_box">
                <div class="mui-panel">
                    <span class="stat"><?php echo number_format($number_of_clicks); ?></span>    
                    <span class="label">Lượt Click</span>
                </div>
            </div>
            <div class="mui-col-md-4 info_box">
                <div class="mui-panel">
                    <span class="stat"><?php echo number_format($number_of_order); ?></span>    
                    <span class="label">Số gói thiệp đã cung cấp</span>
                </div>
            </div>
            <div class="mui-col-md-4 info_box">
                <div class="mui-panel">
                    <span class="stat"><?php echo number_format($number_of_card); ?></span>    
                    <span class="label">Số lượng thiệp được sử dụng</span>
                </div>
            </div>
        </div>
        <div class="mui-row">
            <div class="mui-col-md-12">
                <div id="affiliate_detail">   
                    <div class="aff_link">
                        <div class="aff_label">Liên kết giới thiệu của bạn</div>
                        <input class="aff_input" type="text" readonly="" value="<?php echo $aff_link; ?>">
                    </div>
                    <div class="mui-panel">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mui-col-md-3" id="commission">
        <div class="mui-panel">        
            <div class="panel-body">
                <div class="summary-total">
                    <div class="price">
                        <span class="price-total">Số tiền hoa hồng được hưởng</span>
                        <span class="price-amount"><?php echo number_format($commission_withdraw); ?> đ</span>
                    </div>
                </div>
                <div class="summary-content">
                    <ul class="summary-list">
                        <li class="list-item faded">
                            <span class="item-name">Doanh số tháng</span>
                            <span class="item-value"><?php echo number_format($monthly_sales); ?> đ</span>
                        </li>
                        <li class="list-item faded">
                            <span class="item-name">Mức chiết khấu hiện tại</span>
                            <span class="item-value"><?php echo number_format($discount_amount * 100); ?>%</span>
                        </li>
                        <li class="list-item faded">
                            <span class="item-name">Hoa hồng chờ nhận</span>
                            <span class="item-value"><?php echo number_format($monthly_sales * $discount_amount); ?> đ</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="panel-footer">
                <?php
                    if ($commission_withdraw >= 100000) {
                        echo '<a href="?direct=withdrawrequest" class="mui-btn hera-btn">
                            <i class="fa fa-credit-card" aria-hidden="true"></i> Yêu cầu thanh toán
                        </a>';
                    } else {
                        echo '<button class="mui-btn hera-btn" disabled>
                            <i class="fa fa-credit-card" aria-hidden="true"></i> Yêu cầu thanh toán
                        </button>';
                    }
                ?>
            </div>
        </div>
    </div>
</div>