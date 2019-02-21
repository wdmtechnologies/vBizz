<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

class VbizzViewImtask extends JViewLegacy
{    
    function display($tpl = null)
    {
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.imtask.list.';
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		$document = JFactory::getDocument();
		
		$layout = JRequest::getCmd('layout', '');
		
		// Get data from the model
		if($layout=="import")	{
			$this->fields = $this->get('FileFields');
			$this->config = $this->get('Config');
			$this->item = $this->get('Item');
		} 
		else if($layout=="edit")	{
			$this->item = $this->get('Item');
			$this->config = $this->get('Config');
		} else {
			
			$items		=  $this->get( 'Items');
			$pagination = $this->get('Pagination');
			$this->config = $this->get('Config');
			
			$this->assignRef('items',		$items);
			$this->assignRef('pagination', $pagination);
			
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
		}
		parent::display($tpl);
    }
}
