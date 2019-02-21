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
jimport('joomla.filter.input');
 
class VaccountTableDocuments extends JTable
{
	var $id = null;
	var $title = null;
	var $description = null;
	var $doc = null;
	var $created_by = null;
	
	
	function __construct(& $db) {
		parent::__construct('#__vbizz_documents', 'id', $db);
	}
	
	function bind($array, $ignore = '')
	{
		if (key_exists('params', $array) && is_array($array['params']))
		{
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}
		
		return parent :: bind($array, $ignore);
	}
	
	function check()
	{
		$this->id = intval($this->id);
		
		if(empty($this->title))	{
			$this->setError( JText::_('ENTER_DOCUMENT_TITLE') );
			return false;
		}
		
		if($this->_db->getErrorNum())	{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		$files = JFactory::getApplication()->input->files->get('doc');
		if(!$this->id && $files["error"]>0){
			$this->setError( JText::_('PLEASE_UPLOAD_DOCUMENT') );
			return false;
		}
		/* $mime_type = $files['type']; */
		if($files["error"]== 0){
			$ext = pathinfo($files["name"], PATHINFO_EXTENSION);
			$config = $this->getConfig();
			$valid_ext = $config->document_type;
			$valid_ext = explode(',', $valid_ext);
			if(!in_array($ext, $valid_ext)){
				$this->setError( JText::_('INVALID_DOCUMENT') );
				return false;
			}
		}
		return parent::check();
	}
	
	function store($updateNulls = false)
	{
		
		$user = JFactory::getUser();
		$date   = JFactory::getDate();
		if(!$this->id)	{
			$this->created_by=$user->id;
			$this->created = $date->toSql();
		}
		//store document
		jimport('joomla.filesystem.file');
		$upload_dir = JPATH_COMPONENT.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'documents';
		$files = JFactory::getApplication()->input->files->get('doc');
		if($files["error"]== 0){
			//if updating,remove old file first
			if($this->id && !JFile::delete($upload_dir.DIRECTORY_SEPARATOR.$this->doc)){
				$this->setError(JText::_('UNABLE_TO_UPDATE_DOCUMENT'));
				return false;
			}
			$doc = time().JFile::makeSafe($files['name']);
			$this->doc = $doc;
			$this->size = $files['size'];
			$new_path = $upload_dir.DIRECTORY_SEPARATOR.$doc;
			if(!JFile::upload($files['tmp_name'], $new_path)){
				$this->setError(JText::_('UPLOAD_DOCUMENT_FAILED'));
				return false;
			}
		}
		
		//store other symbol
		$symbol_file = JFactory::getApplication()->input->files->get('symbol_extra');
		if($this->thumb3=='symbol_extra' && $symbol_file["error"]==0){
			
			if(!empty($this->thumb2)){
				if(!JFile::delete($upload_dir.DIRECTORY_SEPARATOR.$this->thumb2)){
					$this->setError(JText::_('UNABLE_TO_UPDATE_DOCUMENT_SYMBOL'));
					return false;
				}
			}
			$symbol = time().JFile::makeSafe($symbol_file['name']);
			$new_path = $upload_dir.DIRECTORY_SEPARATOR.$symbol;
			if(!JFile::upload($symbol_file['tmp_name'], $new_path)){
				$this->setError(JText::_('UPLOAD_DOCUMENT_SYMBOL_FAILED'));
				return false;
			}
			$this->thumb2 = $symbol;
		}
		if($this->thumb3!='symbol_extra' && !empty($this->thumb2)){
			if(!JFile::delete($upload_dir.DIRECTORY_SEPARATOR.$this->thumb2)){
				$this->setError(JText::_('UNABLE_TO_UPDATE_DOCUMENT_SYMBOL'));
				return false;
			}
			$this->thumb2 = '';
		}
		
		if(!parent::store($updateNulls)){
			return false;
		}
		return true;
	}
	
	// get configuration setting
	function getConfig()
	{
		$ownerId = VaccountHelper::getOwnerId();
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->document_acl);
		$config->document_acl = $registry;
		return $config;
	}
	
}