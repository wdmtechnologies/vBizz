<?php
/**
 * @package 	mod_vbizz_login - vBizz Login Module
 * @version		1.0.0
 * @created		April 2017
 * @author		WDMtech
 * @email		info@wdmtech.com
 * @website		https://www.wdmtech.com
 * @support		Forum - https://www.wdmtech.com/support-forum
 * @copyright	Copyright (C) 2017 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLs
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldAsset extends JFormField {

    protected $type = 'Asset';

    protected function getInput() {		
		JHTML::_('behavior.framework');		
		$document	= JFactory::getDocument();
		if (!version_compare(JVERSION, '3.0', 'ge')) {
			$checkJqueryLoaded = false;
			$header = $document->getHeadData();
			foreach($header['scripts'] as $scriptName => $scriptData)
			{
				if(substr_count($scriptName,'/jquery')){
					$checkJqueryLoaded = true;
				}
			}	
			//Add js
			if(!$checkJqueryLoaded) 
			$document->addScript(JURI::root().$this->element['path'].'js/jquery.min.js');
			$document->addScript(JURI::root().$this->element['path'].'js/chosen.jquery.min.js');		
			//Add css         
			$document->addStyleSheet(JURI::root().$this->element['path'].'css/chosen.css');        			
		}		
		$document->addStyleSheet(JURI::root().$this->element['path'].'js/colorpicker/colorpicker.css');   
		$document->addStyleSheet(JURI::root().$this->element['path'].'css/bt.css');	
		$document->addScript(JURI::root().$this->element['path'].'js/colorpicker/colorpicker.js');
		$document->addScript(JURI::root().$this->element['path'].'js/bt.js');	  
                
        return null;
    }
}
?>