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
JHTML::_('behavior.framework');
JHTML::_('behavior.calendar'); 
JHTML::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.formvalidation');
$input = JFactory::getApplication()->input;
// Checking if loaded via index.php or component.php
$tmpl = $input->getCmd('tmpl', '');

$db = JFactory::getDbo();
 $version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);   
if($this->income->eid) {
	$query = 'SELECT name from #__vbizz_users where userid='.$this->income->eid;
	$db->setQuery( $query );
	$customer = $db->loadResult();
} else {
	$customer='';
}    

if(isset($this->income->employee) && $this->income->employee) {
	$query = 'SELECT name from #__vbizz_users where userid='.$this->income->employee;
	$db->setQuery( $query );
	$employee = $db->loadResult();
} else {  
	$employee='';
}
    $invoiceid = '';
	if($this->income->id){
		$query = 'SELECT id from #__vbizz_invoices where `transaction_id`='.$this->income->id;
	$db->setQuery( $query );
	$invoiceid = $db->loadResult();
	}
	
$pdf_title = $str=preg_replace('/\s+/', '', $this->income->title);
$pdf_title = strtolower($pdf_title);

$document = JFactory::getDocument(); 

if($tmpl)
{
	$document->addStyleSheet(JURI::root().'components/com_vbizz/assets/css/vbizz.css');

}
 
$document->addScript(JUri::root(true).'/components/com_vbizz/assets/js/account.js');
$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->income_acl->get('addaccess');
$edit_access = $this->config->income_acl->get('editaccess');
$delete_access = $this->config->income_acl->get('deleteaccess');

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
	$addaccess=false;
}

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
	$editaccess=false;
}

if($delete_access) {
	$deleteaccess = false;
	foreach($groups as $group) {
		if(in_array($group,$delete_access))
		{
			$deleteaccess=true;
			break;
		}
	}
} else {
	$deleteaccess=false;
}


$html =$dhtml= '';
foreach($this->tax as $row)
{
	$html .='<option value='.$row->id.'>'.$row->tax_name.'</option>';
}
//$html .='</select>';

//$dhtml = '<select class=discount name=discount[] multiple >';
foreach($this->discount as $row)
{
	$dhtml .='<option value='.$row->id.'>'.$row->discount_name.'</option>';
}
//$dhtml .='</select>';

//custom-item
$newcustom_tax = '';
$newcustom_discount = '';

$custHtml = '<select class=tax name=custom_tax[] multiple >';
foreach($this->tax as $row)
{
	$custHtml .='<option value='.$row->id.'>'.$row->tax_name.'</option>';
	$newcustom_tax .='<option value='.$row->id.'>'.$row->tax_name.'</option>';
}
$custHtml .='</select>';

$custDHtml = '<select class=discount name=custom_discount[] multiple >';
foreach($this->discount as $row)
{
	$custDHtml .='<option value='.$row->id.'>'.$row->discount_name.'</option>';
	$newcustom_discount .='<option value='.$row->id.'>'.$row->discount_name.'</option>';
}
$custDHtml .='</select>';

if($this->config->enable_tax_discount==1){
	
	$tax_disc = '<div class=tax-block><span class=tax_label>'.JText::_('TAX').': </span><span class=sel-tax><select class=tax name=tax[] multiple >'.$html.'</select></span></div><div class=discount-block><span class=discount_label>'.JText::_('DISCOUNT').': </span><span class=sel-dis><select class=discount name=discount[] multiple >'.$dhtml.'<select></span></div>';
	
	$custom_tax_disc = '<div class=custom-tax-block><span class=tax_label>'.JText::_('TAX').': </span><span class=sel-tax>'.$custHtml.'</span></div><div class=custom-discount-block><span class=discount_label>'.JText::_('DISCOUNT').': </span><span class=sel-dis>'.$custDHtml.'</span></div>';
	
} else {
	$tax_disc = '';
	$custom_tax_disc = '';
}  

 ?>
 
