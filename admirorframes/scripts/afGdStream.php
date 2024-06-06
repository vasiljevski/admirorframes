<?php

/* ------------------------------------------------------------------------
  # plg_admirorframes - Admiror Frames Plugin
  # ------------------------------------------------------------------------
  # author    Vasiljevski & Kekeljevic
  # copyright Copyright (C) 2011 admiror-design-studio.com. All Rights Reserved.
  # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.admiror-design-studio.com/joomla-extensions
  # Technical Support:  Forum - http://www.vasiljevski.com/forum/index.php
  # Version: 5.0.0
  ------------------------------------------------------------------------- */

function get_parameter($id)
{
    return isset($_GET[$id]) ? basename(urldecode($_GET[$id])) : '';
}

$base_dir = realpath(__DIR__ . '/..');

$id = get_parameter('id');
$template = get_parameter('template');
$bgcolor = get_parameter('bgcolor');
$colorize = get_parameter('colorize');
$ratio = get_parameter('ratio');

$src_file = $base_dir . DIRECTORY_SEPARATOR . 'templates/' . $template . DIRECTORY_SEPARATOR . $id . '.png';

// Ensure src_file is not empty and has a valid format
if (empty($src_file) || !preg_match('/\.png$/i', $src_file) || preg_match('/^https?:\/\//i', $src_file)) {
    exit();
}

$src_img = @imagecreatefrompng($src_file);
if ($src_img === false) {
    exit();
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


// Ensure all necessary variables are set and valid
if (!isset($dst_w, $dst_h, $bgcolor, $src_img, $src_x, $src_y, $src_w, $src_h)) {
    exit();
}

// Create a true color image
$dst_img = imagecreatetruecolor($dst_w, $dst_h);
if (!$dst_img) {
    exit();
}

// Convert background color from hex to RGB
$AF_bgcolor_RGB = array(
    hexdec(substr($bgcolor, 0, 2)),
    hexdec(substr($bgcolor, 2, 2)),
    hexdec(substr($bgcolor, 4, 2))
);

// Allocate background color
$AF_BGCOLOR = imagecolorallocate($dst_img, $AF_bgcolor_RGB[0], $AF_bgcolor_RGB[1], $AF_bgcolor_RGB[2]);
if ($AF_BGCOLOR === false) {
    imagedestroy($dst_img);
    exit();
}

// Fill the image with the background color
imagefill($dst_img, 0, 0, $AF_BGCOLOR);

// Copy and resample the source image
if (!imagecopyresampled($dst_img, $src_img, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)) {
    imagedestroy($dst_img);
    imagedestroy($src_img);
    exit();
}

// Set the content type header to PNG
header('Content-Type: image/png');

// Output the image as PNG
if (!imagepng($dst_img)) {  // Use imagepng() for PNG format
    imagedestroy($dst_img);
    imagedestroy($src_img);
    exit();
}

// Free up memory
imagedestroy($dst_img);
imagedestroy($src_img);
