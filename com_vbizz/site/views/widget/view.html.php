<?php
/*------------------------------------------------------------------------
# com_vbizz - vBizz
# ------------------------------------------------------------------------
# author    Mohd Waseem Khan
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.wdmtech.com
# Technical Support:  Forum - https://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

class VbizzViewWidget extends JViewLegacy
{    
    function display($tpl = null)
    { 
		
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$context			= 'com_vbizz.widget.list.';
		$layout = JFactory::getApplication()->input->getCmd('layout', '');
		
		$user  = JFactory::getUser();
		if($user->id==0){
			$msg = JText::_('VDATA_LOGIN_ALERT');
			$mainframe->redirect(JRoute::_('index.php?option=com_users&view=login'), $msg);
		}
		
		//get filter valur from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', '', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
		$sql_query = $mainframe->getUserStateFromRequest($context.'sql_query', 'sql_query', '', 'string');
		$table_name = $mainframe->getUserStateFromRequest($context.'table_name_old', 'table_name', '', 'string');
		
		
		
		//$document->addStyleSheet(JURI::root().'components/com_vbizz/assets/css/vbizz.css');
		$document->addStyleSheet(JUri::root().'components/com_vbizz/assets/css/jquery.ui.theme.css');
		$document->addStyleSheet(JUri::root().'components/com_vbizz/assets/css/jquery.ui.datepicker.css');
		$document->addStyleSheet('modules/mod_vbizz_search/assets/css/jquery-ui.css');
		if (version_compare ( JVERSION, '3.0', 'ge' ))
			$document->addScript(JUri::root(true).'/media/jui/js/jquery.min.js');
		if (version_compare ( JVERSION, '3.0', 'lt' )) 
			 $document->addScript(JUri::root(true).'/components/com_vbizz/assets/js/jquery.1.10.js');
		 
		$document->addScript(JUri::root(true).'/components/com_vbizz/assets/js/noconflict.js');

		$document->addScript('components/com_vbizz/assets/js/jquery-ui.js');
		$document->addStyleSheet(JUri::root().'components/com_vbizz/assets/css/chosen.css');
		$document->addScript(JUri::root().'components/com_vbizz/assets/js/chosen.jquery.min.js'); 
		
		$document->addStyleSheet(JURI::root().'templates/vacount/css/style.css');
		$document->addStyleSheet(JURI::root().'templates/vacount/css/icomoon.css');

		$document->addScript(JURI::root().'components/com_vbizz/assets/js/query-builder.js');
		$document->addScript(JURI::root().'components/com_vbizz/assets/js/moment.js');
		$document->addScript(JURI::root().'components/com_vbizz/assets/js/jquery-ui-timepicker-addon.js');
		$document->addScript(JURI::root().'components/com_vbizz/assets/js/query-builder-sql-support.js');
		$document->addStyleSheet(JURI::root().'components/com_vbizz/assets/css/query-builder.css');
		$document->addStyleSheet(JURI::root().'components/com_vbizz/assets/css/jquery-ui-timepicker-addon.min.css'); 
		$document->addScript(JURI::root().'components/com_vbizz/assets/js/mini_color.js');
		$document->addStyleSheet(JURI::root().'components/com_vbizz/assets/css/mini_color.css');
		
		
		$js = 'jQuery(function() {jQuery("#vbizzpanel").prepend("<div class=\"loading\"><div class=\"loading-icon\"><div></div></div></div>"); });';
		$document->addScriptDeclaration($js);
		
		$this->widget	= $this->get('Item');
			
							
		parent::display($tpl);
       
    }
	

  
  
}
