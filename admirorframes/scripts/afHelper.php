<?php

/* ------------------------------------------------------------------------
  # plg_admirorframes - Admiror Frames Plugin
  # ------------------------------------------------------------------------
  # author    Vasiljevski & Kekeljevic
  # copyright Copyright (C) 2011 admiror-design-studio.com. All Rights Reserved.
  # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.admiror-design-studio.com/joomla-extensions
  # Technical Support:  Forum - http://www.vasiljevski.com/forum/index.php
  # Version: 3.0.0
  ------------------------------------------------------------------------- */

class afHelper
{

    var $params = array();
    var $staticParams = array();
    var $path = '';

    function __construct($globalParams, $path)
    {
        // Default parameters
        $this->staticParams['template'] = $globalParams->get('af_template', 'default');
        $this->staticParams['bgcolor'] = $globalParams->get('af_bgcolor', 'white');
        $this->staticParams['colorize'] = $globalParams->get('af_colorize', '');
        $this->staticParams['ratio'] = $globalParams->get('af_ratio', '100');
        $this->staticParams['width'] = $globalParams->get('af_width', '100%');
        $this->staticParams['height'] = $globalParams->get('af_height', '');
        $this->staticParams['margin'] = $globalParams->get('af_margin', '0');
        $this->staticParams['padding'] = $globalParams->get('af_padding', '0');
        $this->staticParams['horiAlign'] = $globalParams->get('af_horiAlign', 'left');
        $this->staticParams['vertAlign'] = $globalParams->get('af_vertAlign', 'top');
        $this->staticParams['float'] = $globalParams->get('af_float', 'none');
        $this->staticParams['showSignature'] = $globalParams->get('af_showSignature', '1');
        $this->staticParams['disableSignature'] = $globalParams->get('af_disable_signature', '0');
        $this->params['templates_BASE'] = JPATH_BASE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR .
            'content' . $path . 'templates' . DIRECTORY_SEPARATOR;
        $this->params['templates_ROOT'] = JURI::root() . 'plugins/content' . $path . 'templates/';
        $this->path = $path;
    }

private function afCreateImg($ID)
{
    $pluginUrl = "plugins/content{$this->path}scripts/afGdStream.php?src_file=%s&bgcolor=%s&colorize=%s&ratio=%s";
    $imgUrl = sprintf(
        $pluginUrl,
        urlencode($this->params['templates_BASE'] . $this->params['template'] . DIRECTORY_SEPARATOR . $ID . ".png"),
        urlencode($this->params['bgcolor']),
        urlencode($this->params['colorize']),
        urlencode($this->params['ratio'])
    );
    return (string) JURI::root() . $imgUrl;
}

    //Gets the attributes value by name, else returns false
    protected function afGetAttribute($attrib, $tag, $default = false)
    {
        static $re = '/%s=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is';
        $re = sprintf($re, preg_quote($attrib));

        // get attribute from html tag
        $tag = str_replace("}", "", $tag);
        if (preg_match($re, $tag, $match)) {
            return urldecode($match[2]);
        }
        return $default;
    }

