<?php
/*------------------------------------------------------------------------
# com_vbizz - vBizz
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 wwww.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech..com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
/**
 * Script file of vBizz component
 */
class com_vbizzInstallerScript
{
	
	var $messages;
	var $status;
	var	$sourcePath;

	function execute()
	{

		//get version number from manifest file.
		$jinstaller	= JInstaller::getInstance();
		$installer = new VbizzInstaller( $jinstaller );
		$installer->execute();

		$this->messages	= $installer->getMessages();
	}
	function install($parent) 
	{
		
		
		
	}
 
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		
		
		
	}
 
	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) 
	{
		
	}
 
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
		
	}
 
	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
		
		
		
		$messages = (array)$this->messages;
		
		?>

	<style type="text/css">
	.adminform tr th{
		display:none;
	}

	/* TYPOGRAPHY AND SPACING */
	#vbizz-installer td{
		font-size:11px;
		line-height:1.7;
		font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
	}
	#vbizz-installer td table td{
		padding:5px 2px 5px 10px;
	}

	/* MESSAGES */
	#vbizz-message {
		border:1px solid #ccc;
		padding:13px;
		border-radius:2px;
		-moz-border-radius:2px;
		-webkit-border-radius:2px;
		font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
	}

	#vbizz-message.error {
		border-color:#900;
		color: #900;
		font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
	}

	#vbizz-message.info {
		background:#ECEFF6;
		border-color:#c4cbdd;
		color:#555;
		font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
	}

	#vbizz-message.warning {
		border-color:#f90;
		color: #c30;
		font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
	}
	#stylized {
    background: none repeat scroll 0 0 #EBF4FB;
    border: 1px solid #B7DDF2;
	font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
	}
	.myform {
		height: auto;
		margin: 0 auto;
		padding: 14px;
		width: auto;
	}
	</style>
	<div id="stylized" class="myform">
	<table id="vbizz-installer" width="100%" border="0" cellpadding="0" cellspacing="0">
		<?php
			foreach ($messages as $message) {
				?>
				<tr>
					<td><div id="vbizz-message" class="<?php echo $message['type']; ?>"><?php echo ucfirst($message['type']) . ' : ' . $message['message']; ?></div></td>
				</tr>
				<?php
			}
		?>
		<tr>
			<td>
				<div style="padding:20px 0;"><img src="<?php echo JURI::root().'components/com_vbizz/assets/images/logo.png'?>" style="height:128px;"/></div>
			</td>
		</tr>
		<tr>
			<td>
				<div style="width:700px; padding-left:10px">
					<h1 style="margin-top: 0px; margin-left: 0px;"><?php echo JText::_('COM_VBIZZ_TITLE');?></h1><?php echo JText::_('COM_VBIZZ_DESCRIPTIONS');?> 
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<table>
					<tr>
						<td colspan="2">To get our latest news and promotions :</td>
					</tr>
					<tr>
						<td>Like us on Facebook :</td>
						<td>
							<div id="fb-root"></div>
							<script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, 'script', 'facebook-jssdk'));</script>
							<div class="fb-like" data-href="https://www.facebook.com/wdmtechnologies" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false"></div>
						</td>
					<tr>
						<td>Follow us on Twitter :</td>
						<td>
							<a href="https://twitter.com/wdmtechnologies" class="twitter-follow-button" data-show-count="false">Follow @wdmtechnologies</a>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
						</td>
					</tr>
