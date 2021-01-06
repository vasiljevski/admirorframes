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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of Admiror Gallery component
 */
class plgcontentadmirorframesInstallerScript {

    /**
     * Install the component
     *
     * @param $parent
     *
     * @return void
     */
    function install($parent) {
        
    }

    /**
     * Uninstall the component
     *
     * @param $parent
     *
     * @return void
     */
    function uninstall($parent) {
        
    }

    /**
     * Update the component
     *
     * @param $parent
     *
     * @return void
     */
    function update($parent) {
        //On update we just call install, no special case for updating.
        $this->install($parent);
    }

    /**
     * Run before an install/update/uninstall method
     *
     * @param $type
     * @param $parent
     *
     * @return void
     */
    function preflight($type, $parent) {
        
    }

    /**
     * Run after an install/update/uninstall method
     *
     * @param $type
     * @param $parent
     *
     * @return void
     *
     * @throws Exception
     */
    function postflight($type, $parent) {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        if (!JFile::Move($parent->getParent()->getPath('extension_root') . DIRECTORY_SEPARATOR . "_admirorframes.xml", $parent->getParent()->getPath('extension_root') . DIRECTORY_SEPARATOR . "admirorframes.xml")) {
            JFactory::getApplication()->enqueueMessage('Manifest file could not be renamed. Please go to plugins/content/admirorframes and rename _admirorframes.xml to admirorframes.xml', 'error');
        }
    }

}