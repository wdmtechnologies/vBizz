<?php
/*------------------------------------------------------------------------
# com_vbizz - vReview
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.wdmtech.com
# Technical Support: Forum - https://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class VbizzViewUsers extends JViewLegacy
{
	
	public function display($tpl = null)
    {
		
		$mainframe = JFactory::getApplication();
		$context			= 'com_vbizz.users.list.';
		$layout = JRequest::getCmd('layout', '');
		
		//get filter valur from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
        
		//check if vbizz search module is enabled or not
		
		if($layout=='users'){
		$this->items = $this->get('Items');	
		$pagination = $this->get('Pagination');
			
		$this->assignRef('pagination', $pagination);
		$this->lists['order_Dir'] = $filter_order_Dir;
		$this->lists['order']     = $filter_order;

		// search filter
		$this->lists['search']= $search;
		}else{
		$this->item = $this->get('Item');
		$this->countries = $this->get('Countries');
		$this->states = $this->get('StateVal');	
		}		
		
		$this->config = $this->get('Config');
		
		parent::display($tpl);
        
    }
  
  
}
