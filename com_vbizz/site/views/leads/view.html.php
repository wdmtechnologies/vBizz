<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );

class VbizzViewLeads extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.leads.list.';
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$lead_industry_selected     = $mainframe->getUserStateFromRequest( $context.'lead_industry', 'lead_industry', '', 'string' );
		$lead_status_selected     = $mainframe->getUserStateFromRequest( $context.'lead_status', 'lead_status', '', 'string' );
		$lead_source_selected     = $mainframe->getUserStateFromRequest( $context.'lead_source', 'lead_source', '', 'string' );
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$layout = JRequest::getCmd('layout', '');
		
		// Get data from the model		
		$this->config = $this->get('Config');
		
		
		$lead_status = $this->get('LeadStatus');
			$lead_industry = $this->get('LeadIndustry'); 
			$this->lead_sources = $this->get('LeadSources');
			
			$this->lead_source_html = array();  
			$this->lead_source_html[] = JHTML::_('select.option','', JText::_( 'COM_VBIZZ_NONE') );
			for($i=0;$i<count($this->lead_sources);$i++)
			$this->lead_source_html[] = JHTML::_('select.option',$this->lead_sources[$i]->source_value, JText::_($this->lead_sources[$i]->source_name) );
			
			$this->lists['lead_source'] = JHTML::_('select.genericlist', $this->lead_source_html, 'lead_source', 'class="inputbox" size="1" onchange="submitform();" style="width:150px;"', 'value', 'text', $lead_source_selected );
			
			$this->lead_industry_html = array();
			$this->lead_industry_html[] = JHTML::_('select.option','', JText::_( 'COM_VBIZZ_NONE') );
			for($i=0;$i<count($lead_industry);$i++)
			$this->lead_industry_html[] = JHTML::_('select.option',$lead_industry[$i]->industry_value, JText::_($lead_industry[$i]->industry_name) );
			
			$this->lists['lead_industry'] = JHTML::_('select.genericlist', $this->lead_industry_html, 'lead_industry', 'class="inputbox" size="1" onchange="submitform();" style="width:150px;"', 'value', 'text', $lead_industry_selected );
			
			$this->lead_status_html = array();
			$this->lead_status_html[] = JHTML::_('select.option','', JText::_( 'COM_VBIZZ_NONE') );
			for($i=0;$i<count($lead_status);$i++)
			$this->lead_status_html[] = JHTML::_('select.option',$lead_status[$i]->source_value, JText::_($lead_status[$i]->source_name) );
			
			$this->lists['lead_status'] = JHTML::_('select.genericlist', $this->lead_status_html, 'lead_status', 'class="inputbox" size="1" onchange="submitform();"style="width:150px;"', 'value', 'text', $lead_status_selected );
		if($layout == 'edit')
		{
			
			$this->leads = $this->get('Item');
			$this->type = $this->get('Types');
			$this->discount = $this->get('Discount');
			$this->comments = $this->get('Comments');
			$this->tax = $this->get('Tax');
			$this->multi_item = $this->get('MultiItem');
			$this->custom_item = $this->get('CustomItem');
			$this->all_multi_item = $this->get('AllMultiItems');
			
			$isNew		= ($this->leads->id < 1);		

			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
			
		}
		elseif($layout == 'detail')
		{
			$this->lead_status = $this->get('LeadStatus');
			$this->lead_industry = $this->get('LeadIndustry');
			$this->lead_sources = $this->get('LeadSources');
		}
		else {
	
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