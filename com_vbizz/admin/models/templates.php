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
jimport('joomla.application.component.model');

class VbizzModelTemplates extends JModelLegacy
{
	var $_list;
    var $_data = null;
	var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.templates.list.';
		  
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		  
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}
	
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	
	function _buildQuery()
	{
		$query = 'SELECT * FROM #__vbizz_templates';
		return $query;
	}
	
	function getItems()
	{
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data ))
		{
			$query = $this->_buildQuery();
			$filter = $this->_buildItemFilter();
			$query .= $filter;
			
			$orderby = $this->_buildItemOrderBy();
			$query .= $orderby;

			$this->_data = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit'));
			echo $this->_db->getErrorMsg();
		}
		return $this->_data;
	}
	function getTotal()
	{
		// Load the content if it doesn't already exist
        if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$filter = $this->_buildItemFilter();
			$query .= $filter;
            $this->_total = $this->_getListCount($query);     
        }
        return $this->_total;
	}
	
	function getPagination()
	{
		// Load the content if it doesn't already exist
        if (empty($this->_pagination))
		{
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }
        return $this->_pagination;
	}
	
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.templates.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','asc','word' );
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.templates.list.';
		
		$filter_status		= $this->getState( 'filter_status' );
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$where = array();
		
		if ($search)
		{
			if(is_numeric($search)) {
				$where[] = 'id= '.$this->_db->Quote($search);
			} else {
				$where[] = 'LOWER(template_name) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		}
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = 'SELECT * FROM #__vbizz_templates WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = null;
			$this->_data->keyword = null;
			$this->_data->multi_keyword = null;
			$this->_data->sale_order = null;
			$this->_data->sale_order_multi_item = null;
			$this->_data->quotation = null;
			$this->_data->multi_quotation = null;
			$this->_data->template_name = null;
			$this->_data->image = null;
			$this->_data->image_ext = null;
		}
		return $this->_data;
	}
	
	public function getTable($type = 'Vaccount', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	function getTables()
	{
		
		$query = 'show tables';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadColumn();
		
		return $items;
	}
	
	function store()
	{	
		$row = $this->getTable('Templates', 'VaccountTable');
		$post=JRequest::get('post');
		
		$user = JFactory::getUser();
		
		$post['keyword']  		=	JRequest::getVar('keyword', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['multi_keyword']  =	JRequest::getVar('multi_keyword', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['sale_order']  		=	JRequest::getVar('sale_order', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['sale_order_multi_item']  =	JRequest::getVar('sale_order_multi_item', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['quotation']  =JRequest::getVar('quotation', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['multi_quotation']  =JRequest::getVar('multi_quotation', '', 'post', 'string', JREQUEST_ALLOWRAW);
		
		jimport('joomla.filesystem.file');
		
		$time = time();
		$image = JRequest::getVar("image", null, 'files', 'array');
		$allowed = array('.jpg', '.jpeg', '.gif', '.png');
		$image['image']=str_replace(' ', '', JFile::makeSafe($image['name']));	
		$image_tmp=$image["tmp_name"];
		
		$ext = strrchr($image['image'], '.');
		
		if(!empty($image['name']))	{
			
			if(!in_array($ext, $allowed))
			{
				
				$this->setError(JText::_('IMAGE_TYPE_NOT_ALLOWED'));
				return false;
			} 
		}
		
		$post['image_ext']  = $ext;
		
		$row->load(JRequest::getInt('id', 0));
		
		// Bind the form fields to the table
		if (!$row->bind($post)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $row->getErrorMsg() );
			return false;
		}
		
		if(!$post['id'])
		{
			JRequest::setVar('id', $row->id);
		}
		
		if(!$post['id']) {
			$id = $row->id;
		}else {
			$id = $post['id'];
		}
		
		
		if(!empty($image['name']))	{
			$url=JPATH_SITE.'/components/com_vbizz/invoice/invoice_'.$id.$ext;
			$thumb=JPATH_SITE.'/components/com_vbizz/invoice/thumb/invoice_'.$id.$ext;
			
			//Image Resize Start
			
			$size = getimagesize($image_tmp);
			$src_w = $size[0];
			$src_h = $size[1];
			//set the height and width in proportions
			if($src_w > $src_h )
			{
				$width = 350; //New width of image    
				$height = $size[1]/$size[0]*$width; //This maintains proportions
				$width1 = 350; //New width of image    
				$height1 = $size[1]/$size[0]*$width; //This maintains proportions
			}else	{
				$height = 125;
				$width = $size[0]/$size[1]*$height; //This maintains proportions
				$height1 = 360;
				$width1 = $size[0]/$size[1]*$height; //This maintains proportions
			}
			
			// set image new width and height 
			$width1 = 350; //New width of image    
			$height1 = $size[1]/$size[0]*$width; //This maintains proportions
			
			$new_image = imagecreatetruecolor($width, $height);
			$new_image1 = imagecreatetruecolor($width1, $height1);
			
			if($size['mime'] == "image/jpeg")
				$tmp = imagecreatefromjpeg($image_tmp);
			elseif($size['mime'] == "image/gif")
				$tmp = imagecreatefromgif($image_tmp);
			else
			
			$tmp = imagecreatefrompng($image_tmp);
			imagecopyresampled($new_image, $tmp,0,0,0,0, $width, $height, $src_w, $src_h);
			
			
			if($size['mime'] == "image/jpeg")
				imagejpeg($new_image, $thumb);
			
			elseif($size['mime'] == "image/gif")
				imagegif($new_image, $thumb);
			
			else
				imagepng($new_image, $thumb);
			
			imagecopyresampled($new_image1, $tmp,0,0,0,0, $width1, $height1, $src_w, $src_h);
			
			if($size['mime'] == "image/jpeg")
				imagejpeg($new_image1, $url);
			
			elseif($size['mime'] == "image/gif")
				imagegif($new_image1, $url);
			else
				imagepng($new_image1, $url);
			
			//Image Resige End
							
			if(!move_uploaded_file($image_tmp, $url))	{
				$this->setError(JText::_('FILE_NOT_UPLOADED'));
				return false;
			}
			
			if(!$post['id']) {
			$itemid = $row->id;
			}else {
				$itemid = $post['id'];
			}
			
			$date = JFactory::getDate()->toSql();
			
			$datetime = strtotime($date);
			$created = date('M j Y, g:i A', $datetime );
			
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->created = $date;
			$insert->created_by = $user->id;
			$insert->itemid = $itemid;
			$insert->views = $post['view'];
			$insert->type = "data_manipulation";
			if(!$post['id']) {
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_TEMPLATE' ), $post['template_name'], $itemid, 'created', $user->name, $created);
			} else {
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_TEMPLATE' ), $post['template_name'], $itemid, 'edited', $user->name, $created);
			}
			
			if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
			
		}
		
		return true;
	}
	
	
	function delete()
	{
		$user = JFactory::getUser();
		
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$row = $this->getTable('Templates', 'VaccountTable');

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
				
				$date = JFactory::getDate()->toSql();
				
				$datetime = strtotime($date);
				$created = date('M j Y, g:i A', $datetime );
				
				
				$insert = new stdClass();
				$insert->id = null;
				$insert->created = $date;
				$insert->created_by = $user->id;
				$insert->itemid = $cid;
				$insert->views = "templates";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_TEMPLATE_DELETE' ), $cid, $user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
			}
		}
		return true;
	}
	
	
	function getDefTmpl()
	{
		$query = 'SELECT * from #__vbizz_templates';
		$this->_db->setQuery( $query);
		$tmpid = $this->_db->loadObjectList();
		
		return $tmpid;
		
	}
}