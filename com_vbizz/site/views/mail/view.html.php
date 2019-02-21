<?php
/*------------------------------------------------------------------------
# com_vbizz - vBizz
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.wdmtech.com
# Technical Support: Forum - https://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );

class VbizzViewMail extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.mail.list.';
		$layout = JRequest::getCmd('layout', '');
		$actionvalue	= JRequest::getInt('action_value',0);
		
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		$action_value = $mainframe->getUserStateFromRequest($context.'action_value', 'action_value', $actionvalue, 'int');
			
		if(count($errors=$this->get('Errors')))
		{
			JError::raiseError(500,implode('<br />',$errors));
			return false;
		}
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		$layout = JRequest::getCmd('layout', '');
		$document =  JFactory::getDocument();
		$document->addStyleSheet('components/com_vbizz/assets/css/jquery-ui.css');
		$document->addScript('components/com_vbizz/assets/js/jquery.1.10.js');
		$document->addScript('components/com_vbizz/assets/js/jquery-ui.js');
		
		
		$this->config = $this->get('Config');
	    $this->maillist = $this->get('Maillist');
	
		if($layout == 'edit')	{
		
			$this->item = $this->get('Item');
			$isNew		= ($this->item->id < 1);
			
		}elseif($layout == 'configuration')	{
		
			$this->mailsetting = $this->get('Mailsetting');	
			
		}elseif($layout == 'modal')	{
		
			$this->item = $this->get('Item');	
			$this->allmailid = $this->get('Allmailid');	
			
		}else {
			
			$items	=  $this->get( 'Items');
			//print_r($items);
			$total =  $this->get( 'Total');
			$pagination = $this->get('Pagination');
			$this->config = $this->get('Config');
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			$this->lists['search']= $search;
			$this->lists['action_value']= $action_value;
			$this->assignRef('items',		$items);
			$this->assignRef('pagination', $pagination);

		}
		parent::display($tpl);
	}
}