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
defined('_JEXEC') or die('Restricted access');
//Import library dependencies
jimport('joomla.event.plugin');
jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.folder');

class plgContentAdmirorframes extends JPlugin
{

    //Constructor
    function __construct(&$subject, $params)
    {
        parent::__construct($subject, $params);
    }

    //Joomla 1.5
    public function onPrepareContent($row, &$params, $limitstart = 0)
    {
        if (preg_match("#{AF[^}]*}(.*?){/AF}#s", strtoupper($row->text))) {
            $row->text = $this->textToFrame($row->text);
        }
    }

    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        if (is_object($row)) {
            $this->onPrepareContent($row, $params, $page);
        } else {
            if (preg_match("#{AF[^}]*}(.*?){/AF}#s", strtoupper($row))) {
                $row = $this->textToFrame($row);
            }
        }
        return true;
    }

    function textToFrame($row)
    {
        if (!function_exists('gd_info')) {
            try {
                JFactory::getApplication()->enqueueMessage(JText::_('GD support is not enabled'), 'error');
            } catch (Exception $e) {
                var_dump($e->getMessage());
            }
            return -1;
        }
        require_once(dirname(__FILE__) . '/admirorframes/scripts/afHelper.php');

        $path = '/admirorframes/admirorframes/';
        if (Joomla\CMS\Version::MAJOR_VERSION != 4) {
            $path = '/admirorframes/';
        }
        $AF = new afHelper($this->params, $path);

        if (preg_match_all("#{AF[^}]*}(.*?){/AF}|{af[^}]*}(.*?){/af}#s", $row, $matches) > 0) {
            foreach ($matches[0] as $matchKey => $matchValue) {
                $patterns = array();
                $patterns[0] = '|{AF[^}]*}|i';
                $patterns[1] = '|{/AF}|i';
                $replacements = array();
                $replacements[0] = '';
                $replacements[1] = '';
                $html = $AF->agCreateFrame(preg_replace($patterns, $replacements, $matchValue), $matchValue, $matchKey . "_" . rand(0, 1000000));
                $row = str_replace($matchValue, $html, $row);
            }
            $row .= '<div style="clear:both"></div>';
            /* ========================= SIGNATURE ====================== */
            if (!$AF->staticParams['disableSignature']) {
                if ($AF->staticParams['showSignature'] == 1) {
                    $row .= '<div style="display:block; font-size:10px;">';
                } else {
                    $row .= '<div style="display:block; font-size:10px; overflow:hidden; height:1px; padding-top:1px;">';
                }
                $row .= '<br /><a href="http://www.admiror-design-studio.com/en/joomla-extensions" target="_blank">AdmirorFrames 3.0</a>, ' . JText::_("author/s") . ' <a href="http://www.vasiljevski.com/" target="_blank">Vasiljevski</a> & <a href="http://www.admiror-design-studio.com" target="_blank">Kekeljevic</a>.<br /></div>';
            }
        }//if (preg_match_all("#{AdmirorFrames[^}]*}(.*?){/AdmirorFrames}#s", $row, $matches, PREG_PATTERN_ORDER)>0)
        return $row;
    }

//function textToFrame($row)
}

