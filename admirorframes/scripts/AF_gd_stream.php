<?php

/* ------------------------------------------------------------------------
  # plg_admirorframes - Admiror Frames Plugin
  # ------------------------------------------------------------------------
  # author    Vasiljevski & Kekeljevic
  # copyright Copyright (C) 2011 admiror-design-studio.com. All Rights Reserved.
  # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.admiror-design-studio.com/joomla-extensions
  # Technical Support:  Forum - http://www.vasiljevski.com/forum/index.php
  # Version: 2.1
  ------------------------------------------------------------------------- */
if ($_GET['src_file'] == "")
    exit;

$src_file = urldecode($_GET['src_file']);
$bgcolor = $_GET['bgcolor'];
$colorize = $_GET['colorize'];
$ratio = $_GET['ratio'];

// Create src_img
if (preg_match("/png/i", $src_file)) {
    @$src_img = imagecreatefrompng($src_file);
}

$src_w = imageSX($src_img); //$src_width
$src_h = imageSY($src_img); //$src_height
$src_x = 0;
$src_y = 0;
$dst_w = round($src_w * $ratio);
$dst_h = round($src_h * $ratio);
if ($dst_w < 4)
    $dst_w = 4;
if ($dst_h < 4)
    $dst_h = 4;


if ($colorize != "disable") {
    $AF_colorize_RGB = array(
        base_convert(substr($colorize, 0, 2), 16, 10),
        base_convert(substr($colorize, 2, 2), 16, 10),
        base_convert(substr($colorize, 4, 2), 16, 10)
    );
    $x = 0;
    while ($x < $src_w) {
        $y = 0;
        while ($y < $src_h) {
            $rgb = imagecolorat($src_img, $x, $y);
            $r = (($rgb >> 16) & 0xFF) + $AF_colorize_RGB[0];
            $g = (($rgb >> 8) & 0xFF) + $AF_colorize_RGB[1];
            $b = ($rgb & 0xFF) + $AF_colorize_RGB[2];
            $a = $rgb >> 24;
            $r = ($r > 255) ? 255 : (($r < 0) ? 0 : $r);
            $g = ($g > 255) ? 255 : (($g < 0) ? 0 : $g);
            $b = ($b > 255) ? 255 : (($b < 0) ? 0 : $b);
            $new_pxl = imagecolorallocatealpha($src_img, $r, $g, $b, $a);
            if ($new_pxl == false) {
                $new_pxl = imagecolorclosestalpha($src_img, $r, $g, $b, $a);
            }
            imagesetpixel($src_img, $x, $y, $new_pxl);
            ++$y;
        }
        ++$x;
    }
}


@$dst_img = imagecreatetruecolor($dst_w, $dst_h);

$AF_bgcolor_RGB = array(
    base_convert(substr($bgcolor, 0, 2), 16, 10),
    base_convert(substr($bgcolor, 2, 2), 16, 10),
    base_convert(substr($bgcolor, 4, 2), 16, 10)
);

$AF_BGCOLOR = imagecolorallocate($dst_img, $AF_bgcolor_RGB[0], $AF_bgcolor_RGB[1], $AF_bgcolor_RGB[2]);

imagefill($dst_img, 0, 0, $AF_BGCOLOR);

@imagecopyresampled($dst_img, $src_img, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

@imagejpeg($dst_img, NULL, 96);

@imagedestroy($dst_img);
@imagedestroy($src_img);
?>
