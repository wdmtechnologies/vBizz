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
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.calendar'); 


//check joomla version
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion>='3.0') { 
	JHtml::_('formbehavior.chosen', 'select');
}
         $date = JFactory::getDate();     
		   
		  
//get joomla active languages
$activeLangs = array() ;
$jlangTag = array() ;
$language =JFactory::getLanguage();
$jLangs = $language->getKnownLanguages(JPATH_BASE);
foreach ($jLangs as $jLang) {
	$jlangTag[] = $jLang['tag'];
	$activeLangs[] = $jLang['name'];
	
}

?>


<script>

jQuery(document).ready(function()

{

    jQuery('*[rel=tooltip]').tooltip();

 

    // Turn radios into btn-group

    jQuery('.radio.btn-group label').addClass('btn');

    jQuery(".btn-group label:not(.active)").click(function()

    {

        var label = jQuery(this);

        var input = jQuery('#' + label.attr('for'));

 

        if (!input.prop('checked')) {

            label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');

            if (input.val() == ''|| input.val() == 0) {

                label.addClass('active btn-danger');

            } else {

                label.addClass('active btn-success');

            }

            input.prop('checked', true);

        }

    });

    jQuery(".btn-group input[checked=checked]").each(function()

    {

        if (jQuery(this).val() == '' || jQuery(this).val() == 0) { 

            jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-danger');

        }  else {

            jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-success');

        }

    });

               

});

