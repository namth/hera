<?php 
// print_r($_POST);
if (have_posts()) {
    while (have_posts()) {
        the_post();

        $current_user = wp_get_current_user();
        # get data from invoice
        $id_invoice = get_the_title();
        $customer   = get_field('customer');
        $status     = get_field('status');
        $activate   = get_field('activate');
        
        $package_id     = get_field('package');
        $coupon_id      = get_field('coupon');
        $total          = get_field('total');
        $final_total    = get_field('final_total');
        $paid           = get_field('paid');

        # calculate payment date
        $create_date = get_the_date('U');
        $payment_date = date_i18n('j F Y', strtotime('+7 day', $create_date));

        # coupon
        $coupon_name = get_field('coupon_name', $coupon_id);
        $coupon_type = get_field('coupon_type', $coupon_id);
        $coupon_value = get_field('coupon_value', $coupon_id);

        if ( isset($_POST['momo_field']) &&
        wp_verify_nonce($_POST['momo_field'], 'momo') ) {
            
            $config = file_get_contents(get_template_directory_uri() . '/inc/config.json');
            $array = json_decode($config, true);
            
            
            $endpoint = "https://test-payment.momo.vn/gw_payment/transactionProcessor";
            // $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
            
            // print_r($array);
            $partnerCode = $array["partnerCode"];
            $accessKey = $array["accessKey"];
            $secretKey = $array["secretKey"];
            $orderInfo = "Thanh toán qua MoMo";
            $amount = $final_total;
            $orderId = time() . "";
            $returnUrl = get_permalink();
            $notifyurl = $returnUrl;
            // Lưu ý: link notifyUrl không phải là dạng localhost
            $extraData = "";

            $requestId = time() . "";
            $requestType = "captureMoMoWallet";
            
            //before sign HMAC SHA256 signature
            $rawHash =  "partnerCode=" . $partnerCode . 
                        "&accessKey=" . $accessKey . 
                        "&requestId=" . $requestId . 
                        "&amount=" . $amount . 
                        "&orderId=" . $orderId . 
                        "&orderInfo=" . $orderInfo . 
                        "&returnUrl=" . $returnUrl . 
                        "&notifyUrl=" . $notifyurl . 
                        "&extraData=" . $extraData;
            $signature = hash_hmac("sha256", $rawHash, $secretKey);
            echo "Signature: ";
            print_r($signature);
            $data = array('partnerCode' => $partnerCode,
                'accessKey' => $accessKey,
                'requestId' => $requestId,
                'requestType' => $requestType,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'returnUrl' => $returnUrl,
                'notifyUrl' => $notifyurl,
                'extraData' => $extraData,
                'signature' => $signature);
            $result = wp_remote_post($endpoint, $data);
            $jsonResult = json_decode(wp_remote_retrieve_body($result));
            
            echo "Ket qua goi API: ";
            print_r($jsonResult);
            // header('Location: ' . $jsonResult['payUrl']);
        }

        get_header();
        get_template_part('header', 'topbar');


?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-2 npl">
            <?php
            get_sidebar();
            ?>
        </div>
        <div class="mui-col-md-10">
            <div class="breadcrumb">
                <a href="<?php echo get_bloginfo('url'); ?>">Trang chủ</a>
                <i class="fa fa-chevron-right"></i>
                <a href="<?php echo get_bloginfo('url') ."/danh-sach-don-hang/"; ?>">Danh sách hoá đơn</a>
                <i class="fa fa-chevron-right"></i>
                <span><?php echo get_the_title(); ?></span>
            </div>
            <div class="mui-panel" id="checkout">
                <h3 class="title_general mui--divider-bottom">Mã đơn hàng: <b><?php echo get_the_title(); ?></b></h3>
                <div class="mui-row">
                    <div class="mui-col-md-6 mui-col-sm-7">
                    <?php 
                        $done_payment = false;
                        if (in_array($status, ["Chưa thanh toán", "Thanh toán thiếu"])) {
                            $total_label = $status == "Chưa thanh toán" ? "Thành tiền":"Cần thanh toán";
                            $status_class = "error_notification";
                            echo "<table>
                                    <tr>
                                        <td>Ngày tạo hóa đơn:</td>
                                        <td>" . date_i18n('j F Y', $create_date) . "</td>
                                    </tr>
                                    <tr>
                                        <td>Hạn thanh toán:</td>
                                        <td>" . $payment_date . "</td>
                                    </tr>
                                </table>";
                        } else if (in_array($status, ["Đã thanh toán", "Thanh toán dư"])) {
                            $total_label = "Tiền còn thừa";
                            $status_class = "success_notification";
                            $done_payment = true;
                        } else {
                            $status_class = "notification";
                        }
                    ?>
                        
                    </div>
                    <div class="mui-col-md-6 mui-col-sm-5" id="status">
                        <span class="<?php echo $status_class; ?>"><?php echo $status; ?></span>
                    </div>
                </div>
                <div class="mui-row">
                    <div class="mui-col-md-6 mt20 btxs">
                        <h4 style="font-weight:bold;">Nhà cung cấp dịch vụ</h4>
                        <?php 
                            $inova_info = get_field('inova_info', 'option');
                            echo wpautop($inova_info);
                        ?>
                    </div>
                    <div class="mui-col-md-6 mt20 btxs">
                        <h4 style="font-weight:bold;">Thông tin khách hàng</h4>
                        <?php 
                            # Lấy thông tin khách hàng mua hàng
                            $displayname = $current_user->display_name;
                            $user_email  = $current_user->user_email;
                            $user_phone  = get_field('phone', 'user_' . $current_user->ID);
                            $user_address  = get_field('address', 'user_' . $current_user->ID);

                            echo wpautop('<i class="fa fa-user-circle-o" aria-hidden="true"></i> ' . $displayname);
                            echo wpautop('<i class="fa fa-envelope-o" aria-hidden="true"></i> ' . $user_email);
                            echo wpautop('<i class="fa fa-phone" aria-hidden="true"></i> ' . $user_phone);
                            echo wpautop('<i class="fa fa-address-book-o" aria-hidden="true"></i> ' . $user_address);

                        ?>
                    </div>
                </div>
                <div class="mui-row">
                    <div class="mui-col-md-12" id="invoice">
                        <h4>Invoice Items</h4>
                        <table>
                            <thead>
                                <tr>
                                    <th>Mô tả chi tiết</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    echo "<tr>
                                            <td>" . get_the_title($package_id) . "</td>
                                            <td>" . number_format($total) . " ₫</td>
                                        </tr>";

                                    # Hiển thị mã coupon nếu có.
                                    if ($coupon_id) {
                                        if ($coupon_type == "Phần trăm") {
                                            $coupon = number_format($coupon_value) . "%";
                                        } else {
                                            $coupon = number_format($coupon_value) . " ₫";
                                        }
                                        echo "<tr class='coupon'>
                                                <td>Mã giảm giá <span class='code'>" . $coupon_name . "</span></td>
                                                <td>" . $coupon . "</td>
                                            </tr>";
                                    }

                                    # Hiển thị số tiền đã thanh toán
                                    if ($paid) {
                                        echo "<tr class='sub_total'>
                                                <td>Đã thanh toán</td>
                                                <td>" . number_format($paid) . " ₫</td>
                                            </tr>";

                                        $final_total -= $paid; 
                                    }

                                    echo "<tr class='final_total'>
                                            <td style='border-top: 1px solid lightgray;'>" . $total_label . "</td>
                                            <td style='border-top: 1px solid lightgray;'>" . number_format(abs($final_total)) . " ₫</td>
                                        </tr>";
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php 
                        if (!$done_payment) {
                    ?>
                    <div class="mui-col-md-12" id="payment">
                        <ul class="mui-tabs__bar mui-tabs__bar--justified">
                            <li class="mui--is-active bank_transfer"><a data-mui-toggle="tab" data-mui-controls="bank_transfer">CHUYỂN KHOẢN NGÂN HÀNG</a></li>
                            <li class="pay_momo"><a data-mui-toggle="tab" data-mui-controls="pay_momo">THANH TOÁN QUA MOMO</a></li>
                        </ul>
                        <div class="mui-tabs__pane mui--is-active" id="bank_transfer">
                            <div class="payment_notification">
                                <h5><b>Lưu ý</b></h5>
                                <p>Để hệ thống tự động kích hoạt ngay trong 1 phút (ngay sau khi chúng tôi nhận đủ thanh toán qua ngân hàng), quý khách vui lòng thực hiện đủ các bước sau:</p>
                                <ul>
                                    <li>Nhập chính xác <b><?php echo $id_invoice; ?></b> vào nội dung chuyển khoản.</li>
                                    <li>Chuyển khoản đúng số tiền là <b><?php echo number_format($final_total) . " ₫"; ?></b> để hệ thống tự nhận diện kích hoạt ngay.</li>
                                </ul>

                                <p>Nếu quý khách chuyển khoản khác ngân hàng có thể dùng hình thức chuyển khoản nhanh để được kích hoạt ngay.</p>
                            </div>
                        </div>
                        <div class="mui-tabs__pane" id="pay_momo">
                            <form action="#" method="POST" enctype="multipart/form-data">
                                <?php wp_nonce_field('momo', 'momo_field'); ?>
                                <button class="mui-btn hera-btn">QR CODE</button>
                            </form>
                        </div>
                    </div>
                    <?php 
                        }
                    
                        if (!$activate) {
                    ?>
                    <div id="check_payment" class="mui-col-md-12">
                        <button class="mui-btn hera-btn active_now">Kích hoạt dịch vụ ngay</button>
                        <input type="hidden" name="order_id" value="<?php echo get_the_ID(); ?>">
                    </div>
                    <?php 
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="fullloading">
    <div>
        <h3 class="description"><span class="blink_me">Đang đồng bộ hoá với ngân hàng ...</span></h3>
        <img src="<?php echo get_template_directory_uri(); ?>/img/bank_loading.gif" alt="" />
        <span class="close_button">X</span>
    </div>
</div>
<script>
    jQuery(document).ready(function ($) {
        // Gọi lệnh đồng bộ tới casso
        $("#check_payment .active_now").click(function(){
            $.ajax({
                type: "POST",
                url: AJAX.ajax_url,
                data: {
                    action: "syncCasso",
                },
                beforeSend: function() {
                    $("#fullloading").css('display','flex');
                    $("#fullloading .description").html('<span class="blink_me">Đang đồng bộ hoá với ngân hàng ...</span>');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(xhr.responseText);
                    console.log(thrownError);
                },
                success: function (resp) {
                    // var obj = JSON.parse(resp);

                },
            });

            // Cài đặt bộ hẹn giờ kiểm tra order 
            setInterval(checkOrder, 2000);
            return false;
        });

        function checkOrder(){
            var order_id = $('input[name="order_id"]').val();
            // Gọi ajax để kiểm tra hoá đơn cho tới khi được kích hoạt
            $.ajax({
                type: "POST",
                url: AJAX.ajax_url,
                data: {
                    action: "checkOrder",
                    order: order_id
                },
                beforeSend: function() {
                    $("#fullloading .description").html('<span class="blink_me">Kiểm tra giao dịch ...</span>');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(xhr.responseText);
                    console.log(thrownError);
                },
                success: function (resp) {
                    console.log(resp);
                    var obj = JSON.parse(resp);
                    if (obj['done'] == true) {
                        clearAllInterval();

                        // redirect to thank you page 
                        window.location.replace(obj['url']);
                    }
                },
            });
        }

        // Xử lý khi bấm vào nút close trên màn hình
        $("#fullloading .close_button").click(function(){
            /* Ẩn loading */
            $("#fullloading").css('display','none');
            
            /* Xoá check */
            clearAllInterval();
        });

    });
</script>
<?php
    }
}
get_footer();