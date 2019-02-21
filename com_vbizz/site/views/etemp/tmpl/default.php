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
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
//echo '<pre>';print_r($this->etemp);

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();
 $db = JFactory::getDbo();

/* */
//check acl for edit access
$edit_access = $this->config->etemp_acl->get('editaccess');

if($edit_access) {
	$editaccess = false;
	foreach($groups as $group) {
		if(in_array($group,$edit_access))
		{
			$editaccess=true;
			break;
		}
	}
} else {
	$editaccess=true;
}

$editor = JFactory::getEditor();

?>

<?php 
$js = '
		function getTmplVal(id)
		{              
			jQuery.ajax({
				type: "POST",
				dataType:"JSON",
				data: {"option":"com_vbizz", "view":"templates", "task":"TmplVal", "id":id, "tmpl":"component"},
				async: false,
				cache: false,
				
				beforeSend: function() {
				},
				
				complete: function()      {
				},
				
				success: function(data){
					if(data.result=="success"){
						
						'.$editor->setContent('keyword','data.keyword').';
						'.$editor->setContent('multi_keyword','data.multi_keyword').';
						'.$editor->setContent('quotation','data.quotation').';
						'.$editor->setContent('multi_quotation','data.multi_quotation').';
						'.$editor->setContent('venderinvoice','data.venderinvoice').';
						'.$editor->setContent('vender_multi_invoice','data.vender_multi_invoice').';
						'.$editor->setContent('vendorquotation','data.vendorquotation').';
						'.$editor->setContent('vendor_multi_quotation','data.vendor_multi_quotation').';
						SqueezeBox.close();
					}
				}
			});
		
		}';
	
	$document =  JFactory::getDocument();
	$document->addScriptDeclaration($js);
?>

<script>
jQuery(function() {
	jQuery( "#tabs" ).tabs();
});
</script>

<header class="header">
		<div class="container-title">
				<h1 class="page-title"><?php echo JText::_('INVOICE'); ?></h1>
		</div>
</header>
<div class="content_part">
<form action="index.php" method="post" name="adminForm" id="adminForm">


