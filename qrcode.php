<?php 
/* 
* Template name: QR Code generate
*/
/* require_once get_template_directory() . '/vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeNone;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeShrink;
use Endroid\QrCode\Writer\PngWriter;

$result = Builder::create()
    ->writer(new PngWriter())
    ->writerOptions([])
    ->data('http://localhost/hera/qrcode/')
    ->encoding(new Encoding('UTF-8'))
    // ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
    ->size(300)
    ->margin(10)
    ->roundBlockSizeMode(new RoundBlockSizeModeNone())
    // ->logoPath('http://localhost/hera/wp-content/uploads/2023/07/Fuzzy_LeeWedding-logo.png')
    ->logoPath('https://assets-prod.sumo.prod.webservices.mozgcp.net/media/uploads/images/2019-10-22-22-36-41-1c7095.png')
    ->logoResizeToWidth(82)
    ->logoPunchoutBackground(true)
    ->validateResult(false)
    ->build();
 */
// print_r($result);
?>
<!-- <img src="<?php //echo $result->getDataUri(); ?>" alt="QR Code"> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Code Styling</title>
    <script type="text/javascript" src="https://unpkg.com/qr-code-styling@1.5.0/lib/qr-code-styling.js"></script>
</head>
<body>
<div id="canvas"></div>
<script type="text/javascript">

    let dotColor = "#916d45";
    let bgColor = "#916d4500";
    let logo = "";
    let data = "https://www.facebook.com/";

    const qrCode = new QRCodeStyling({
        width: 300,
        height: 300,
        type: "svg",
        data: data,
        image: logo,
        dotsOptions: {
            color: dotColor,
            type: "dots"
        },
        cornersSquareOptions: {
            color: dotColor,
            type: "extra-rounded"
        },
        cornersDotOptions: {
            type: "dot"
        },
        backgroundOptions: {
            color: bgColor,
        },
        imageOptions: {
            crossOrigin: "anonymous",
            margin: 10
        }
    });

    qrCode.append(document.getElementById("canvas"));
    // qrCode.download({ name: "qr", extension: "svg" });
</script>
</body>
</html>