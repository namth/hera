<?php
/* 
    Template Name: Login
*/
if (is_user_logged_in()) {
    // redirect sang trang chủ
    wp_redirect(get_bloginfo('url'));
    exit;
} else {
    // check form
    if (
        $_SERVER['REQUEST_METHOD'] == 'POST' &&
        isset($_POST['post_nonce_field']) &&
        wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')
    ) {

        if (isset($_POST)) {
            $error = false;

            if (isset($_POST['username']) && ($_POST['username'] != "")) {
                $username = $_POST['username'];
            } else {
                $error = true;
                $error_user = __('Mời bạn nhập User ID / Email.', 'qlcv');
            }

            if (isset($_POST['password']) && ($_POST['password'] != "")) {
                $password = $_POST['password'];
            } else {
                $error = true;
                $error_password = __('Mời bạn nhập mật khẩu.', 'qlcv');
            }

            if (isset($_POST['remember']) && ($_POST['remember'] == "on")) {
                $remember = true;
            } else {
                $remember = false;
            }
        } else $error = true;

        if (!$error) {
            // dùng wp_signon() để đăng nhập
            $user = wp_signon(array(
                'user_login'    => $_POST['username'],
                'user_password' => $_POST['password'],
                'remember'      => $remember,
            ), false);

            // print_r($user);

            $userID = $user->ID;

            wp_set_current_user($userID, $username);
            wp_set_auth_cookie($userID, true, false);
            do_action('wp_login', $username);

            // redirect sang trang chủ
            wp_redirect(get_bloginfo('url'));
            exit;
        }
    }
    // redirect sang trang chủ
}
get_header();

?>
<div id="login">

    <div class="large_left">
        
    </div>
    <div class="small_right mui-panel">
        <?php
        if ($error_user) {
            echo $error_user;
        } else if ($error_password) {
            echo $error_password;
        }
        ?>

        <form class="mui-form" method="POST">
            <legend>Login</legend>
            <div class="mui-textfield mui-textfield--float-label">
                <input type="text" required name="username">
                <label>Username</label>
            </div>
            <div class="mui-textfield mui-textfield--float-label">
                <input type="password" required name="password">
                <label>Password</label>
            </div>
            <?php
            wp_nonce_field('post_nonce', 'post_nonce_field');
            ?>
            <div class="mui-checkbox">
                <label for="remember">
                    <input id="remember" type="checkbox" checked name="remember"> Ghi nhớ.
                </label>
            </div>
            <button type="submit" class="mui-btn mui-btn--raised mui-btn--primary">Đăng nhập</button>
        </form>

    </div>
</div>
<?php
get_footer();
?>