<tr>
                    	<td colspan="2">Post on our <a href="http://www.wdmtech.com/support-forum" target="_blank">Support Forum</a> for any Assistance</td>
                    </tr>
					<tr>
						<td colspan="2">If you use vAccount, please post a rating and a review at <a href="http://extensions.joomla.org/extensions/extension/financial/cost-calculators/vbizz" target="_blank">Joomla! Extension Directory</a>.</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
    </div>
	
	<?php
	
		/* $db = JFactory::getDbo();
		
		$user = JFactory::getUser();
		
		$query = 'UPDATE #__vbizz_accounts SET '.$db->quoteName('created_by').'= '.$db->quote($user->id);
		$db->setQuery( $query );
		
		if(!$db->query())	{
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		$query = 'UPDATE #__vbizz_customer SET '.$db->quoteName('created_by').'= '.$db->quote($user->id);
		$db->setQuery( $query );
		
		if(!$db->query())	{
			$this->setError($db->getErrorMsg());
			return false;
		}  
		
		$query = 'UPDATE #__vbizz_discount SET '.$db->quoteName('created_by').'= '.$db->quote($user->id);
		$db->setQuery( $query );
		
		if(!$db->query())	{
			$this->setError($db->getErrorMsg());
			return false;
		}  
		
		$query = 'UPDATE #__vbizz_invoices SET '.$db->quoteName('created_by').'= '.$db->quote($user->id);
		$db->setQuery( $query );
		
		if(!$db->query())	{
			$this->setError($db->getErrorMsg());
			return false;
		}  
		
		$query = 'UPDATE #__vbizz_items SET '.$db->quoteName('created_by').'= '.$db->quote($user->id);
		$db->setQuery( $query );
		
		if(!$db->query())	{
			$this->setError($db->getErrorMsg());
			return false;
		}  
		
		$query = 'UPDATE #__vbizz_items SET '.$db->quoteName('created_by').'= '.$db->quote($user->id);
		$db->setQuery( $query );
		
		if(!$db->query())	{
			$this->setError($db->getErrorMsg());
			return false;
		}   */
		
		/* $query = 'UPDATE #__vbizz_tmode SET '.$db->quoteName('created_by').'= '.$db->quote($user);
		$db->setQuery( $query );
		
		if(!$db->query())	{
			$this->setError($db->getErrorMsg());
			return false;
		}   */
		
		/* $query = 'UPDATE #__vbizz_tran SET '.$db->quoteName('created_by').'= '.$db->quote($user->id);
		$db->setQuery( $query );
		
		if(!$db->query())	{
			$this->setError($db->getErrorMsg());
			return false;
		}  
		
		$query = 'UPDATE #__vbizz_transaction SET '.$db->quoteName('created_by').'= '.$db->quote($user->id);
		$db->setQuery( $query );
		
		if(!$db->query())	{
			$this->setError($db->getErrorMsg());
			return false;
		}  
		
		$query = 'UPDATE #__vbizz_vendor SET '.$db->quoteName('created_by').'= '.$db->quote($user->id);
		$db->setQuery( $query );
		
		if(!$db->query())	{
			$this->setError($db->getErrorMsg());
			return false;
		}
		

		$query = 'SELECT title from #__usergroups';
		$db->setQuery($query);
		$client_groups_title = $db->loadColumn();
		
		$query = 'SELECT lft,rgt from #__usergroups where id=8';
		$db->setQuery($query);
		$super_user_lft_rgt = $db->loadObject();
		
		if(!in_array('Owner',$client_groups_title)){
			$insert = new stdClass();
			$insert->id = null;
			$insert->title = 'Owner';
			$insert->parent_id =1;
			$insert->lft =$super_user_lft_rgt->rgt+1;
			$insert->rgt =$super_user_lft_rgt->rgt+2;
			if($db->insertObject('#__usergroups', $insert, 'id')){
				$query = 'UPDATE #__vbizz_configuration SET '.$db->quoteName('owner_group_id').'= '.$db->quote($db->insertid());
				$db->setQuery( $query );
				$db->execute();
			}
		}else{
			
			$query = 'SELECT `id` from #__usergroups where title='.$db->Quote('Owner');
			$db->setQuery($query);
			$owner_group_id = $db->loadResult(); 
			
			//$owner_group_id = array_search('Owner', $client_groups_title);
			
			$query = 'UPDATE #__vbizz_configuration SET '.$db->quoteName('owner_group_id').'= '.$db->quote($owner_group_id);
			$db->setQuery( $query );
			$db->execute();
		}
		
		if(!in_array('Employee',$client_groups_title)){
			$insert = new stdClass();
			$insert->id = null;
			$insert->title = 'Employee';
			$insert->parent_id =1;
			$insert->lft =$super_user_lft_rgt->rgt+3;
			$insert->rgt =$super_user_lft_rgt->rgt+4;
			if($db->insertObject('#__usergroups', $insert, 'id')){
				$query = 'UPDATE #__vbizz_configuration SET '.$db->quoteName('employee_group_id').'= '.$db->quote($db->insertid());
				$db->setQuery( $query );
				$db->execute();
			}
		}else{
			
			$query = 'SELECT `id` from #__usergroups where title='.$db->Quote('Employee');
			$db->setQuery($query);
			$employee_group_id = $db->loadResult();
			
			//$employee_group_id = array_search('Employee', $client_groups_title);
			
			$query = 'UPDATE #__vbizz_configuration SET '.$db->quoteName('employee_group_id').'= '.$db->quote($employee_group_id);
			$db->setQuery( $query );
			$db->execute();
		}
		
		if(!in_array('Vendor',$client_groups_title)){
			$insert = new stdClass();
			$insert->id = null;
			$insert->title = 'Vendor';
			$insert->parent_id =1;
			$insert->lft =$super_user_lft_rgt->rgt+5;
			$insert->rgt =$super_user_lft_rgt->rgt+6;
			if($db->insertObject('#__usergroups', $insert, 'id')){
				$query = 'UPDATE #__vbizz_configuration SET '.$db->quoteName('vender_group_id').'= '.$db->quote($db->insertid());
				$db->setQuery( $query );
				$db->execute();
			}
		}else{
			
			$query = 'SELECT `id` from #__usergroups where title='.$db->Quote('Vendor');
			$db->setQuery($query);
			$vender_group_id = $db->loadResult();
			
			//$vender_group_id = array_search('Vendor', $client_groups_title);
			
			$query = 'UPDATE #__vbizz_configuration SET '.$db->quoteName('vender_group_id').'= '.$db->quote($vender_group_id);
			$db->setQuery( $query );
			$db->execute();
			
		}
		
		if(!in_array('Client',$client_groups_title)){
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->title = 'Client';
			$insert->parent_id =1;	
			$insert->lft =$super_user_lft_rgt->rgt+7;
			$insert->rgt =$super_user_lft_rgt->rgt+8;			
			if($db->insertObject('#__usergroups', $insert, 'id')){
				$query = 'UPDATE #__vbizz_configuration SET '.$db->quoteName('client_group_id').'= '.$db->quote($db->insertid());
				$db->setQuery( $query );
				$db->execute();
			}
		}else{
			
			$query = 'SELECT `id` from #__usergroups where title='.$db->Quote('Client');
			$db->setQuery($query);
			$client_group_id = $db->loadResult(); 
			
			//$client_group_id = array_search('Client', $client_groups_title);
			
			$query = 'UPDATE #__vbizz_configuration SET '.$db->quoteName('client_group_id').'= '.$db->quote($client_group_id);
			$db->setQuery( $query );
			$db->execute();
			
		}
				
		$query = 'UPDATE #__usergroups SET '.$db->quoteName('rgt').'= '.$db->quote($super_user_lft_rgt->rgt+9).' where id=1';
		$db->setQuery( $query );
		$db->execute();
		
		$query = 'SELECT `id` from #__usergroups where title='.$db->Quote('Owner');
		$db->setQuery($query);
		$owner_group = $db->loadResult();
		
		if(!empty($owner_group)){
			
			$query = 'SELECT `group_id` from #__user_usergroup_map where user_id='.$db->Quote($user->id);
			$db->setQuery($query);
			$user_group_id = $db->loadColumn();
			
			if(!in_array($owner_group,$user_group_id)){
				
				$query = 'insert into #__user_usergroup_map(user_id,group_id) values('.$db->quote($user->id).', '.$db->quote($owner_group).')';
				$db->setQuery( $query );
				$db->execute();
				
			}
			
		}
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->name =$user->name;
		$insert->email = $user->email;
		$insert->widget_ordering ='1,2,3,4,5,6';
		$db->insertObject('#__vbizz_users', $insert, 'id'); */
		
		
	}
}
