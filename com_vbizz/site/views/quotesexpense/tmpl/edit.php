<?php
/*------------------------------------------------------------------------
# com_vbizz - vBizz
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.wdmtech.com
# Technical Support: Forum - https://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.calendar');
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');


$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->quotes_acl->get('addaccess');
$edit_access = $this->config->quotes_acl->get('editaccess');
$delete_access = $this->config->quotes_acl->get('deleteaccess');

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
	$deleteaccess=true;
}

$input = JFactory::getApplication()->input;
// Checking if loaded via index.php or component.php
$tmpl = $input->getCmd('tmpl', '');

$db = JFactory::getDbo();



if(VaccountHelper::checkOwnerGroup()) {
	if( $this->quotes->created_by == $user->id ) {
		$createInvoice = true;
	} else {
		$createInvoice = false;
	}
	$createProject = true;
	
} else if(VaccountHelper::checkVenderGroup()) {
	$createInvoice = true;
	$createProject = false;
} else {
	$createInvoice = false;
	$createProject = false;
}

$query = 'SELECT count(*) from #__vbizz_vendor where userid = '.$userId;
$db->setQuery($query);
$vendor = $db->loadResult();


if($this->quotes->customer) {
	$query = 'SELECT name from #__vbizz_users where userid='.$this->quotes->customer;
	$db->setQuery( $query );
	$customer = $db->loadResult();
} else {
	$customer='';
}

$date = JFactory::getDate();
$curr_date = $date->format('Y-m-d');

if($this->quotes->id)
{
	$quote_date = $this->quotes->quote_date;
} else {
	$quote_date = $curr_date;
}


$html = '<select class=tax name=tax[] multiple >';
foreach($this->tax as $row)
{
	$html .='<option value='.$row->id.'>'.$row->tax_name.'</option>';
}
$html .='</select>';

$dhtml = '<select class=discount name=discount[] multiple >';
foreach($this->discount as $row)
{
	$dhtml .='<option value='.$row->id.'>'.$row->discount_name.'</option>';
}
$dhtml .='</select>';
//custom-item
$custHtml = '<select class=tax name=custom_tax[] multiple >';
foreach($this->tax as $row)
{
	$custHtml .='<option value='.$row->id.'>'.$row->tax_name.'</option>';
}
$custHtml .='</select>';

$custDHtml = '<select class=discount name=custom_discount[] multiple >';
foreach($this->discount as $row)
{
	$custDHtml .='<option value='.$row->id.'>'.$row->discount_name.'</option>';
}
$custDHtml .='</select>';

if($this->config->enable_tax_discount==1){
	
	$tax_disc = '<div class=tax-block><span class=tax_label>'.JText::_('TAX').': </span><span class=sel-tax>'.$html.'</span></div><div class=discount-block><span class=discount_label>'.JText::_('DISCOUNT').': </span><span class=sel-dis>'.$dhtml.'</span></div>';
	
	$custom_tax_disc = '<div class=custom-tax-block><span class=tax_label>'.JText::_('TAX').': </span><span class=sel-tax>'.$custHtml.'</span></div><div class=custom-discount-block><span class=discount_label>'.JText::_('DISCOUNT').': </span><span class=sel-dis>'.$custDHtml.'</span></div>';
	
} else {
	$tax_disc = '';
	$custom_tax_disc = '';
}

?>

<?php 
$js = '
		function getItemVal(id)
		{              
		
			jQuery.ajax(
			{
				url: "",
				type: "POST",
				dataType:"json",
				data: {"option":"com_vbizz", "view":"quotesexpense", "task":"getItemVal", "tmpl":"component", "id":id},
				
				success: function(data) 
				{
					if(data.result=="success"){
						
						if(jQuery("#"+data.itemid).length>0)
						{
							alert("'.JText::_('ALREADY_ADDED').'");
							SqueezeBox.close();
							return false;
						}
						
						if(jQuery("input[name=amount]").val()=="")
						{
							var amt=0;
						}else {
							var amt = jQuery("input[name=amount]").val();
						}
						var new_amt = (parseFloat(amt)) + (parseFloat(data.amount));
						var new_quantity = 1;
						if(jQuery("input[name=quantity]").val()!="")
						{
							new_quantity= new_quantity+parseInt(jQuery("input[name=quantity]").val());
						}
						jQuery("input[name=quantity]").val(new_quantity);
						jQuery("input[name=amount]").val(new_amt);
						jQuery("input[name=amount]").attr("readonly","readonly")
						var moreItem = jQuery("#more-item");
						
						jQuery("<div class=multi-item><div class=title-block><span class=item_title>'.JText::_('TITLE').': </span><span class=item_value>"+data.itemtitle+ "</span></div><div class=amount-block><span class=item_amounts>'.JText::_('AMOUNT').': </span><span class=item_amount_value><input class=item_amount type=text autocomplete=off name=\'item_amount[]\' value="+data.amount+" ></span></div><div class=quantity-block><span class=item_quantitys>'.JText::_('QUANTITY').': </span><span class=item_quantity_value><input class=item_quantity type=text autocomplete=off name=item_quantity[] value=></span></div>'.$tax_disc.'<div class=item_button><a class=remNew href=javascript:void()><i class=\"fa fa-remove\"></i></a></div><div id="+data.itemid+"></div><input type=hidden class=item_id name=item_id[] value="+data.itemid+" /></div>").appendTo(moreItem);
						
						jQuery("select").chosen({"disable_search_threshold":0,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Elija una opción","placeholder_text_single":"Select an option","no_results_text":"No results match"});
						
						jQuery(".remNew").addClass("btn");
						jQuery("#"+id).parent().find("select.tax").attr("name","tax["+id+"][]");
						jQuery("#"+id).parent().find("select.discount").attr("name","discount["+id+"][]");
						
					}
					SqueezeBox.close();
				}
				
			});
		
		}';
	
$jscust = '
	function getCustVal(id,name)
	{              
	
		var old_id = document.getElementById("customer").value;
		if (old_id != id) {
			document.getElementById("customer").value = id;
			document.getElementById("cust").value = name;
			document.getElementById("cust").className = document.getElementById("cust").className.replace(" invalid" , "");
		}
		SqueezeBox.close();
	
	}';
	

$document =  JFactory::getDocument();
$document->addScriptDeclaration($js);
$document->addScriptDeclaration($jscust);

$itemTitle = preg_replace('/\s+/', '', $this->quotes->title);
$itemName = strtolower($itemTitle);
						
 ?>
<script type="text/javascript">
jQuery(document).on('change','input.item_quantity',function() { 
	
	var customAmt = 0;
	var customItem = 0;
	jQuery("input.custom_amount_val").each(function(){
		var itQt = jQuery(this).parents('.custom-line').find('input.custom_quantity_val').val();
		if( (isNaN(itQt)) || (itQt==0) || (itQt=="") ) {
			itQt = 1;
		}
		if (!isNaN(jQuery(this).val()) && jQuery(this).val().length != 0) {
			customAmt += (parseFloat(jQuery(this).val())) * (parseInt(itQt));
		}
		customItem += itQt;
		
	});
	
	var totalamount = customAmt;
	
	
		
	this.value = this.value.replace(/[^0-9\.]/g,'');
	
	var quantity = customItem;
	var newItmAmt = totalamount;  
	jQuery("#more-item").find('input.item_quantity').each(function(){ 
		
		var itmQty = jQuery(this).val();
		if( (isNaN(itmQty)) || (itmQty==0) || (itmQty=="") ) {
			itmQty = 1;
		}
		var allAmount = jQuery(this).parents('.multi-item').find('input.item_amount').val();
		newItmAmt += (parseFloat(allAmount)) * (parseInt(itmQty));
		
		if (!isNaN(this.value) && this.value.length != 0) {
			quantity += parseInt(jQuery(this).val());
		}
	});
    
	jQuery('input[name="amount"]').val(newItmAmt);
	jQuery('input[name="quantity"]').val(quantity);
});  
jQuery(document).on('change','input.item_amount',function() { 
	
	var customAmt = 0;
	var customItem = 0;
	jQuery("input.custom_amount_val").each(function(){
		var itQt = jQuery(this).parents('.custom-line').find('input.custom_quantity_val').val();
		if( (isNaN(itQt)) || (itQt==0) || (itQt=="") ) {
			itQt = 1;
		}
		if (!isNaN(jQuery(this).val()) && jQuery(this).val().length != 0) {
			customAmt += (parseFloat(jQuery(this).val())) * (parseInt(itQt));
		}
		customItem += itQt;
		
	});
	
	var totalamount = customAmt;
	
	
		
	this.value = this.value.replace(/[^0-9\.]/g,'');
	
	var quantity = customItem;
	var newItmAmt = totalamount;
	jQuery("#more-item").find('input.item_quantity').each(function(){ 
		
		var itmQty = jQuery(this).val();
		if( (isNaN(itmQty)) || (itmQty==0) || (itmQty=="") ) {
			itmQty = 1;
		}
		var allAmount = jQuery(this).parents('.multi-item').find('input.item_amount').val();
		newItmAmt += (parseFloat(allAmount)) * (parseInt(itmQty));
		
		if (!isNaN(this.value) && this.value.length != 0) {
			quantity += parseInt(jQuery(this).val());
		}
	});
    
	jQuery('input[name="amount"]').val(newItmAmt);
	jQuery('input[name="quantity"]').val(quantity);
}); 
jQuery(document).on('click','.remNew',function() {
	var qty = jQuery('input[name="quantity"]').val();
	var itemQty = jQuery(this).parents('.multi-item').find('input.item_quantity').val();
	var new_qauntity = (parseInt(qty)) - (parseInt(itemQty));
	jQuery('input[name="quantity"]').val(new_qauntity);
	
	if((jQuery('input[name="quantity"]').val()==0) || (jQuery('input[name="quantity"]').val()=="NaN") || (jQuery('input[name="quantity"]').val()==""))
	{
		jQuery('input[name="quantity"]').val('');
	}
	
	var amount = jQuery('input[name="amount"]').val();
	var itemAmt = jQuery(this).parents('.multi-item').find('span.item_amount_value').text();
	var new_amount = (parseFloat(amount)) - (parseFloat(itemAmt));
	jQuery('input[name="amount"]').val(new_amount);
	
	if((jQuery('input[name="amount"]').val()==0)||(jQuery('input[name="amount"]').val()=="NaN")||(jQuery('input[name="amount"]').val()==""))
	{
		jQuery('input[name="amount"]').val('')
	}
	
	jQuery(this).parents('.multi-item').remove(); 
	var quantity = 0;
	var newItmAmt = 0;
	/* jQuery("#more-item").find('input.item_quantity').each(function(){
		
		var itmQty = jQuery(this).val();
		if( (isNaN(itmQty)) || (itmQty==0) || (itmQty=="") ) {
			itmQty = 1;
		}
		var allAmount = jQuery(this).parents().parent().parent().find('span.item_amount_value').text();
		newItmAmt += (parseFloat(allAmount)) * (parseInt(itmQty));
		
		if (!isNaN(this.value) && this.value.length != 0) {
			quantity += parseInt(jQuery(this).val());
		}
	});
	
	var newQuantity = quantity;
	var newAmt = newItmAmt;
	jQuery("#custom-item").find('input.custom_quantity_val').each(function(){
		
		var itmQty = jQuery(this).val();
		if( (isNaN(itmQty)) || (itmQty==0) || (itmQty=="") ) {
			itmQty = 1;
		}
		var allAmount = jQuery(this).parent().parent().parent().find('input.custom_amount_val').val();
		
		if (!isNaN(allAmount) && allAmount.length != 0) {
			newAmt += (parseFloat(allAmount)) * (parseInt(itmQty));
		}
		
		if (!isNaN(this.value) && this.value.length != 0) {
			newQuantity += parseInt(jQuery(this).val());
		}
	});
  
	jQuery('input[name="amount"]').val(newAmt);
	jQuery('input[name="quantity"]').val(newQuantity); */
	
	return false; 
});
jQuery(document).on('click','.remove',function() {
	
	var itemid = jQuery(this).attr('itid');
	var quote_id = '<?php echo $this->quotes->id; ?>';
	var that=this;
	
	jQuery.ajax(
	{
		url: "",
		type: "POST",
		dataType:"json",
		data: {"option":"com_vbizz", "view":"quotesexpense", "task":"removeItem", "tmpl":"component", "itemid":itemid, "quote_id":quote_id},
		
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
				
				var amount = jQuery('input[name="amount"]').val();
				var itemAmt = jQuery(that).parent().parent().find('span.item_amount_value').text();
				var new_amount = (parseFloat(amount)) - (parseFloat(itemAmt));
				jQuery('input[name="amount"]').val(new_amount);
				
				if((jQuery('input[name="amount"]').val()==0)||(jQuery('input[name="amount"]').val()=="NaN")||(jQuery('input[name="amount"]').val()==""))
				{
					jQuery('input[name="amount"]').val('')
				}
				
				jQuery(that).parent().parent().remove();
				jQuery(that).remove ();
				
			}
		}
		
	});
	
});

	
Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		var form = document.adminForm;
	
		/* if(form.title.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_TITLE'); ?>");
			return false;
		} */
		
		if(form.amount.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_AMOUNT'); ?>");
			return false;
		}
		
		<?php if(!VaccountHelper::checkVenderGroup()) { ?>
		<?php if($this->config->enable_cust==1) { ?>
		if(form.customer.value == 0)	{
			alert("<?php echo sprintf ( JText::_( 'ERRSELTERMTXT' ), $this->config->customer_view_single); ?>");
			return false;
		}
		<?php } ?>
		<?php } ?>
		
		if(form.quantity.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_QUANTITY'); ?>");
			return false;
		}

		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