jQuery(function() {
	jQuery( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
	jQuery( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
	
	
	jQuery( "#config" ).tabs({
      beforeLoad: function( event, ui ) { 
        ui.jqXHR.fail(function() {
          ui.panel.html( "<?php echo JText::_('COULDNOT_LOAD_TAB'); ?>" );
        });
      }
    });
	
	jQuery(document).ready(function(){
		<?php if($this->main_config->enable_employee){ ?>
		loadEmployee();
		<?php } ?>
		loadInvoice();
		<?php if($this->main_config->enable_yodlee){ ?>
		loadYodlee();
		<?php } ?>
	});
	
	/* jQuery(document).on('click','#config-8',function() {
		alert('Employee');
		loadEmployee();
	}); */
	
	loadEmployee = function()
	{
		jQuery.ajax(
		{
			url: "index.php",
			type: "POST",
			dataType:"json",
			data: { "option":"com_vbizz", "view":"config", "task":"loadEmployee", "tmpl":"component" },
			
			beforeSend: function() {
				jQuery("#config-8").find("span.loadingbox").show();
			},
			
			complete: function()      {
				jQuery("#config-8").find("span.loadingbox").hide();
			},
			
			success: function(data) 
			{
				if(data.result=="success"){
					jQuery( "#config-8" ).html(data.employee);
					
					jQuery('select').chosen({"disable_search_threshold":0,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
					
					jQuery('.hasTip').each(function() {
						var title = jQuery(this).attr('title');
						if (title) {
							var parts = title.split('::', 2);
							jQuery(this).data('tip:title', parts[0]);
							jQuery(this).data('tip:text', parts[1]);
						}
					});
					var JTooltips = new Tips(jQuery('.hasTip').get(), {"maxTitleChars": 50,"fixed": false});
			
				} else {
					jQuery( "#config-8" ).html("<?php echo JText::_('COULDNOT_LOAD_TAB'); ?>");
				}
			}
			
		});
		
	}
	
	loadInvoice = function()
	{
		jQuery.ajax(
		{
			url: "index.php",
			type: "POST",
			dataType:"json",
			data: { "option":"com_vbizz", "view":"config", "task":"loadInvoiceSetting", "tmpl":"component" },
			
			beforeSend: function() {
				jQuery("#config-3").find("span.loadingbox").show();
			},
			
			complete: function()      {
				jQuery("#config-3").find("span.loadingbox").hide();
			},
			
			success: function(data) 
			{
				if(data.result=="success"){
					jQuery( "#config-3" ).html(data.invoice);
					jQuery('select').chosen({"disable_search_threshold":0,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
				} else {
					jQuery( "#config-3" ).html("<?php echo JText::_('COULDNOT_LOAD_TAB'); ?>");
				}
			}
			
		});
		
	}
	
	loadYodlee = function()
	{
		jQuery.ajax(
		{
			url: "index.php",
			type: "POST",
			dataType:"json",
			data: { "option":"com_vbizz", "view":"config", "task":"loadYodleeSetting", "tmpl":"component" },
			
			beforeSend: function() {
				jQuery("#config-7").find("span.loadingbox").show();
			},
			
			complete: function()      {
				jQuery("#config-7").find("span.loadingbox").hide();
			},
			
			success: function(data) 
			{
				if(data.result=="success"){
					jQuery( "#config-7" ).html(data.invoice);
					jQuery('select').chosen({"disable_search_threshold":0,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
					
					jQuery('.hasTip').each(function() {
						var title = jQuery(this).attr('title');
						if (title) {
							var parts = title.split('::', 2);
							jQuery(this).data('tip:title', parts[0]);
							jQuery(this).data('tip:text', parts[1]);
						}
					});
					var JTooltips = new Tips(jQuery('.hasTip').get(), {"maxTitleChars": 50,"fixed": false});
					
				} else {
					jQuery( "#config-7" ).html("<?php echo JText::_('COULDNOT_LOAD_TAB'); ?>");
				}
			}
			
		});
		
	}
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
		<?php if($this->main_config->enable_items) { ?>
		if( ((form.item_view.value != "") && (form.item_view_single.value=="")) || ((form.item_view_single.value != "") && (form.item_view.value=="")) )	{
			alert("<?php echo JText::_('BOTH_TRMFIELD_REQ'); ?>");
			return false;
		}
		<?php } ?>
		if( ((form.type_view.value != "") && (form.type_view_single.value=="")) || ((form.type_view_single.value != "") && (form.type_view.value=="")) )	{
			alert("<?php echo JText::_('BOTH_TRMFIELD_REQ'); ?>");
			return false;
		}
		<?php if($this->main_config->enable_cust) { ?>
		if( ((form.customer_view.value != "") && (form.customer_view_single.value=="")) || ((form.customer_view_single.value != "") && (form.customer_view.value=="")) )	{
			alert("<?php echo JText::_('BOTH_TRMFIELD_REQ'); ?>");
			return false;
		}
		<?php } ?>
		<?php if($this->main_config->enable_vendor) { ?>
		if( ((form.vendor_view.value != "") && (form.vendor_view_single.value=="")) || ((form.vendor_view_single.value != "") && (form.vendor_view.value=="")) )	{
			alert("<?php echo JText::_('BOTH_TRMFIELD_REQ'); ?>");
			return false;
		}
		<?php } ?>
		if(form.from_email.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_SENDER_EMAIL'); ?>");
			return false;
		}
		
		if(form.from_name.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_EMAIL_SENDER_NAME'); ?>");
			return false;
		}
		<?php if($this->main_config->enable_yodlee) { ?>
		var enable_yodlee = form.enable_yodlee.value;
		
		if(enable_yodlee==1) {
			if(form.cobrandLogin.value == "")	{
				alert("<?php echo JText::_('COBRAND_LOGIN_REQ'); ?>");
				return false;
			}
			
			if(form.cobrandPassword.value == "")	{
				alert("<?php echo JText::_('COBRAND_PASSWORD_REQ'); ?>");
				return false;
			}
			
			if(form.restUrl.value == "")	{
				alert("<?php echo JText::_('REST_URL_REQ'); ?>");
				return false;
			}
			
			if(form.cob_uname.value == "")	{
				alert("<?php echo JText::_('YODLEE_LOGIN_REQ'); ?>");
				return false;
			}
			
			if(form.cob_password.value == "")	{
				alert("<?php echo JText::_('YODLEE_PASSWORD_REQ'); ?>");
				return false;
			}
		}
		<?php } ?>
		if(typeof(validateit) == 'function')	{
			
			if(!validateit())
				return false;
		}
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
</script>

<script type="text/javascript">

jQuery(document).on('click','#addemail',function() {
	
	var moreItem = jQuery("#admin-email");
	
	jQuery('<div class="adm-email"><input class="text_area" type="text" name="admin_email[]" value="" ><div class="rem_button"><a class="remNew btn" href="javascript:void();"><i class="fa fa-remove"></i></a></div></div>').appendTo(moreItem);
	return false;
	
});

jQuery(document).on('click','.remNew',function() {
	jQuery(this).parents('.adm-email').remove();
	
	return false;
});


</script>

<header class="header">
		<div class="container-title">
				<h1 class="page-title"><?php echo JText::_('CONFIGURATION'); ?></h1>
		</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=config');?>" method="post" name="adminForm" id="adminForm">

<div class="row-fluid">
<div class="span12">
	<div class="btn-toolbar" id="toolbar">
		<div class="btn-wrapper"  id="toolbar-apply">
			<span onclick="Joomla.submitbutton('apply')" class="btn btn-small btn-success">
			<span class="fa fa-check"></span> <?php echo JText::_('SAVE'); ?></span>
		</div>
		
		<div class="btn-wrapper"  id="toolbar-cancel">
			<span onclick="Joomla.submitbutton('cancel')" class="btn btn-small">
			<span class="fa fa-close"></span> <?php echo JText::_('CLOSE'); ?></span>
		</div>
	</div>
</div>
</div>

<div class="overview">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'OVERVIEW' ); ?></legend>
	<ul>
		<li><?php	echo JText::_('CONFIGURATION_BASIC_SETTINGS_OVERVIEW');  ?></li>
		<li><?php	echo JText::_('CONFIGURATION_TERMINOLOGIES_OVERVIEW');  ?></li>
		<li><?php	echo JText::_('CONFIGURATION_INVOICE_SETTING_OVERVIEW');  ?></li>		
		<li><?php	echo JText::_('CONFIGURATION_LOCALISATION_OVERVIEW');  ?></li>
		<li><?php	echo JText::_('CONFIGURATION_EMAIL_SETTING_OVERVIEW');  ?></li>
		<li><?php	echo JText::_('CONFIGURATION_ACL_OVERVIEW');  ?></li>
	</ul>
</fieldset>  
</div> 

<div class="col100">
<fieldset class="adminform">
<legend><?php echo JText::_( 'CONFIGURATION_SETTINGS' ); ?></legend>

<div id="config">
        <ul>
            <li><a href="#config-1"><?php	echo JText::_('BASIC');?></a></li>
            <li><a href="#config-2"><?php	echo JText::_('TERMINOLOGIES'); ?></a></li>
            <li><a href="#config-3"><?php	echo JText::_('INVOICE_SETTING'); ?></a></li>
			<li><a href="#config-4"><?php	echo JText::_('LOCALISATION'); ?></a></li>
			<li><a href="#config-5"><?php	echo JText::_('EMAIL'); ?></a></li>
			<li><a href="#config-6"><?php	echo JText::_('SUPPORT'); ?></a></li>
			<?php if($this->main_config->enable_yodlee){ ?>
			<li><a href="#config-7"><?php	echo JText::_('YODLEE'); ?></a></li>
			<?php } ?>
			<?php if($this->main_config->enable_employee){ ?>
			<li><a href="#config-8"><?php	echo JText::_('EMPLOYEE'); ?></a></li>
			<?php } ?>
			<li><a href="#config-9"><?php	echo JText::_('NOTIFICATION'); ?></a></li>
			<li><a href="#config-10"><?php  echo JText::_( 'ACL' );        ?></a></li>
        </ul>
       
<div id="config-1">
<table class="adminform table table-striped">
    <tbody>  
    <?php if($this->main_config->enable_items) { ?>
    <tr>
        <th><label class="hasTip" title="<?php echo JText::_('ENITEMSTXT');?>"><?php echo JText::_('ENITEMS');?></label></th>
        <td>
            <fieldset class="radio btn-group">
                 <label for="enable_items1" id="enable_items-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
                    <input type="radio" name="enable_items" id="enable_items1"value="1" <?php if($this->config->enable_items) echo 'checked="checked"'; ?> />
                 <label for="enable_items0" id="enable_items-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
                	<input type="radio" name="enable_items" id="enable_items0" value="0" <?php if(!$this->config->enable_items) echo 'checked="checked"'; ?> />
					</fieldset> 
			
			
        </td>
    </tr> 
    <?php }  ?>
	
	  <?php if($this->main_config->enable_employee) { ?>
    <tr>
        <th><label class="hasTip" title="<?php echo JText::_('ENEMPLTXT');?>"><?php echo JText::_('ENEMP');?></label></th>
        <td>
            <fieldset class="radio btn-group">
            <label for="enable_employee1" id="enable_employee-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
            <input type="radio" name="enable_employee" id="enable_employee1" value="1" <?php if($this->config->enable_employee) echo 'checked="checked"';?>/>
            <label for="enable_employee0" id="enable_employee-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
            <input type="radio" name="enable_employee" id="enable_employee0" value="0" <?php if(!$this->config->enable_employee) echo 'checked="checked"';?>/>
            </fieldset>
        </td>
    </tr>
     <?php } ?>
	
	 <?php if($this->main_config->enable_vendor) { ?>
    <tr>
        <th><label class="hasTip" title="<?php echo JText::_('ENVNDRTXT');?>"><?php echo JText::_('ENVNDR');?></label></th>
        <td>
            <fieldset class="radio btn-group">
            <label for="enable_vendor1" id="enable_vendor-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
            <input type="radio" name="enable_vendor" id="enable_vendor1" value="1" <?php if($this->config->enable_vendor) echo 'checked="checked"';?>/>
            <label for="enable_vendor0" id="enable_vendor-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
            <input type="radio" name="enable_vendor" id="enable_vendor0"value="0" <?php if(!$this->config->enable_vendor) echo 'checked="checked"';?>/>
            </fieldset>
        </td>
    </tr>
    <?php } ?>
	
	 <?php if($this->main_config->enable_cust) { ?>
    <tr>
        <th><label class="hasTip" title="<?php echo JText::_('ENCUSTTXT');?>"><?php echo JText::_('ENCUST');?></label></th>
        <td>
            <fieldset class="radio btn-group">
            <label for="enable_cust1" id="enable_cust-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
            <input type="radio" name="enable_cust" id="enable_cust1" value="1" <?php if($this->config->enable_cust) echo 'checked="checked"'; ?> />
            <label for="enable_cust0" id="enable_cust-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
            <input type="radio" name="enable_cust" id="enable_cust0"value="0" <?php if(!$this->config->enable_cust) echo 'checked="checked"'; ?> />
            </fieldset>
        </td>
    </tr>
     <?php } ?>
	<?php if($this->main_config->enable_project) { ?>
    <tr>
        <th><label class="hasTip" title="<?php echo JText::_('ENPROJTXT');?>"><?php echo JText::_('ENPROJ');?></label></th>
        <td>
            <fieldset class="radio btn-group">
            <label for="enable_project1" id="enable_project-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
            <input type="radio" name="enable_project" id="enable_project1" value="1" <?php if($this->config->enable_project) echo 'checked="checked"'; ?> />
            <label for="enable_project0" id="enable_project-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
            <input type="radio" name="enable_project" id="enable_project0"value="0" <?php if(!$this->config->enable_project) echo 'checked="checked"'; ?> />
            </fieldset>
        </td>
    </tr>
     <?php } ?>
     <?php if($this->main_config->enable_recur) { ?>
	<tr>
        <th><label class="hasTip" title="<?php echo JText::_('ENRECURTXT');?>"><?php echo JText::_('ENRECUR');?></label></th>
        <td>
            <fieldset class="radio btn-group">
            <label for="enable_recur1" id="enable_recur-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
            <input type="radio" name="enable_recur" id="enable_recur1" value="1" <?php if($this->config->enable_recur) echo 'checked="checked"'; ?> />
            <label for="enable_recur0" id="enable_recur-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
            <input type="radio" name="enable_recur" id="enable_recur0"value="0" <?php if(!$this->config->enable_recur) echo 'checked="checked"'; ?> />
            </fieldset>
        </td>
    </tr>
     <?php }  ?>
	
	<?php if($this->main_config->enable_account) { ?>
    <tr>
        <th><label class="hasTip" title="<?php echo JText::_('ENACCOUNTTXT');?>"><?php echo JText::_('ENACCOUNT');?></label></th>
        <td>
            <fieldset class="radio btn-group">
            <label for="enable_account1" id="enable_account-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
            <input type="radio" name="enable_account" id="enable_account1" value="1" <?php if($this->config->enable_account) echo 'checked="checked"';?> />
            <label for="enable_account0" id="enable_account-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
            <input type="radio" name="enable_account" id="enable_account0"value="0" <?php if(!$this->config->enable_account) echo 'checked="checked"';?> />
            </fieldset>
        </td>
    </tr>
	 <?php } ?>
	
	<?php if($this->main_config->enable_tax_discount) { ?>
	<tr>
        <th><label class="hasTip" title="<?php echo JText::_('ENTAXDISCOUNTTXT');?>"><?php echo JText::_('ENTAXDISCOUNT');?></label></th>
        <td>
            <fieldset class="radio btn-group">
            <label for="enable_tax_discount1" id="enable_tax_discount-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
            <input type="radio" name="enable_tax_discount" id="enable_tax_discount1" value="1" <?php if($this->config->enable_tax_discount) echo 'checked="checked"';?> />
            <label for="enable_tax_discount0" id="enable_tax_discount-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
            <input type="radio" name="enable_tax_discount" id="enable_tax_discount0"value="0" <?php if(!$this->config->enable_tax_discount) echo 'checked="checked"';?> />
            </fieldset>
        </td>
    </tr>
	 <?php }  ?>
	
	<?php if($this->main_config->enable_yodlee) { ?>
	<tr>
        <th><label class="hasTip" title="<?php echo JText::_('ENYODLEETXT');?>"><?php echo JText::_('INTYODLEEAPI');?></label></th>
        <td>
            <fieldset class="radio btn-group">
            <label for="enable_yodlee1" id="enable_yodlee-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
            <input type="radio" name="enable_yodlee" id="enable_yodlee1" value="1" <?php if($this->config->enable_yodlee) echo 'checked="checked"';?> />
            <label for="enable_yodlee0" id="enable_yodlee-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
            <input type="radio" name="enable_yodlee" id="enable_yodlee0"value="0" <?php if(!$this->config->enable_yodlee) echo 'checked="checked"';?> />
            </fieldset>
        </td>
    </tr>
	 <?php }  ?>
	
	<tr>
    	<th width="200"><label class="hasTip" title="<?php echo JText::_('COLUMN_LIMIT_DESC');?>"><?php echo JText::_('DASHBOARD_COLUMN');?></label></th>
    	<td><input class="text_area" type="number" name="column_limit" id="column_limit" value="<?php echo $this->config->column_limit;?>"/></td>
    </tr>
    
    <tr>
    	<th width="200"><label class="hasTip" title="<?php echo JText::_('ROW_LIMIT_DESC'); ?>"><?php echo JText::_('DASHBOARD_ROW'); ?></label></th>
    	<td><input class="text_area" type="number" step="25" name="row_limit" id="row_limit" value="<?php echo $this->config->row_limit;?>" /></td>
    </tr>
    
    
    <tr>
        <th><label class="hasTip" title="<?php echo JText::_('SETBUDGETTXT'); ?>"><?php echo JText::_('SET_BUDGET'); ?></label></th>
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
    	<th width="200"><label class="hasTip" title="<?php echo JText::_('SET_REMINDER1');?>"><?php echo JText::_('REMINDER1');?></label></th>
    	<td><input class="text_area" type="text" name="reminder1" id="reminder1" value="<?php echo $this->config->reminder1;?>"/></td>
    </tr>
    
    <tr>
    	<th width="200"><label class="hasTip" title="<?php echo JText::_('SET_REMINDER2'); ?>"><?php echo JText::_('REMINDER2'); ?></label></th>
    	<td><input class="text_area" type="text" name="reminder2" id="reminder2" value="<?php echo $this->config->reminder2;?>" /></td>
    </tr>
    
    <tr>
    	<th width="200"><label class="hasTip" title="<?php echo JText::_('SET_OVERDUE_REMINDER'); ?>"><?php echo JText::_('OVERDUE_REMINDER'); ?></label></th>
    	<td><input class="text_area" type="text" name="overdue_reminder" id="overdue_reminder" value="<?php echo $this->config->overdue_reminder;?>" /></td>
    </tr>
    
    <tr>
        <th><label class="hasTip" title="<?php echo JText::_('SENDEXPNOTIFYTXT');?>"><?php echo JText::_('SEND_EXP_NOTIFICATION');?></label></th>
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
        <th><label class="hasTip" title="<?php echo JText::_('SENDINCNOTIFYTXT');?>"><?php echo JText::_('SEND_INC_NOTIFICATION');?></label></th>
        <td>
            
            <fieldset id="income_notify" class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="income_notify0" name="income_notify[]" value="admin" <?php if(in_array('admin',$this->config->income_notify)) echo 'checked="checked"'; ?> />
                <label for="income_notify0"><?php echo JText::_( 'ADMIN' ); ?></label>
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
        <th><label class="hasTip" title="<?php echo JText::_('SETRECURCRONTXT'); ?>"><?php echo JText::_('SET_CRON_JOB_RECURR'); ?></label></th>
        <td><?php echo JURI::root(); ?>components/com_vbizz/cron/cron.php?userid=OWNERID</td>
    </tr>
    
    <tr>
        <th><label class="hasTip" title="<?php echo JText::_('SETIMPCRONTXT');?>"><?php echo JText::_('SET_CRON_JOB_IMP');?></label></th>
        <td><?php echo JURI::root(); ?>components/com_vbizz/cron/cron_imp.php?id=ID</td>
    </tr>
    
    <tr>
        <th><label class="hasTip" title="<?php echo JText::_('SETEXPCRONTXT');?>"><?php echo JText::_('SET_CRON_JOB_EXP');?></label></th>
        <td><?php echo JURI::root(); ?>components/com_vbizz/cron/cron_exp.php?id=ID</td>
    </tr>
	
	<tr>
        <th><label class="hasTip" title="<?php echo JText::_('SETYODLEECRONTXT');?>"><?php echo JText::_('SET_YODLEE_CRON');?></label></th>
        <td><?php echo JURI::root(); ?>components/com_vbizz/cron/yodlee.php?userid=OWNERID</td>
    </tr>
	
	<tr>
        <th><label class="hasTip" title="<?php echo JText::_('SETSALARYCRONTXT'); ?>"><?php echo JText::_('SET_SAL_CRON'); ?></label></th>
        <td><?php echo JURI::root(); ?>components/com_vbizz/cron/salary.php?userid=OWNERID</td>
    </tr>
    
    </tbody>
</table>
</div>
<div id="config-2">
	<legend class="cnfg_trmnlgy"><?php echo JText::_( 'CHANGE_TERMINOLOGY_TEXT' ); ?></legend>
	<table class="adminform table table-striped">
    <tbody>
	
		<tr class="cnfg_trmnlgy_hd">
            <th><?php echo JText::_('TERMINOLOGY');?></th>
            <th><?php echo JText::_('PLURAL_TEXT');?></th>
            <th><?php echo JText::_('SINGULAR_TEXT');?></th>
        </tr>
        <?php if($this->main_config->enable_items) { ?>
        <tr>
            <td width="200"><label><?php echo JText::_('ITEMS');?></label></td>
            <td><input class="text_area" type="text" name="item_view" id="item_view" value="<?php echo $this->config->item_view;?>"/></td>
            <td><input class="text_area" type="text" name="item_view_single" id="item_view_single" value="<?php echo $this->config->item_view_single;?>"/></td>
        </tr>
        <?php } ?>
		
        <tr>
            <td><label><?php echo JText::_('TRANSACTION_TYPES');?></label></td>
            <td><input class="text_area" type="text" name="type_view" id="type_view" value="<?php echo $this->config->type_view;?>"/></td>
            <td><input class="text_area" type="text" name="type_view_single" id="type_view_single" value="<?php echo $this->config->type_view_single;?>"/></td>
        </tr>
       
	
	 <?php if($this->main_config->enable_cust) { ?>
        <tr>
            <td><label><?php echo JText::_('CUSTOMERS');?></label></td>
            <td><input class="text_area" type="text" name="customer_view" id="customer_view" value="<?php echo $this->config->customer_view;?>"/></td>
            <td><input class="text_area" type="text" name="customer_view_single" id="customer_view_single" value="<?php echo $this->config->customer_view_single;?>"/></td>
        </tr>
	 <?php }  ?> 
		 
	  <?php if($this->main_config->enable_vendor) { ?>
        <tr>
            <td><strong><label><?php echo JText::_('VENDORS');?></label></td>
            <td><input class="text_area" type="text" name="vendor_view" id="vendor_view" value="<?php echo $this->config->vendor_view;?>"/></td>
            <td><input class="text_area" type="text" name="vendor_view_single" id="vendor_view_single" value="<?php echo $this->config->vendor_view_single;?>"/></td>
        </tr>
        <?php }  ?>  
		 
    </tbody>
    </table>
	
</div>

<div id="config-3">
<span class="loadingbox" style="display:none;"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/loading.gif" />' ?></span>
</div>

<div id="config-4">
	<table class="adminform table table-striped">
		<tbody> 
			<tr>
				<th width="200"><label class="hasTip" title="<?php echo JText::_('SET_CURRENCY'); ?>"><?php echo JText::_('CURRENCY_CODE');?></label></th>
				<td><input class="text_area" type="text" name="currency" id="currency" value="<?php echo $this->config->currency;?>"/></td>
			</tr>
			
			<tr>
				<th><label class="hasTip" title="<?php echo JText::_('PAYPALEMAILTXT'); ?>"><?php echo JText::_('PAYPAL_EMAIL');?></label></th>
				<td><input class="text_area" type="text" name="paypal_email" id="paypal_email" value="<?php echo $this->config->paypal_email;?>"/></td>
			</tr>
			
			<tr>
				<th><label class="hasTip" title="<?php echo JText::_('DEFCOUNTRYTXT'); ?>"><?php echo JText::_('DEFAULT_COUNTRY'); ?></label></th>
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
				<th><label class="hasTip" title="<?php echo JText::_('DEFLANGTXT');?>"><?php echo JText::_('DEF_LANG');?></label></th>
				<td>
					<select name="default_language" id="default_language">
						<option value=""><?php echo '-- '. JText::_('USE_DEFAULT').' --'; ?></option>
						<?php	for($i=0;$i<count($jlangTag);$i++)	{	?>
						<option value="<?php echo $jlangTag[$i]; ?>" <?php if($jlangTag[$i]==$this->config->default_language) echo 'selected="selected"'; ?>> <?php echo JText::_($activeLangs[$i]); ?> </option>
					<?php	}	?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label class="hasTip" title="<?php echo JText::_('DATEFORMATTXT');?>"><?php echo JText::_('DATEFORMAT');?></label></th>
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
				<th><label class="hasTip" title="<?php echo JText::_('CURFORMTXT'); ?>"><?php echo JText::_('CURRFORMAT'); ?></label></th>
				
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
	<table class="adminform table table-striped">
		<tbody> 
			<tr>
				<th width="200"><label class="hasTip" title="<?php echo JText::_('FROMEMAILTXT'); ?>"><?php echo JText::_('FROM_EMAIL');?></label></th>
				<td><input class="text_area" type="text" name="from_email" id="from_email" value="<?php echo $this->config->from_email;?>"/></td>
			</tr>
			
			<tr>
				<th width="200"><label class="hasTip" title="<?php echo JText::_('FROMNAMETXT'); ?>"><?php echo JText::_('FROM_NAME');?></label></th>
				<td><input class="text_area" type="text" name="from_name" id="from_name" value="<?php echo $this->config->from_name;?>"/></td>
			</tr>
		</tbody>
	</table>
	</div>
	
	
	<div id="config-6">
	<table class="adminform table table-striped">
		<tbody> 
			<tr>
				<th width="200"><label class="hasTip" title="<?php echo JText::_('SUBSEMAILTXT');?>"><?php echo JText::_('SEND_EMAIL_TO_SUBSCRIBER');?></label></th>
				<td>
					<fieldset class="radio btn-group">
					<label for="send_subscriber_email1" id="send_subscriber_email-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
					<input type="radio" name="send_subscriber_email" id="send_subscriber_email1" value="1" <?php if($this->config->send_subscriber_email) echo 'checked="checked"';?> />
					<label for="send_subscriber_email0" id="send_subscriber_email-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
					<input type="radio" name="send_subscriber_email" id="send_subscriber_email0"value="0" <?php if(!$this->config->send_subscriber_email) echo 'checked="checked"';?> />
					</fieldset>
				</td>
			</tr>
			
			<tr>
				<th colspan="0">
					<button id="addemail" class="btn btn-success"/><i class="fa fa-plus"></i> <?php echo JText::_('ADD_ADMIN_EMAIL'); ?></button>
				</th>
				
				<td id="admin-email">
				<?php 
					$admin_email = $this->config->admin_email;
					for($i=0; $i<count($admin_email);$i++) {
				?>
				
				<div class="adm-email">
					<input class="text_area" type="text" name="admin_email[]" value="<?php echo $admin_email[$i]; ?>" >
					<div class="rem_button"><a class="remNew btn" href="javascript:void();"><i class="fa fa-remove"></i></a></div>
				</div>
				
				<?php } ?>
				</td>
			</tr>
		</tbody>
	</table>
	</div>
	
	<div id="config-7">
	<span class="loadingbox" style="display:none;"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/loading.gif" />' ?></span>
	</div>
	
	<div id="config-8">
	<span class="loadingbox" style="display:none;"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/loading.gif" />' ?></span>
	</div>
	
	<div id="config-9">
	<table class="adminform table table-striped">
		<tbody>
		
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification0" name="notification[]" value="support" <?php if(in_array('support',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification0"><?php echo JText::_( 'SUPPORT_FORUM' ); ?></label>
				</li></ul></fieldset>
                </td>

			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification1" name="notification[]" value="bug" <?php if(in_array('bug',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification1"><?php echo JText::_( 'BUG_TRACKER' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification2" name="notification[]" value="income" <?php if(in_array('income',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification2"><?php echo JText::_( 'INCOME' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification3" name="notification[]" value="expense" <?php if(in_array('expense',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification3"><?php echo JText::_( 'EXPENSE' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification4" name="notification[]" value="items" <?php if(in_array('items',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification4"><?php if($this->config->item_view != ""){ echo $this->config->item_view; } else {echo JText::_( 'ITEMS' );} ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification5" name="notification[]" value="stock" <?php if(in_array('stock',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification5"><?php echo JText::_( 'STOCKS' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification6" name="notification[]" value="assets" <?php if(in_array('assets',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification6"><?php echo JText::_( 'ASSETS' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification7" name="notification[]" value="projects" <?php if(in_array('projects',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification7"><?php echo JText::_( 'PROJECTS' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification8" name="notification[]" value="ptask" <?php if(in_array('ptask',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification8"><?php echo JText::_( 'TASK' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification9" name="notification[]" value="tran" <?php if(in_array('tran',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification9"><?php if($this->config->type_view != ""){ echo $this->config->type_view; } else {echo JText::_( 'TRANSACTION_TYPE' );} ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification10" name="notification[]" value="mode" <?php if(in_array('mode',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification10"><?php echo JText::_( 'TRANSACTION_MODE' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification11" name="notification[]" value="accounts" <?php if(in_array('accounts',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification11"><?php echo JText::_( 'ACCOUNTS' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification12" name="notification[]" value="banking" <?php if(in_array('banking',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification12"><?php echo JText::_( 'BANKING' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification13" name="notification[]" value="recurr" <?php if(in_array('recurr',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification13"><?php echo JText::_( 'RECURRING_TRANSACTION' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification14" name="notification[]" value="tax" <?php if(in_array('tax',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification14"><?php echo JText::_( 'TAX' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification15" name="notification[]" value="discount" <?php if(in_array('discount',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification15"><?php echo JText::_( 'DISCOUNT' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification16" name="notification[]" value="import" <?php if(in_array('import',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification16"><?php echo JText::_( 'IMPORT_EXPORT' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification17" name="notification[]" value="customer" <?php if(in_array('customer',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification17"><?php if($this->config->customer_view != ""){ echo $this->config->customer_view; } else {echo JText::_( 'CUSTOMERS' ); } ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification18" name="notification[]" value="vendor" <?php if(in_array('vendor',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification18"><?php if($this->config->vendor_view != ""){ echo $this->config->vendor_view; } else { echo JText::_( 'VENDORS' ); } ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification19" name="notification[]" value="employee" <?php if(in_array('employee',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification19"><?php echo JText::_( 'EMPLOYEE' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification20" name="notification[]" value="imtask" <?php if(in_array('imtask',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification20"><?php echo JText::_( 'IMPORT_TASK' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification21" name="notification[]" value="exptask" <?php if(in_array('exptask',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification21"><?php echo JText::_( 'EXPORT_TASK' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification22" name="notification[]" value="invoices" <?php if(in_array('invoices',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification22"><?php echo JText::_( 'INVOICES' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification23" name="notification[]" value="quotes" <?php if(in_array('quotes',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification23"><?php echo JText::_( 'QUOTATION' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
			
			<tr>
				<td>
				<fieldset class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="notification24" name="notification[]" value="imtask" <?php if(in_array('imtask',$this->config->notification)) echo 'checked="checked"'; ?> />
                <label for="notification24"><?php echo JText::_( 'IMPORT_TASK' ); ?></label>
				</li></ul></fieldset>
                </td>
			<tr>
                
		</tbody>
	</table>
	</div>
	<div id="config-10">
	 <div id="tabs">
        <ul>
		   <?php 
		   $main_config = VaccountHelper::getMainConfig();
		   if($main_config->enable_items==1){
		   ?>
            <li><a href="#tabs-25"><?php	echo !empty($this->config->item_view)?$this->config->item_view:JText::_('ITEMS');?></a></li> 
		   <?php } ?>
			<li><a href="#tabs-1"><?php echo JText::_('INCOME'); ?></a></li>
			<li><a href="#tabs-2"><?php echo JText::_('EXPENSE'); ?></a></li>
            <li><a href="#tabs-3"><?php	echo JText::_('TYPES'); ?></a></li>
            <li><a href="#tabs-4"><?php	echo JText::_('MODE'); ?></a></li>
			 <?php if($main_config->enable_account==1){ ?>
            <li><a href="#tabs-5"><?php	echo JText::_('ACCOUNTS'); ?></a></li>
			 <?php } ?>
			 
            <li><a href="#tabs-6"><?php	echo JText::_('TAX'); ?></a></li>
            <li><a href="#tabs-7"><?php	echo JText::_('DISCOUNT'); ?></a></li>
            <li><a href="#tabs-8"><?php	echo JText::_('IMPORT_EXPORT'); ?></a></li>
			 <?php if($main_config->enable_cust==1){ ?>
            <li><a href="#tabs-9"><?php	echo JText::_('CUSTOMER'); ?></a></li>
			 <?php } ?>
			 <?php if($main_config->enable_vendor==1){ ?>
            <li><a href="#tabs-10"><?php echo JText::_('VENDOR'); ?></a></li>
			<?php } ?>
            <li><a href="#tabs-11"><?php echo JText::_('SCHEDULE_TASK'); ?></a></li>
            <li><a href="#tabs-12"><?php echo JText::_('RECURRING'); ?></a></li>
            <li><a href="#tabs-13"><?php echo JText::_('INVOICE_TEMPLATE'); ?></a></li>
			<?php if($this->main_config->enable_project) { ?>
            <li><a href="#tabs-14"><?php echo JText::_('PROJECTS'); ?></a></li>
			<li><a href="#tabs-15"><?php echo JText::_('PROJECT_TASK'); ?></a></li>
			<?php } ?>
			<li><a href="#tabs-16"><?php echo JText::_('INVOICES'); ?></a></li>
			<li><a href="#tabs-17"><?php echo JText::_('QUOTATION'); ?></a></li>
			<?php if($main_config->enable_employee==1){ ?>
			<li><a href="#tabs-18"><?php echo JText::_('EMPLOYEE'); ?></a></li>
			<?php } ?>
			<li><a href="#tabs-19"><?php echo JText::_('EMPLOYEE_AUDIT'); ?></a></li>
			<li><a href="#tabs-20"><?php echo JText::_('SUPPORT_FORUM'); ?></a></li>
			<li><a href="#tabs-21"><?php echo JText::_('PROJECT_MILESTONE'); ?></a></li>
			<li><a href="#tabs-22"><?php echo JText::_('BUG_TRACKER'); ?></a></li>
			<li><a href="#tabs-23"><?php echo JText::_('ATTENDANCE'); ?></a></li>
			<li><a href="#tabs-24"><?php echo JText::_('WIDGET'); ?></a></li>
			<li><a href="#tabs-26"><?php echo JText::_('LEADS'); ?></a></li>
        </ul>
		 <div class="adminform vc_type">  
		 <?php if($main_config->enable_items==1){ ?>
		 <div id="tabs-25">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('transaction_acl[access_interface][]',$this->config->transaction_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'transaction_acl_access_interface',VaccountHelper::AccessLevel('transaction_acl', 'access_interface'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php 
					 echo VaccountHelper::vbizzusergroup('transaction_acl[addaccess][]',$this->config->transaction_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'transaction_acl_addaccess',VaccountHelper::AccessLevel('transaction_acl', 'addaccess'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php
                    echo VaccountHelper::vbizzusergroup('transaction_acl[editaccess][]',$this->config->transaction_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'transaction_acl_editaccess',VaccountHelper::AccessLevel('transaction_acl', 'editaccess'));

					 ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php 
					 echo VaccountHelper::vbizzusergroup('transaction_acl[deleteaccess][]',$this->config->transaction_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'transaction_acl_deleteaccess',VaccountHelper::AccessLevel('transaction_acl', 'deleteaccess'));
					 ?>
                    </td>
                </tr>
            </table>
          </div>
		 <?php } ?>
		 <div id="tabs-1">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('income_acl[access_interface][]',$this->config->income_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'income_acl_access_interface',VaccountHelper::AccessLevel('income_acl', 'access_interface'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php 
					 echo VaccountHelper::vbizzusergroup('income_acl[addaccess][]',$this->config->income_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'income_acl_addaccess',VaccountHelper::AccessLevel('income_acl', 'addaccess'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php
                    echo VaccountHelper::vbizzusergroup('income_acl[editaccess][]',$this->config->income_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'income_acl_editaccess',VaccountHelper::AccessLevel('income_acl', 'editaccess'));

					 ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php 
					 echo VaccountHelper::vbizzusergroup('income_acl[deleteaccess][]',$this->config->income_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'income_acl_deleteaccess',VaccountHelper::AccessLevel('income_acl', 'deleteaccess'));
					 ?>
                    </td>
                </tr>
            </table>
          </div>
		  <div id="tabs-2">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php
                      echo VaccountHelper::vbizzusergroup('expense_acl[access_interface][]',$this->config->expense_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'expense_acl_access_interface',VaccountHelper::AccessLevel('expense_acl', 'access_interface'));
                       ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('expense_acl[addaccess][]',$this->config->expense_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'expense_acl_addaccess',VaccountHelper::AccessLevel('expense_acl', 'addaccess'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('expense_acl[editaccess][]',$this->config->expense_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'expense_acl_editaccess',VaccountHelper::AccessLevel('expense_acl', 'editaccess'));
					
					 ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('expense_acl[deleteaccess][]',$this->config->expense_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'expense_acl_deleteaccess',VaccountHelper::AccessLevel('expense_acl', 'deleteaccess'));
					
					 ?>
                    </td>
                </tr>
            </table>
        </div>    
        <div id="tabs-3">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('type_acl[access_interface][]',$this->config->type_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'type_acl_access_interface',VaccountHelper::AccessLevel('type_acl', 'access_interface'));
					
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('type_acl[addaccess][]',$this->config->type_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'type_acl_addaccess',VaccountHelper::AccessLevel('type_acl', 'addaccess'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('type_acl[editaccess][]',$this->config->type_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'type_acl_editaccess',VaccountHelper::AccessLevel('type_acl', 'editaccess'));
					 ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('type_acl[deleteaccess][]',$this->config->type_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'type_acl_deleteaccess',VaccountHelper::AccessLevel('type_acl', 'deleteaccess'));
					
					 ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="tabs-4">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('mode_acl[access_interface][]',$this->config->mode_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'mode_acl_access_interface',VaccountHelper::AccessLevel('mode_acl', 'access_interface'));
					
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('mode_acl[addaccess][]',$this->config->mode_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'mode_acl_addaccess',VaccountHelper::AccessLevel('mode_acl', 'addaccess'));
					
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('mode_acl[editaccess][]',$this->config->mode_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'mode_acl_editaccess',VaccountHelper::AccessLevel('mode_acl', 'editaccess'));
					
					 ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php 
					
					echo VaccountHelper::vbizzusergroup('mode_acl[deleteaccess][]',$this->config->mode_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'mode_acl_deleteaccess',VaccountHelper::AccessLevel('mode_acl', 'deleteaccess'));
					
					 ?>
                    </td>
                </tr>
            </table>
        </div>
         <?php if($main_config->enable_account==1){ ?>
        <div id="tabs-5">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('account_acl[access_interface][]',$this->config->account_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'account_acl_access_interface',VaccountHelper::AccessLevel('account_acl', 'access_interface'));
					
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('account_acl[addaccess][]',$this->config->account_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'account_acl_addaccess',VaccountHelper::AccessLevel('account_acl', 'addaccess'));
					
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('account_acl[editaccess][]',$this->config->account_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'account_acl_editaccess',VaccountHelper::AccessLevel('account_acl', 'editaccess'));
					 ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('account_acl[deleteaccess][]',$this->config->account_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'account_acl_deleteaccess',VaccountHelper::AccessLevel('account_acl', 'deleteaccess'));
					
					 ?>
                    </td>
                </tr>
            </table>
        </div>
		 <?php } ?>
        <div id="tabs-6">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('tax_acl[access_interface][]',$this->config->tax_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'tax_acl_access_interface',VaccountHelper::AccessLevel('tax_acl', 'access_interface'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('tax_acl[addaccess][]',$this->config->tax_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'tax_acl_addaccess',VaccountHelper::AccessLevel('tax_acl', 'addaccess'));
					
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('tax_acl[editaccess][]',$this->config->tax_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'tax_acl_editaccess',VaccountHelper::AccessLevel('tax_acl', 'editaccess'));
					 ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('tax_acl[deleteaccess][]',$this->config->tax_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'tax_acl_deleteaccess',VaccountHelper::AccessLevel('tax_acl', 'deleteaccess'));
					
					 ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="tabs-7">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('discount_acl[access_interface][]',$this->config->discount_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'discount_acl_access_interface',VaccountHelper::AccessLevel('discount_acl', 'access_interface'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('discount_acl[addaccess][]',$this->config->discount_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'discount_acl_addaccess',VaccountHelper::AccessLevel('discount_acl', 'addaccess'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('discount_acl[editaccess][]',$this->config->discount_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'discount_acl_editaccess',VaccountHelper::AccessLevel('discount_acl', 'editaccess'));
					 ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('discount_acl[deleteaccess][]',$this->config->discount_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'discount_acl_deleteaccess',VaccountHelper::AccessLevel('discount_acl', 'deleteaccess'));
					 ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="tabs-8">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('import_acl[access_interface][]',$this->config->import_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'import_acl_access_interface',VaccountHelper::AccessLevel('import_acl', 'access_interface'));
					
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('import_acl[addaccess][]',$this->config->import_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'import_acl_addaccess',VaccountHelper::AccessLevel('import_acl', 'addaccess'));
					 ?></td>
                </tr>
            </table>
        </div>
        <?php if($main_config->enable_cust==1){ ?>
        <div id="tabs-9">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('customer_acl[access_interface][]',$this->config->customer_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'customer_acl_access_interface',VaccountHelper::AccessLevel('customer_acl', 'access_interface'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('customer_acl[addaccess][]',$this->config->customer_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'customer_acl_addaccess',VaccountHelper::AccessLevel('customer_acl', 'addaccess'));
					
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('customer_acl[editaccess][]',$this->config->customer_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'customer_acl_editaccess',VaccountHelper::AccessLevel('customer_acl', 'editaccess'));
					 ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('customer_acl[deleteaccess][]',$this->config->customer_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'customer_acl_deleteaccess',VaccountHelper::AccessLevel('customer_acl', 'deleteaccess'));
					 ?>
                    </td>
                </tr>
            </table>
        </div>
        <?php } ?>
		<?php if($main_config->enable_vendor==1){ ?>
        <div id="tabs-10">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('vendor_acl[access_interface][]',$this->config->vendor_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'vendor_acl_access_interface',VaccountHelper::AccessLevel('vendor_acl', 'access_interface'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('vendor_acl[addaccess][]',$this->config->vendor_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'vendor_acl_addaccess',VaccountHelper::AccessLevel('vendor_acl', 'addaccess'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('vendor_acl[editaccess][]',$this->config->vendor_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'vendor_acl_editaccess',VaccountHelper::AccessLevel('vendor_acl', 'editaccess'));
					
					 ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('vendor_acl[deleteaccess][]',$this->config->vendor_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'vendor_acl_deleteaccess',VaccountHelper::AccessLevel('vendor_acl', 'deleteaccess'));
					 ?>
                    </td>
                </tr>
            </table>
        </div>
        <?php } ?>
        <div id="tabs-11">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('imp_shd_task_acl[access_interface][]',$this->config->imp_shd_task_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'imp_shd_task_acl_access_interface',VaccountHelper::AccessLevel('imp_shd_task_acl', 'access_interface'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('imp_shd_task_acl[addaccess][]',$this->config->imp_shd_task_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'imp_shd_task_acl_addaccess',VaccountHelper::AccessLevel('imp_shd_task_acl', 'addaccess'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('imp_shd_task_acl[editaccess][]',$this->config->imp_shd_task_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'imp_shd_task_acl_editaccess',VaccountHelper::AccessLevel('imp_shd_task_acl', 'editaccess'));
					 ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php 
					
					echo VaccountHelper::vbizzusergroup('imp_shd_task_acl[deleteaccess][]',$this->config->imp_shd_task_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'imp_shd_task_acl_deleteaccess',VaccountHelper::AccessLevel('imp_shd_task_acl', 'deleteaccess'));
					 ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="tabs-12">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('recur_acl[access_interface][]',$this->config->recur_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'recur_acl_access_interface',VaccountHelper::AccessLevel('recur_acl', 'access_interface'));
				 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('recur_acl[addaccess][]',$this->config->recur_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'recur_acl_addaccess',VaccountHelper::AccessLevel('recur_acl', 'addaccess'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('recur_acl[editaccess][]',$this->config->recur_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'recur_acl_editaccess',VaccountHelper::AccessLevel('recur_acl', 'editaccess'));
					
					 ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('recur_acl[deleteaccess][]',$this->config->recur_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'recur_acl_deleteaccess',VaccountHelper::AccessLevel('recur_acl', 'deleteaccess'));
					 ?>
                    </td>
                </tr>
            </table>
        </div>
                
        <div id="tabs-13">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('etemp_acl[access_interface][]',$this->config->etemp_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'etemp_acl_access_interface',VaccountHelper::AccessLevel('etemp_acl', 'access_interface'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('etemp_acl[addaccess][]',$this->config->etemp_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'etemp_acl_addaccess',VaccountHelper::AccessLevel('etemp_acl', 'addaccess'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('etemp_acl[editaccess][]',$this->config->etemp_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'etemp_acl_editaccess',VaccountHelper::AccessLevel('etemp_acl', 'editaccess'));
					
					?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('etemp_acl[deleteaccess][]',$this->config->etemp_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'etemp_acl_deleteaccess',VaccountHelper::AccessLevel('etemp_acl', 'deleteaccess'));
					 ?>
                    </td>
                </tr>
                
            </table>
        </div>
		
        <?php if($this->main_config->enable_project) { ?>
        <div id="tabs-14">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('project_acl[access_interface][]',$this->config->project_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'project_acl_access_interface',VaccountHelper::AccessLevel('project_acl', 'access_interface'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php 
					 echo VaccountHelper::vbizzusergroup('project_acl[addaccess][]',$this->config->project_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'project_acl_addaccess',VaccountHelper::AccessLevel('project_acl', 'addaccess'));
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php
                      echo VaccountHelper::vbizzusergroup('project_acl[editaccess][]',$this->config->project_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'project_acl_editaccess',VaccountHelper::AccessLevel('project_acl', 'editaccess'));
					 ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('project_acl[deleteaccess][]',$this->config->project_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'project_acl_deleteaccess',VaccountHelper::AccessLevel('project_acl', 'deleteaccess'));
					 ?>
                    </td>
                </tr>
            </table>
        </div>
		
		<div id="tabs-15">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('project_task_acl[access_interface][]',$this->config->project_task_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'project_task_acl_access_interface',VaccountHelper::AccessLevel('project_task_acl', 'access_interface'));
					
					 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('project_task_acl[addaccess][]',$this->config->project_task_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'project_task_acl_addaccess',VaccountHelper::AccessLevel('project_task_acl', 'addaccess'));
					
				 ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php 
					echo VaccountHelper::vbizzusergroup('project_task_acl[editaccess][]',$this->config->project_task_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'project_task_acl_editaccess',VaccountHelper::AccessLevel('project_task_acl', 'editaccess'));
					 ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('project_task_acl[deleteaccess][]',$this->config->project_task_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'project_task_acl_deleteaccess',VaccountHelper::AccessLevel('project_task_acl', 'deleteaccess')); ?>
                    </td>
                </tr>
            </table>
        </div>
		<?php }  ?>  
		<div id="tabs-16">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('invoice_acl[access_interface][]',$this->config->invoice_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'invoice_acl_access_interface',VaccountHelper::AccessLevel('invoice_acl', 'access_interface')); ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('invoice_acl[addaccess][]',$this->config->invoice_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'invoice_acl_addaccess',VaccountHelper::AccessLevel('invoice_acl', 'addaccess')); ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('invoice_acl[editaccess][]',$this->config->invoice_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'invoice_acl_editaccess',VaccountHelper::AccessLevel('invoice_acl', 'editaccess')); ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('invoice_acl[deleteaccess][]',$this->config->invoice_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'invoice_acl_deleteaccess',VaccountHelper::AccessLevel('invoice_acl', 'deleteaccess')); ?>
                    </td>
                </tr>
                
            </table>
        </div>
		
		<div id="tabs-17">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('quotes_acl[access_interface][]',$this->config->quotes_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'quotes_acl_access_interface',VaccountHelper::AccessLevel('quotes_acl', 'access_interface')); ?></td>
                </tr>  
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('quotes_acl[addaccess][]',$this->config->quotes_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'quotes_acl_addaccess',VaccountHelper::AccessLevel('quotes_acl', 'addaccess')); ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('quotes_acl[editaccess][]',$this->config->quotes_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'quotes_acl_editaccess',VaccountHelper::AccessLevel('quotes_acl', 'editaccess')); ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('quotes_acl[deleteaccess][]',$this->config->quotes_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'quotes_acl_deleteaccess',VaccountHelper::AccessLevel('quotes_acl', 'deleteaccess')); ?>
                    </td>
                </tr>
                
            </table>
        </div>
		<?php if($main_config->enable_employee==1){ ?>
		<div id="tabs-18">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('employee_acl[access_interface][]',$this->config->employee_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'employee_acl_access_interface',VaccountHelper::AccessLevel('employee_acl', 'access_interface')); ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('employee_acl[addaccess][]',$this->config->employee_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'employee_acl_addaccess',VaccountHelper::AccessLevel('employee_acl', 'addaccess')); ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php  echo VaccountHelper::vbizzusergroup('employee_acl[editaccess][]',$this->config->employee_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'employee_acl_editaccess',VaccountHelper::AccessLevel('employee_acl', 'editaccess')); ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php 
					
					echo VaccountHelper::vbizzusergroup('employee_acl[deleteaccess][]',$this->config->employee_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'employee_acl_deleteaccess',VaccountHelper::AccessLevel('employee_acl', 'deleteaccess')); ?>
                    </td>
                </tr>
            </table>
        </div>
		<?php } ?>
		<div id="tabs-19">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('employee_manage_acl[access_interface][]',$this->config->employee_manage_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'employee_manage_acl_access_interface',VaccountHelper::AccessLevel('employee_manage_acl', 'access_interface')); ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('employee_manage_acl[addaccess][]',$this->config->employee_manage_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'employee_manage_acl_addaccess',VaccountHelper::AccessLevel('employee_manage_acl', 'addaccess')); ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('employee_manage_acl[editaccess][]',$this->config->employee_manage_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'employee_manage_acl_editaccess',VaccountHelper::AccessLevel('employee_manage_acl', 'editaccess')); ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('employee_manage_acl[deleteaccess][]',$this->config->employee_manage_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'employee_manage_acl_deleteaccess',VaccountHelper::AccessLevel('employee_manage_acl', 'deleteaccess')); ?>
                    </td>
                </tr>
            </table>
        </div>
		
		<div id="tabs-20">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php 
					
					$enabled_element = VaccountHelper::AccessLevel('support_acl', 'access_interface'); 
					echo VaccountHelper::vbizzusergroup('support_acl[access_interface][]',$this->config->support_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'support_acl_access_interface',$enabled_element); ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('support_acl[addaccess][]',$this->config->support_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'support_acl_addaccess',VaccountHelper::AccessLevel('support_acl', 'addaccess')); ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('support_acl[editaccess][]',$this->config->support_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'support_acl_editaccess',VaccountHelper::AccessLevel('support_acl', 'editaccess')); ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('support_acl[deleteaccess][]',$this->config->support_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'support_acl_deleteaccess',VaccountHelper::AccessLevel('support_acl', 'deleteaccess')); ?>
                    </td>
                </tr>
            </table> 
        </div>
		
		<div id="tabs-21">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('milestone_acl[access_interface][]',$this->config->milestone_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'milestone_acl_access_interface',VaccountHelper::AccessLevel('milestone_acl', 'access_interface')); ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php echo VaccountHelper::vbizzusergroup('milestone_acl[addaccess][]',$this->config->milestone_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'milestone_acl_addaccess',VaccountHelper::AccessLevel('milestone_acl', 'addaccess')); ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php $enable_array = VaccountHelper::AccessLevel('milestone_acl', 'editaccess');
					echo VaccountHelper::vbizzusergroup('milestone_acl[editaccess][]',$this->config->milestone_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'milestone_acl_editaccess',$enable_array); ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php $enable_array = VaccountHelper::AccessLevel('milestone_acl', 'deleteaccess');
					echo VaccountHelper::vbizzusergroup('milestone_acl[deleteaccess][]',$this->config->milestone_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'milestone_acl_deleteaccess',$enable_array); ?>
                    </td>
                </tr>
            </table>
        </div>
		
		<div id="tabs-22">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php $enable_array = VaccountHelper::AccessLevel('bug_acl', 'access_interface');
					echo VaccountHelper::vbizzusergroup('bug_acl[access_interface][]',$this->config->bug_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'bug_acl_access_interface',$enable_array); ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php $enable_array = VaccountHelper::AccessLevel('bug_acl', 'addaccess');
					echo VaccountHelper::vbizzusergroup('bug_acl[addaccess][]',$this->config->bug_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'bug_acl_addaccess',$enable_array); ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php $enable_array = VaccountHelper::AccessLevel('bug_acl', 'editaccess');
					echo VaccountHelper::vbizzusergroup('bug_acl[editaccess][]',$this->config->bug_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'bug_acl_editaccess',$enable_array); ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php $enable_array = VaccountHelper::AccessLevel('bug_acl', 'deleteaccess');
					echo VaccountHelper::vbizzusergroup('bug_acl[deleteaccess][]',$this->config->bug_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'bug_acl_deleteaccess',$enable_array); ?>
                    </td>
                </tr>
            </table>
        </div>
		
		<div id="tabs-23">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></th>
                    <td><?php $enable_array = VaccountHelper::AccessLevel('attendance_acl', 'access_interface');
					echo VaccountHelper::vbizzusergroup('attendance_acl[access_interface][]',$this->config->attendance_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'attendance_acl_access_interface',$enable_array); ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php $enable_array = VaccountHelper::AccessLevel('attendance_acl', 'addaccess');
					echo VaccountHelper::vbizzusergroup('attendance_acl[addaccess][]',$this->config->attendance_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'attendance_acl_addaccess',$enable_array); ?></td>
                </tr>
            </table>
        </div>
        <div id="tabs-24">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php $enable_array = VaccountHelper::AccessLevel('widget_acl', 'access_interface');
					
					echo VaccountHelper::vbizzusergroup('widget_acl[access_interface][]',$this->config->widget_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'widget_acl_access_interface',$enable_array); ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php 
					
					$enable_array = VaccountHelper::AccessLevel('widget_acl', 'addaccess');
					
					echo VaccountHelper::vbizzusergroup('widget_acl[addaccess][]',$this->config->widget_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'widget_acl_addaccess',$enable_array); ?></td>
                </tr>  
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php $enable_array = VaccountHelper::AccessLevel('widget_acl', 'editaccess');
				
					echo VaccountHelper::vbizzusergroup('widget_acl[editaccess][]',$this->config->widget_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'widget_acl_editaccess',$enable_array); ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php 
                    $enable_array = VaccountHelper::AccessLevel('widget_acl', 'deleteaccess');
					
					echo VaccountHelper::vbizzusergroup('widget_acl[deleteaccess][]',$this->config->widget_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'widget_acl_deleteaccess',$enable_array);
					//echo JHtml::_('access.usergroup', 'widget_acl[deleteaccess][]', $this->config->widget_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
                    </td>  
                </tr> 
            </table>
        </div>
		<div id="tabs-26">
            <table class="table table-striped">
            	<tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('INTERFACETXT');?>"><?php echo JText::_('ACCESSINTERFACE');?></label></td>
                    <td><?php $enable_array = VaccountHelper::AccessLevel('leads_acl', 'access_interface');
					echo VaccountHelper::vbizzusergroup('leads_acl[access_interface][]',$this->config->leads_acl->get('access_interface'), 'class="multiple" multiple="multiple" size="5"', false,'leads_acl_access_interface',$enable_array); ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('ADDACLTXT'); ?>"><?php echo JText::_('ADD'); ?></label></th>
                    <td><?php $enable_array = VaccountHelper::AccessLevel('leads_acl', 'addaccess');
					echo VaccountHelper::vbizzusergroup('leads_acl[addaccess][]',$this->config->leads_acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false,'leads_acl_addaccess',$enable_array); ?></td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('EDITACLTXT'); ?>"><?php echo JText::_('EDIT'); ?></label></th>
                    <td><?php $enable_array = VaccountHelper::AccessLevel('leads_acl', 'editaccess');
					echo VaccountHelper::vbizzusergroup('leads_acl[editaccess][]',$this->config->leads_acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false,'leads_acl_editaccess',$enable_array); ?>
                    </td>
                </tr>
                
                <tr>
                    <th class="key"><label class="hasTip" title="<?php echo JText::_('DELETEACLTXT'); ?>"><?php echo JText::_('DELETE'); ?></label></th>
                    <td><?php $enable_array = VaccountHelper::AccessLevel('leads_acl', 'deleteaccess');
					echo VaccountHelper::vbizzusergroup('leads_acl[deleteaccess][]',$this->config->leads_acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false,'leads_acl_deleteaccess',$enable_array); ?>
                    </td>
                </tr>
            </table>
        </div>
		</div>
      </div>
	</div>  
	
</div>
</fieldset>
</div>

<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->config->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="config" />
</form>
</div>
</div>
</div>