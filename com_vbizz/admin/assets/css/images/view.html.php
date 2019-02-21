<?php
/*------------------------------------------------------------------------
# com_hexdata - HexData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

class HexdataViewWidget extends JViewLegacy
{    
    function display($tpl = null)
    { 
		
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$context			= 'com_hexdata.widget.list.';
		$layout = JRequest::getCmd('layout', '');
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', '', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
		$sql_query = $mainframe->getUserStateFromRequest($context.'sql_query', 'sql_query', '', 'string');
		$table_name = $mainframe->getUserStateFromRequest($context.'table_name_old', 'table_name', '', 'string');
		   $document->addScript(JURI::root().'media/com_hexdata/js/query-builder.js');
			$document->addScript(JURI::root().'media/com_hexdata/js/moment.js');
			$document->addScript(JURI::root().'media/com_hexdata/js/jquery-ui-timepicker-addon.js');
			$document->addScript(JURI::root().'media/com_hexdata/js/query-builder-sql-support.js');
			$document->addStyleSheet(JURI::root().'media/com_hexdata/css/query-builder.css');
            $document->addStyleSheet(JURI::root().'media/com_hexdata/css/jquery-ui-timepicker-addon.min.css'); 
		if($layout=="form")	{
			
			$document->addScript(JURI::root().'media/com_hexdata/js/jquery.ui.datepicker.js');
			//$document->addScript(JURI::root().'media/com_hexdata/js/jquery.datetimepicker.js');
			$document->addStyleSheet(JURI::root().'media/com_hexdata/css/jquery.ui.theme.css');
			$document->addStyleSheet(JURI::root().'media/com_hexdata/css/jquery.ui.datepicker.css');
			$document->addStyleSheet(JURI::root().'media/com_hexdata/css/jquery.ui.datepicker.css');
			
			
			
			
            $data = $this->get('Columninfo');	
            $this->rowinfo = $data[0];	
           $this->rowvalue = $data[1];		   
			
			JToolBarHelper::title(JText::_( 'HEXDATA_ADD_NEW_WIDGET' ), 'quickview' );
			
			
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel('close');
		
		}
		elseif($layout=="export"){
			
			JToolBarHelper::title(JText::_( 'HEXDATA_PHP_MY_ADMIN_EXPORT' ), 'quickexport' );
			
		}
		else	{
			
			JToolBarHelper::title( JText::_( 'HEXDATA_WIDGET' ), 'quickview' );
			$this->widget	= $this->get('Item');
			//$this->chart_type = $this->chart_type();
			
		}
							
		parent::display($tpl);
       
    }
	

  
  
}
