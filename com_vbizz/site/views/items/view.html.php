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

class VbizzViewItems extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.items.list.';
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$filter_type     = $mainframe->getUserStateFromRequest( $context.'filter_type', 'filter_type', '', 'int' );
		$pro     = $mainframe->getUserStateFromRequest( $context.'pro', 'pro', '', 'string' );
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		$selected_category = $mainframe->getUserStateFromRequest( $context.'category', 'category', '', 'int' );
		$layout = JRequest::getCmd('layout', '');
		
		
		// Get data from the model
		if($layout == 'edit')	{
			$this->item = $this->get('Item');
			$isNew		= ($this->item->id < 1);
			$this->type = $this->get('Types');
			$this->config = $this->get('Config');
			$this->category = $this->getCategory($this->item->category);
			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
			
		}
		else {
			
			// Get data from the model
			$items		=  $this->get( 'Items');
			$pagination = $this->get('Pagination');
			$this->config = $this->get('Config');
			//$this->category = $this->getCategory($selected_category);
			//Filter Types
			$this->category = '<select name="category" class="inputbox" size="1" onchange="submitform( );" id="category"><option value="">'.JText::_("SELECT_CATEGORY").'</option>'.$this->getCategory($selected_category).'</select>';
			$types[] = JHTML::_('select.option',  '', sprintf ( JText::_( 'SELTERMTXT' ), $this->config->type_view_single) );
			$typ = $this->get('Types');
			for($i=0;$i<count($typ);$i++)
			$types[] = JHTML::_('select.option',  $typ[$i]->id, $typ[$i]->treename );
			$this->lists['ttypes'] = JHTML::_('select.genericlist', $types, 'filter_type', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_type );
	
			$this->assignRef('items',		$items);
			$this->assignRef('pagination', $pagination);
			
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
			// Search filter
			$this->lists['search']= $search;
			$this->lists['pro']= $pro;
		}
		parent::display($tpl);
	}
	function getCategory($selected_category='')
	{
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$ownerId = VaccountHelper::getOwnerId();
		
		$query = 'SELECT * from #__vbizz_items_category where ownerid='.$db->quote($ownerId);
		$db->setQuery($query);
		$category = $db->loadAssocList(); 
		$category =  $this->getBuildTree($category);
		
		return $this->getPrintTree($category,0,null,$selected_category);
	}
	
	function getBuildTree(Array $data, $parent = 0) {
    $tree = array();
    foreach ($data as $d) {
        if ($d['parent'] == $parent) {
            $children = $this->getBuildTree($data, $d['id']);
            // set a trivial key
            if (!empty($children)) {
                $d['_children'] = $children;
            }
            $tree[] = $d;
        }
    }
    return $tree;
    }
	function getPrintTree($tree, $r = 0, $p = null, $selected='') {
		static $html = '';
		foreach ($tree as $i => $t) {
			$dash = ($t['parent'] == 0) ? '' : str_repeat('-', $r) .' ';
			$html .= "<option value='".$t['id']."'".($t['id']==$selected?' selected="selected"':'').">".$dash.$t['title']."</option>";
			if ($t['parent'] == $p) {
				// reset $r
				$r = 0;
			}
			if (isset($t['_children'])) {
				$this->getPrintTree($t['_children'], ++$r, $t['parent'], $selected);
			}
		}
	return $html;	
	}
}