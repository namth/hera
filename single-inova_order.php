<?php 
if (have_posts()) {
    while (have_posts()) {
        the_post();

        $popup = false;
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
        $uuid           = get_field('uuid');

        # calculate payment date
        $create_date = get_the_date('U');
        $payment_date = date_i18n('j F Y', strtotime('+7 day', $create_date));

        # coupon
        $coupon_name = get_field('coupon_name', $coupon_id);
        $coupon_type = get_field('coupon_type', $coupon_id);
        $coupon_value = get_field('coupon_value', $coupon_id);

        # Xử lý khi bấm vào nút thanh toán momo
        if ( isset($_POST['momo_field']) &&
        wp_verify_nonce($_POST['momo_field'], 'momo') ) {
                        
            $orderInfo = "Thanh toán qua MoMo";
            $amount = $final_total;
            $orderId = incrementalHash(10);
            $redirectUrl = get_permalink();
            $ipnUrl = get_bloginfo('url') . '/wp-json/hera/v1/momo_endpoint';
            $extraData = base64_encode('{"inova_orderid" : ' . get_the_ID() . '}');


            $requestId = gen_uuid();
            $requestType = "captureWallet";
            
            //before sign HMAC SHA256 signature
            $rawHash =  
                "accessKey=" . MOMO_ACCESS_KEY . 
                "&amount=" . $amount . 
                "&extraData=" . $extraData .
                "&ipnUrl=" . $ipnUrl .
                "&orderId=" . $orderId .
                "&orderInfo=" . $orderInfo .
                "&partnerCode=" . MOMO_PARTNER_CODE .
                "&redirectUrl=" . $redirectUrl .
                "&requestId=" . $requestId .
                "&requestType=" . $requestType;

            $signature = hash_hmac("sha256", $rawHash, MOMO_SECRET_KEY);

            // echo "Signature: ";
            // print_r($signature);
            $data = array(
                'partnerCode'   => MOMO_PARTNER_CODE,
                'partnerName'   => 'INOVA',
                'storeId'       => 'Hera',
                'requestType'   => $requestType,
                'ipnUrl'        => $ipnUrl,
                'redirectUrl'   => $redirectUrl,
                'orderId'       => $orderId,
                'amount'        => $amount,
                "lang"          => "vi",
                'orderInfo'     => $orderInfo,
                'requestId'     => $requestId,
                'extraData'     => $extraData,
                'signature'     => $signature,
                'item'          => [
                    'id'        => get_the_ID(),
                    'name'      => get_the_title($package_id),
                    'description' => '',
                    'price'     => $amount,
                    'quantity'  => 1,
                    'unit'      => 'Gói',
                    'totalPrice'=> $amount,
                    'taxAmount' => ''
                ],
            );

            $result = wp_remote_post(MOMO_ENDPOINT, array(
                'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
                'body'        => json_encode($data),
                'method'      => 'POST',
                'data_format' => 'body',
            ));
            $jsonResult = json_decode(wp_remote_retrieve_body($result));

            // print_r($jsonResult);
            if ($jsonResult->payUrl) {
                # Lưu lại pay URL của momo để xác minh giao dịch sau này.
                update_field('field_636c856e9d08d', $jsonResult->payUrl);
                wp_redirect($jsonResult->payUrl);
                exit;
            }
        }

        # Xử lý nếu có response trả về
        if (isset($_GET['partnerCode'])) {
            $momopay = momo_check_order($_GET);
            if ($momopay) {
                # Nếu thanh toán thành công thì chuyển sang trang cảm ơn
                wp_redirect(get_bloginfo("url") . "/thank-you/");
                exit;
            } else {
                # Nếu không thì thông báo lỗi để người dùng thanh toán lại 
                if ($_GET['message'] != ""){
                    $popup = true;
                    $message = $_GET['message'];
                }

            }
        }

        get_header();
        get_template_part('header', 'topbar');

        if ($popup) {
            echo '<div class="popup_momo">
                    <div>
                        <img src="' . get_template_directory_uri() . '/img/payment-fail.png" alt="">
                        <span>' . $message . '</span>
                        <p>Bạn có thể bấm vào tab "THANH TOÁN QUA MOMO" để thanh toán lại.</p>
                        <span class="close_popup">x</span>
                    </div>
                </div>';
        }
        
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
                        $close_payment = ($final_total == 0);
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
                            $close_payment = true;
                        } else {
                            $status_class = "notification";
                            $close_payment = true;
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
                                        if ($coupon_name) {
                                            $coupon_label = "<span class='code'>" . $coupon_name . "</span>";
                                        }
                                        echo "<tr class='coupon'>
                                                <td>Mã giảm giá " . $coupon_label . "</td>
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
                        if (!$close_payment) {
                    ?>
                    <div class="mui-col-md-12" id="payment">
                        <ul class="mui-tabs__bar mui-tabs__bar--justified">
                            <!-- <li class="mui--is-active bank_transfer"><a data-mui-toggle="tab" data-mui-controls="bank_transfer">CHUYỂN KHOẢN NGÂN HÀNG</a></li> -->
                            <li class="mui--is-active pay_momo"><a data-mui-toggle="tab" data-mui-controls="pay_momo">THANH TOÁN QUA MOMO</a></li>
                        </ul>
                        <!-- <div class="mui-tabs__pane mui--is-active" id="bank_transfer">
                            <div class="payment_notification">
                                <h5><b>Lưu ý</b></h5>
                                <p>Để hệ thống tự động kích hoạt ngay trong 1 phút (ngay sau khi chúng tôi nhận đủ thanh toán qua ngân hàng), quý khách vui lòng thực hiện đủ các bước sau:</p>
                                <ul>
                                    <li>Nhập chính xác <b><?php echo $id_invoice; ?></b> vào nội dung chuyển khoản.</li>
                                    <li>Chuyển khoản đúng số tiền là <b><?php echo number_format($final_total) . " ₫"; ?></b> để hệ thống tự nhận diện kích hoạt ngay.</li>
                                </ul>

                                <p>Nếu quý khách chuyển khoản khác ngân hàng có thể dùng hình thức chuyển khoản nhanh để được kích hoạt ngay.</p>
                                <p>Quý khách có thể chuyển vào một trong số các tài khoản dưới đây:</p>
                                <div class="bank_info">
                                    <ul>
                                        <li><img src="<?php echo get_template_directory_uri(); ?>/img/tpbank.webp" alt="TPBank" width="250"></li>
                                        <li>Số tài khoản: <b>14719869999</b></li>
                                        <li>Tên tài khoản: <b>TRAN HAI NAM</b></li>
                                    </ul>
                                    <ul>
                                        <li><img src="<?php echo get_template_directory_uri(); ?>/img/techcombank.svg" alt="Techcombank" width="333"></li>
                                        <li>Số tài khoản: <b>19038145926015</b></li>
                                        <li>Tên tài khoản: <b>Công ty TNHH Công Nghệ INOVA</b></li>
                                    </ul>
                                </div>
                                <?php 
                                    if (!$activate) {
                                ?>
                                <div id="check_payment" class="mui-col-md-12">
                                    <p><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Sau khi chuyển khoản xong, quý khách có thể bấm vào nút bên dưới để hệ thống tự động kiểm tra tài khoản và kích hoạt dịch vụ.</p>
                                    <button class="mui-btn hera-btn active_now">Kích hoạt dịch vụ ngay</button>
                                    <input type="hidden" name="order_id" value="<?php echo get_the_ID(); ?>">
                                </div>
                                <?php 
                                    }
                                ?>
                            </div>
                        </div> -->
                        <div class="mui-tabs__pane mui--is-active" id="pay_momo">
                            <h3 style="text-align: center;">Hệ thống sẽ tự động kích hoạt ngay sau khi chúng tôi nhận đủ thanh toán qua Momo<br> quý khách vui lòng bấm vào nút bên dưới để thanh toán cho đơn hàng:</h3>
                            <br>
                            <form action="#" method="POST" enctype="multipart/form-data">
                                <?php wp_nonce_field('momo', 'momo_field'); ?>
                                <img src="<?php echo get_template_directory_uri() ?>/img/logomomo.png" alt="" width="100px">
                                <button class="mui-btn hera-btn">Chuyển tới trang thanh toán</button>
                            </form>
                            <br>
                        </div>
                    </div>
                    <?php 
                        } else {
                            $active_data = inova_encrypt(json_encode([
                                'package_id'    => $package_id,
                                'cards'         => get_field('cards'),
                                'order_id'      => get_the_ID()
                            ]), 'e');
                            # Nếu đã đóng payment mà chưa được kích hoạt thì chuyển đến trang kích hoạt ngay.
                            if (!$activate && $status == "Đã thanh toán") {
                                echo '<a href="' . $active_data . '" class="mui-btn hera-btn active_free" style="margin: 5px auto;display: table;">Kích hoạt ngay</a>';
                            }
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
        <p>Việc xác nhận chuyển khoản có thể mất khoảng 10 phút<br> bạn có thể quay lại trang chủ và chờ đợi thiệp được kích hoạt.</p>
        <span class="close_button">X</span>
    </div>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/js/single-inova_order.js"></script>
<?php
    }
}
get_footer();