<div class="subhead">
	<div class="container-fluid">
        <div id="container-collapse" class="container-collapse"></div>
        <div class="row-fluid">
            <div class="span12">
                <div id="toolbar" class="btn-toolbar">
                    <?php if($editaccess) { ?>
                    <div class="btn-wrapper"  id="toolbar-apply">
                        <span onclick="Joomla.submitbutton('apply')" class="btn btn-small btn-success">
                        <span class="fa fa-check"></span> <?php echo JText::_('SAVE'); ?></span>
                    </div>
                    <?php } ?>
                    <div class="btn-wrapper"  id="toolbar-cancel">
                        <span onclick="Joomla.submitbutton('cancel')" class="btn btn-small">
                        <span class="fa fa-close"></span> <?php echo JText::_('CLOSE'); ?></span>
                    </div>
                    <div class="btn-wrapper"  id="toolbar-select"><a class="btn btn-primary modal btn-small" id="modal1" title="Select" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=templates&layout=modal&tmpl=component';?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
            <span class="hasTip" title="You want another etemp template"><i class="fa fa-check"></i> <?php echo JText::_('SELECT_TEMPLATE'); ?></span></a></div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<div class="overview">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'OVERVIEW' ); ?></legend>
	<ul>
		<li><?php	echo JText::_('NEW_ETEMP_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col101">
<fieldset class="adminform">
<legend><?php echo JText::_( 'EMAIL_TEMPLATES' ); ?></legend>

<div id="tabs">
        <ul>
		    <li><a href="#tabs-5"><?php	echo JText::_('SALE_ORDER_RECIEPT_TEMPLATE');?></a></li>
            <li><a href="#tabs-1"><?php	echo JText::_('INVOICE_TEMPLATE');?></a></li>
            <li><a href="#tabs-2"><?php	echo JText::_('QUOTATION_TEMPLATE'); ?></a></li>
			<li><a href="#tabs-3"><?php	echo JText::_('VENDOR_INVOICE_TEMPLATE');?></a></li>
			<li><a href="#tabs-4"><?php	echo JText::_('VENDOR_QUOTATION_TEMPLATE');?></a></li>
        </ul>
        
	<div id="tabs-1">
	<table class="adminform table table-striped etemp_temp" width="100%">
		<tbody> 
			<tr>
				<td width="69%" valign="top">
					<label><strong><?php echo JText::_('TEMPLATE'); ?>:</strong></label></br>
					
					<?php echo $editor->display( 'keyword', $this->etemp->keyword , '350', '300', '60', '20', false ); ?>
					
					
						<label><strong><?php echo JText::_('ITEM_TEMPLATE'); ?>:</strong></label></br>
						<?php echo $editor->display( 'multi_keyword', $this->etemp->multi_keyword , '350', '300', '60', '20', false ); ?>
					
				</td>
			
				<td width="31%" valign="top" align="right">
				<table class="adminlist">
					<tbody>
						<tr>
							<th colspan="2"> <?php echo JText::_('KEYWORD_USED_IN_EMAIL'); ?></th>
						</tr>
						<tr>
							<th class="key">{companylogo}</th>
							<td><?php echo JText::_('COMPANYLOGO'); ?></td>
						</tr>
						<tr>
							<th class="key">{companyname}</th>
							<td><?php echo JText::_('COMPANYNAME'); ?></td>
						</tr>
						<tr>
							<th class="key">{companyaddress}</th>
							<td><?php echo JText::_('CONTACTADDRESS'); ?></td>
						</tr>
						<tr>
							<th class="key">{companycity}</th>
							<td><?php echo JText::_('COMPANYCITY'); ?></td>
						</tr>
						<tr>
							<th class="key">{companystate}</th>
							<td><?php echo JText::_('COMPANYSTATE'); ?></td>
						</tr>
						<tr>
							<th class="key">{companycountry}</th>
							<td><?php echo JText::_('COMPANYCOUNTRY'); ?></td>
						</tr>
						<tr>
							<th class="key">{contactnumber}</th>
							<td><?php echo JText::_('CONTACTNUMBER'); ?></td>
						</tr>
						<tr>
							<th class="key">{contactemail}</th>
							<td><?php echo JText::_('CONTACTEMAIL'); ?></td>
						</tr>
						<tr>
							<th class="key">{name}</th>
							<td><?php echo JText::_('NAME'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{item}</th>
							<td><?php echo JText::_('ITEM'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{quantity}</th>
							<td><?php echo JText::_('QUANTITY'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{date}</th>
							<td><?php echo JText::_('DATE'); ?></td>
						</tr>
						<tr>
							<th class="key">{due_date}</th>
							<td><?php echo JText::_('DUE_DATE'); ?></td>
						</tr>
						<tr>
							<th class="key">{actual_amount}</th>
							<td><?php echo JText::_('ACTUAL_AMOUNT'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{final_amount}</th>
							<td><?php echo JText::_('FINAL_AMOUNT'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{type}</th>
							<td><?php echo JText::_('TRANSACTION_TYPE'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{mode}</th>
							<td><?php echo JText::_('TRANSACTION_MODE'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{tranid}</th>
							<td><?php echo JText::_('TRANSACTION_ID'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{groups}</th>
							<td><?php echo JText::_('GROUPS'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{comments}</th>
							<td><?php echo JText::_('DESCRIPTION'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{address}</th>
							<td><?php echo JText::_('CLIENT_ADDRESS'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{city}</th>
							<td><?php echo JText::_('CLIENT_CITY'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{state}</th>
							<td><?php echo JText::_('CLIENT_STATE'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{country}</th>
							<td><?php echo JText::_('CLIENT_COUNTRY'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{zip}</th>
							<td><?php echo JText::_('ZIP'); ?></td>
						</tr>
						
						<?php if($this->config->enable_items==1) { ?>
						<tr>
							<th class="key">{actual_total}</th>
							<td><?php echo JText::_('ACTUAL_TOTAL'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{final_total}</th>
							<td><?php echo JText::_('FINAL_TOTAL'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{tax}</th>
							<td><?php echo JText::_('TOTAL_TAX'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{discount}</th>
							<td><?php echo JText::_('TOTAL_DISCOUNT'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{tax TAXID}</th>
							<td><?php echo JText::_('APPLICABLE_TAX_NAME'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{discount DISCOUNTID}</th>
							<td><?php echo JText::_('APPLICABLE_DISCOUNT_NAME'); ?></td>
						</tr>
						<?php } ?>
						</tbody>
				</table>
				</td>
			</tr>
			
		</tbody>
	</table>
	</div>

   <div id="tabs-5">
	<table class="adminform table table-striped etemp_temp" width="100%">
		<tbody> 
			<tr>
				<td width="69%" valign="top">
					<label><strong><?php echo JText::_('TEMPLATE'); ?>:</strong></label></br>
					
					<?php echo $editor->display( 'sale_order', $this->etemp->sale_order , '350', '300', '60', '20', false ); ?>
					
					
						<label><strong><?php echo JText::_('ITEM_TEMPLATE'); ?>:</strong></label></br>
						<?php echo $editor->display( 'sale_order_multi_item', $this->etemp->sale_order_multi_item , '350', '300', '60', '20', false ); ?>
					
				</td>
			
				<td width="31%" valign="top" align="right">
				<table class="adminlist">
					<tbody>
						<tr>
							<th colspan="2"> <?php echo JText::_('KEYWORD_USED_IN_EMAIL'); ?></th>
						</tr>
						<tr>
							<th class="key">{companylogo}</th>
							<td><?php echo JText::_('COMPANYLOGO'); ?></td>
						</tr>
						<tr>
							<th class="key">{companyname}</th>
							<td><?php echo JText::_('COMPANYNAME'); ?></td>
						</tr>
						<tr>
							<th class="key">{companyaddress}</th>
							<td><?php echo JText::_('CONTACTADDRESS'); ?></td>
						</tr>
						<tr>
							<th class="key">{companycity}</th>
							<td><?php echo JText::_('COMPANYCITY'); ?></td>
						</tr>
						<tr>
							<th class="key">{companystate}</th>
							<td><?php echo JText::_('COMPANYSTATE'); ?></td>
						</tr>
						<tr>
							<th class="key">{companycountry}</th>
							<td><?php echo JText::_('COMPANYCOUNTRY'); ?></td>
						</tr>
						<tr>
							<th class="key">{contactnumber}</th>
							<td><?php echo JText::_('CONTACTNUMBER'); ?></td>
						</tr>
						<tr>
							<th class="key">{contactemail}</th>
							<td><?php echo JText::_('CONTACTEMAIL'); ?></td>
						</tr>
						<tr>
							<th class="key">{name}</th>
							<td><?php echo JText::_('NAME'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{item}</th>
							<td><?php echo JText::_('ITEM'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{quantity}</th>
							<td><?php echo JText::_('QUANTITY'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{date}</th>
							<td><?php echo JText::_('DATE'); ?></td>
						</tr>
						<tr>
							<th class="key">{due_date}</th>
							<td><?php echo JText::_('DUE_DATE'); ?></td>
						</tr>
						<tr>
							<th class="key">{actual_amount}</th>
							<td><?php echo JText::_('ACTUAL_AMOUNT'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{final_amount}</th>
							<td><?php echo JText::_('FINAL_AMOUNT'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{type}</th>
							<td><?php echo JText::_('TRANSACTION_TYPE'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{mode}</th>
							<td><?php echo JText::_('TRANSACTION_MODE'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{tranid}</th>
							<td><?php echo JText::_('TRANSACTION_ID'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{groups}</th>
							<td><?php echo JText::_('GROUPS'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{comments}</th>
							<td><?php echo JText::_('DESCRIPTION'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{address}</th>
							<td><?php echo JText::_('CLIENT_ADDRESS'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{city}</th>
							<td><?php echo JText::_('CLIENT_CITY'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{state}</th>
							<td><?php echo JText::_('CLIENT_STATE'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{country}</th>
							<td><?php echo JText::_('CLIENT_COUNTRY'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{zip}</th>
							<td><?php echo JText::_('ZIP'); ?></td>
						</tr>
						
						<?php if($this->config->enable_items==1) { ?>
						<tr>
							<th class="key">{actual_total}</th>
							<td><?php echo JText::_('ACTUAL_TOTAL'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{final_total}</th>
							<td><?php echo JText::_('FINAL_TOTAL'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{tax}</th>
							<td><?php echo JText::_('TOTAL_TAX'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{discount}</th>
							<td><?php echo JText::_('TOTAL_DISCOUNT'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{tax TAXID}</th>
							<td><?php echo JText::_('APPLICABLE_TAX_NAME'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{discount DISCOUNTID}</th>
							<td><?php echo JText::_('APPLICABLE_DISCOUNT_NAME'); ?></td>
						</tr>
						<?php } ?>
						</tbody>
				</table>
				</td>
			</tr>
			
		</tbody>
	</table>
	</div>
	<div id="tabs-2">
	<table class="adminform table table-striped etemp_temp" width="100%">
		<tbody> 
			<tr>
				<td width="69%" valign="top">
					<label><strong><?php echo JText::_('TEMPLATE'); ?>:</strong></label></br>
					
					<?php echo $editor->display( 'quotation', $this->etemp->quotation , '350', '300', '60', '20', false ) ?>
					
					<?php if($this->config->enable_items==1) { ?>
						<label><strong><?php echo JText::_('MULTI_TEMPLATE'); ?>:</strong></label></br>
						<?php echo $editor->display( 'multi_quotation', $this->etemp->multi_quotation , '350', '300', '60', '20', false ); ?>
					<?php } ?>
					
				</td>
			
				<td width="31%" valign="top" align="right">
				<table class="adminlist">
					<tbody>
						<tr>
							<th colspan="2"> <?php echo JText::_('KEYWORD_USED_IN_QUOTATION'); ?></th>
						</tr>
						<tr>
							<th class="key">{companylogo}</th>
							<td><?php echo JText::_('COMPANYLOGO'); ?></td>
						</tr>
						<tr>
							<th class="key">{companyname}</th>
							<td><?php echo JText::_('COMPANYNAME'); ?></td>
						</tr>
						<tr>
							<th class="key">{companyaddress}</th>
							<td><?php echo JText::_('CONTACTADDRESS'); ?></td>
						</tr>
						<tr>
							<th class="key">{companycity}</th>
							<td><?php echo JText::_('COMPANYCITY'); ?></td>
						</tr>
						<tr>
							<th class="key">{companystate}</th>
							<td><?php echo JText::_('COMPANYSTATE'); ?></td>
						</tr>
						<tr>
							<th class="key">{companycountry}</th>
							<td><?php echo JText::_('COMPANYCOUNTRY'); ?></td>
						</tr>
						<tr>
							<th class="key">{contactnumber}</th>
							<td><?php echo JText::_('CONTACTNUMBER'); ?></td>
						</tr>
						<tr>
							<th class="key">{contactemail}</th>
							<td><?php echo JText::_('CONTACTEMAIL'); ?></td>
						</tr>
						<tr>
							<th class="key">{name}</th>
							<td><?php echo JText::_('NAME'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{item}</th>
							<td><?php echo JText::_('ITEM'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{quantity}</th>
							<td><?php echo JText::_('QUANTITY'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{quote_date}</th>
							<td><?php echo JText::_('QUOTE_DATE'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{actual_amount}</th>
							<td><?php echo JText::_('ACTUAL_AMOUNT'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{final_amount}</th>
							<td><?php echo JText::_('FINAL_AMOUNT'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{description}</th>
							<td><?php echo JText::_('DESCRIPTION'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{customer_notes}</th>
							<td><?php echo JText::_('CUSTOMER_NOTES'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{address}</th>
							<td><?php echo JText::_('CLIENT_ADDRESS'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{city}</th>
							<td><?php echo JText::_('CLIENT_CITY'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{state}</th>
							<td><?php echo JText::_('CLIENT_STATE'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{country}</th>
							<td><?php echo JText::_('CLIENT_COUNTRY'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{zip}</th>
							<td><?php echo JText::_('ZIP'); ?></td>
						</tr>
						
						<?php if($this->config->enable_items==1) { ?>
						<tr>
							<th class="key">{actual_total}</th>
							<td><?php echo JText::_('ACTUAL_TOTAL'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{final_total}</th>
							<td><?php echo JText::_('FINAL_TOTAL'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{tax}</th>
							<td><?php echo JText::_('TOTAL_TAX'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{discount}</th>
							<td><?php echo JText::_('TOTAL_DISCOUNT'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{applicable_tax}</th>
							<td><?php echo JText::_('APPLICABLE_TAX_NAME'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{applicable_discount}</th>
							<td><?php echo JText::_('APPLICABLE_DISCOUNT_NAME'); ?></td>
						</tr>
						<?php } ?> 
						
					</tbody>
				</table>
				</td>
			</tr>
			
		</tbody>
	</table>
	</div>
	<div id="tabs-3">
	<table class="adminform table table-striped etemp_temp" width="100%">
		<tbody> 
			<tr>
				<td width="69%" valign="top">
					<label><strong><?php echo JText::_('TEMPLATE'); ?>:</strong></label></br>
					
					<?php echo $editor->display( 'venderinvoice', $this->etemp->venderinvoice , '350', '300', '60', '20', false ); ?>
					
					
						<label><strong><?php echo JText::_('ITEM_TEMPLATE'); ?>:</strong></label></br>
						<?php echo $editor->display( 'vender_multi_invoice', $this->etemp->vender_multi_invoice , '350', '300', '60', '20', false ); ?>
					
				</td>
			
				<td width="31%" valign="top" align="right">
				<table class="adminlist">
					<tbody>
						<tr>
							<th colspan="2"> <?php echo JText::_('KEYWORD_USED_IN_EMAIL'); ?></th>
						</tr>
						<tr>
							<th class="key">{companylogo}</th>
							<td><?php echo JText::_('COMPANYLOGO'); ?></td>
						</tr>
						<tr>
							<th class="key">{companyname}</th>
							<td><?php echo JText::_('COMPANYNAME'); ?></td>
						</tr>
						<tr>
							<th class="key">{companyaddress}</th>
							<td><?php echo JText::_('CONTACTADDRESS'); ?></td>
						</tr>
						<tr>
							<th class="key">{companycity}</th>
							<td><?php echo JText::_('COMPANYCITY'); ?></td>
						</tr>
						<tr>
							<th class="key">{companystate}</th>
							<td><?php echo JText::_('COMPANYSTATE'); ?></td>
						</tr>
						<tr>
							<th class="key">{companycountry}</th>
							<td><?php echo JText::_('COMPANYCOUNTRY'); ?></td>
						</tr>
						<tr>
							<th class="key">{contactnumber}</th>
							<td><?php echo JText::_('CONTACTNUMBER'); ?></td>
						</tr>
						<tr>
							<th class="key">{contactemail}</th>
							<td><?php echo JText::_('CONTACTEMAIL'); ?></td>
						</tr>
						<tr>
							<th class="key">{name}</th>
							<td><?php echo JText::_('NAME'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{item}</th>
							<td><?php echo JText::_('ITEM'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{quantity}</th>
							<td><?php echo JText::_('QUANTITY'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{date}</th>
							<td><?php echo JText::_('DATE'); ?></td>
						</tr>
						<tr>
							<th class="key">{due_date}</th>
							<td><?php echo JText::_('DUE_DATE'); ?></td>
						</tr>
						<tr>
							<th class="key">{actual_amount}</th>
							<td><?php echo JText::_('ACTUAL_AMOUNT'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{final_amount}</th>
							<td><?php echo JText::_('FINAL_AMOUNT'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{type}</th>
							<td><?php echo JText::_('TRANSACTION_TYPE'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{mode}</th>
							<td><?php echo JText::_('TRANSACTION_MODE'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{tranid}</th>
							<td><?php echo JText::_('TRANSACTION_ID'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{groups}</th>
							<td><?php echo JText::_('GROUPS'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{comments}</th>
							<td><?php echo JText::_('DESCRIPTION'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{address}</th>
							<td><?php echo JText::_('CLIENT_ADDRESS'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{city}</th>
							<td><?php echo JText::_('CLIENT_CITY'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{state}</th>
							<td><?php echo JText::_('CLIENT_STATE'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{country}</th>
							<td><?php echo JText::_('CLIENT_COUNTRY'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{zip}</th>
							<td><?php echo JText::_('ZIP'); ?></td>
						</tr>
						
						<?php if($this->config->enable_items==1) { ?>
						<tr>
							<th class="key">{actual_total}</th>
							<td><?php echo JText::_('ACTUAL_TOTAL'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{final_total}</th>
							<td><?php echo JText::_('FINAL_TOTAL'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{tax}</th>
							<td><?php echo JText::_('TOTAL_TAX'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{discount}</th>
							<td><?php echo JText::_('TOTAL_DISCOUNT'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{tax TAXID}</th>
							<td><?php echo JText::_('APPLICABLE_TAX_NAME'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{discount DISCOUNTID}</th>
							<td><?php echo JText::_('APPLICABLE_DISCOUNT_NAME'); ?></td>
						</tr>
						<?php } ?>
						</tbody>
				</table>
				</td>
			</tr>
			
		</tbody>
	</table>
	</div>
	<div id="tabs-4">
	<table class="adminform table table-striped etemp_temp" width="100%">
		<tbody> 
			<tr>
				<td width="69%" valign="top">
					<label><strong><?php echo JText::_('TEMPLATE'); ?>:</strong></label></br>
					
					<?php echo $editor->display( 'vendorquotation', $this->etemp->vendorquotation , '350', '300', '60', '20', false ) ?>
					
					<?php if($this->config->enable_items==1) { ?>
						<label><strong><?php echo JText::_('MULTI_TEMPLATE'); ?>:</strong></label></br>
						<?php echo $editor->display( 'vendor_multi_quotation', $this->etemp->vendor_multi_quotation , '350', '300', '60', '20', false ); ?>
					<?php } ?>
					
				</td>
			
				<td width="31%" valign="top" align="right">
				<table class="adminlist">
					<tbody>
						<tr>
							<th colspan="2"> <?php echo JText::_('KEYWORD_USED_IN_VENDOR_QUOTATION'); ?></th>
						</tr>
						<tr>
							<th class="key">{companylogo}</th>
							<td><?php echo JText::_('COMPANYLOGO'); ?></td>
						</tr>
						<tr>
							<th class="key">{companyname}</th>
							<td><?php echo JText::_('COMPANYNAME'); ?></td>
						</tr>
						<tr>
							<th class="key">{companyaddress}</th>
							<td><?php echo JText::_('CONTACTADDRESS'); ?></td>
						</tr>
						<tr>
							<th class="key">{companycity}</th>
							<td><?php echo JText::_('COMPANYCITY'); ?></td>
						</tr>
						<tr>
							<th class="key">{companystate}</th>
							<td><?php echo JText::_('COMPANYSTATE'); ?></td>
						</tr>
						<tr>
							<th class="key">{companycountry}</th>
							<td><?php echo JText::_('COMPANYCOUNTRY'); ?></td>
						</tr>
						<tr>
							<th class="key">{contactnumber}</th>
							<td><?php echo JText::_('CONTACTNUMBER'); ?></td>
						</tr>
						<tr>
							<th class="key">{contactemail}</th>
							<td><?php echo JText::_('CONTACTEMAIL'); ?></td>
						</tr>
						<tr>
							<th class="key">{name}</th>
							<td><?php echo JText::_('NAME'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{item}</th>
							<td><?php echo JText::_('ITEM'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{quantity}</th>
							<td><?php echo JText::_('QUANTITY'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{quote_date}</th>
							<td><?php echo JText::_('QUOTE_DATE'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{actual_amount}</th>
							<td><?php echo JText::_('ACTUAL_AMOUNT'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{final_amount}</th>
							<td><?php echo JText::_('FINAL_AMOUNT'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{description}</th>
							<td><?php echo JText::_('DESCRIPTION'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{customer_notes}</th>
							<td><?php echo JText::_('CUSTOMER_NOTES'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{address}</th>
							<td><?php echo JText::_('CLIENT_ADDRESS'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{city}</th>
							<td><?php echo JText::_('CLIENT_CITY'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{state}</th>
							<td><?php echo JText::_('CLIENT_STATE'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{country}</th>
							<td><?php echo JText::_('CLIENT_COUNTRY'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{zip}</th>
							<td><?php echo JText::_('ZIP'); ?></td>
						</tr>
						
						<?php if($this->config->enable_items==1) { ?>
						<tr>
							<th class="key">{actual_total}</th>
							<td><?php echo JText::_('ACTUAL_TOTAL'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{final_total}</th>
							<td><?php echo JText::_('FINAL_TOTAL'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{tax}</th>
							<td><?php echo JText::_('TOTAL_TAX'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{discount}</th>
							<td><?php echo JText::_('TOTAL_DISCOUNT'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{applicable_tax}</th>
							<td><?php echo JText::_('APPLICABLE_TAX_NAME'); ?></td>
						</tr>
						
						<tr>
							<th class="key">{applicable_discount}</th>
							<td><?php echo JText::_('APPLICABLE_DISCOUNT_NAME'); ?></td>
						</tr>
						<?php } ?> 
						
					</tbody>
				</table>
				</td>
			</tr>
			
		</tbody>
	</table>
	</div>
</div>

</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->etemp->id; ?>" />
<input type="hidden" name="created_by" value="<?php echo $userId; ?>">
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="etemp" />
</form>
</div>
</div>
</div>