<?php 
$t_palace = VaccountHelper::getThousandPlace();
$d_palace = VaccountHelper::getDecimalPlace();
$js = '';
if(empty($t_palace)) {
$js .=	'var thousand_formating = \'""\'';
}
else{ 
$js .= ' var thousand_formating = String('.VaccountHelper::getThousandPlace().');';	
}
if(empty($d_palace))
{
	$js .= 'var decimal_formating = \'""\'';    
}
else 
{
	$js .= 'var decimal_formating = String('.VaccountHelper::getDecimalPlace().');';
}
$js .= ' 
		function getItemVal(id)
		{              
		
			jQuery.ajax(
			{
				url: "",
				type: "POST",
				dataType:"json",
				data: {"option":"com_vbizz", "view":"income", "task":"getItemVal", "tmpl":"component", "id":id},
				
				success: function(data) 
				{
					if(data.result=="success"){
						
						if(jQuery("#"+data.itemid).length>0)
						{
							alert("'.JText::_('ALREADY_ADDED').'");
							SqueezeBox.close();
							return false;
						}
						
						
						if(jQuery("input[name=actual_amount]").val()=="")
						{
							var amt=0; 
						}else {
							var amt = accounting.unformat(jQuery("input[name=actual_amount]").val(), decimal_formating);
						}
						var new_amt = (parseFloat(amt)) + (parseFloat(data.amount));
						var new_quantity = 1;
						if(jQuery("input[name=quantity]").val()!="")
						{
							new_quantity= new_quantity+parseFloat(accounting.unformat(jQuery("input[name=quantity]").val(),decimal_formating));
						}
						jQuery("input[name=quantity]").val(accounting.formatNumber(new_quantity,2,thousand_formating,decimal_formating));
						jQuery("input[name=actual_amount]").val(accounting.formatNumber(new_amt,2,thousand_formating, decimal_formating));
						jQuery("input[name=actual_amount]").attr("readonly","readonly")
						 var ii = jQuery("div.multi-item").length;
						var moreItem = jQuery("#more-item"); 
						data.amount = accounting.formatNumber(data.amount,2,thousand_formating, decimal_formating);
						var quant = accounting.formatNumber(1,2,thousand_formating, decimal_formating);
						var newhtml = "<div class=multi-item><div class=title-block><span class=item_title>'.JText::_('TITLE').': </span><span class=item_value>"+data.itemtitle+ "</span></div><div class=amount-block><span class=item_amounts>'.JText::_('AMOUNT').': </span><span class=item_amount_value><input class=item_amount type=text autocomplete=off name=\'item_amount[]\' value="+data.amount+" ></span></div><div class=quantity-block><span class=item_quantity>'.JText::_('QUANTITY').': </span><span class=item_quantity_value><input class=item_quantity type=text autocomplete=off name=item_quantity[] value="+quant+"><span style=color:#FF0000;>'.JText::_('AVAILABLE_QUANTITY').': "+data.stock+"</span></span></div>";';
						if($this->config->enable_tax_discount==1)
						{     
		                 $js .= ' newhtml +="<div class=\"tax-block\"><span class=\"tax_label\">'.JText::_("TAX").': </span><span class=\"sel-tax\"><select class=\"tax\" name=\"tax["+data.itemid+"][]\" id=\"tax"+ii+"\"  multiple>'.$html.'</select></span></div><div class=\"discount-block\"><span class=\"discount_label\">'.JText::_('DISCOUNT').': </span><span class=\"sel-dis\"><select class=\"discount\" name=\"discount["+data.itemid+"][]\" id=\"discount"+ii+"\"  multiple>'.  $dhtml.'</select></span></div>";';         
		               }            
		        $js .= ' newhtml +="<div class=item_button><a class=remNew href=javascript:void(0) id=item_setting><i class=\"fa fa-remove\"></i></a></div><div id="+data.itemid+"></div><input type=hidden class=item_id name=item_id[] value="+data.itemid+" /></div>";
						jQuery(newhtml).appendTo(moreItem);
						jQuery("select").chosen({"disable_search_threshold":0,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
						
						jQuery(".remNew").addClass("btn");
						
						
					}
					SqueezeBox.close();
				}
				
			});
		
		}';
		
		$jscust = '
		function getCustVal(id,name)
		{              
		
			var old_id = document.getElementById("eid").value;
			if (old_id != id) {
				document.getElementById("eid").value = id;
				document.getElementById("cust").value = name;
				document.getElementById("cust").className = document.getElementById("cust").className.replace(" invalid" , "");
				
			}
			SqueezeBox.close(); 
		
		}
		function getEmpVal(id,name)
		{              
		
			var old_id = document.getElementById("employee").value;
			if (old_id != id) {
				document.getElementById("employee").value = id;
				document.getElementById("emp_id").value = name;
				document.getElementById("emp_id").className = document.getElementById("emp_id").className.replace(" invalid" , "");
				
			}
			SqueezeBox.close();
		
		}';
	
	$document =  JFactory::getDocument();
	$document->addScriptDeclaration($js);
	$document->addScriptDeclaration($jscust);     
	
 ?>
 
<script type="text/javascript">


jQuery(function() {
	
	jQuery('select[name=tid]').change(function(){
		var tid = this.value;
		jQuery("#modal1").attr("href", "<?php echo JURI::root().'index.php?option=com_vbizz&view=items&layout=modal&tmpl=component&filter_type='?>"+tid);
	});
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
	window.addEvent('domready', function(){
		document.formvalidator.setHandler('datevalidate', function(value) {
		var timestamp=Date.parse(value);
       
		if (isNaN(timestamp)==true)
		{
		return false;

		}
		else
		{
			return true;
		}
		
		});
	});
});

jQuery(document).on('change','input.item_quantity',function() {
	jQuery(this).val(accounting.formatNumber(accounting.unformat(jQuery(this).val(),decimal_formating),2, thousand_formating, decimal_formating));
	var customAmt = 0;
	var customQnt = 0;
	jQuery("input.custom_amount_val").each(function(){
		var itQt = jQuery(this).parents('.custom-line').find('input.custom_quantity_val').val();
		itQt = accounting.unformat(itQt, decimal_formating);
		 if(itQt==0 || itQt=="") {
			itQt = 0;
		} 
		var c_amount = accounting.unformat(jQuery(this).val(), decimal_formating);
		customAmt += (parseFloat(c_amount)) * (parseFloat(itQt));
		customQnt += parseFloat(itQt);
	});
	
	
	var quantity = customQnt;
	var newItmAmt = customAmt;
	jQuery("#more-item").find('input.item_quantity').each(function(){
		
		var itmQty = accounting.unformat(jQuery(this).val(),decimal_formating);
		if(parseFloat(itmQty)<0) {
			itmQty = 0;
		}
		var allAmount = accounting.unformat(jQuery(this).parents('.multi-item').find('input.item_amount').val(),decimal_formating);
		//if(parseFloat(allAmount)>0)
		newItmAmt += (parseFloat(allAmount)) * (parseInt(itmQty));
		
		if (itmQty > 0) {
			quantity += (itmQty);
		}
	});
  
	jQuery('input[name="actual_amount"]').val(accounting.formatNumber(newItmAmt,2,thousand_formating,decimal_formating));
	jQuery('input[name="quantity"]').val(accounting.formatNumber(quantity,2, thousand_formating, decimal_formating));
	
});

jQuery(document).on('click','.remNew',function() {
	var section = jQuery(this).attr('id')=='item_setting'?'multi-item':'custom-line';
	removeItemSection(this, section);    
	
});
function removeItemSection(removeSectionItems,section)
	{
	var qty = accounting.unformat(jQuery('input[name="quantity"]').val(),decimal_formating);
	var inputname = section=='item'?'item_quantity':'custom_quantity_val';
	var inputamount = section=='item'?'item_amount':'custom_amount_val';
	var section_name = section=='item'?'multi-item':'custom-line';
	var itemQty = accounting.unformat(jQuery(removeSectionItems).parents('.'+section).find('input.'+inputname).val(),decimal_formating);
	
	
	var new_quantity = (parseFloat(qty)) - (parseFloat(itemQty));
	jQuery('input[name="quantity"]').val(accounting.formatNumber(new_quantity,2,thousand_formating,decimal_formating));
	
	
	var amount = accounting.unformat(jQuery('input[name="actual_amount"]').val(),decimal_formating);
	var itemAmt = accounting.unformat(jQuery(removeSectionItems).parents('.'+section).find('input.'+inputamount).val(),decimal_formating);
	
	var new_amount = (parseFloat(amount)) - (parseFloat(itemAmt));
	jQuery('input[name="actual_amount"]').val(accounting.formatNumber(new_amount,2, thousand_formating, decimal_formating));
	
	
	
	jQuery(removeSectionItems).parents('.'+section).remove();  
	
	}

jQuery(document).on('click','.remove',function() {
	
	var itemid = jQuery(this).attr('itid');
	var transaction_id = '<?php echo $this->income->id; ?>';
	var that=this;
	
	jQuery.ajax(
	{
		url: "",
		type: "POST",
		dataType:"json",
		data: {"option":"com_vbizz", "view":"income", "task":"removeItem", "tmpl":"component", "itemid":itemid, "transaction_id":transaction_id},
		
		beforeSend: function() {
			jQuery(that).find("span.loadingbox").show();
		},
		
		complete: function()      {
			jQuery(that).find("span.loadingbox").hide();
		},
		
		success: function(data) 
		{
			if(data.result=="success"){
				
				var qty = jQuery('input[name="quantity"]').val();
				var itemQty = jQuery(that).parent().parent().find('input.item_quantity').val();
				var new_qauntity = (parseInt(qty)) - (parseInt(itemQty));
				jQuery('input[name="quantity"]').val(new_qauntity);
				
				if((jQuery('input[name="quantity"]').val()==0) || (jQuery('input[name="quantity"]').val()=="NaN") || (jQuery('input[name="quantity"]').val()==""))
				{
					jQuery('input[name="quantity"]').val('');
				}
				
				var amount = jQuery('input[name="actual_amount"]').val();
				var itemAmt = jQuery(that).parent().parent().find('span.item_amount_value').text();
				var newItAmt = (parseFloat(itemAmt)) * (parseInt(itemQty));
				var new_amount = (parseFloat(amount)) - (parseFloat(newItAmt));
				jQuery('input[name="actual_amount"]').val(new_amount);
				
				if((jQuery('input[name="actual_amount"]').val()==0)||(jQuery('input[name="actual_amount"]').val()=="NaN")||(jQuery('input[name="actual_amount"]').val()==""))
				{
					jQuery('input[name="actual_amount"]').val('')
				}
				
				jQuery(that).parents('.multi-item').remove();
				//jQuery(that).remove ();
				
			}
		}
		
	});
	
});


sumbitIframe = function()
{
	Joomla.submitbutton('saveIframe');
	//window.parent.SqueezeBox.close();
}
	
Joomla.submitbutton = function(task) {    
	if (task == 'cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		var form = document.adminForm;
	
		var valids = document.formvalidator.isValid(document.id('adminForm'));
		if(parseInt(jQuery('div.multi-item').length) == 0 && parseInt(jQuery('div.custom-line').length) == 0)	{
			 jQuery('html, body').animate({
                scrollTop:  jQuery('td#custom-item').offset().top
            }, 1500);
			alert("<?php echo JText::_('PLZ_ENTER_ITEMS_ALERT'); ?>");
			return false;
			
			
		}
		
		if(valids)
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}

jQuery(function() {
	
	jQuery(document).on('click','#addcustom',function() {
		
		var i = jQuery('div.custom-line').length;	
		var customItem = jQuery("#custom-item");
		 
		var newhtml = '<div class="custom-line"><div class="custom-title-block"><span class="custom_title"><label id="custom_title-msg" for="custom_title'+i+'" class="hasTip" title="<?php echo JText::_('TITLE'); ?>"><?php echo JText::_('TITLE'); ?>: </label></span><span class="custom_value"><input type="text" class="item_title_value required" name="custom_title[]" id="custom_title'+i+'" value="" /></span></div><div class="custom-amount-block"><span class="custom_amount"><label id="custom_amount-msg" for="custom_amount'+i+'" class="hasTip" title="<?php echo JText::_('AMOUNT'); ?>"><?php echo JText::_('AMOUNT'); ?>: </label></span><span class="custom_amount_value"><input type="text" autocomplete="off" class="custom_amount_val required" id="custom_amount'+i+'" name="custom_amount[]" value="" /><?php echo ' '.$this->config->currency; ?></span></div><div class="custom-quantity-block"><span class="custom_quantity"><label id="custom_quantity-msg" for="custom_quantity'+i+'" class="hasTip" title="<?php echo JText::_('AMOUNT'); ?>"><?php echo JText::_('QUANTITY'); ?>: </label></span><span class="custom_quantity_value"><input type="text"  autocomplete="off" class="custom_quantity_val required" name="custom_quantity[]" id="custom_quantity'+i+'" value="" /></span></div>';  
        <?php  if($this->config->enable_tax_discount==1){ ?>
		newhtml += '<div class="custom-tax-block"><span class="tax_label"><?php echo JText::_("TAX");?>: </span><span class=sel-tax><select class="tax" name="custom_tax['+i+'][]" id="custom_tax'+i+'"  multiple><?php echo $newcustom_tax;?></select></span></div><div class="custom-discount-block"><span class="discount_label"><?php echo JText::_('DISCOUNT');?>: </span><span class=sel-dis><select class="discount" name="custom_discount['+i+'][]" id="custom_discount'+i+'"  multiple><?php echo $newcustom_discount;?></select></span></div>';
		<?php } ?>    		
		
		newhtml += '<div class="custom_button"><a class="remNew btn" href="javascript:void(0);" id="custom-setting"><i class="fa fa-remove"></i> </a></div><div id="cust_'+i+'"></div></div>';
		
		jQuery(newhtml).appendTo(customItem);
		
		jQuery("select").chosen({"disable_search_threshold":0,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
	});     
	
	jQuery(document).on('change','input.custom_amount_val',function() {
		
		jQuery(this).val(accounting.formatNumber(accounting.unformat(jQuery(this).val(),decimal_formating),2, thousand_formating, decimal_formating));
		var itemAmt = 0;
		var itemQnt = 0;
		jQuery("#more-item").find('input.item_quantity').each(function(){
			
			var itQt = parseFloat(accounting.unformat(jQuery(this).val(),decimal_formating));
			
			
			if(itQt==0 || itQt=="" ) {
				itQt = 0;
			}
			var it_amount = parseFloat(accounting.unformat(jQuery(this).parents('.more-item').find('input.item_amount').val(),decimal_formating));
			if(it_amount>0)
			itemAmt += (it_amount * itQt); 
		
			itemQnt += itQt;
		});
		
		
		
		
		var totalamount = itemAmt; 
		
		jQuery("#custom-item").find('input.custom_amount_val').each(function(){
			var allQt = jQuery(this).parents('.custom-line').find('input.custom_quantity_val').val(); 
			allQt = accounting.unformat(allQt,decimal_formating); 
			if(allQt==0 || allQt=="" ) {
				allQt = 0;
			} 
			var new_amount = accounting.unformat(jQuery(this).val(),decimal_formating);
			if (new_amount != 0) { 
				totalamount += (parseFloat(new_amount)*parseFloat(allQt));
			}
			itemQnt += allQt;   
		});
	  
	  jQuery('input[name="actual_amount"]').val(accounting.formatNumber(totalamount,2, thousand_formating, decimal_formating));
	  jQuery('input[name="quantity"]').val(accounting.formatNumber(itemQnt,2, thousand_formating, decimal_formating));
	  return false;
	});
	
	
	jQuery(document).on('change','input.custom_quantity_val',function() {  
		
		jQuery(this).val(accounting.formatNumber(accounting.unformat(jQuery(this).val(),decimal_formating), 2 , thousand_formating ,decimal_formating));
		var itemAmt = 0;
		var itemQty = 0;
		jQuery("#more-item").find('input.item_quantity').each(function(){
			var itQt =  parseInt(accounting.unformat(jQuery(this).val(),decimal_formating));
			
			if(itQt==0|| itQt=="") {
				itQt = 0;
			}
			var it_amount = accounting.unformat(parseFloat(jQuery(this).parents('.multi-item').find('input.item_amount').val()),decimal_formating);
			if(it_amount>0)
			itemAmt += (it_amount*itQt);
		
			itemQty += itQt;
		});
		
		var totalamount = itemAmt;
		
		var quantity = itemQty;  
		var newItmAmt = totalamount;
		jQuery("#custom-item").find('input.custom_quantity_val').each(function(){
			
			var itmQty = parseFloat(accounting.unformat(jQuery(this).val(),decimal_formating));
			 if(itmQty<0) {
				itmQty = 0;
			} 
			
		 
			var allAmount = parseFloat(accounting.unformat(jQuery(this).parents('.custom-line').find('input.custom_amount_val').val(),decimal_formating));
			
			if (allAmount>0) {
				newItmAmt += (allAmount * itmQty);
			}
			
			quantity += (itmQty);
		});  
	   
		jQuery('input[name="actual_amount"]').val(accounting.formatNumber(newItmAmt,2, thousand_formating, decimal_formating));
		jQuery('input[name="quantity"]').val(accounting.formatNumber(quantity,2, thousand_formating, decimal_formating));
		return false;
	});
	
	jQuery(document).on('click','.remCust',function() {
		
		var qty = parseFloat(acounting.unformat(jQuery('input[name="quantity"]').val(),decimal_formating));
		var itemQty = parseFloat(acounting.unformat(jQuery(this).parents('.custom-line').find('input.custom_quantity_val').val(),decimal_formating));
		if( itemQty<0) {
			itemQty = 0;
		}
		var new_qauntity = qty - itemQty;
		jQuery('input[name="quantity"]').val(acounting.formatNumber(new_qauntity,2, thousand_formating,decimal_formating));
		
		
		
		var amount = parseFloat(acounting.unformat(jQuery('input[name="actual_amount"]').val(),decimal_formating));
		var itemAmt = parseFloat(acounting.unformat(jQuery(this).parents('.custom-line').find('input.custom_amount_val').val(),decimal_formating));
		
		var new_amount = amount - itemAmt;
		jQuery('input[name="actual_amount"]').val(acounting.formatNumber(new_amount,2,thousand_formating,decimal_formating));
		jQuery(this).parents('.custom-line').remove();
	});

});

</script>  
<div id="system-message-container" style="width:78%;float:left;"></div>
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo isset($this->income->id)&&$this->income->id>0?JText::_('INCOMEEDIT'):JText::_('INCOMENEW'); ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=income'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
<?php if(!$tmpl) { ?>
<div class="row-fluid">
    <div class="span12">
        <div class="btn-toolbar" id="toolbar">
        <?php if($editaccess) { ?>
            <div class="btn-wrapper"  id="toolbar-apply">
                <span onclick="Joomla.submitbutton('apply')" class="btn btn-small btn-success">
                <span class="fa fa-check"></span> <?php echo JText::_('SAVE'); ?></span>
            </div>
            <div class="btn-wrapper"  id="toolbar-save">
                <span onclick="Joomla.submitbutton('save')" class="btn btn-small">
                <span class="fa fa-check"></span> <?php echo JText::_('SAVE_N_CLOSE'); ?></span>
            </div>
			<div class="btn-wrapper"  id="toolbar-save-new">
                <span onclick="Joomla.submitbutton('saveNew')" class="btn btn-small">
                <span class="fa fa-plus"></span> <?php echo JText::_('SAVE_N_NEW'); ?></span>
            </div>
            <?php if($this->config->enable_cust==1) { ?>
				<?php if($this->income->id) { ?>
				<div class="btn-wrapper"  id="toolbar-pdf">
					<span onclick="Joomla.submitbutton('pdf')" class="btn btn-small">
					<span class="fa fa-plus"></span> <?php echo JText::_('CREATE_PDF'); ?></span>
				</div> 
				<div class="btn-wrapper"  id="toolbar-pdf">
					<a class="modal btn btn-small" id="modal1" title="Select" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=income&task=print_bill&tmpl=component&cid[]='.$this->income->id; ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><span class="fa fa-plus"></span> <?php echo JText::_('PRINT_BILL'); ?></span></a>
					
				</div>
				<?php } ?>
				 <?php if($this->income->create_invoice) { ?>
				<div class="btn-wrapper"  id="toolbar-mailing">
					<span onclick="Joomla.submitbutton('mailing')" class="btn btn-small">
					<span class="fa fa-envelope-o"></span> <?php echo JText::_('EMAIL'); ?></span>
				</div>
				 <?php } ?>
             <?php } ?>
        <?php } ?>
		
            <div class="btn-wrapper"  id="toolbar-cancel">
                <span onclick="Joomla.submitbutton('cancel')" class="btn btn-small">
                <span class="fa fa-close"></span> <?php echo JText::_('CLOSE'); ?></span>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div class="overview">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'OVERVIEW' ); ?></legend>
	<ul>
		<li><?php	echo JText::_('NEW_INCOME_OVERVIEW');  ?></li>
		<li><?php	echo JText::_('ADD_CUSTOMER_OVERVIEW');  ?></li>
		<li><?php	echo JText::_('CREATE_INVOICE_OVERVIEW');  ?></li>
		<li><?php	echo JText::_('DOWNLOAD_SEND_INVOICE_OVERVIEW');  ?></li>
		<li><?php	echo JText::_('ADD_MULTIPLE_ITEMS_OVERVIEW');  ?></li>
	</ul>
</fieldset> 
</div>


<div class="col100">
<fieldset class="adminform">
<legend><?php if($this->income->id) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW');?></legend>
<table class="adminform table table-striped">
		
<?php if(!$tmpl && $this->income->id) { 

		$itemTitle = preg_replace('/\s+/', '', $this->income->title);
		$itemName = strtolower($itemTitle);  
	?>
<div class="v-pdf"><a href='<?php echo JURI::root() . 'components/com_vbizz/pdf/salesorder/'.$itemName.$this->income->id.'sales'.".pdf" ?>' class="pdf btn btn-success"  target="_blank"><label class="hasTip" title="<?php echo JText::_('PDFTXT'); ?>"><i class="fa fa-download"></i> <?php echo JText::_('DOWNLOAD_PDF'); ?></label></a></div>
<?php } ?>
        
<tbody>
        <tr>
            <th width="200"><label id="title-msg" for="title" class="hasTip" title="<?php echo JText::_('TITLETXT'); ?>">
                <?php echo JText::_('TITLE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><input class="text_area required" type="text" name="title" id="title" value="<?php echo $this->income->title;?>"/></td>
        </tr>
        
        <tr>
            <th><label class="hasTip" id="tdate-msg" for="tdate" title="<?php echo JText::_('DATETXT'); ?>">
                <?php echo JText::_('TRANSACTION_DATE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><?php echo JHTML::_('calendar', $this->income->tdate, "tdate" , "tdate", VaccountHelper::DateFormat_javascript($this->config->date_format),' class="required validate-datevalidate"'); ?></td>
        </tr>
        
        <tr>
            <th><label id="quantity-msg" for="quantity" class="hasTip" title="<?php echo JText::_('QTYTXT'); ?>">  
                <?php echo JText::_('QUANTITY'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><input class="text_area required" type="text" name="quantity" readonly="readonly"  id="quantity" value="<?php echo VaccountHelper::getNumberFormatValue($this->income->quantity);?>"/></td>
        </tr>  
        
        <tr>    
            <th><label id="actual_amount-msg" for="actual_amount" class="hasTip" title="<?php echo JText::_('AMNTXT'); ?>">
                <?php echo JText::_('ACTUAL_AMOUNT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><input class="text_area required" type="text" name="actual_amount" id="actual_amount" readonly="readonly" value="<?php if($this->income->tax_inclusive==1){echo VaccountHelper::getNumberFormatValue($this->income->actual_amount+$this->income->tax_amount);} else {echo VaccountHelper::getNumberFormatValue($this->income->actual_amount);}?>"/><?php echo ' '.$this->config->currency; ?></td>
        </tr>
		
		<?php if($this->config->enable_tax_discount==1){ ?>
		<tr>
			<th><label class="hasTip" title="<?php echo JText::_('TAXINCTXT'); ?>"><?php echo JText::_('TAX_INCL'); ?>:</label></th>
			<td>
				<fieldset class="radio btn-group" style="margin-bottom:9px;">
				<label for="tax_inclusive1" id="tax_inclusive-lbl" class="radio"><?php echo JText::_('YS'); ?></label>
				<input type="radio" name="tax_inclusive" id="tax_inclusive1" value="1" <?php if($this->income->tax_inclusive) echo 'checked="checked"';?> />
				<label for="tax_inclusive0" id="tax_inclusive-lbl" class="radio"><?php echo JText::_('NOS'); ?></label>
				<input type="radio" name="tax_inclusive" id="tax_inclusive0" value="0"  <?php if(!$this->income->tax_inclusive) echo 'checked="checked"';?>/>
				</fieldset>
			</td>
		</tr>
		<?php	}	?>
		
        
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('AMOUNTSTATTXT'); ?>"><?php echo JText::_('AMOUNT_STATUS'); ?></label></th>
            <td>
                <select name="status">
                <option value=""><?php echo JText::_('SELECT_AMOUNT_STATUS'); ?></option>
                <option value="1" <?php if($this->income->status==1) echo 'selected="selected"'; ?>><?php echo JText::_('PAID'); ?></option>
                <option value="0" <?php if($this->income->status==0) echo 'selected="selected"'; ?>><?php echo JText::_('UNPAID'); ?></option>
                </select>
            </td>
        </tr>
        
        <tr>
            <th><label id="tid-msg" for="tid" class="hasTip" title="<?php echo sprintf ( JText::_( 'SELTRTYPEDESCTXT' ), $this->config->type_view_single); ?>">
                <?php echo $this->config->type_view_single; ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td>
                <select name="tid" id="tid" class="required">
                <option value=""><?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->type_view_single); ?></option>
                <?php	for($i=0;$i<count($this->ttype);$i++)	{	?>
                <option value="<?php echo $this->ttype[$i]->id; ?>" <?php if($this->ttype[$i]->id==$this->income->tid) echo 'selected="selected"'; ?>> <?php echo JText::_($this->ttype[$i]->treename); ?> </option>
                <?php	}	?>
                </select>
            </td>
        </tr>
        
        <tr>
            <th><label id="mid-msg" for="mid" class="hasTip" title="<?php echo JText::_('MODTXT'); ?>">
                <?php echo JText::_('TRANSACTION_MODE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td>
                <select name="mid" id="mid" class="required">
                <option value=""><?php echo JText::_('SELECT_TRANSACTION_MODE'); ?></option>
                <?php	for($i=0;$i<count($this->mode);$i++)	{	?>
                <option value="<?php echo $this->mode[$i]->id; ?>" <?php if($this->mode[$i]->id==$this->income->mid) echo 'selected="selected"'; ?>> <?php echo JText::_($this->mode[$i]->title); ?> </option>
                <?php	}	?>
                </select>
            </td>
        </tr>
        
        <?php if($this->config->enable_account==1) { ?>
        <tr>
            <th><label class="hasTip" id="account_id-msg" for="account_id" title="<?php echo JText::_('SELECTACCTXT'); ?>"><?php echo JText::_('SELECT_ACCOUNT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
            <td>
                <select name="account_id" id="account_id" class="required">
                <option value=""><?php echo JText::_('SELECT_ACCOUNT'); ?></option>
                <?php	for($i=0;$i<count($this->account);$i++)	{	?>
                <option value="<?php echo $this->account[$i]->id; ?>" <?php if($this->account[$i]->id==$this->income->account_id) echo 'selected="selected"'; ?>> <?php echo JText::_($this->account[$i]->account_name); ?> </option>
                <?php	}	?>
                </select>
            </td>
        </tr>
        <?php	}  	?>
        <?php if($this->config->employeecommission) {
              if(VaccountHelper::checkOwnerGroup()){
		?>    
		
		<tr>
            <th><label class="hasTip" id="saleman-msg" for="saleman" title="<?php echo JText::_( 'SELECT_SALESMAN' ) ?>">
                <?php echo JText::_( 'SELECT_SALESMAN' ); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
			<td><select name="salesman" id="salesman"><option value=""><?php echo JText::_("SELECT_SALESMAN");?></option> 
			<?php  
            foreach($this->employeeListing as $key=>$value)
			echo '<option value="'.$value->id.'"'.($value->id==$this->income->salesman?' selected="selected"':'').'>'.$value->name.'</option>';
			?></select></td>
		<?php }
				  if(VaccountHelper::checkEmployeeGroup())
				  { ?>  
			    <input type="hidden" name="salesman" value="<?php echo $userId;?>"> <?php }  } ?>
        <?php if($this->config->enable_cust==1 && (VaccountHelper::checkOwnerGroup() || VaccountHelper::checkEmployeeGroup())) { ?>
        <tr>  
            <th><label class="hasTip" id="eid-msg" for="eid" title="<?php echo sprintf ( JText::_( 'SELTERMDESCTXT' ), $this->config->customer_view_single); ?>">
                <?php echo $this->config->customer_view_single; ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td class="sel_customer"><input id="cust" type="text" readonly="" value="<?php if($customer){ echo $customer;} else {echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single);} ?>">
            <a class="btn btn-primary modal" id="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=customer&layout=modal&tmpl=component&assign_user=1';?>" title="<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single); ?>"> 
            <i class="fa fa-user hasTip" title="<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single); ?>"></i>
            </a>
			</td>
            <input id="eid" class="required" type="hidden" value="<?php echo $this->income->eid; ?>" name="eid" />
        </tr>
        <?php } 
		$emp_id =0;
		?>
          
        <tr> 
            <th><label class="hasTip" title="<?php echo JText::_('TRANIDTXT'); ?>"><?php echo JText::_('TRANSACTION_ID'); ?></label></th>
            <td><input class="text_area" type="text" name="tranid" id="tranid" value="<?php echo $this->income->tranid;?>"/></td>
        </tr>
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('RCTUPLDTXT'); ?>"><?php echo JText::_('RECIEPT_UPLOAD'); ?></label></th>
            <td><input type="file" name="reciept" id="reciept" class="inputbox" size="50" value=""/>
                <?php if(isset($this->income->reciept)) { ?>
				<a target="_blank" href="components/com_vbizz/uploads/reciept/<?php echo $this->income->reciept;?>"><?php echo $this->income->reciept;?></a>
				<?php } ?>
            </td>
        </tr>
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('CMNTXT'); ?>"><?php echo JText::_('COMMENTS'); ?></label></th>
            <td>
			<textarea class="text_area" name="comments" id="comment" rows="4" cols="50"><?php echo $this->income->comments;?></textarea></td>
        </tr>
        
        <?php 
		
		if($this->config->enable_items==1) { ?>
        <tr>
        	<?php if($editaccess) { ?>
        	<th colspan="0">
            	<a class="modal" id="modal1" title="Select" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=items&layout=modal&pro=inc&tmpl=component&filter_type='.$this->income->tid; ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
        		<button id="addnew" class="btn btn-success"><i class="fa fa-plus"></i> <?php echo sprintf ( JText::_( 'ADDITEMTXT' ), $this->config->item_view_single); ?></button>
                </a>
            </th> 
            <?php } ?>
            <td id="more-item">
            <?php for($i=0;$i<count($this->multi_item);$i++) { 
				$items = $this->multi_item[$i]; 
				if($items->quantity2==0) {
					$quantity2 = JText::_('UNLIMITED');
				} else {
					$quantity2 = $items->quantity2;
				}
			
			?>
            <div class="multi-item">
                <div class="title-block">
                	<span class="item_title"><?php echo JText::_('TITLE'); ?>: </span>
                    <span class="item_value"><?php echo $items->title; ?></span>
                </div>
                <div class="amount-block">
                	<span class="item_amounts"><?php echo JText::_('AMOUNT'); ?>: </span>
                	<input type="text" class="item_amount" name="item_amount[]" value="<?php echo VaccountHelper::getNumberFormatValue($items->amt); ?>">
                </div>
                <div class="quantity-block">
                	<span class="item_quantitys"><?php echo JText::_('QUANTITY'); ?>: </span>
                	<span class="item_quantity_value">  
						<input class="item_quantity" type="text" autocomplete="off" name="item_quantity[]" value="<?php echo VaccountHelper::getNumberFormatValue($items->quant); ?>" placeholder="">
						<span style="color:#FF0000;"><?php echo JText::_('AVAILABLE_QUANTITY') ; ?>: <?php echo $quantity2; ?></span>
					</span>
                </div>  
				
				<?php if($this->config->enable_tax_discount==1){ ?>
				<div class="tax-block">
                	<span class="tax_label"><?php echo JText::_('TAX'); ?>: </span>
					<span class="sel-tax">
						<select class="tax" id="tax<?php echo $i?>" name="tax[<?php echo $items->id; ?>][]" multiple="multiple" >
						<?php	for($j=0;$j<count($this->tax);$j++)	{	?>
							<option value="<?php echo $this->tax[$j]->id; ?>" <?php if(in_array($this->tax[$j]->id,$items->tax)) { echo 'selected="selected"';}?>> <?php echo JText::_($this->tax[$j]->tax_name); ?> </option>
						
						<?php	}	?>
						</select>
					</span>
				</div>
				
				<div class="discount-block"> 
					<span class="discount_label"><?php echo JText::_('DISCOUNT'); ?>: </span>  
					<span class="sel-dis">
						<select class="discount" id="discount<?php echo $i?>" name="discount[<?php echo $items->id; ?>][]" multiple="multiple" >
						<?php	
						echo VaccountHelper::getDicountOptionList($items->id,$items->discount);?>
						
						</select>
					</span>
				</div>
                <?php	}	?>
				
                <div class="item_button">  
				
				<a class="remove btn" itId="<?php echo $items->id; ?>"  href="javascript:void(0)"><span class="loadingbox" style="display:none;"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/load.gif" />' ?></span><i class="fa fa-remove"></i></a>
                </div>
                <div class="itId" id="<?php echo $items->id; ?>"></div>
                <input type="hidden" class="item_id" name="item_id[]" value="<?php echo $items->id; ?>" />
                
            </div>
            <?php } ?>  
            </td>
            
		</tr>
        <?php } ?>
		
		<tr>
			<td colspan="0">
        		<label class="add_custom_information"><span id="addcustom" class="btn btn-success">
				<i class="fa fa-plus"></i> <?php echo JText::_('ADD_CUSTOM'); ?>
				</span></label>
            </td>
			
			<td id="custom-item">
				<?php 
				$c = 0;
				foreach($this->custom_item as $key => $items ) {
				
			?>
			
			<div class="custom-line"> 
				<div class="custom-title-block">
                	<span class="custom_title"> <label id="custom_title-msg" for="custom_title<?php echo $c;?>" class="hasTip" title="<?php echo JText::_('TITLE'); ?>"> <?php echo JText::_('TITLE'); ?>: </label></span>
                    <span class="custom_value"><input class="item_title_value required" type="text" value="<?php echo $items->title; ?>" name="custom_title[]" id="custom_title<?php echo $c;?>"></span>
                </div>
				
				<div class="custom-amount-block">
					<span class="custom_amount"><label id="custom_amount-msg" for="custom_amount<?php echo $c;?>" class="hasTip" title="<?php echo JText::_('AMOUNT'); ?>"><?php echo JText::_('AMOUNT'); ?>: </label></span>
					<span class="custom_amount_value"><input class="custom_amount_val required" id="custom_amount<?php echo $c;?>" type="text" value="<?php echo VaccountHelper::getNumberFormatValue($items->amount); ?>" name="custom_amount[]" autocomplete="off"></span>
				</div>
				
				<div class="custom-quantity-block">
					<span class="custom_quantity"><label id="custom_quantity-msg" for="custom_quantity<?php echo $c;?>" class="hasTip" title="<?php echo JText::_('QUANTITY'); ?>"><?php echo JText::_('QUANTITY'); ?>: </label></span>
					<span class="custom_quantity_value"><input class="custom_quantity_val required" id="custom_quantity<?php echo $c;?>" type="text" value="<?php echo VaccountHelper::getNumberFormatValue($items->quantity); ?>" name="custom_quantity[]" autocomplete="off"></span>
				</div>
				
				<?php if($this->config->enable_tax_discount==1){ ?>
                <div class="custom-tax-block">
                	<span class="tax_label"><?php echo JText::_('TAX'); ?>: </span>
					<span class="sel-tax">
						<select class="tax" name="custom_tax[<?php echo $c; ?>][]" multiple="multiple" >
						<?php	for($j=0;$j<count($this->tax);$j++)	{	?>
							<option value="<?php echo $this->tax[$j]->id; ?>" <?php if(in_array($this->tax[$j]->id,$items->tax)) { echo 'selected="selected"';}?>> <?php echo JText::_($this->tax[$j]->tax_name); ?> </option>
							
						
						<?php	}	?>
						</select>
					</span>
				</div>
				
				<div class="custom-discount-block">
					<span class="discount_label"><?php echo JText::_('DISCOUNT'); ?>: </span>
					<span class="sel-dis">
						<select class="discount" name="custom_discount[<?php echo $c; ?>][]" multiple="multiple" >
						<?php	for($j=0;$j<count($this->discount);$j++)	{	?>
							<option value="<?php echo $this->discount[$j]->id; ?>" <?php if(in_array($this->discount[$j]->id,$items->discount)) { echo 'selected="selected"';}?>> <?php echo JText::_($this->discount[$j]->discount_name); ?> </option>
							
						
						<?php	}	?>
						</select>
					</span>
				</div>
				<?php	}	?>
				
				<div class="custom_button"><a class="remNew btn btn-success" href="javascript:void(0);" id="custom-setting"><i class="fa fa-remove"></i> </a></div>
				<div id="cust_<?php echo $c; ?>"></div>
			</div>
            <?php $c++; } ?>
            </td>
		</tr>
           
        <?php if($tmpl) { ?>
        <tr>
            <td></td>
            <td class="save_button">
                <input type="button" onclick="sumbitIframe()" class="btn btn-small btn-success" value="<?php echo JText::_('SAVE'); ?>"/>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="cid" value="<?php echo $this->income->id; ?>" />
<input type="hidden" name="final_amount" value="" />
<input type="hidden" name="count" value="<?php echo $c; ?>" />
<?php /*?><input type="hidden" id="item_title" name="item_title[]" value="" />
<input type="hidden" id="item_amount" name="item_amount[]" value="" /><?php */?>
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="income" />  
</form>
</div>
</div>
</div>