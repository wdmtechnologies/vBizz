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
jimport('joomla.application.component.controllerform');

class VbizzControllerMail extends VbizzController
{
	
	function __construct()
	{
		parent::__construct();
		
		$config = $this->getModel('mail')->getConfig();

		if($config->enable_cust==0) {
			JError::raiseError(404, JText::_('Page not found'));
		}
		
		$user = JFactory::getUser();
		$userId = $user->id;
 
		if(!$userId)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect(JRoute::_('index.php?option=com_vbizz&view=mail'), $msg);
		}
 
		$this->registerTask( 'add'  , 	'edit' );
	 
	}
	
	

	function edit()
	{
		JRequest::setVar( 'view', 'mail' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		
		$model = $this->getModel('mail');
		$user = JFactory::getUser();
		
		parent::display();
	}

	function save()
	{
		$model = $this->getModel('mail');
	 
		
		$tmpl = JRequest::getVar('tmpl','');
		
		if($tmpl)
		{
			$link = 'index.php?option=com_vbizz&view=mail&layout=modal&tmpl=component';
		} else {
			$link = JRoute::_('index.php?option=com_vbizz&view=mail');
		}
		
		$model->checkIn();
		
		if ($model->store($post)) {
			$msg = JText::_( 'CUSTOMER_SAVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('mail');
		
		if ($model->store($post)) {
			$msg = JText::_( 'CUSTOMER_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=mail&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=mail&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
		
	}

	function remove()
	{
		$model = $this->getModel('mail');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'CUSTOMER_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=mail'), $msg );
	}
	

	function cancel()
	{
		
		$tmpl = JRequest::getVar('tmpl','');
		
		if($tmpl)
		{
			$link = 'index.php?option=com_vbizz&view=mail&layout=modal&tmpl=component';
		} else {
			$link = JRoute::_('index.php?option=com_vbizz&view=mail');
		}
		
		$model = $this->getModel('mail');
		$model->checkIn();
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( $link, $msg );
	}
	
	
	function delete_mails(){
		
		$msg_number = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$db =JFactory::getDBO();
		
		if (count( $msg_number )) {
			foreach($msg_number as $cid) {
				$query='select published from #__vbizz_mail_integration WHERE id='.$cid;	
				$db->setQuery( $query );
				$result=$db->loadResult();
				
				if($result==1){
					$query='update #__vbizz_mail_integration SET published=0 WHERE id='.$cid;
				}else{
					$query='delete  from #__vbizz_mail_integration WHERE id='.$cid;
				}
					
				$db->setQuery( $query );
				$db->execute();		
			}
		}
	 
		if($db->execute()){
			$msg = JText::_('Mail Deleted Succesfull!!..');
		}else{
			$msg = JText::_('Mail Not deleted');
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_vbizz&view=mail&delete_msg=1'), $msg);
	}
 
 
	function move_trash(){
		
		$msg_number = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		
		$db =JFactory::getDBO();
		
		if (count( $msg_number )) {
			foreach($msg_number as $cid) {
				$query='update #__vbizz_mail_integration SET published=0 WHERE id='.$cid;
				$db->setQuery( $query );
				$db->execute();		
			}
		}
	 
		if($db->execute()){
			$msg = JText::_('MOVE_TO_TRASH_SUCCESSFULL');
		}else{
			$msg = JText::_('MOVE_TO_TRASH_FAILED');
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_vbizz&view=mail'), $msg);
	}
	
	
	
	function new_mail_fetch(){ 
	
		$model = $this->getModel('mail');
		$mbox = $model->getMaillist();

		$obj = new stdClass();
		$obj->result = "error";
		$db =JFactory::getDBO();	
		$user = JFactory::getUser();
		$userId = $user->id;
		$query = 'SELECT message_id from #__vbizz_mail_integration';
		$db->setQuery($query);
		$messages_id = $db->loadColumn();
		
		if(!empty($mbox)){
			imap_ping($mbox);
			$MC = imap_check($mbox);			
			$result = imap_fetch_overview($mbox,"1:{$MC->Nmsgs}",0);
			$count=0;
			for($j=0;$j<count($result);$j++){ 
			if(in_array($result[$j]->uid,$messages_id)==false){$count++;
				$insert = new stdClass();
				$insert->id = null;
				$insert->userid = $userId;
				$insert->published =1;
				
				//$body = imap_fetchbody($mbox, $result[$j]->msgno, "2");
				$header = imap_headerinfo($mbox, $result[$j]->msgno);

				foreach ((array)$header->to as $to_extra) {
					if(!empty($to_extra->personal)){
						$to_name=$to_extra->personal;
						$insert->to_name =$to_name;
					}
						$to_email=$to_extra->mailbox.'@'.$to_extra->host;
						$insert->to_email =$to_email;
				}	
				foreach ((array)$header->from as $from_extra) {
					if(!empty($from_extra->personal)){
					$from_name=$from_extra->personal;
					$insert->from_name =$from_name;
					}
					$from_email=$from_extra->mailbox.'@'.$from_extra->host;
					$insert->from_email =$from_email;
				}					
				 if(!empty($header->cc)){
					foreach ((array)$header->cc as $cc_extra)
					$cc=$cc_extra->mailbox.'@'.$cc_extra->host;
					$insert->cc = $cc;
					}
				$structure=imap_fetchstructure($mbox,$result[$j]->msgno); 
				$partno=$structure->type;
				$i=0;
				if(!empty($structure->parts )){
					foreach ($structure->parts as $partnos=>$p){
						if($i==0){
							$type_text=$p->subtype;
							
						}
						$i++ ;
					}
				}
				if(empty($type_text)){
					$type_text='PLAIN';
				}
				$insert->subtype = $type_text;
				
				$attachments = array();
				if(isset($structure->parts) && count($structure->parts)) 
					{
						for($i = 0; $i < count($structure->parts); $i++) 
						{
							$attachments[$i] = array(
								'is_attachment' => false,
								'filename' => '',
								'name' => '',
								'attachment' => ''
							);
						
							if($structure->parts[$i]->ifdparameters) 
							{
								foreach($structure->parts[$i]->dparameters as $object) 
								{
									if(strtolower($object->attribute) == 'filename') 
									{
										$attachments[$i]['is_attachment'] = true;
										$attachments[$i]['filename'] = $object->value;
									}
								}
							}
						
							if($structure->parts[$i]->ifparameters) 
							{
								foreach($structure->parts[$i]->parameters as $object) 
								{
									if(strtolower($object->attribute) == 'name') 
									{
										$attachments[$i]['is_attachment'] = true;
										$attachments[$i]['name'] = $object->value;
									}
								}
							}
						
							if($attachments[$i]['is_attachment']) 
							{
								$attachments[$i]['attachment'] = imap_fetchbody($mbox, $result[$j]->msgno, $i+1);
								$encoding=$structure->parts[$i]->encoding;
								$insert->encoding = $encoding;
								
								/*if($structure->parts[$i]->encoding == 3) 
								{ 
									$attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
								}
								 
								elseif($structure->parts[$i]->encoding == 4) 
								{ 
									$attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
								}*/
							}
						}
					}
				foreach($attachments as $attachment)
				{
					if($attachment['is_attachment'] == 1)
					{ 
					$insert->attachments =1;
					$insert->attachments_name = $attachment['name'];
					$insert->attachments_files_path = $attachment['attachment'];					
					}
				}
				
				//$body_messge = ($partno)?imap_fetchbody( $mbox,$result[$j]->msgno,$partno):imap_body($mbox,$result[$j]->msgno);
				
				if($partno){
					if($type_text=='ALTERNATIVE')
						$part_num=1.2;
					else
						$part_num=1;
					 
					$body_messge=imap_fetchbody( $mbox,$result[$j]->msgno,$part_num);
					
				}else{
					$body_messge=imap_body($mbox,$result[$j]->msgno);
				}
				
			 

				$insert->subject = $result[$j]->subject;
				$insert->body_messge = $body_messge;
				$insert->seen = $result[$j]->seen;
				$insert->mail_date = $result[$j]->date;
				$insert->message_id = $result[$j]->uid;
				$insert->deleted = $result[$j]->deleted;
				//$insert->encoding = $encoding;
				//size in bytes
				$insert->size= $result[$j]->size;
				$db->insertObject('#__vbizz_mail_integration', $insert, 'id');	
			}

			}
			
			$obj->result=$count==0?JText::_('NO_NEWMAIL_FETCH'):$count.'-'.JText::_('New mail fetch');
		}
		else
			$obj->result = JText::_('NO_NEWMAIL_FETCH');
	 	
		jexit(json_encode($obj)); 	
	}
	
	
	function move_archive(){
		
		$msg_number = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$model = $this->getModel('mail');
		$db =JFactory::getDBO();
		//$mbox = $model->getMaillist();	
		//$dirpath=JPATH_SITE.'/components/com_vbizz/assets/images/mail_attach/';
		//for ($i = 0; $i <count($msg_number); $i++) { 
			//imap_mail_move($mbox,$msg_number[$i],'INBOX/Saved');
		///}
		if (count( $msg_number )) {
			foreach($msg_number as $cid) {
			$query='update #__vbizz_mail_integration SET archive_mail=1 WHERE id='.$cid;
			$db->setQuery( $query );
			$db->execute();		
			}
		}
		if($db->execute()){
			//!empty($msg_number) and (imap_expunge($mbox))){
			$msg = JText::_('MOVE_ARCHIVE_SUCCESSFULL');
		}else{
			$msg = JText::_('MOVE_ARCHIVE_FIALED');
		}
 
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=mail'), $msg );
	}
	
	 
	 function imap_setting()
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$data = JRequest::get( 'post' );
		$imap_password = $data['imap_password'];
		$imap_email = $data['imap_email'];
		$imap_username = $data['imap_username'];
		$imap_port = $data['imap_port'];
		$imap_host = $data['imap_host'];
		$imap_security = $data['imap_security'];
		
		$smtp_password = $data['smtp_password'];
		$smtp_email = $data['smtp_email'];
		$smtp_username = $data['smtp_username'];
		$smtp_port = $data['smtp_port'];
		$smtp_host = $data['smtp_host'];
		$smtp_security = $data['smtp_security'];	
		$smtp_authentication = $data['smtp_authentication'];	

		$query = 'SELECT count(*) from #__vbizz_mail_setting where userid='.$user->id;
		$db->setQuery($query);
		$count_user = $db->loadResult();
		
		$insert = new stdClass();
		$insert->userid = $user->id;
		$insert->imap_email = $imap_email;
		$insert->imap_username = $imap_username;
		$insert->imap_password = $imap_password;
		$insert->imap_port = $imap_port;
		$insert->imap_host = $imap_host;
		$insert->imap_security = $imap_security;
		
		$insert->smtp_password = $smtp_password;
		$insert->smtp_email = $smtp_email;
		$insert->smtp_username = $smtp_username;
		$insert->smtp_port = $smtp_port;
		$insert->smtp_host = $smtp_host;
		$insert->smtp_security = $smtp_security;
		$insert->smtp_authentication = $smtp_authentication;
		
		if($count_user>0){	
		
				$query = 'UPDATE #__vbizz_mail_setting set imap_password='.$db->Quote($imap_password).',imap_port='.$db->Quote($imap_port).',imap_host='.$db->Quote($imap_host).',imap_security='.$db->Quote($imap_security).',smtp_username='.$db->Quote($smtp_username).',smtp_email='.$db->Quote($smtp_email).',smtp_password='.$db->Quote($smtp_password).',smtp_port='.$db->Quote($smtp_port).',smtp_host='.$db->Quote($smtp_host).',smtp_security='.$db->Quote($smtp_security).',smtp_authentication='.$db->Quote($smtp_authentication).' where userid='.$user->id;
				$db->setQuery($query);
				$db->query();
				$msg = JText::_('SETTING_SAVED_MSG');
			
		}else {
			
			if(!$db->insertObject('#__vbizz_mail_setting', $insert, 'id'))	{
				$msg = JText::_('ERROR_MAIL_SETTING');
			} 

		}
 
		$this->setRedirect( 'index.php?option=com_vbizz&view=mail&layout=configuration&tmpl=component', $msg );
			
	} 

	
	function sendCustomEmail()
	{  
		$db = JFactory::getDbo();
		$data = JRequest::get( 'post' );
		$mainframe = JFactory::getApplication();
		$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();
		$user = JFactory::getUser();
		
		jimport('joomla.filesystem.file');
		
		$data['body_text'] = JRequest::getVar('body_text', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$type_mail = $data['type_mail'];
		$email_to = $data['email_to'];
		$email_cc = $data['email_cc'];	
		
		$image = JRequest::getVar('attach_file', null, 'FILES', 'array');
		$allowed = array('.jpg', '.jpeg', '.gif', '.png','.pdf','.xl','.doc');
		$dirpath=JPATH_SITE.'/components/com_vbizz/assets/images/mail_attach/';
		$count_atatch=0;
		$attachments=array();
  
		for($i=0;$i<count($image['name']);$i++)
		//foreach ($image['name'] as $filename) 
		{  
			$filename=$image['name'][$i];
			$image_tmp=$image['tmp_name'][$i];
		    $ext = strrchr($filename, '.');
		   	if(!empty($filename) and !in_array($ext, $allowed)){
			$msg=JText::_('IMAGE_TYPE_NOT_ALLOWED');
			$this->setRedirect( 'index.php?option=com_vbizz&view=mail&layout=modal&tmpl=component', $msg );
			return false;
			}
			if(move_uploaded_file($image_tmp, $dirpath.'_'.$filename)){
				$path=$dirpath.'_'.$filename;
				array_push($attachments,$path);
				$count_atatch++;
			}
			$image_tmp='';
		 
		}
		
		$name_from = $user->name;
		$email_from =$user->email;
		$subject = $data['subject'];
		$body = $data['body_text'];

		$email_to_array=explode(",",$email_to);
		$email_cc_array = explode(",",$email_cc);		
		
		$sender = array($email_from,$name_from ); 
			
			$mailer->setSender($sender);
			
			for( $i=0;$i<count($email_to_array);$i++){
				$mailer->addRecipient(trim($email_to_array[$i]));
			}
			$mailer->setSubject($subject);
			$mailer->setBody($body);
			//$mailer->addAttachment($attachments);
			
		  for( $i=0;$i<count($attachments);$i++){
				$mailer->addAttachment($attachments[$i]);
				 
			}
			  
			for( $i=0;$i<count($email_cc_array);$i++){
				$mailer->addCC(trim($email_cc_array[$i]));
			}
			 
			$mailer->IsHTML(true);
			if($type_mail==1)
				$mailer->addReplyTo( array( $email_to_array[0], $email_to_array[0] ) );
		
		$send = $mailer->send();

		if ( $send ) {
		$msg = JText::_('MAIL_SENT_SUCCESS');
		$this->setRedirect( 'index.php?option=com_vbizz&view=mail&layout=modal&sent=1&tmpl=component', $msg );
		
			for( $i=0;$i<count($attachments);$i++){
				unlink($attachments[$i]);
			}

		}
		else{
			$msg = JText::_('MAIL_SENT_FAILED');
			$this->setRedirect( 'index.php?option=com_vbizz&view=mail&layout=modal&sent=0&tmpl=component', $msg );
		}
	}
	
	function moveToBug() {
		
		$obj = new stdClass();
		$obj->result = "error";
		$obj->msg = JText::_('YOU_R_NOT_AUTHORISE');
		
		$db =JFactory::getDBO();	
		$user = JFactory::getUser();
		
		$config = $this->getModel('mail')->getConfig();

		$groups = $user->getAuthorisedGroups();

				
		$add_access = $config->bug_acl->get('addaccess');

		if($add_access) {
			$addaccess = false;
			foreach($groups as $group) {
				if(in_array($group,$add_access))
				{
					$addaccess=true;
					break;
				}
			}
		} else {
			$addaccess=true;
		}

		if($addaccess)
		{
		
			$data = JRequest::get( 'post' );
			
			$mailid 	= $data['id'];
			$notes 	= $data['notes'];
			
			$query = 'SELECT count(*) from #__vbizz_bug where mailid='.$mailid;
			$db->setQuery($query);
			$count = $db->loadResult();
			
			if($count) {
				$obj->result = "error";
				$obj->msg = JText::_('ALLREADY_ADDED_TO_BUG');
			} else {
				$insert = new stdClass();
				$insert->mailid = $mailid;
				$insert->notes = $notes;
				$insert->created_by = $user->id;
				
				if(!$db->insertObject('#__vbizz_bug', $insert, 'id'))	{
					$obj->result = "error";
					$obj->msg = JText::_('NOT_MOVED_TO_BUG');
				} else {
					
					$query = 'UPDATE #__vbizz_mail_integration set bug=1 where id='.$mailid;
					$db->setQuery($query);
					$db->query();
					
					$obj->result = "success";
					$obj->msg = JText::_('MOVED_TO_BUG');
				}
			}
		}
		
		jexit(json_encode($obj));
		
	}
	
}