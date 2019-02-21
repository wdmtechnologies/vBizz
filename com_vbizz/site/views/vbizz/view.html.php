<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.wdmtech.com
# Technical Support: Forum - https://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access'); 

class VbizzViewVbizz extends JViewLegacy
{
    function display($tpl = null)
    { 
		$document =  JFactory::getDocument();
		
		$layout = JRequest::getCmd('layout', '');
		
		$this->config = $this->get('Config'); 
		if($layout =='widgetlisting')
		{
			$this->widgetlisting = $this->get('WidgetListing');
			$this->pagination = $this->get('Pagination');
			
		}
		if (version_compare ( JVERSION, '3.0', 'ge' ))
			$document->addScript(JUri::root(true).'/media/jui/js/jquery.min.js');
		if (version_compare ( JVERSION, '3.0', 'lt' )) 
			 $document->addScript(JUri::root(true).'/components/com_vbizz/assets/js/jquery.1.10.js');
		
		
		$this->profiles = $this->get('Profiles');
		$typ = $this->get('Types');
		$ttypes =array();
		$ttypes[] = JHTML::_( 'select.option',  '', sprintf ( JText::_( 'SELTERMTXT' ), $this->config->type_view_single) );
			
			for($i=0;$i<count($typ);$i++)
			$ttypes[] = JHTML::_('select.option',  $typ[$i]->id, $typ[$i]->treename );
			$this->transection = JHTML::_('select.genericlist', $ttypes, 'transection_type', 'class="inputbox" size="1" style="width:40%;"', 'value', 'text', '');
			
		
		parent::display($tpl);
    }
}
