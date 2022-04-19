<?php
/* 
    Template Name: Edit Cards
*/
get_header();

if (isset($_POST['html']) && ($_POST['html'] != "")) {
    update_field('field_60e16c5bab331', $_POST['html'], $postid);
    update_field('field_60e16c8bab332', $_POST['css'], $postid);
    update_field('field_60e2a2eda1482', $_POST['components'], $postid);
    update_field('field_60e2a2e0a1481', $_POST['assets'], $postid);
    update_field('field_60e2a2fca1483', $_POST['styles'], $postid);
}


if (isset($_GET['id']) && $_GET['id']) {
    $postid = $_GET['id'];
    $html = get_field('html', $postid);
    $css = get_field('css', $postid);
    $assets = get_field('assets', $postid);
    $components = get_field('components', $postid);
    $styles = get_field('styles', $postid);

?>
    <div class="mui-container-fluid">
        <div class="mui-row">
            <div class="mui-col-md-2">
                <button class="mui-btn mui-btn--danger" onclick="activateModal()">Tạo thiệp mới</button>
            </div>
            <div class="mui-col-md-7">
                <div class="mui-panel">
                    <h3>Sửa thiệp</h3>
                    <form class="mui-form" method="POST">
                        <div class="mui-textfield">
                            <label for="html">HTML</label>
                            <textarea placeholder="HTML" name="html"><?php echo $html; ?></textarea>
                        </div>
                        <div class="mui-textfield">
                            <label for="css">CSS</label>
                            <textarea placeholder="CSS" name="css"><?php echo $css; ?></textarea>
                        </div>
                        <div class="mui-textfield">
                            <label for="components">Components</label>
                            <textarea placeholder="Components" name="components"><?php echo $components; ?></textarea>
                        </div>
                        <div class="mui-textfield">
                            <label for="assets">Assets</label>
                            <textarea placeholder="Assets" name="assets"><?php echo $assets; ?></textarea>
                        </div>
                        <div class="mui-textfield">
                            <label for="styles">Styles</label>
                            <textarea placeholder="Styles" name="styles"><?php echo $styles; ?></textarea>
                        </div>

                        <button type="submit" class="mui-btn mui-btn--raised">Submit</button>
                    </form>
                </div>
            </div>
            <div class="mui-col-md-3"></div>
        </div>
    </div>
    <div class="mui-col-md-4" id="create_card_form">
        <form class="mui-form" method="POST">
            <legend>Tạo thiệp mới</legend>
            <div class="mui-textfield">
                <input type="text" placeholder="Tên mẫu" name="card_name">
            </div>
            <button type="submit" class="mui-btn mui-btn--primary">Submit</button>
        </form>
    </div>
<?php
}
get_footer();
?>