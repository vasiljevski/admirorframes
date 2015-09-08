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
defined('_JEXEC') or die('Restricted access');
//Import library dependencies
jimport('joomla.event.plugin');
jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.folder');

class plgContentAdmirorframes extends JPlugin {

    //Constructor
    function plgContentAdmirorframes(&$subject, $params) {
        parent::__construct($subject, $params);
    }

    //Joomla 1.5
    public function onPrepareContent(&$row, &$params, $limitstart = 0) {
        if (preg_match("#{AF[^}]*}(.*?){/AF}#s", strtoupper($row->text))) {
            $row->text = $this->textToFrame($row->text);
        }
    }

    public function onContentPrepare($context, &$row, &$params, $page = 0) {
        if (is_object($row)) {
            return $this->onPrepareContent($row, $params, $page);
        } else {
            if (preg_match("#{AF[^}]*}(.*?){/AF}#s", strtoupper($row))) {
                $row = $this->textToFrame($row);
            }
        }
        return true;
    }

    function textToFrame($row) {
        if (!function_exists('gd_info')) {
            // ERROR - Invalid image
            return JFactory::getApplication()->enqueueMessage(JText::_('GD support is not enabled'), 'error');
        }
        require_once (dirname(__FILE__) . '/admirorframes/scripts/AF_helper.php');

        $version = new JVersion();
        if ($version->RELEASE == "1.5") {
            $AF = new AF_helper($this->params, JPATH_BASE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR . 'admirorframes' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR, JURI::root() . 'plugins/content/admirorframes/templates/', $version->RELEASE);
        } else {
            $AF = new AF_helper($this->params, JPATH_BASE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR . 'admirorframes' . DIRECTORY_SEPARATOR . 'admirorframes' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR, JURI::root() . 'plugins/content/admirorframes/admirorframes/templates/', $version->RELEASE);
        }

        if (preg_match_all("#{AF[^}]*}(.*?){/AF}|{af[^}]*}(.*?){/af}#s", $row, $matches, PREG_PATTERN_ORDER) > 0) {
            foreach ($matches[0] as $matchKey => $matchValue) {
                //print_r($matchValue);
                $patterns = array();
                $patterns[0] = '|{AF[^}]*}|i';
                $patterns[1] = '|{/AF}|i';
                $replacements = array();
                $replacements[0] = '';
                $replacements[1] = '';
                $html = $AF->AF_createFrame(preg_replace($patterns, $replacements, $matchValue), $matchValue, $matchKey . "_" . rand(0, 1000000));
                $row = str_replace($matchValue, $html, $row);
            }
            $row .='<div style="clear:both"></div>';
            /* ========================= SIGNATURE ====================== */
            if (!$AF->staticParams['disableSignature']) {
                if ($AF->staticParams['showSignature'] == 1) {
                    $row .= '<div style="display:block; font-size:10px;">';
                } else {
                    $row .= '<div style="display:block; font-size:10px; overflow:hidden; height:1px; padding-top:1px;">';
                }
                $row .= '<br /><a href="http://www.admiror-design-studio.com/en/joomla-extensions" target="_blank">AdmirorFrames 2.0</a>, ' . JText::_("author/s") . ' <a href="http://www.vasiljevski.com/" target="_blank">Vasiljevski</a> & <a href="http://www.admiror-design-studio.com" target="_blank">Kekeljevic</a>.<br /></div>';
            }
        }//if (preg_match_all("#{AdmirorFrames[^}]*}(.*?){/AdmirorFrames}#s", $row, $matches, PREG_PATTERN_ORDER)>0)
        return $row;
    }

//function textToFrame($row)
}

//class plgContentAdmirorFrames extends JPlugin
?>
