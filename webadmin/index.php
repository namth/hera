<div class="mui-row">
    <div class="mui-col-md-3">
        <div class="box">
            <h3>Tổng số người dùng</h3>
            <h2><?php echo get_user_count(); ?></h2>
        </div>
    </div> 
    <div class="mui-col-md-3">
        <div class="box">
            <h3>Tổng số mẫu thiệp</h3>
            <h2>-</h2>
        </div>
    </div> 
    <div class="mui-col-md-3">
        <div class="box">
            <h3>Tổng số đơn hàng</h3>
            <h2><?php echo wp_count_posts("inova_order")->publish; ?></h2>
        </div>
    </div> 
    <div class="mui-col-md-3">
        <div class="box">
        </div>
    </div> 
</div>
