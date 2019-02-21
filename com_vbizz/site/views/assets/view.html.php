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
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );

class VbizzViewAssets extends JViewLegacy
{
	function display($tpl = null)
	{
		//require_once JPATH_BASE . '/administrator/components/com_vbizz/helpers/vbizz.php';
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.assets.list.';
		
		//get filter variable from session
		
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$filter_type     = $mainframe->getUserStateFromRequest( $context.'filter_type', 'filter_type', '', 'int' );
		$filter_mode     = $mainframe->getUserStateFromRequest( $context.'filter_mode', 'filter_mode', '', 'int' );
		$filter_begin    = $mainframe->getUserStateFromRequest( $context.'filter_begin', 'filter_begin', '', 'string' );
		$filter_end      = $mainframe->getUserStateFromRequest( $context.'filter_end', 'filter_end', '', 'string' );
		$amount_status   = $mainframe->getUserStateFromRequest( $context.'amount_status', 'amount_status', 'status', 'cmd' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$layout = JRequest::getCmd('layout', '');
		
		
		if($layout == 'edit')	{
			// Get data from the model
			$this->item = $this->get('Item');
			$isNew		= ($this->item->id < 1);
			$this->config = $this->get('Config');
			$this->types = $this->get('Types');
			$this->mode = $this->get('Modes');
			$this->vendor = $this->get('Vendor');
			$this->account = $this->get('Accounts');
			$this->discount = $this->get('Discount');
			$this->tax = $this->get('Tax');
			
			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
			
		}
		else {
			
			// Get data from the model
			$items		=  $this->get( 'Items');
			$pagination = $this->get('Pagination');
			$this->config = $this->get('Config');
			$this->final_expense = $this->get( 'FinalAmount');
			
			//Filter Types
			$ttypes[] = JHTML::_('select.option',  '', sprintf ( JText::_( 'SELTERMTXT' ), $this->config->type_view_single) );
			$typ = $this->get('Types');
			for($i=0;$i<count($typ);$i++)
			$ttypes[] = JHTML::_('select.option',  $typ[$i]->id, $typ[$i]->treename );
			$this->lists['ttypes'] = JHTML::_('select.genericlist', $ttypes, 'filter_type', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_type );
			
			//Filter Mode
			$modes[] = JHTML::_('select.option',  '', JText::_( 'SELECT_MODE' ));
			$mde = $this->get('Modes');
			for($i=0;$i<count($mde);$i++)
			$modes[] = JHTML::_('select.option',$mde[$i]->id, $mde[$i]->title );
			$this->lists['modes'] = JHTML::_('select.genericlist', $modes, 'filter_mode', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_mode );
			
			//Amount Status
			$status[] = JHTML::_('select.option',  '',JText::_( 'AMOUNT_STATUS' ));
			$stat = array('Paid','Unpaid');
			//$pub = array('1','0');
			for($i=0;$i<count($stat);$i++)
			$status[] = JHTML::_('select.option',$stat[$i], $stat[$i] );
			$this->lists['status'] = JHTML::_('select.genericlist', $status, 'amount_status', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $amount_status );
			
			$this->assignRef('items', $items);
			$this->assignRef('pagination', $pagination);
			
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
			// Search filter
			$this->lists['search']= $search;
			
			$this->state->get['filter_begin']= $filter_begin;
			$this->state->get['filter_end']= $filter_end;
			
		}
		parent::display($tpl);
	}
}