jQuery(function() {
	var i = jQuery('input[name="count"]').val();
	jQuery(document).on('click','#addcustom',function() {
		i++;
		var customItem = jQuery("#custom-item");
		
		jQuery('<div class="custom-line"><div class="custom-title-block"><span class="custom_title"><?php echo JText::_('TITLE'); ?>: </span><span class="custom_value"><input type="text" class="item_title_value" name="custom_title[]" value="" /></span></div><div class="custom-amount-block"><span class="custom_amount"><?php echo JText::_('AMOUNT'); ?>: </span><span class="custom_amount_value"><input type="text" autocomplete="off" class="custom_amount_val" name="custom_amount[]" value="" /><?php echo ' '.$this->config->currency; ?></span></div><div class="custom-quantity-block"><span class="custom_quantity"><?php echo JText::_('QUANTITY'); ?>: </span><span class="custom_quantity_value"><input type="text"  autocomplete="off" class="custom_quantity_val" name="custom_quantity[]" value="" /></span></div><?php echo $custom_tax_disc; ?><div class="custom_button"><a class="remNew btn" href="javascript:void();"><i class="fa fa-remove"></i> </a></div><div id="cust_'+i+'"></div>').appendTo(customItem);
		
		jQuery("select").chosen({"disable_search_threshold":0,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Elija una opción","placeholder_text_single":"Select an option","no_results_text":"No results match"});
		
		jQuery("#cust_"+i).parent().find("select.tax").attr("name","custom_tax[cust"+i+"][]");
		jQuery("#cust_"+i).parent().find("select.discount").attr("name","custom_discount[cust"+i+"][]");
		
	}); 
	
	jQuery(document).on('change','input.custom_amount_val',function() {
		
		var itemAmt = 0;
		var itemQty = 0;
		jQuery("input.item_amount").each(function(){
			var itQt = jQuery(this).parents('.multi-item').find('input.item_quantity').val();
			if( (isNaN(itQt)) || (itQt==0) || (itQt=="") ) {
				itQt = 1;
			}
			itemAmt += (parseFloat(jQuery(this).val())) * (parseInt(itQt));
			itemQty += parseInt(itQt);
		});
		
		
		this.value = this.value.replace(/[^0-9\.]/g,'');
		
		var totalamount = itemAmt;
		jQuery("#custom-item").find('input.custom_amount_val').each(function(){
			var allQt = jQuery(this).parents('.custom-line').find('input.custom_quantity_val').val();
			if( (isNaN(allQt)) || (allQt==0) || (allQt=="") ) {
				allQt = 1;
			}
			if (jQuery(this).val() != "") {
				totalamount += (parseFloat(jQuery(this).val())) * (parseInt(allQt));
			}
			itemQty += parseInt(allQt);
		});
	  
	  jQuery('input[name="amount"]').val(totalamount);
	  jQuery('input[name="quantity"]').val(itemQty);
	});
	
	
	jQuery(document).on('change','input.custom_quantity_val',function() {
		
		var itemAmt = 0;
		var itemQty = 0;
		jQuery("#more-item").find('input.item_amount').each(function(){
			var itQt = jQuery(this).parents('.multi-item').find('input.item_quantity').val();
			if( (isNaN(itQt)) || (itQt==0) || (itQt=="") ) {
				itQt = 1;
			}
			itemAmt += (parseFloat(jQuery(this).val())) * (parseInt(itQt));
			itemQty += parseInt(itQt);
		});
		
		var totalamount = itemAmt;
		
		
		var quantity = itemQty;
		var newItmAmt = totalamount;
		jQuery("#custom-item").find('input.custom_quantity_val').each(function(){
			
			var itmQty = jQuery(this).val();
			if( (isNaN(itmQty)) || (itmQty==0) || (itmQty=="") ) {
				itmQty = 1;
			}
			var allAmount = jQuery(this).parents('.custom-line').find('input.custom_amount_val').val();
			
			if (!isNaN(allAmount) && allAmount.length != 0) {
				newItmAmt += (parseFloat(allAmount)) * (parseInt(itmQty));
			}
			
			quantity += parseInt(jQuery(this).val());
		});
	  
		jQuery('input[name="amount"]').val(newItmAmt);
		jQuery('input[name="quantity"]').val(quantity);
	});
	
	jQuery(document).on('click','.remCust',function() { 
		
		var qty = jQuery('input[name="quantity"]').val();
		var itemQty = jQuery(this).parents('.custom-line').find('input.custom_quantity_val').val();
		var new_qauntity = (parseInt(qty)) - (parseInt(itemQty));
		jQuery('input[name="quantity"]').val(new_qauntity);
		
		var amount = jQuery('input[name="actual_amount"]').val();
		var itemAmt = jQuery(this).parents('.custom-line').find('input.custom_amount_val').val();
		if(itemAmt==0 || itemAmt=="NaN" || itemAmt=="") {
			itemAmt = 0;
		}
		var new_amount = (parseFloat(amount)) - (parseFloat(itemAmt));
		jQuery('input[name="actual_amount"]').val(new_amount);
		
		
	
		jQuery(this).parents('.custom-line').remove();
		var quantity = 0;
		var newItmAmt = 0;
		jQuery("#more-item").find('input.item_quantity').each(function(){
			
			var itmQty = jQuery(this).val();
			if( (isNaN(itmQty)) || (itmQty==0) || (itmQty=="") ) {
				itmQty = 1;
			}
			var allAmount = jQuery(this).parents('.multi-item').find('input.item_amount').val();
			newItmAmt += (parseFloat(allAmount)) * (parseInt(itmQty));
			
			if (!isNaN(this.value) && this.value.length != 0) {
				quantity += parseInt(jQuery(this).val());
			}
		});
		
		var newQuantity = quantity;
		var newAmt = newItmAmt;
		jQuery("#custom-item").find('input.custom_quantity_val').each(function(){
			
			var itmQty = jQuery(this).val();
			if( (isNaN(itmQty)) || (itmQty==0) || (itmQty=="") ) {
				itmQty = 1;
			}
			var allAmount = jQuery(this).parents('.custom-line').find('input.custom_amount_val').val();
			
			if (!isNaN(allAmount) && allAmount.length != 0) {
				newAmt += (parseFloat(allAmount)) * (parseInt(itmQty));
			}
			
			if (!isNaN(this.value) && this.value.length != 0) {
				newQuantity += parseInt(jQuery(this).val());
			}
		});
	  
		jQuery('input[name="amount"]').val(newAmt);
		jQuery('input[name="quantity"]').val(newQuantity);
		return false;
	});

});
<?php if(!empty($this->quotes->id)) { ?>  
function showMessageSection(){ 
	jQuery('.comment_section_add').addClass('expanded');
	jQuery('.collapsed_content').remove();
	jQuery('.editor').remove();
	tinyMCE.init({selector: "textarea.comments_message",
	toolbar: " image bold italic strikethrough | formatselect | undo redo | cut copy paste | bullist numlist | undo redo | link unlink dummyimg | mybutton",
    menubar: false,
   toolbar_items_size: 'small',
   setup: function(editor) {
        editor.addButton('mybutton', { 
            text:"<?php echo JText::_('IMAGE');?>",
            icon: false,
            onclick: function(e) {
                console.log(jQuery(e.target));
                if(jQuery(e.target).prop("tagName") == 'BUTTON'){
                    console.log(jQuery(e.target).parent().parent().find('input').attr('id'));
                    if(jQuery(e.target).parent().parent().find('input').attr('id') != 'tinymce-uploader') {
                        jQuery(e.target).parent().parent().append('<input id="tinymce-uploader" type="file" name="pic" accept="image/*" style="display:none">');
                    }
                    jQuery('#tinymce-uploader').trigger('click');
                    jQuery('#tinymce-uploader').change(function(){
                        var input, file, fr, img;

                        if (typeof window.FileReader !== 'function') {
                            write("The file API isn't supported on this browser yet.");
                            return;
                        }

                        input = document.getElementById('tinymce-uploader');
                        if (!input) {
                            write("Um, couldn't find the imgfile element.");
                        } else if (!input.files) {
                            write("This browser doesn't seem to support the `files` property of file inputs.");
                        } else if (!input.files[0]) {
                            write("Please select a file before clicking 'Load'");
                        } else {
                            file = input.files[0];
                            fr = new FileReader();
                            fr.onload = createImage;
                            fr.readAsDataURL(file);
                        }

                        function createImage() {
                            img = new Image();
                            img.src = fr.result;
                            editor.insertContent('<img src="'+img.src+'"/>');
                        }
                    });

                }

                if(jQuery(e.target).prop("tagName") == 'DIV'){
                    if(jQuery(e.target).parent().find('input').attr('id') != 'tinymce-uploader') {
                        console.log(jQuery(e.target).parent().find('input').attr('id'));                                
                        jQuery(e.target).parent().append('<input id="tinymce-uploader" type="file" name="pic" accept="image/*" style="display:none">');
                    }
                    jQuery('#tinymce-uploader').trigger('click');
                    jQuery('#tinymce-uploader').change(function(){
                        var input, file, fr, img;

                        if (typeof window.FileReader !== 'function') {
                            write("The file API isn't supported on this browser yet.");
                            return;
                        }

                        input = document.getElementById('tinymce-uploader');
                        if (!input) {
                            write("Um, couldn't find the imgfile element.");
                        } else if (!input.files) {
                            write("This browser doesn't seem to support the `files` property of file inputs.");
                        } else if (!input.files[0]) {
                            write("Please select a file before clicking 'Load'");
                        } else {
                            file = input.files[0];
                            fr = new FileReader();
                            fr.onload = createImage;
                            fr.readAsDataURL(file);
                        }

                        function createImage() {
                            img = new Image();
                            img.src = fr.result;
                             editor.insertContent('<img src="'+img.src+'"/>');
                        }
                    });
                }

                if(jQuery(e.target).prop("tagName") == 'I'){
                    console.log(jQuery(e.target).parent().parent().parent().find('input').attr('id')); if(jQuery(e.target).parent().parent().parent().find('input').attr('id') != 'tinymce-uploader') {               jQuery(e.target).parent().parent().parent().append('<input id="tinymce-uploader" type="file" name="pic" accept="image/*" style="display:none">');
                                                                                           }
                    jQuery('#tinymce-uploader').trigger('click');
                    jQuery('#tinymce-uploader').change(function(){
                        var input, file, fr, img;

                        if (typeof window.FileReader !== 'function') {
                            write("The file API isn't supported on this browser yet.");
                            return;
                        }

                        input = document.getElementById('tinymce-uploader');
                        if (!input) {
                            write("Um, couldn't find the imgfile element.");
                        } else if (!input.files) {
                            write("This browser doesn't seem to support the `files` property of file inputs.");
                        } else if (!input.files[0]) {
                            write("Please select a file before clicking 'Load'");
                        } else {
                            file = input.files[0];
                            fr = new FileReader();
                            fr.onload = createImage;
                            fr.readAsDataURL(file);
                        }

                        function createImage() {
                            img = new Image();
                            img.src = fr.result;
                             editor.insertContent('<img src="'+img.src+'"/>');
                        }
                    });
                }

            }
        });
    }
   
});
	jQuery('.comment_section_add_msg').show();
	
}
function AddMessageSection(){
	if(jQuery('.comment_section_add_msg').hasClass('disabled')){
		return ;
	} 
    if(tinymce.get('comments_message').getContent()==''){
	return ;	
	}	
	jQuery('.comment_section_add_msg').addClass('disabled');
	jQuery.ajax({
				  url: "index.php",
				  type: "POST",
				  dataType: "json",
				  data: {"option":"com_vbizz", "view":"quotesexpense", "task":"addcomments", "tmpl":"component", "section":"quotesexpense", "section_id":<?php echo (int)$this->quotes->id; ?>, "msg":tinymce.get('comments_message').getContent(), "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
				  beforeSend: function()	{
					jQuery(".vchart_overlay").show();
				  },
				  complete: function()	{
					jQuery(".vchart_overlay").hide();
				  },
				  success: function(res)	{
					
					if(res.result == "success"){
						jQuery('.comment_section_add_msg').removeClass('disabled');
						jQuery('.discussion_messages').append(res.html);
						var tinymce_editor_id = 'comments_message'; 
						tinymce.get(tinymce_editor_id).setContent('');
					}	
					else
						alert(res.error);
					
				  },
				  error: function(jqXHR, textStatus, errorThrown)	{
					  alert(textStatus);				  
				  }
			});
	
}
function view_text () {
			// Find html elements.
			var textArea = document.getElementById('my_text');
			var div = document.getElementById('view_text');
			
			// Put the text in a variable so we can manipulate it.
			var text = textArea.value;
			
			// Make sure html and php tags are unusable by disabling < and >.
			text = text.replace(/\</gi, "<");
			text = text.replace(/\>/gi, ">");
			
			// Exchange newlines for <br />
			text = text.replace(/\n/gi, "<br />");
			
			// Basic BBCodes.
			text = text.replace(/\[b\]/gi, "<b>");
			text = text.replace(/\[\/b\]/gi, "</b>");
			
			text = text.replace(/\[i\]/gi, "<i>");
			text = text.replace(/\[\/i\]/gi, "</i>");
			
			text = text.replace(/\[u\]/gi, "<u>");
			text = text.replace(/\[\/u\]/gi, "</u>");
			
			// Print the text in the div made for it.
			div.innerHTML = text;
		}
		
		function mod_selection (val1,val2) {
			// Get the text area
			var textArea = document.getElementById('my_text');
			
			// IE specific code.
			if( -1 != navigator.userAgent.indexOf ("MSIE") ) { 
				
				var range = document.selection.createRange();
				var stored_range = range.duplicate();
				
				if(stored_range.length > 0) {
					stored_range.moveToElementText(textArea);
					stored_range.setEndPoint('EndToEnd', range);
					textArea.selectionstart = stored_range.text.length - range.text.length;
					textArea.selectionend = textArea.selectionstart + range.text.length;
				}
			}
			// Do we even have a selection?
			if (typeof(textArea.selectionstart) != "undefined") {
				// Split the text in three pieces - the selection, and what comes before and after.
				var begin = textArea.value.substr(0, textArea.selectionstart);
				var selection = textArea.value.substr(textArea.selectionstart, textArea.selectionend - textArea.selectionstart);
				var end = textArea.value.substr(textArea.selectionend);
				
				// Insert the tags between the three pieces of text.
				textArea.value = begin + val1 + selection + val2 + end;
			}
		} 
	function refreshDashboard() {   
        jQuery.noConflict();
        
        var jqxhr = jQuery.ajax({
            type: "POST",
			dataType: "json",
            url: "index.php",
             data: {"option":"com_vbizz", "view":"quotesexpense", "task":"UpdateComments", "ptaskid":"<?php echo (int)$this->quotes->id; ?>", "tmpl":"component", "<?php echo JSession::getFormToken(); ?>":1, 'abase':1}
        }).done(function(resp){
            console.log('REFRESH');
            jQuery('.discussion_messages').replaceWith(resp.html);
            
            
        });
    }
jQuery(document).ready(function(){  
	 setInterval("refreshDashboard()", 10000);
});	
<?php } ?>
</script>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo isset($this->quotes->id)&&$this->quotes->id>0?JText::_('QUOTEDIT'):JText::_('QUOTNEW'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=quotesexpense'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

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
					
					<?php if($this->quotes->id) { ?>
					<div class="btn-wrapper"  id="toolbar-mailing">
						<span onclick="Joomla.submitbutton('mailing')" class="btn btn-small">
						<span class="fa fa-envelope-o"></span> <?php echo JText::_('EMAIL'); ?></span>
					</div>
					<?php } ?>
                <?php } ?>
			
			<?php if($this->quotes->id) { ?>
					
				<?php if($this->quotes->approved==0) { ?> 
					<div class="btn-wrapper"  id="toolbar-mailing">
						<span onclick="Joomla.submitbutton('approve')" class="btn btn-small">
						<span class="fa fa-check-square-o"></span> <?php echo JText::_('APPROVE'); ?></span>
					</div>
				<?php } ?>
				
				<?php if($this->quotes->reject==0) { ?>
					<div class="btn-wrapper"  id="toolbar-mailing">
						<span onclick="Joomla.submitbutton('reject')" class="btn btn-small">
						<span class="fa fa-close"></span> <?php echo JText::_('REJECT'); ?></span>
					</div>
				<?php } ?>
				
				<?php if($createInvoice && $this->quotes->approved==1) { ?>
				<div class="btn-wrapper"  id="toolbar-pdf">
					<span onclick="Joomla.submitbutton('moveInvoice')" class="btn btn-small">
					<span class="fa fa-plus"></span> <?php echo JText::_('CREATE_INVOICE'); ?></span>
				</div>
				<?php } ?>
				
				<?php if($createProject && $this->quotes->approved==1) { ?>
				<div class="btn-wrapper"  id="toolbar-mailing">
					<span onclick="Joomla.submitbutton('moveProject')" class="btn btn-small">
					<span class="fa fa-plus"></span> <?php echo JText::_('CREATE_PROJECT'); ?></span>
				</div>
				<?php } ?>
					
			<?php } ?>
			
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
		<li><?php	echo JText::_('NEW_QUOTES_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">
<fieldset class="adminform">
<legend><?php if($this->quotes->id) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW'); ?></legend>
<table class="adminform table table-striped">

<?php if($this->quotes->id) { ?>
<div class="v-pdf"><a href='<?php echo JURI::root() . 'components/com_vbizz/pdf/quotation/'.$itemName.'quotation'.".pdf" ?>' class="pdf btn btn-success"  target="_blank"><label class="hasTip" title="<?php echo JText::_('PDFTXT'); ?>"><?php echo JText::_('DOWNLOAD_QUOTATION'); ?></label></a></div>
<?php } ?>

	<tbody>
		
        <tr>
            <th width="200"><label class="hasTip" title="<?php echo JText::_('QUOTETITLETXT'); ?>">
                <?php echo JText::_('TITLE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><input class="text_area" type="text" name="title" id="title" value="<?php echo $this->quotes->title;?>"/></td>
        </tr>
		
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('QTYTXT'); ?>">
                <?php echo JText::_('QUANTITY'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><input class="text_area" type="text" name="quantity" <?php if($this->config->enable_items==1) { ?> readonly="readonly" <?php } ?> id="quantity" value="<?php echo $this->quotes->quantity;?>"/></td>
        </tr>
        
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('AMNTXT'); ?>">
                <?php echo JText::_('AMOUNT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><input class="text_area" type="text" name="amount" id="amount" <?php if($this->config->enable_items==1) { ?> readonly="readonly" <?php } ?> value="<?php echo $this->quotes->amount;?>"/><?php echo ' '.$this->config->currency; ?></td>
        </tr>
		
		<?php if(!$this->config->enable_items) { ?>
		
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('TAXTXT'); ?>"><?php echo JText::_('TAX'); ?></label></th>
            <td>
                <select name="tax[]" multiple="multiple">
                <?php	for($i=0;$i<count($this->tax);$i++)	{	?>
                <option value="<?php echo $this->tax[$i]->id; ?>" <?php if(in_array($this->tax[$i]->id,$this->quotes->tax)) { echo 'selected="selected"';?>> <?php echo JText::_($this->tax[$i]->tax_name); ?> </option>
							<?php 	} else{?>
								<option value="<?php echo $this->tax[$i]->id; ?>"><?php echo JText::_($this->tax[$i]->tax_name);?></option>
								<?php }?>
                <?php	}	?>
                </select>
            </td>
        </tr>
		
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('DISCOUNTTXT'); ?>"><?php echo JText::_('DISCOUNT'); ?></label></th>
            <td>
                <select name="discount[]" multiple="multiple">
                <?php	for($i=0;$i<count($this->discount);$i++)	{	?>
                <option value="<?php echo $this->discount[$i]->id; ?>" <?php if(in_array($this->discount[$i]->id,$this->quotes->discount)) { echo 'selected="selected"';?>> <?php echo JText::_($this->discount[$i]->discount_name); ?> </option>
							<?php 	} else { ?>
								<option value="<?php echo $this->discount[$i]->id; ?>"><?php echo JText::_($this->discount[$i]->discount_name);?></option>
								<?php } ?>
                <?php	}	?>
                </select>
            </td>
        </tr>
		
		<?php } ?>
		
		<?php if(!VaccountHelper::checkVenderGroup() && $this->config->enable_cust) { ?>
		<tr>
            <th><label class="hasTip" title="<?php echo sprintf ( JText::_( 'SELTERMDESCTXT' ), $this->config->vendor_view_single); ?>">
                <?php echo $this->config->vendor_view_single; ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td class="sel_customer"><input id="cust" type="text" readonly="" value="<?php if($customer){ echo $customer;} else {echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->vendor_view_single);} ?>">
            <a class="btn btn-primary modal" id="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=customer&layout=modal&tmpl=component&for=expense';?>" title="<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->vendor_view_single); ?>">
            <i class="fa fa-user hasTip" title="<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->vendor_view_single); ?>"></i>
            </a>
			</td>
            <input id="customer" type="hidden" value="<?php echo $this->quotes->customer; ?>" name="customer" />
        </tr>
		<?php } ?>
		
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('DESCRIPTIONTXT'); ?>"><?php echo JText::_('DESCRIPTION'); ?></label></th>
            <td><textarea class="text_area" name="description" id="description" rows="4" cols="50"><?php echo $this->quotes->description;?></textarea></td>
        </tr>
		
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('CUSTCMNTTXT'); ?>"><?php echo JText::_('CUSTOMER_NOTES'); ?></label></th>
            <td><textarea class="text_area" name="customer_notes" id="customer_notes" rows="4" cols="50"><?php echo $this->quotes->customer_notes;?></textarea></td>
        </tr>
       
        <?php if($this->config->enable_items==1) { ?>
        <tr>
        	<?php if($editaccess) { ?>
        	<th colspan="0">
            	<a class="modal" id="modal1" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=items&layout=modal&pro=exp&tmpl=component';?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
        		<button id="addnew" class="btn btn-success">
				<i class="fa fa-plus"></i> <?php echo sprintf ( JText::_( 'ADDITEMTXT' ), $this->config->item_view_single); ?>
				</button>
                </a>
            </th>
            <?php } ?>
            <td id="more-item">
            <?php for($i=0;$i<count($this->multi_item);$i++) { 
			
			$items = $this->multi_item[$i]; 
			
			?>
            <div class="multi-item">
                <div class="title-block">
                	<span class="item_title"><?php echo JText::_('TITLE'); ?>: </span>
                    <span class="item_value"><?php echo $items->title; ?></span>
                </div>
                <div class="amount-block">
                	<span class="item_amounts"><?php echo JText::_('AMOUNT'); ?>: </span>
                	<span class="item_amount_value"><input class="item_amount" type="text" autocomplete="off" name="item_amount[]" value="<?php echo $items->amt; ?>">
                </div>
                <div class="quantity-block">
                	<span class="item_quantity"><?php echo JText::_('QUANTITY'); ?>: </span>
                	<span class="item_quantity_value">
                	<input class="item_quantity" type="text" autocomplete="off" name="item_quantity[]" value="<?php echo $items->quant; ?>">
                </span>
                </div>
				
				<?php if($this->config->enable_tax_discount==1){ ?>
				<div class="tax-block">
                	<span class="tax_label"><?php echo JText::_('TAX'); ?>: </span>
					<span class="sel-tax">
						<select class="tax" name="tax[<?php echo $items->id; ?>][]" multiple="multiple" >
						<?php	for($j=0;$j<count($this->tax);$j++)	{	?>
							<option value="<?php echo $this->tax[$j]->id; ?>" <?php if(in_array($this->tax[$j]->id,$items->tax)) { echo 'selected="selected"';?>> <?php echo JText::_($this->tax[$j]->tax_name); ?> </option>
							<?php 	} else{?>
								<option value="<?php echo $this->tax[$j]->id; ?>"><?php echo JText::_($this->tax[$j]->tax_name);?></option>
								<?php }?>
						
						<?php	}	?>
						</select>
					</span>
				</div>
				
				<div class="discount-block">
					<span class="discount_label"><?php echo JText::_('DISCOUNT'); ?>: </span>
					<span class="sel-dis">
						<select class="discount" name="discount[<?php echo $items->id; ?>][]" multiple="multiple" >
						<?php	for($j=0;$j<count($this->discount);$j++)	{	?>
							<option value="<?php echo $this->discount[$j]->id; ?>" <?php if(in_array($this->discount[$j]->id,$items->discount)) { echo 'selected="selected"';?>> <?php echo JText::_($this->discount[$j]->discount_name); ?> </option>
							<?php 	} else{?>
								<option value="<?php echo $this->discount[$j]->id; ?>"><?php echo JText::_($this->discount[$j]->discount_name);?></option>
								<?php }?>
						
						<?php	}	?>
						</select>
					</span>
				</div>
                <?php	}	?>
				
                <div class="item_button">
				<a class="remove btn" itId="<?php echo $items->id; ?>"  href="javascript:void()"><span class="loadingbox" style="display:none;"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/load.gif" />' ?></span><i class="fa fa-remove"></i></a>
                </div>
                <div id="<?php echo $items->id; ?>"></div>
                <input type="hidden" class="item_id" name="item_id[]" value="<?php echo $items->id; ?>" />
                
            </div>
            <?php } ?>
            </td>
            
		</tr>
		
		<tr>
			<td colspan="0">
        		<input type="button" id="addcustom" value="<?php echo JText::_('ADD_CUSTOM'); ?>" class="btn btn-success" style="margin-bottom:10px" />
            </td>
			
			<td id="custom-item">
				<?php 
				$c = 0;
				foreach($this->custom_item as $key => $items ) {
				$c++;
			?>
			
			<div class="custom-line">
				<div class="custom-title-block">
                	<span class="custom_title"><?php echo JText::_('TITLE'); ?>: </span>
                    <span class="custom_value"><input class="item_title_value" type="text" value="<?php echo $items->title; ?>" name="custom_title[]"></span>
                </div>
				
				<div class="custom-amount-block">
					<span class="custom_amount"><?php echo JText::_('AMOUNT'); ?>: </span>
					<span class="custom_amount_value"><input class="custom_amount_val" type="text" value="<?php echo $items->amount; ?>" name="custom_amount[]" autocomplete="off"></span>
				</div>
				
				<div class="custom-quantity-block">
					<span class="custom_quantity"><?php echo JText::_('QUANTITY'); ?>: </span>
					<span class="custom_quantity_value"><input class="custom_quantity_val" type="text" value="<?php echo $items->quantity; ?>" name="custom_quantity[]" autocomplete="off"></span>
				</div>
				
				<?php if($this->config->enable_tax_discount==1){ ?>
                <div class="custom-tax-block">
                	<span class="tax_label"><?php echo JText::_('TAX'); ?>: </span>
					<span class="sel-tax">
						<select class="tax" name="custom_tax['cust<?php echo $c; ?>'][]" multiple="multiple" >
						<?php	for($j=0;$j<count($this->tax);$j++)	{	?>
							<option value="<?php echo $this->tax[$j]->id; ?>" <?php if(in_array($this->tax[$j]->id,$items->tax)) { echo 'selected="selected"';?>> <?php echo JText::_($this->tax[$j]->tax_name); ?> </option>
							<?php 	} else{?>
								<option value="<?php echo $this->tax[$j]->id; ?>"><?php echo JText::_($this->tax[$j]->tax_name);?></option>
								<?php }?>
						
						<?php	}	?>
						</select>
					</span>
				</div>
				
				<div class="custom-discount-block">
					<span class="discount_label"><?php echo JText::_('DISCOUNT'); ?>: </span>
					<span class="sel-dis">
						<select class="discount" name="custom_discount['cust<?php echo $c; ?>'][]" multiple="multiple" >
						<?php	for($j=0;$j<count($this->discount);$j++)	{	?>
							<option value="<?php echo $this->discount[$j]->id; ?>" <?php if(in_array($this->discount[$j]->id,$items->discount)) { echo 'selected="selected"';?>> <?php echo JText::_($this->discount[$j]->discount_name); ?> </option>
							<?php 	} else{?>
								<option value="<?php echo $this->discount[$j]->id; ?>"><?php echo JText::_($this->discount[$j]->discount_name);?></option>
								<?php }?>
						
						<?php	}	?>
						</select>
					</span>
				</div>
				<?php	}	?>
				
				<div class="custom_button"><a class="remCust btn btn-success" href="javascript:void();"><i class="fa fa-remove"></i> </a></div>
				<div id="cust_<?php echo $c; ?>"></div>
			</div>
			
            <?php } ?>
            </td>
		</tr>
		
        <?php } ?>
        
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->quotes->id; ?>" />
<input type="hidden" name="count" value="<?php echo $c; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="quotesexpense" />
<input type="hidden" name="tmpl" value="<?php echo $tmpl;?>" />
</form>
<?php if(!empty($this->quotes->id)) { ?> 
<div class="comment_section">
	<div class="comment_section_listing">
		<div class="discussion_title"></div>  
		<div class="discussion_messages">
					<?php 
					$userdetails = VaccountHelper::UserDetails();
					for($c = 0; $c<count($this->comments); $c++)
					{ 
				        $comment =  $this->comments[$c];     
						$userdetails = VaccountHelper::UserDetails($comment->created_by);
				    ?>
					<div class="discussion_message" id="discussion_message<?php echo $c+1;?>">
                    <span class="msg_imag"><a  href="<?php echo JRoute::_('index.php?option=com_vbizz&view=quotes');?>"><img alt="<?php echo $userdetails->name;?>" class="avatar" src="<?php echo JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($userdetails->profile_pic) && !empty($userdetails->profile_pic)?$userdetails->profile_pic:"profile.png");?>" title="<?php echo $userdetails->name;?>" width="96" height="96"></a></span>
					<span class="msg_detail_section">
<br>
					<span class="owner_name"><strong><?php echo $userdetails->name;?></strong></span><span class="write_msg"><?php echo $comment->msg;?></span><span class="msg_detail_post"><span class="datetime_label"><?php echo JText::_('POSTED_ON');?></span><?php echo VaccountHelper::calculate_time_span($comment->date);?></span> 	</span>				
					</div>	
					<?php }   
					$userdetails = VaccountHelper::UserDetails(); 
					?>
		</div>			
		</div>
  <div class="comment_section_add new">
    
	<span class="msg_imag"><a  href="<?php echo JRoute::_('index.php?option=com_vbizz&view=quotes');?>"><img alt="<?php echo $userdetails->name;?>" class="avatar" src="<?php echo JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($userdetails->profile_pic) && !empty($userdetails->profile_pic)?$userdetails->profile_pic:"profile.png");?>" title="<?php echo $userdetails->name;?>" width="96" height="96"></a></span>
	<span class="new_message_add_section"><div class="collapsed_content">
		<header class="text_entry no_shadow">
		  <div class="prompt" onclick="showMessageSection();" role="button"><?php echo JText::_('VACCOUNT_ADD_COMMENT_OR_UPLOAD_FILE');?></div>
		</header>
   </div>
	
	
	<div class="comment_section_add_msg">
	<?php  
	$editor = JFactory::getEditor('tinymce');
	echo $editor->display("comments_messages",  '', "300px", "200px", "5", "5",false, 'comments_messages', null, null, array('mode' => 'normal'));    
	?>  
	<textarea name="comments_message" class="comments_message" id="comments_message" cols="5" rows="5" style="width: 600px; height:300px;" ></textarea> 
	
     <div class="submit">
	  <button class="action_button green" onclick="AddMessageSection();" name="commit"><?php echo JText::_('VACCOUNT_ADD_CMMENTS');?></button>
	</div>  
</div>
</span>
</div>
</div>
<?php } ?> 
</div>
</div>
</div>