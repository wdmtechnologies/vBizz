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

class VbizzViewCountry extends JViewLegacy
{
	function display($tpl = null)
	{
		
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.country.list.';
		
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$filter_status     = $mainframe->getUserStateFromRequest( $context.'filter_status', 'filter_status', 'published', 'cmd' );
		
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
			$this->country = $this->get('Item');
			$isNew		= ($this->country->id < 1);		

			$text = $isNew ? JText::_( 'NEW' ) : JText::_( 'EDIT' );
			JToolBarHelper::title(   JText::_( 'COUNTRY' ).': <small><small>[ ' . $text.' ]</small></small>', 'flag.png' );
			
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
			JToolBarHelper::title(   JText::_( 'COUNTRIES' ), 'flag.png' );
			if (($canDo->get('core.create'))) {
				JToolBarHelper::addNew();
			}
			if (($canDo->get('core.edit'))) {
				JToolBarHelper::editList();
			}
			if ($canDo->get('core.edit.state')) {
				JToolBarHelper::publishList();
				JToolBarHelper::unpublishList();
			}
			if (($canDo->get('core.delete'))) {
				JToolBarHelper::deleteList(JText::_(''), 'remove');
			}
			
			if ($user->authorise('core.admin', 'com_vbizz'))
			{
				//JToolbarHelper::preferences('com_vbizz');
			}
	
			// Get data from the model
			$items		=  $this->get( 'Items');
			$pagination = $this->get('Pagination');
			
			//Filter Published
			$published[] = JHTML::_('select.option',  '',JText::_( 'STATUS' ));
			$pubs = array('Published','Unpublished');
			//$pub = array('1','0');
			for($i=0;$i<count($pubs);$i++)
			$published[] = JHTML::_('select.option',$pubs[$i], $pubs[$i] );
			$this->lists['published'] = JHTML::_('select.genericlist', $published, 'filter_status', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_status );
			
	
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