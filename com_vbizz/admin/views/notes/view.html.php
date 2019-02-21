<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );

class VbizzViewNotes extends JViewLegacy
{
	function display($tpl = null)
	{
		
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.notes.list.';
		
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		$filter_type     = $mainframe->getUserStateFromRequest( $context.'filter_type', 'filter_type', '', 'string' );
		$filter_view     = $mainframe->getUserStateFromRequest( $context.'filter_view', 'filter_view', '', 'string' );
		
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$layout = JRequest::getCmd('layout', '');
		
		$canDo = VaccountHelper::getActions();
		
		$user = JFactory::getUser();
		$version = new JVersion;
		$joomla = $version->getShortVersion();
		$jversion = substr($joomla,0,3); 
		$this->sidebar ='';
		if($jversion>=3.0) 
		{
		$this->sidebar = JHtmlSidebar::render();
		}
		
		if($layout == 'edit')	{
			$this->notes = $this->get('Item');
			$isNew		= ($this->notes->id < 1);		

			$text = $isNew ? JText::_( 'NEW' ) : JText::_( 'EDIT' );
			JToolBarHelper::title(   JText::_( 'NOTE' ).': <small><small>[ ' . $text.' ]</small></small>', 'file-2.png' );
			
			if (($canDo->get('core.edit'))) {
				JToolBarHelper::apply();
				JToolBarHelper::save();
			}
			if ($isNew)  {
				JToolBarHelper::cancel();
			} else {
				// for existing items the button is renamed `close`
				JToolBarHelper::cancel( 'cancel', 'Close' );
			}
		}
		else {
			JToolBarHelper::title(   JText::_( 'NOTES' ), 'file-2.png' );
			if (($canDo->get('core.create'))) {
				JToolBarHelper::addNew();
			}
			if (($canDo->get('core.edit'))) {
				JToolBarHelper::editList();
			}
			
			if (($canDo->get('core.delete'))) {
				JToolBarHelper::deleteList(JText::_(''), 'remove');
			}
			
			if ($user->authorise('core.admin', 'com_vbizz'))
			{
				//JToolbarHelper::preferences('com_vbizz');
			}
			
			$types[] = JHTML::_('select.option',  '',JText::_( 'SELECT_NOTE_TYPE' ));
			$type =array(
				JText::_('NOTIFICATION'), 
				JText::_('DATA_MANIPULATION'), 
				JText::_('CONFIGURATION'), 
				JText::_('IMPORT_EXPORT'), 
				JText::_('RECURRING')
			);
			
			$typ = array(
				'notification',
				'data_manipulation',
				'configuration',
				'import_export',
				'recurring'
			);
			
			for($i=1;$i<=count($typ);$i++)
			$types[] = JHTML::_('select.option',  $typ[$i-1], $type[$i-1]);
			$this->lists['types'] = JHTML::_('select.genericlist', $types, 'filter_type', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_type );
			
			
			
			$viewlt[] = JHTML::_('select.option',  '',JText::_( 'SELECT_VIEW' ));
			
			$vw = array(
				JText::_('ACTIVITY'),
				JText::_('CONFIGURATION'),
				JText::_('INCOME'),
				JText::_('EXPENSE'),
				JText::_('ITEMS'),
				JText::_('TRANSACTION_TYPE'),
				JText::_('TRANSACTION_MODE'),
				JText::_('ACCOUNTS'),
				JText::_('TAX'),
				JText::_('DISCOUNT'),
				JText::_('RECURRING_TRANSACTION'),
				JText::_('IMPORT_EXPORT'),
				JText::_('CUSTOMER'),
				JText::_('VENDOR'),
				JText::_('COUNTRIES'),
				JText::_('STATES'),
				JText::_('INVOICE'),
				JText::_('IMPORT_TASK'),
				JText::_('EXPORT_TASK'),
				JText::_('EMPLOYEE'),
				JText::_('TEMPLATES')
			);
			
			$vwup = array(
				'notes',
				'configuration',
				'income',
				'expense',
				'items',
				'tran',
				'mode',
				'accounts',
				'tax',
				'discount',
				'recurr',
				'import',
				'customer',
				'vendor',
				'country',
				'state',
				'invoice',
				'imtask',
				'exptask',
				'employee',
				'templates'
			);
			
			for($i=1;$i<=count($vw);$i++)
			$viewlt[] = JHTML::_('select.option',  $vwup[$i-1], $vw[$i-1]);
			$this->lists['viewlt'] = JHTML::_('select.genericlist', $viewlt, 'filter_view', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_view );
	
			// Get data from the model
			$items		=  $this->get( 'Items');
			$pagination = $this->get('Pagination');

			
	
			$this->assignRef('items',		$items);
			$this->assignRef('pagination', $pagination);
			
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
			// Search filter
			$this->lists['search']= $search;
		}
		parent::display($tpl);
	}
}