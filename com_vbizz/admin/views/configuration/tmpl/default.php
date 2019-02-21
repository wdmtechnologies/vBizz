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
defined('_JEXEC') or die('Restricted access');       
JHTML::_('behavior.tooltip');

$version = new JVersion;    
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion>='3.0') {
	JHtml::_('formbehavior.chosen', 'select');
}

$activeLangs = array() ;
$jlangTag = array() ;
$language =JFactory::getLanguage();
$jLangs = $language->getKnownLanguages(JPATH_BASE);
foreach ($jLangs as $jLang) {
	$jlangTag[] = $jLang['tag'];
	$activeLangs[] = $jLang['name'];
	
}  
 
$date = JFactory::getDate();

//$income_notify = json_decode($this->config->income_notify);
?>
<script>
jQuery(function() {
	jQuery( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
	jQuery( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
	jQuery( "#config" ).tabs();
});
</script>
<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		
		var form = document.adminForm;
	
		if(form.currency.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_CURRENCY'); ?>");
			return false;
		}
		
		if(typeof(validateit) == 'function')	{
			
			if(!validateit())
				return false;
		}
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
</script>
<?php if (!empty( $this->sidebar)) : ?>   
		<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
		<?php endif;?>
<form action="index.php?option=com_vbizz&view=configuration" method="post" name="adminForm" id="adminForm">
<div class="col100">
<legend><?php echo JText::_( 'CONFIGURATION' ); ?></legend>

<div id="config">
        <ul>
            <li><a href="#config-1"><?php	echo JText::_('BASIC_SETTINGS');?></a></li>
            <li><a href="#config-2"><?php	echo JText::_('TERMINOLOGIES'); ?></a></li>
            <li><a href="#config-3"><?php	echo JText::_('INVOICE_SETTING'); ?></a></li>
			<li><a href="#config-4"><?php	echo JText::_('LOCALISATION'); ?></a></li>
			<li><a href="#config-5"><?php	echo JText::_('NOTIFICATION'); ?></a></li>
			<li><a href="#config-7"><?php	echo JText::_('EMAIL_NOTIFICATION'); ?></a></li>
			<li><a href="#config-6"><?php echo JText::_( 'ACL' ); ?></a></li>
        </ul>
        
	<div id="config-1">
	<table class="adminform table table-striped">
		<tbody>    
		<tr>
			<td><label class="hasTip" title="<?php echo JText::_('ENITEMSTXT');?>"><?php echo JText::_('ENITEMS');?></label></td>
			<td>
				<fieldset class="radio btn-group">
				<label for="enable_items1" id="enable_items-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
				<input type="radio" name="enable_items" id="enable_items1" value="1" <?php if($this->config->enable_items) echo 'checked="checked"';?>/>
				<label for="enable_items0" id="enable_items-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
				<input type="radio" name="enable_items" id="enable_items0" value="0" <?php if(!$this->config->enable_items) echo 'checked="checked"';?>/>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><label class="hasTip" title="<?php echo JText::_('ENPROJTXT');?>"><?php echo JText::_('ENPROJ');?></label></td>
			<td>
				<fieldset class="radio btn-group">
				<label for="enable_project1" id="enable_project-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
				<input type="radio" name="enable_project" id="enable_project1" value="1" <?php if($this->config->enable_project) echo 'checked="checked"';?>/>
				<label for="enable_project0" id="enable_project-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
				<input type="radio" name="enable_project" id="enable_project0" value="0" <?php if(!$this->config->enable_project) echo 'checked="checked"';?>/>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><label class="hasTip" title="<?php echo JText::_('ENEMPLTXT');?>"><?php echo JText::_('ENEMP');?></label></td>
			<td>
				<fieldset class="radio btn-group">
				<label for="enable_employee1" id="enable_employee-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
				<input type="radio" name="enable_employee" id="enable_employee1" value="1" <?php if($this->config->enable_employee) echo 'checked="checked"';?>/>
				<label for="enable_employee0" id="enable_employee-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
				<input type="radio" name="enable_employee" id="enable_employee0" value="0" <?php if(!$this->config->enable_employee) echo 'checked="checked"';?>/>
				</fieldset>
			</td>
		</tr>
		
		<tr>
			<td><label class="hasTip" title="<?php echo JText::_('ENVNDRTXT');?>"><?php echo JText::_('ENVNDR');?></label></td>
			<td>
				<fieldset class="radio btn-group">
				<label for="enable_vendor1" id="enable_vendor-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
				<input type="radio" name="enable_vendor" id="enable_vendor1" value="1" <?php if($this->config->enable_vendor) echo 'checked="checked"';?>/>
				<label for="enable_vendor0" id="enable_vendor-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
				<input type="radio" name="enable_vendor" id="enable_vendor0"value="0" <?php if(!$this->config->enable_vendor) echo 'checked="checked"';?>/>
				</fieldset>
			</td>
		</tr>
		
		<tr>
			<td><label class="hasTip" title="<?php echo JText::_('ENCUSTTXT');?>"><?php echo JText::_('ENCUST');?></label></td>
			<td>
				<fieldset class="radio btn-group">
				<label for="enable_cust1" id="enable_cust-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
				<input type="radio" name="enable_cust" id="enable_cust1" value="1" <?php if($this->config->enable_cust) echo 'checked="checked"'; ?> />
				<label for="enable_cust0" id="enable_cust-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
				<input type="radio" name="enable_cust" id="enable_cust0"value="0" <?php if(!$this->config->enable_cust) echo 'checked="checked"'; ?> />
				</fieldset>
			</td>
		</tr>    
		<tr>
			<td><label class="hasTip" title="<?php echo JText::_('COM_VBIZZ_USER_ASSIGN_ENABLED_DESC');?>"><?php echo JText::_('COM_VBIZZ_USER_ASSIGN_ENABLED');?></label></td>
			<td>
				<fieldset class="radio btn-group">
				<label for="user_assign_enable1" id="user_assign_enable-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
				<input type="radio" name="user_assign_enable" id="user_assign_enable1" value="1" <?php if($this->config->user_assign_enable) echo 'checked="checked"';?>/>
				<label for="user_assign_enable0" id="user_assign_enable-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
				<input type="radio" name="user_assign_enable" id="user_assign_enable0" value="0" <?php if(!$this->config->user_assign_enable) echo 'checked="checked"';?>/>
				</fieldset>
			</td>
		</tr>
		<tr>  
			<td><label class="hasTip" title="<?php echo JText::_('ENRECURTXT');?>"><?php echo JText::_('ENRECUR');?></label></td>
			<td>
				<fieldset class="radio btn-group">
				<label for="enable_recur1" id="enable_recur-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
				<input type="radio" name="enable_recur" id="enable_recur1" value="1" <?php if($this->config->enable_recur) echo 'checked="checked"'; ?> />
				<label for="enable_recur0" id="enable_recur-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
				<input type="radio" name="enable_recur" id="enable_recur0"value="0" <?php if(!$this->config->enable_recur) echo 'checked="checked"'; ?> />
				</fieldset>
			</td>
		</tr>
		
		<tr>
			<td><label class="hasTip" title="<?php echo JText::_('ENACCOUNTTXT');?>"><?php echo JText::_('ENACCOUNT');?></label></td>
			<td>
				<fieldset class="radio btn-group">
				<label for="enable_account1" id="enable_account-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
				<input type="radio" name="enable_account" id="enable_account1" value="1" <?php if($this->config->enable_account) echo 'checked="checked"';?> />
				<label for="enable_account0" id="enable_account-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
				<input type="radio" name="enable_account" id="enable_account0"value="0" <?php if(!$this->config->enable_account) echo 'checked="checked"';?> />
				</fieldset>
			</td>
		</tr>
		
		<tr>
			<td><label class="hasTip" title="<?php echo JText::_('ENTAXDISCOUNTTXT');?>"><?php echo JText::_('ENTAXDISCOUNT');?></label></td>
			<td>
				<fieldset class="radio btn-group">
				<label for="enable_tax_discount1" id="enable_tax_discount-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
				<input type="radio" name="enable_tax_discount" id="enable_tax_discount1" value="1" <?php if($this->config->enable_tax_discount) echo 'checked="checked"';?> />
				<label for="enable_tax_discount0" id="enable_tax_discount-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
				<input type="radio" name="enable_tax_discount" id="enable_tax_discount0"value="0" <?php if(!$this->config->enable_tax_discount) echo 'checked="checked"';?> />
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><label class="hasTip" title="<?php echo JText::_('SHOWHEADERFRONTHAND_DESC');?>"><?php echo JText::_('SHOWHEADERFRONTHAND');?></label></td>
			<td>
				<fieldset class="radio btn-group">
				<label for="showheader1" id="showheader-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
				<input type="radio" name="showheader" id="showheader1" value="1" <?php if($this->config->showheader) echo 'checked="checked"';?> />
				<label for="showheader0" id="showheader-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
				<input type="radio" name="showheader" id="showheader0"value="0" <?php if(!$this->config->showheader) echo 'checked="checked"';?> />
				</fieldset>
			</td>  
		</tr>
		<!--<tr>
			<td><label class="hasTip" title="<?php echo JText::_('ENYODLEETXT');?>"><?php echo JText::_('INTYODLEEAPI');?></label></td>
			<td>  
				<fieldset class="radio btn-group">
				<label for="enable_yodlee1" id="enable_yodlee-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
				<input type="radio" name="enable_yodlee" id="enable_yodlee1" value="1" <?php if($this->config->enable_yodlee) echo 'checked="checked"';?> />
				<label for="enable_yodlee0" id="enable_yodlee-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
				<input type="radio" name="enable_yodlee" id="enable_yodlee0"value="0" <?php if(!$this->config->enable_yodlee) echo 'checked="checked"';?> />
				</fieldset>
			</td>
		</tr> -->
		<tr>
			<td><label class="hasTip" title="<?php echo JText::_('ENDOCUMENTTXT');?>"><?php echo JText::_('ENDOCUMENT');?></label></td>
			<td>
				<fieldset class="radio btn-group">
				<label for="enable_document1" id="enable_document-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
				<input type="radio" name="enable_document" id="enable_document1" value="1" <?php if($this->config->enable_document) echo 'checked="checked"';?> />
				<label for="enable_document0" id="enable_document-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
				<input type="radio" name="enable_document" id="enable_document0"value="0" <?php if(!$this->config->enable_document) echo 'checked="checked"';?> />
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><label class="hasTip" title="<?php echo JText::_('ENDOCUMENT_TYPETXT');?>"><?php echo JText::_('ENDOCUMENT_TYPE');?></label></td>
			<td>
				<textarea name="document_type" rows="4" cols="50"><?php echo $this->config->document_type;?></textarea>
			</td>
		</tr>
		<tr>
			<td><label class="hasTip" title="<?php echo JText::_('SETBUDGETTXT'); ?>"><?php echo JText::_('SET_BUDGET'); ?>:</label></td>
			<td>
				<select name="budget_time">
				<option value=""><?php echo JText::_('SELECT_BUDGET_TIME'); ?></option>
				<option value="weekly" <?php if($this->config->budget_time=="weekly") echo 'selected="selected"'; ?>><?php echo JText::_('WEEKLY');?></option>
				<option value="monthly" <?php if($this->config->budget_time=="monthly") echo 'selected="selected"'; ?>><?php echo JText::_('MONTHLY');?></option>
				<option value="quaterly" <?php if($this->config->budget_time=="quaterly") echo 'selected="selected"';?>><?php echo JText::_('QUATERLY');?></option>
				<option value="yearly" <?php if($this->config->budget_time=="yearly") echo 'selected="selected"'; ?>><?php echo JText::_('YEARLY');?></option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td width="200"><label class="hasTip" title="<?php echo JText::_('SET_REMINDER1');?>"><?php echo JText::_('REMINDER1');?>:</label></td>
			<td><input class="text_area" type="text" name="reminder1" id="reminder1" value="<?php echo $this->config->reminder1;?>"/></td>
		</tr>
		
		<tr>
			<td width="200"><label class="hasTip" title="<?php echo JText::_('SET_REMINDER2'); ?>"><?php echo JText::_('REMINDER2'); ?>:</label></td>
			<td><input class="text_area" type="text" name="reminder2" id="reminder2" value="<?php echo $this->config->reminder2;?>" /></td>
		</tr>
		
		<tr>
			<td width="200"><label class="hasTip" title="<?php echo JText::_('SET_OVERDUE_REMINDER'); ?>"><?php echo JText::_('OVERDUE_REMINDER'); ?>:</label></td>
			<td><input class="text_area" type="text" name="overdue_reminder" id="overdue_reminder" value="<?php echo $this->config->overdue_reminder;?>" /></td>
		</tr>
		
		<tr>
			<td><label class="hasTip" title="<?php echo JText::_('SENDEXPNOTIFYTXT');?>"><?php echo JText::_('SEND_EXP_NOTIFICATION');?></label></td>
			<td>
				<fieldset class="radio btn-group">
				<label for="expense_notify1" id="expense_notify-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
				<input type="radio" name="expense_notify" id="expense_notify1" value="1" <?php if($this->config->expense_notify) echo 'checked="checked"';?> />
				<label for="expense_notify0" id="expense_notify-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
				<input type="radio" name="expense_notify" id="expense_notify0"value="0" <?php if(!$this->config->expense_notify) echo 'checked="checked"';?> />
				</fieldset>
			</td>
		</tr>
		
		<tr>
			<td><label class="hasTip" title="<?php echo JText::_('SENDINCNOTIFYTXT');?>"><?php echo JText::_('SEND_INC_NOTIFICATION');?></label></td>
			<td>
				
				<fieldset id="income_notify" class="checkboxes">
				<ul>
					<li>
					<input type="checkbox" id="income_notify0" name="income_notify[]" value="admin" <?php if(in_array('admin',$this->config->income_notify)) echo 'checked="checked"'; ?> />
					<label for="income_notify0"><?php echo JText::_( 'OWNER' ); ?></label>
					</li>
					<li>
					<input type="checkbox" id="income_notify1" name="income_notify[]" value="client" <?php if(in_array('client',$this->config->income_notify)) echo 'checked="checked"'; ?> />
					<label for="income_notify1"><?php echo JText::_( 'CLIENT' ); ?></label>
					</li>
				</ul>
			</fieldset>
			</td>
		</tr>
		
		<tr>
					<td valign="top" class="key" width="200">
					<label><?php echo JText::_( 'COM_VBIZZ_OWNER_GROUP_ID' ); ?></label>
					</td>
					<td><label><?php echo JHtml::_('access.usergroup', 'owner_group_id', $this->config->owner_group_id,'size="5"',false); ?></label></td>
				</tr>
				<tr>
					<td valign="top" class="key" width="200">
					<label><?php echo JText::_( 'COM_VBIZZ_EMPLOYEE_GROUP_ID' ); ?></label>
					</td>
					<td><label><?php echo JHtml::_('access.usergroup', 'employee_group_id', $this->config->employee_group_id,'size="5"',false); ?></label></td>
				</tr>
				<tr>
					<td valign="top" class="key" width="200">
					<label><?php echo JText::_( 'COM_VBIZZ_VENDER_GROUP_ID' ); ?></label>
					</td>
					<td><label><?php echo JHtml::_('access.usergroup', 'vender_group_id', $this->config->vender_group_id,'size="5"',false); ?></label></td>
				</tr>
				<tr>
					<td valign="top" class="key" width="200">
					<label><?php echo JText::_( 'COM_VBIZZ_CLIENT_GROUP_ID' ); ?></label>
					</td>
					<td><label><?php echo JHtml::_('access.usergroup', 'client_group_id', $this->config->client_group_id,'size="5"',false); ?></label></td>
				</tr>
		
		</tbody>
	</table>
	</div>
	
	
	<div id="config-2">
	<table class="adminform table table-striped">
	<legend style="border: medium none; margin: 0px 0px 5px;"><?php echo JText::_( 'CHANGE_TERMINOLOGY_TEXT' ); ?></legend>
		<tbody>
			
			<tr>
				<th><?php echo JText::_('TERMINOLOGY');?></th>
				<th><?php echo JText::_('PLURAL_TEXT');?></th>
				<th><?php echo JText::_('SINGULAR_TEXT');?></th>
			</tr>
			
			<tr>
				<td width="200"><label><?php echo JText::_('ITEMS');?>:</label></td>
				<td><input class="text_area" type="text" name="item_view" id="item_view" value="<?php echo $this->config->item_view;?>"/></td>
				<td><input class="text_area" type="text" name="item_view_single" id="item_view_single" value="<?php echo $this->config->item_view_single;?>"/></td>
			</tr>
			
			<tr>
				<td><label><?php echo JText::_('TRANSACTION_TYPES');?>:</label></td>
				<td><input class="text_area" type="text" name="type_view" id="type_view" value="<?php echo $this->config->type_view;?>"/></td>
				<td><input class="text_area" type="text" name="type_view_single" id="type_view_single" value="<?php echo $this->config->type_view_single;?>"/></td>
			</tr>
			
			<tr>
				<td><label><?php echo JText::_('CUSTOMERS');?>:</label></td>
				<td><input class="text_area" type="text" name="customer_view" id="customer_view" value="<?php echo $this->config->customer_view;?>"/></td>
				<td><input class="text_area" type="text" name="customer_view_single" id="customer_view_single" value="<?php echo $this->config->customer_view_single;?>"/></td>
			</tr>
			
			<tr>
				<td><label><?php echo JText::_('VENDORS');?>:</label></td>
				<td><input class="text_area" type="text" name="vendor_view" id="vendor_view" value="<?php echo $this->config->vendor_view;?>"/></td>
				<td><input class="text_area" type="text" name="vendor_view_single" id="vendor_view_single" value="<?php echo $this->config->vendor_view_single;?>"/></td>
			</tr>
			
		</tbody>
		</table>
	</div>
	
	
	<div id="config-3">
	<table class="adminform table table-striped">
		<tbody>
			
			<tr>
				<td><input type="radio" name="invoice_setting" value="1" <?php if($this->config->invoice_setting==1) echo 'checked="checked"';?> ><?php echo JText::_('INV_RAND');?></td>
			</tr>
			
			<tr>
				<td><input type="radio" name="invoice_setting" value="2" <?php if($this->config->invoice_setting==2) echo 'checked="checked"';?> ><?php echo JText::_('INV_DATE_SEQ');?></td>
			</tr>
			
			<tr>
				<td><input type="radio" name="invoice_setting" value="3" <?php if($this->config->invoice_setting==3) echo 'checked="checked"';?> ><?php echo JText::_('INPUT_OWN_INV_NO');?></td>
			</tr>
			
			<tr>
				<td><input type="radio" name="invoice_setting" value="4" <?php if($this->config->invoice_setting==4) echo 'checked="checked"';?> ><?php echo JText::_('INV_SEQ');?></td>
			</tr>
			
			<tr>
				<td><input type="radio" name="invoice_setting" value="5" <?php if($this->config->invoice_setting==5) echo 'checked="checked"';?> ><?php echo JText::_('CUST_INV_PRE_SUF');?></td>
			</tr>
			
			<tr>
				<td>
					
					<div class="custom-prefix" style="display: inline-block;">
						<label><?php echo JText::_('PREFIX');?>:</label>
						<input class="text_area" type="text" name="custom_invoice_prefix" value="<?php echo $this->config->custom_invoice_prefix;?>"/>
					</div>
					<div class="custom-seq" style="display: inline-block;">
						<label><?php echo JText::_('STARTING_SEQ');?>:</label>
						<input class="text_area" type="text" name="custom_invoice_seq" value="<?php echo $this->config->custom_invoice_seq;?>"/>
					</div>
					<div class="custom-suffix" style="display: inline-block;">
						<label><?php echo JText::_('SUFFIX');?>:</label>
						<input class="text_area" type="text" name="custom_invoice_suffix" value="<?php echo $this->config->custom_invoice_suffix;?>"/>
					</div>
				</td>
					
			</tr>
			
		</tbody>
		</table>
	</div>
	

	<div id="config-4">
	<table class="adminform table table-striped">
		<tbody> 
			<tr>
				<td width="200"><label class="hasTip" title="<?php echo JText::_('SET_CURRENCY'); ?>"><?php echo JText::_('CURRENCY_CODE');?>:</label></td>
				<td><input class="text_area" type="text" name="currency" id="currency" value="<?php echo $this->config->currency;?>"/></td>
			</tr>
			
			<tr>
				<td><label class="hasTip" title="<?php echo JText::_('DEFCOUNTRYTXT'); ?>"><?php echo JText::_('DEFAULT_COUNTRY'); ?></label>
				</td>
				<td>
					<select name="default_country" id="default_country">
					<option value=""><?php echo JText::_('SELECT_COUNTRY'); ?></option>
					<?php	for($i=0;$i<count($this->countries);$i++)	{	?>
					<option value="<?php echo $this->countries[$i]->id; ?>" <?php if($this->countries[$i]->id==$this->config->default_country) echo 'selected="selected"'; ?>> <?php echo JText::_($this->countries[$i]->country_name); ?></option>
					<?php	}	?>
					</select>
				</td>
			</tr>
			<tr><td><label class="hasTip" title="<?php echo JText::_('TIMEZONE_DESC');?>"><?php echo JText::_('TIMEZONE');?></label></td><td>
			<?php   echo VaccountHelper::getDateDefaultTimeZoneListing($this->config->timezones);
			 ?>
			
			</td></tr>
			<tr>
				<td><label class="hasTip" title="<?php echo JText::_('DEFLANGTXT');?>"><?php echo JText::_('DEF_LANG');?></label></td>
				<td>
					<select name="default_language" id="default_language">
						<option value=""><?php echo '-- '. JText::_('USE_DEFAULT').' --'; ?></option>
						<?php	for($i=0;$i<count($jlangTag);$i++)	{	?>
						<option value="<?php echo $jlangTag[$i]; ?>" <?php if($jlangTag[$i]==$this->config->default_language) echo																																																																															  				'selected="selected"'; ?>> <?php echo JText::_($activeLangs[$i]); ?> </option>
					<?php	}	?>
					</select>
				</td>
			</tr>
			
			<tr>
				<td><label class="hasTip" title="<?php echo JText::_('DATEFORMATTXT');?>"><?php echo JText::_('DATEFORMAT');?></label></td>
				<td>
					<select name="date_format">
					<option value="d/m/Y" <?php if($this->config->date_format=="d/m/Y") echo 'selected="selected"';?>><?php echo $date->format('d/m/Y'); ?></option>
					<option value="d.m.Y" <?php if($this->config->date_format=="d.m.Y") echo 'selected="selected"';?>><?php echo $date->format('d.m.Y'); ?></option>
					<option value="d-m-Y" <?php if($this->config->date_format=="d-m-Y") echo 'selected="selected"';?>><?php echo $date->format('d-m-Y'); ?></option>
					<option value="m/d/Y" <?php if($this->config->date_format=="m/d/Y") echo 'selected="selected"';?>><?php echo $date->format('m/d/Y'); ?></option>
					<option value="Y/m/d" <?php if($this->config->date_format=="Y/m/d") echo 'selected="selected"';?>><?php echo $date->format('Y/m/d'); ?></option>
					<option value="Y-m-d" <?php if($this->config->date_format=="Y-m-d") echo 'selected="selected"';?>><?php echo $date->format('Y-m-d'); ?></option>
					<option value="M d Y" <?php if($this->config->date_format=="M d Y") echo 'selected="selected"';?>><?php echo $date->format('M d Y'); ?></option>
					<option value="d M Y" <?php if($this->config->date_format=="d M Y") echo 'selected="selected"';?>><?php echo $date->format('d M Y'); ?></option>
					<option value="jS M y" <?php if($this->config->date_format=="jS M y") echo 'selected="selected"';?>><?php echo $date->format('jS M y'); ?></option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td><label class="hasTip" title="<?php echo JText::_('CURFORMTXT'); ?>"><?php echo JText::_('CURRFORMAT'); ?>:</label></td>
				
				<td>
					<select name="currency_format">
					<option value=""><?php echo JText::_('SELECT_CURR_FORMAT'); ?></option>
					<option value="1" <?php if($this->config->currency_format=="1") echo 'selected="selected"';?>>1234.56</option>
					<option value="2" <?php if($this->config->currency_format=="2") echo 'selected="selected"';?>>1,234.56</option>
					<option value="3" <?php if($this->config->currency_format=="3") echo 'selected="selected"';?>>1234,56</option>
					<option value="4" <?php if($this->config->currency_format=="4") echo 'selected="selected"';?>>1.234,56</option>
					
					</select>
				</td>
			</tr>
			
		</tbody>
	</table>
	</div>
	
	<div id="config-5">
	<table class="adminform table table-striped tb_noti">
		<tbody>
		
			<tr>
				<td>
                <input type="checkbox" id="notification0" name="notification[]" value="support" <?php if(in_array('support',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification0"><?php echo JText::_( 'SUPPORT_FORUM' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification1" name="notification[]" value="bug" <?php if(in_array('bug',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification1"><?php echo JText::_( 'BUG_TRACKER' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification2" name="notification[]" value="income" <?php if(in_array('income',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification2"><?php echo JText::_( 'INCOME' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification3" name="notification[]" value="expense" <?php if(in_array('expense',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification1"><?php echo JText::_( 'EXPENSE' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification4" name="notification[]" value="items" <?php if(in_array('items',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification4"><?php if($this->config->item_view != ""){ echo $this->config->item_view; } else {echo JText::_( 'ITEMS' );} ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification5" name="notification[]" value="stock" <?php if(in_array('stock',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification5"><?php echo JText::_( 'STOCKS' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification6" name="notification[]" value="assets" <?php if(in_array('assets',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification6"><?php echo JText::_( 'ASSETS' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification7" name="notification[]" value="projects" <?php if(in_array('projects',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification7"><?php echo JText::_( 'PROJECTS' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification8" name="notification[]" value="ptask" <?php if(in_array('ptask',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification8"><?php echo JText::_( 'TASK' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification9" name="notification[]" value="tran" <?php if(in_array('tran',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification9"><?php if($this->config->type_view != ""){ echo $this->config->type_view; } else {echo JText::_( 'TRANSACTION_TYPE' );} ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification10" name="notification[]" value="mode" <?php if(in_array('mode',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification10"><?php echo JText::_( 'TRANSACTION_MODE' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification11" name="notification[]" value="accounts" <?php if(in_array('accounts',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification11"><?php echo JText::_( 'ACCOUNTS' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification12" name="notification[]" value="banking" <?php if(in_array('banking',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification12"><?php echo JText::_( 'BANKING' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification13" name="notification[]" value="recurr" <?php if(in_array('recurr',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification13"><?php echo JText::_( 'RECURRING_TRANSACTION' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification14" name="notification[]" value="tax" <?php if(in_array('tax',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification14"><?php echo JText::_( 'TAX' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification15" name="notification[]" value="discount" <?php if(in_array('discount',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification15"><?php echo JText::_( 'DISCOUNT' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification16" name="notification[]" value="import" <?php if(in_array('import',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification16"><?php echo JText::_( 'IMPORT_EXPORT' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification17" name="notification[]" value="customer" <?php if(in_array('customer',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification17"><?php if($this->config->customer_view != ""){ echo $this->config->customer_view; } else {echo JText::_( 'CUSTOMERS' ); } ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification18" name="notification[]" value="vendor" <?php if(in_array('vendor',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification18"><?php if($this->config->vendor_view != ""){ echo $this->config->vendor_view; } else { echo JText::_( 'VENDORS' ); } ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification19" name="notification[]" value="employee" <?php if(in_array('employee',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification19"><?php echo JText::_( 'EMPLOYEE' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification20" name="notification[]" value="imtask" <?php if(in_array('imtask',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification20"><?php echo JText::_( 'IMPORT_TASK' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification21" name="notification[]" value="exptask" <?php if(in_array('exptask',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification21"><?php echo JText::_( 'EXPORT_TASK' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification22" name="notification[]" value="invoices" <?php if(in_array('invoices',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification22"><?php echo JText::_( 'INVOICES' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification23" name="notification[]" value="quotes" <?php if(in_array('quotes',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification23"><?php echo JText::_( 'QUOTATION' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="notification24" name="notification[]" value="imtask" <?php if(in_array('imtask',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification24"><?php echo JText::_( 'IMPORT_TASK' ); ?></label>
                </td>
			<tr>
                
		</tbody>
	</table>
	</div>
	<div id="config-7">
	<table class="adminform table table-striped tb_noti">
		<tbody>
		
			<tr>
				<td>
                <input type="checkbox" id="emailnotification0" name="emailnotification[]" value="support" <?php if(in_array('support',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification0"><?php echo JText::_( 'SUPPORT_FORUM' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification1" name="emailnotification[]" value="bug" <?php if(in_array('bug',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification1"><?php echo JText::_( 'BUG_TRACKER' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification2" name="emailnotification[]" value="income" <?php if(in_array('income',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification2"><?php echo JText::_( 'INCOME' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification3" name="emailnotification[]" value="expense" <?php if(in_array('expense',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification1"><?php echo JText::_( 'EXPENSE' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification4" name="emailnotification[]" value="items" <?php if(in_array('items',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification4"><?php if($this->config->item_view != ""){ echo $this->config->item_view; } else {echo JText::_( 'ITEMS' );} ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification5" name="emailnotification[]" value="stock" <?php if(in_array('stock',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification5"><?php echo JText::_( 'STOCKS' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification6" name="emailnotification[]" value="assets" <?php if(in_array('assets',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification6"><?php echo JText::_( 'ASSETS' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification7" name="emailnotification[]" value="projects" <?php if(in_array('projects',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification7"><?php echo JText::_( 'PROJECTS' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification8" name="emailnotification[]" value="ptask" <?php if(in_array('ptask',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification8"><?php echo JText::_( 'TASK' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification9" name="emailnotification[]" value="tran" <?php if(in_array('tran',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification9"><?php if($this->config->type_view != ""){ echo $this->config->type_view; } else {echo JText::_( 'TRANSACTION_TYPE' );} ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification10" name="emailnotification[]" value="mode" <?php if(in_array('mode',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification10"><?php echo JText::_( 'TRANSACTION_MODE' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification11" name="emailnotification[]" value="accounts" <?php if(in_array('accounts',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification11"><?php echo JText::_( 'ACCOUNTS' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification12" name="emailnotification[]" value="banking" <?php if(in_array('banking',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification12"><?php echo JText::_( 'BANKING' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification13" name="emailnotification[]" value="recurr" <?php if(in_array('recurr',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification13"><?php echo JText::_( 'RECURRING_TRANSACTION' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification14" name="emailnotification[]" value="tax" <?php if(in_array('tax',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification14"><?php echo JText::_( 'TAX' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification15" name="emailnotification[]" value="discount" <?php if(in_array('discount',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification15"><?php echo JText::_( 'DISCOUNT' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification16" name="emailnotification[]" value="import" <?php if(in_array('import',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification16"><?php echo JText::_( 'IMPORT_EXPORT' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification17" name="emailnotification[]" value="customer" <?php if(in_array('customer',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification17"><?php if($this->config->customer_view != ""){ echo $this->config->customer_view; } else {echo JText::_( 'CUSTOMERS' ); } ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification18" name="emailnotification[]" value="vendor" <?php if(in_array('vendor',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification18"><?php if($this->config->vendor_view != ""){ echo $this->config->vendor_view; } else { echo JText::_( 'VENDORS' ); } ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification19" name="emailnotification[]" value="employee" <?php if(in_array('employee',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification19"><?php echo JText::_( 'EMPLOYEE' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification20" name="emailnotification[]" value="imtask" <?php if(in_array('imtask',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification20"><?php echo JText::_( 'IMPORT_TASK' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification21" name="emailnotification[]" value="exptask" <?php if(in_array('exptask',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification21"><?php echo JText::_( 'EXPORT_TASK' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification22" name="emailnotification[]" value="invoices" <?php if(in_array('invoices',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification22"><?php echo JText::_( 'INVOICES' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification23" name="emailnotification[]" value="quotes" <?php if(in_array('quotes',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification23"><?php echo JText::_( 'QUOTATION' ); ?></label>
                </td>
			<tr>
			
			<tr>
				<td>
                <input type="checkbox" id="emailnotification24" name="emailnotification[]" value="imtask" <?php if(in_array('imtask',$this->config->emailnotification)) echo 'checked="checked"'; ?> />
                <label for="emailnotification24"><?php echo JText::_( 'IMPORT_TASK' ); ?></label>
                </td>
			<tr>
                
		</tbody>
	</table>
	</div>
    <div id="config-6">
	<div id="tabs">
        <ul>  
          <li><a href="#tabs-1"><?php	echo !empty($this->config->item_view)?$this->config->item_view:JText::_('ITEM');?></a></li> 
			<li><a href="#tabs-24"><?php echo JText::_('INCOME'); ?></a></li>
			<li><a href="#tabs-25"><?php echo JText::_('EXPENSE'); ?></a></li>
			<li><a href="#tabs-26"><?php echo JText::_('COM_VBIZZ_USER_ASSIGN_ALLOW'); ?></a></li>
            <li><a href="#tabs-2"><?php	echo JText::_('TYPES'); ?></a></li>
            <li><a href="#tabs-3"><?php	echo JText::_('MODE'); ?></a></li>
            <li><a href="#tabs-4"><?php	echo JText::_('ACCOUNTS'); ?></a></li>
            <li><a href="#tabs-5"><?php	echo JText::_('TAX'); ?></a></li>
            <li><a href="#tabs-6"><?php	echo JText::_('DISCOUNT'); ?></a></li>
            <li><a href="#tabs-7"><?php	echo JText::_('IMPORT_EXPORT'); ?></a></li>
            <li><a href="#tabs-8"><?php	echo JText::_('CUSTOMER'); ?></a></li>
            <li><a href="#tabs-9"><?php echo JText::_('VENDOR'); ?></a></li>
            <li><a href="#tabs-10"><?php echo JText::_('SCHEDULE_TASK'); ?></a></li>
            <li><a href="#tabs-11"><?php echo JText::_('RECURRING'); ?></a></li>
            <li><a href="#tabs-12"><?php echo JText::_('INVOICE_TEMPLATE'); ?></a></li>
            <li><a href="#tabs-13"><?php echo JText::_('PROJECTS'); ?></a></li>
			<li><a href="#tabs-14"><?php echo JText::_('PROJECT_TASK'); ?></a></li>
			<li><a href="#tabs-15"><?php echo JText::_('INVOICES'); ?></a></li>
			<li><a href="#tabs-16"><?php echo JText::_('EMPLOYEE'); ?></a></li>
			<li><a href="#tabs-17"><?php echo JText::_('EMPLOYEE_AUDIT'); ?></a></li>
			<li><a href="#tabs-18"><?php echo JText::_('QUOTATION'); ?></a></li>
			<li><a href="#tabs-19"><?php echo JText::_('SUPPORT_FORUM'); ?></a></li>
			<li><a href="#tabs-20"><?php echo JText::_('PROJECT_MILESTONE'); ?></a></li>
			<li><a href="#tabs-21"><?php echo JText::_('BUG_TRACKER'); ?></a></li>
			<li><a href="#tabs-22"><?php echo JText::_('ATTENDANCE'); ?></a></li>
			<li><a href="#tabs-23"><?php echo JText::_('WIDGET'); ?></a></li>
			<li><a href="#tabs-27"><?php echo JText::_('LEADS'); ?></a></li>
			<li><a href="#tabs-28"><?php echo JText::_('DOCUMENTS'); ?></a></li>
        </ul>
        <fieldset class="adminform vc_type">
        
        <div id="tabs-1">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'transaction_acl[access_interface][]', $this->config->transaction_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'transaction_acl[addaccess][]', $this->config->transaction_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'transaction_acl[editaccess][]', $this->config->transaction_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'transaction_acl[deleteaccess][]', $this->config->transaction_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div> 
        <div id="tabs-24">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'income_acl[access_interface][]', $this->config->income_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'income_acl[addaccess][]', $this->config->income_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'income_acl[editaccess][]', $this->config->income_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'income_acl[deleteaccess][]', $this->config->income_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
		<div id="tabs-25">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'expense_acl[access_interface][]', $this->config->expense_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'expense_acl[addaccess][]', $this->config->expense_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'expense_acl[editaccess][]', $this->config->expense_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr> 
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'expense_acl[deleteaccess][]', $this->config->expense_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
        <div id="tabs-2">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'type_acl[access_interface][]', $this->config->type_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'type_acl[addaccess][]', $this->config->type_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'type_acl[editaccess][]', $this->config->type_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'type_acl[deleteaccess][]', $this->config->type_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="tabs-3">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'mode_acl[access_interface][]', $this->config->mode_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'mode_acl[addaccess][]', $this->config->mode_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'mode_acl[editaccess][]', $this->config->mode_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'mode_acl[deleteaccess][]', $this->config->mode_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="tabs-4">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'account_acl[access_interface][]', $this->config->account_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'account_acl[addaccess][]', $this->config->account_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'account_acl[editaccess][]', $this->config->account_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'account_acl[deleteaccess][]', $this->config->account_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="tabs-5">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'tax_acl[access_interface][]', $this->config->tax_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'tax_acl[addaccess][]', $this->config->tax_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'tax_acl[editaccess][]', $this->config->tax_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'tax_acl[deleteaccess][]', $this->config->tax_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="tabs-6">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'discount_acl[access_interface][]', $this->config->discount_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'discount_acl[addaccess][]', $this->config->discount_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'discount_acl[editaccess][]', $this->config->discount_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'discount_acl[deleteaccess][]', $this->config->discount_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="tabs-7">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'import_acl[access_interface][]', $this->config->import_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'import_acl[addaccess][]', $this->config->import_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
            </table>
        </div>
        
        <div id="tabs-8">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'customer_acl[access_interface][]', $this->config->customer_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'customer_acl[addaccess][]', $this->config->customer_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'customer_acl[editaccess][]', $this->config->customer_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'customer_acl[deleteaccess][]', $this->config->customer_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="tabs-9">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'vendor_acl[access_interface][]', $this->config->vendor_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'vendor_acl[addaccess][]', $this->config->vendor_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'vendor_acl[editaccess][]', $this->config->vendor_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'vendor_acl[deleteaccess][]', $this->config->vendor_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="tabs-10">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'imp_shd_task_acl[access_interface][]', $this->config->imp_shd_task_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'imp_shd_task_acl[addaccess][]', $this->config->imp_shd_task_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'imp_shd_task_acl[editaccess][]', $this->config->imp_shd_task_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'imp_shd_task_acl[deleteaccess][]', $this->config->imp_shd_task_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="tabs-11">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'recur_acl[access_interface][]', $this->config->transaction_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'recur_acl[addaccess][]', $this->config->recur_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'recur_acl[editaccess][]', $this->config->recur_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'recur_acl[deleteaccess][]', $this->config->recur_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="tabs-12">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'etemp_acl[access_interface][]', $this->config->etemp_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'etemp_acl[addaccess][]', $this->config->etemp_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'etemp_acl[editaccess][]', $this->config->etemp_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'etemp_acl[deleteaccess][]', $this->config->etemp_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
            </table>
        </div>
        
        <div id="tabs-13">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'project_acl[access_interface][]', $this->config->project_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'project_acl[addaccess][]', $this->config->project_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'project_acl[editaccess][]', $this->config->project_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'project_acl[deleteaccess][]', $this->config->project_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
		
		<div id="tabs-14">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'project_task_acl[access_interface][]', $this->config->project_task_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'project_task_acl[addaccess][]', $this->config->project_task_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'project_task_acl[editaccess][]', $this->config->project_task_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'project_task_acl[deleteaccess][]', $this->config->project_task_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
		
		<div id="tabs-15">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'invoice_acl[access_interface][]', $this->config->invoice_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'invoice_acl[addaccess][]', $this->config->invoice_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'invoice_acl[editaccess][]', $this->config->invoice_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'invoice_acl[deleteaccess][]', $this->config->invoice_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
            </table>
        </div>
		
		<div id="tabs-16">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'employee_acl[access_interface][]', $this->config->employee_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'employee_acl[addaccess][]', $this->config->employee_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'employee_acl[editaccess][]', $this->config->employee_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'employee_acl[deleteaccess][]', $this->config->employee_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
		
		<div id="tabs-17">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'employee_manage_acl[access_interface][]', $this->config->employee_manage_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'employee_manage_acl[addaccess][]', $this->config->employee_manage_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'employee_manage_acl[editaccess][]', $this->config->employee_manage_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'employee_manage_acl[deleteaccess][]', $this->config->employee_manage_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
		
		<div id="tabs-18">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'quotes_acl[access_interface][]', $this->config->quotes_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'quotes_acl[addaccess][]', $this->config->quotes_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'quotes_acl[editaccess][]', $this->config->quotes_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'quotes_acl[deleteaccess][]', $this->config->quotes_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
            </table>
        </div>
		
		<div id="tabs-19">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'support_acl[access_interface][]', $this->config->support_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'support_acl[addaccess][]', $this->config->support_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'support_acl[editaccess][]', $this->config->support_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'support_acl[deleteaccess][]', $this->config->support_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
		
		<div id="tabs-20">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'milestone_acl[access_interface][]', $this->config->milestone_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'milestone_acl[addaccess][]', $this->config->milestone_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'milestone_acl[editaccess][]', $this->config->milestone_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'milestone_acl[deleteaccess][]', $this->config->milestone_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
		
		<div id="tabs-21">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'bug_acl[access_interface][]', $this->config->bug_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'bug_acl[addaccess][]', $this->config->bug_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'bug_acl[editaccess][]', $this->config->bug_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'bug_acl[deleteaccess][]', $this->config->bug_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
		
		<div id="tabs-22">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'attendance_acl[access_interface][]', $this->config->attendance_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'attendance_acl[addaccess][]', $this->config->attendance_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
            </table>
        </div>
		<div id="tabs-23">
        <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'widget_acl[access_interface][]', $this->config->widget_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'widget_acl[addaccess][]', $this->config->widget_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'widget_acl[editaccess][]', $this->config->widget_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'widget_acl[deleteaccess][]', $this->config->widget_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
		<div id="tabs-26">    
        <table class="table table-striped">
            	<tr>   
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'user_assign_acl[access_interface][]', $this->config->user_assign_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
            </table>
        </div>
		<div id="tabs-27">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'leads_acl[access_interface][]', $this->config->leads_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'leads_acl[addaccess][]', $this->config->leads_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'leads_acl[editaccess][]', $this->config->leads_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr> 
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'leads_acl[deleteaccess][]', $this->config->leads_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
		<div id="tabs-28">
            <table class="table table-striped">
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'document_acl[access_interface][]', $this->config->document_acl->get('access_interface') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'document_acl[addaccess][]', $this->config->document_acl->get('addaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'document_acl[editaccess][]', $this->config->document_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('UPLOAD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'document_acl[uploadaccess][]', $this->config->document_acl->get('uploadaccess') , 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                </tr>
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('DOWNLOAD'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'document_acl[downloadaccess][]', $this->config->document_acl->get('downloadaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr> 
                
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></td>
                    <td><?php echo JHtml::_('access.usergroup', 'document_acl[deleteaccess][]', $this->config->document_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>
                </tr>
            </table>
        </div>
        </fieldset>
</div>
	</div>
</div>
</div>

<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->config->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="configuration" />
</form>
</div>
</div>