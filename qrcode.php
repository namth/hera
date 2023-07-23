<?php 
/* 
* Template name: QR Code generate
*/
require_once get_template_directory() . '/vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

$result = Builder::create()
    ->writer(new PngWriter())
    ->writerOptions([])
    ->data('http://localhost/hera/qrcode/')
    ->encoding(new Encoding('UTF-8'))
    // ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
    // ->size(300)
    ->margin(10)
    ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
    // ->logoPath('http://localhost/hera/wp-content/uploads/2023/07/Fuzzy_LeeWedding-logo.png')
    ->logoPath('http://localhost/hera/wp-content/uploads/2023/07/Leewedding.png')
    ->logoResizeToWidth(82)
    ->logoPunchoutBackground(true)
    ->validateResult(false)
    ->build();

// print_r($result);
?>
<img src="<?php echo $result->getDataUri(); ?>" alt="QR Code">