    function agCreateFrame($source_html, $matchValue, $frameID)
    {

        // ---------------------------------------------------------- GET PARAMS
        $this->params['width'] = $this->afGetAttribute("width", $matchValue, $this->staticParams['width']);
        $this->params['bgcolor'] = $this->afGetAttribute("bgcolor", $matchValue, $this->staticParams['bgcolor']);
        $this->params['colorize'] = $this->afGetAttribute("colorize", $matchValue, $this->staticParams['colorize']);
        $this->params['height'] = $this->afGetAttribute("height", $matchValue, $this->staticParams['height']);
        $this->params['ratio'] = (int)$this->afGetAttribute("ratio", $matchValue, $this->staticParams['ratio']);
        $this->params['horiAlign'] = $this->afGetAttribute("horiAlign", $matchValue, $this->staticParams['horiAlign']);
        $this->params['vertAlign'] = $this->afGetAttribute("vertAlign", $matchValue, $this->staticParams['vertAlign']);
        $this->params['template'] = $this->afGetAttribute("template", $matchValue, $this->staticParams['template']);
        $this->params['float'] = $this->afGetAttribute("float", $matchValue, $this->staticParams['float']);
        $this->params['margin'] = (int)$this->afGetAttribute("margin", $matchValue, $this->staticParams['margin']);
        $this->params['padding'] = (int)$this->afGetAttribute("padding", $matchValue, $this->staticParams['padding']);

        $this->params['ratio'] = $this->params['ratio'] / 100;
        if (empty($this->params['colorize']))
            $this->params['colorize'] = "disable";
        $this->params['tableID'] = 'AF_' . $this->params['template'] . '_' . $frameID;

        // -------------------------------------------------------- CREATE TABLE
        $content = "<!-- ADMIROR FRAMES -->";
        $content .= '<table border="0" cellspacing="0" cellpadding="0" class="' . $this->params['tableID'] . '" >' . "\n";
        $content .= '<tbody>' . "\n";
        $content .= '<tr><td class="TL"></td><td class="T"></td><td class="TR"></td></tr>' . "\n";
        $content .= '<tr><td class="L"></td><td class="C">' . $source_html . '</td><td class="R"></td></tr>' . "\n";
        $content .= '<tr><td class="BL"></td><td class="B"></td><td class="BR"></td></tr></tbody></table>' . "\n";


        // ----------------------------------------------------------------- CSS
        list($TL_width, $TL_height, $TL_type, $TL_attr) = getimagesize($this->params['templates_BASE'] . $this->params['template'] . DIRECTORY_SEPARATOR . 'TL.png');
        list($BR_width, $BR_height, $BR_type, $BR_attr) = getimagesize($this->params['templates_BASE'] . $this->params['template'] . DIRECTORY_SEPARATOR . 'BR.png');
        $TL_width = round($TL_width * $this->params['ratio']);
        $TL_height = round($TL_height * $this->params['ratio']);
        if ($TL_width < 4)
            $TL_width = 4;
        if ($TL_height < 4)
            $dTL_height = 4;
        $BR_width = round($BR_width * $this->params['ratio']);
        $BR_height = round($BR_height * $this->params['ratio']);
        if ($BR_width < 4)
            $BR_width = 4;
        if ($BR_height < 4)
            $BR_height = 4;

        $content .= '
        <style type="text/css">
        ';
        $content .= '.' . $this->params['tableID'] . '{ direction :ltr;}';
        $content .= 'table.' . $this->params['tableID'] . '{
            empty-cells:show;
        ' . "\n";
        if (!empty($this->params['float']))
            $content .= 'float:' . $this->params['float'] . ';' . "\n";
        if (!empty($this->params['margin']))
            $content .= 'margin:' . $this->params['margin'] . ';' . "\n";
        if (!empty($this->params['width']))
            $content .= 'width:' . $this->params['width'] . ';' . "\n";
        if (!empty($this->params['height']))
            $content .= 'height:' . $this->params['height'] . ';' . "\n";
        $content .= '}' . "\n";

        $content .= ' 
        table.' . $this->params['tableID'] . ', table.' . $this->params['tableID'] . ' tr, table.' . $this->params['tableID'] . ' td{border:none;margin:0;padding:0;}
        table.' . $this->params['tableID'] . ' .TL{
            background-image:url(' . $this->afCreateImg("TL") . ');
            width:' . $TL_width . 'px;
            height:' . $TL_height . 'px;
            }
        table.' . $this->params['tableID'] . ' .T{
            background-image:url(' . $this->afCreateImg("T") . ');
            }
        table.' . $this->params['tableID'] . ' .TR{
            background-image:url(' . $this->afCreateImg("TR") . ');
            background-position:right top;
            }
        table.' . $this->params['tableID'] . ' .L{
            background-image:url(' . $this->afCreateImg("L") . ');
            }
        ';

        $content .= 'table.' . $this->params['tableID'] . ' .C{
            background-image:url(' . $this->afCreateImg("C") . ');
            ' . "\n";
        if (!empty($this->params['padding']))
            $content .= 'padding:' . $this->params['padding'] . ';' . "\n";
        if (!empty($this->params['horiAlign']))
            $content .= 'text-align:' . $this->params['horiAlign'] . ';' . "\n";
        if (!empty($this->params['vertAlign']))
            $content .= 'vertical-align:' . $this->params['vertAlign'] . ';' . "\n";
        $content .= '}' . "\n";

        $content .= '
        table.' . $this->params['tableID'] . ' .R{
            background-image:url(' . $this->afCreateImg("R") . ');
            background-position:right top;
            }
        table.' . $this->params['tableID'] . ' .BL{
            background-image:url(' . $this->afCreateImg("BL") . ');
            }
        table.' . $this->params['tableID'] . ' .B{
            background-image:url(' . $this->afCreateImg("B") . ');
            }
        table.' . $this->params['tableID'] . ' .BR{
            background-image:url(' . $this->afCreateImg("BR") . ');
            background-position:right top;
            width:' . $BR_width . 'px;
            height:' . $BR_height . 'px;
            }
        </style>
        ';

        return $content;
    }

}

