<?php 
get_header();
get_template_part('header', 'topbar');

if (have_posts()) {
    while (have_posts()) {
        the_post();

        # get data from invoice
        $id_invoice = get_the_ID();
        $customer = get_field('customer');
        $status = get_field('status');
        $normal_cards = get_field('normal_cards');
        $vip_cards = get_field('vip_cards');
        $coupon_id = get_field('coupon');
        $total = get_field('total');
        $sub_total = get_field('sub_total');
        $vat = get_field('vat');
        $final_total = get_field('final_total');

        # calculate payment date
        $create_date = get_the_date('U');
        $payment_date = date_i18n('j F Y', strtotime('+7 day', $create_date));

        # Theme option
        $normal_price = get_field('normal_price','option');
        $vip_price = get_field('vip_price','option');

        # coupon
        $coupon_name = get_field('coupon_name', $coupon_id);
        $coupon_type = get_field('coupon_type', $coupon_id);
        $coupon_value = get_field('coupon_value', $coupon_id);
?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-2">
            <?php
            get_sidebar();
            ?>
        </div>
        <div class="mui-col-md-10">
            <div class="mui-panel" id="checkout">
                <h3 class="title_general mui--divider-bottom">Hoá đơn số <?php echo get_the_title(); ?></h3>
                <div class="mui-row">
                    <div class="mui-col-md-6">
                        <table>
                            <tr>
                                <td>Ngày tạo hóa đơn:</td>
                                <td><?php echo date_i18n('j F Y', $create_date); ?></td>
                            </tr>
                            <tr>
                                <td>Hạn thanh toán:</td>
                                <td><?php echo $payment_date; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="mui-col-md-6" id="status">
                        <?php 
                            if ($status == "Chưa thanh toán") {
                                $status_class = "error_notification";
                            } else if ($status == "Đã thanh toán") {
                                $status_class = "success_notification";
                            } else {
                                $status_class = "notification";
                            }
                        ?>
                        <span class="<?php echo $status_class; ?>"><?php echo $status; ?></span>
                    </div>
                </div>
                <div class="mui-row">
                    <div class="mui-col-md-12" id="invoice">
                        <h4>Invoice Items</h4>
                        <table>
                            <thead>
                                <tr>
                                    <th>Mô tả chi tiết</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    if ($normal_cards) {
                                        echo "<tr>
                                                <td>Thiệp thường</td>
                                                <td>" . $normal_cards . "</td>
                                                <td>" . number_format($normal_price) . " ₫</td>
                                                <td>" . number_format($normal_cards * $normal_price) . "</td>
                                            </tr>";
                                    
                                    }
                                    if ($vip_cards) {
                                        echo "<tr>
                                                <td>Thiệp VIP</td>
                                                <td>" . $vip_cards . "</td>
                                                <td>" . number_format($vip_price) . " ₫</td>
                                                <td>" . number_format($vip_cards * $vip_price) . " ₫</td>
                                            </tr>";
                                    
                                    }
                                ?>
                                <tr class="total">
                                    <td colspan="2"></td>
                                    <td>Tổng tiền dịch vụ</td>
                                    <td><?php echo number_format($total) . " ₫"; ?></td>
                                </tr>
                                <?php 
                                    if ($coupon_id) {
                                        if ($coupon_type == "Phần trăm") {
                                            $coupon = number_format(- $coupon_value) . "%";
                                        } else {
                                            $coupon = number_format(- $coupon_value) . " ₫";
                                        }
                                        echo "<tr class='coupon'>
                                                <td colspan='2'></td>
                                                <td>Mã giảm giá <span class='code'>" . $coupon_name . "</span></td>
                                                <td>" . $coupon . "</td>
                                            </tr>";
                                    }
                                    if ($sub_total) {
                                        echo "<tr class='sub_total'>
                                                <td colspan='2'></td>
                                                <td>Tổng tiền sau giảm giá</td>
                                                <td>" . number_format($sub_total) . " ₫</td>
                                            </tr>";
                                    
                                    }
                                ?>
                                <tr class="vat">
                                    <td colspan="2"></td>
                                    <td>Thuế VAT (10%)</td>
                                    <td><?php echo number_format($vat); ?></td>
                                </tr>
                                <tr class="final_total">
                                    <td colspan="2"></td>
                                    <td>Tổng tiền thanh toán</td>
                                    <td><?php echo number_format($final_total); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mui-col-md-12" id="payment">
                        <ul class="mui-tabs__bar mui-tabs__bar--justified">
                            <li class="mui--is-active"><a data-mui-toggle="tab" data-mui-controls="bank_transfer">Tab-1</a></li>
                            <li><a data-mui-toggle="tab" data-mui-controls="pay_momo">Tab-2</a></li>
                        </ul>
                        <div class="mui-tabs__pane mui--is-active" id="bank_transfer">
                            <div class="payment_notification">
                                <h5>Lưu ý</h5>
                                <p>Để hệ thống tự động kích hoạt ngay trong 1 phút (ngay sau khi AZDIGI nhận đủ thanh toán qua ngân hàng), quý khách vui lòng thực hiện đủ các bước sau:

                                    Nhập chính xác HD179410 vào nội dung chuyển khoản.
                                    Chuyển khoản đúng số tiền là 3,049,200 đ để hệ thống tự nhận diện kích hoạt ngay.

                                    Nếu quý khách chuyển khoản khác ngân hàng có thể dùng hình thức chuyển khoản nhanh để được kích hoạt ngay.

                                    Quý khách nhận hóa đơn VAT vui lòng gửi yêu cầu tại đây. Hóa đơn sẽ được gửi cho quý khách trong vòng 7 ngày kể từ ngày yêu cầu.</p>
                            </div>
                        </div>
                        <div class="mui-tabs__pane" id="pay_momo">
                            <button class="mui-btn hera-btn">Thanh toan momo</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    }
}
get_footer();