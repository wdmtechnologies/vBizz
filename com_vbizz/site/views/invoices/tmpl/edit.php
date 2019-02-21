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
JHTML::_('behavior.calendar');
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.formvalidation');
		$db = JFactory::getDbo();
		$user = JFactory::getUser();      
      
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();

		//check acl for add, edit and delete access
		$add_access = $this->config->invoice_acl->get('addaccess');
		$edit_access = $this->config->invoice_acl->get('editaccess');
		$delete_access = $this->config->invoice_acl->get('deleteaccess');

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
$document = JFactory::getDocument();     
$document->addScript(JUri::root(true).'/components/com_vbizz/assets/js/account.js');
$input = JFactory::getApplication()->input;
// Checking if loaded via index.php or component.php
$tmpl = $input->getCmd('tmpl', '');



$project_id = JRequest::getInt('projectid',0);     
 
if($project_id) {
	$query = 'SELECT * from #__vbizz_projects where id='.$project_id;
	$db->setQuery( $query );
	$projects = $db->loadObject();
	//echo'<pre>';print_r($projects);
	
	$this->invoices->project = $projects->project_name;
}

if(isset($this->invoices->customer)) {  
	$query = 'SELECT name from #__vbizz_users where userid='.$this->invoices->customer;
	$db->setQuery( $query );
	$customer = $db->loadResult();
} else {
	$customer='';
}

$date = JFactory::getDate();
$curr_date = $date->format('Y-m-d');

if($this->invoices->id)
{
	$invoice_date = $this->invoices->invoice_date;
} else {
	$invoice_date = $curr_date;
}


$dhtml=$html = '';
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
$ownerid = VaccountHelper::getOwnerId();
$query = 'SELECT count(*) from #__vbizz_invoices where ownerid='.$db->quote($ownerid).' and `invoice_for`='.$db->quote( "income" );
$db->setQuery( $query );
$count_inv = $db->loadResult();


//get invoice number according to invoice number setting from configuration
$inv_setting = $this->config->invoice_setting;   

if($inv_setting==1){
	$chars = '0123456789';
	$length = 5;
	
	$chars_length = (strlen($chars) - 1);
	$inv_no = $chars {rand(0, $chars_length)};
	for ( $i = 1; $i < $length; $i = strlen($inv_no))
	{
		$r = $chars {rand(0, $chars_length)};
		if ($r != $inv_no {$i - 1})
			$inv_no .= $r;
	}
	if($this->invoices->id) {
		$inv = $this->invoices->invoice_number;
	} else {
		$inv = JText::_('INV').$inv_no;
	}
	$invoice_html = '<th><label>'.JText::_("INVOICE_NO").'</label></th><td><span>'.$inv.'</span><input type="hidden" name="invoice_number" value="'.$inv.'" /></td>';
	
}else if($inv_setting==2){
	$date = JFactory::getDate()->format('Ymd');
	
	if($count_inv==0)
	{
		$seq = $this->config->custom_invoice_seq;
	} else {
		$qry = 'SELECT invoice_number from #__vbizz_invoices where ownerid='.$db->quote($ownerid).' and `invoice_for`='.$db->quote( "income" ).' ORDER BY id DESC LIMIT 1';
		$db->setQuery( $qry );
		$last_invoice_number = $db->loadResult();
		$last_invoice_number = explode('/',$last_invoice_number);
		$seq = (int)$last_invoice_number[2] + 1;
		$seq = VaccountHelper::getCheckInvoice($seq,JText::_('INV')."/".$date."/");
		
	}

	if($this->invoices->id) {
		$inv = $this->invoices->invoice_number;
	} else {
		$inv = JText::_('INV')."/".$date."/".$seq;
	}
	
	$invoice_html = '<th><label>'.JText::_("INVOICE_NO").'</label></th><td><span>'.$inv.'</span><input type="hidden" name="invoice_number" value="'.$inv.'" /></td>';
}else if($inv_setting==3){
	$inv = $this->invoices->invoice_number;
	$invoice_html = '<th><label>'.JText::_("INVOICE_NO").'</label></th><td><input type="text" name="invoice_number" value="'.$inv.'" /></td>';
}else if($inv_setting==4){
	if($count_inv==0)
	{
		$seq = $this->config->custom_invoice_seq;
	} else {
		$qry = 'SELECT invoice_number from #__vbizz_invoices where ownerid='.$db->quote($ownerid).' and `invoice_for`='.$db->quote( "income" ).' ORDER BY id DESC LIMIT 1';
		$db->setQuery( $qry );
		$last_invoice_number = $db->loadResult();
		
		$last_invoice_number = str_replace(JText::_('INV'),"",$last_invoice_number);
		
		$seq = (int)$last_invoice_number + 1;
		$seq = VaccountHelper::getCheckInvoice($seq,JText::_('INV'));
	}
	if($this->invoices->id) {
		$inv = $this->invoices->invoice_number;
	} else {
		$inv = JText::_('INV').$seq;  
	}
	
	$invoice_html = '<th><label>'.JText::_("INVOICE_NO").'</label></th><td><span>'.$inv.'</span><input type="hidden" name="invoice_number" value="'.$inv.'" /></td>';
}else if($inv_setting==5){  
	if($count_inv==0)
	{
		$seq = $this->config->custom_invoice_seq;
	} else {    
		$cret = VaccountHelper::getUserListing().','.VaccountHelper::getVendorListing();
		$qry = 'SELECT invoice_number from #__vbizz_invoices where ownerid='.$db->quote($ownerid).' and `invoice_for`='.$db->quote( "income" ).' ORDER BY id DESC LIMIT 1';   
		$db->setQuery( $qry );
		$last_invoice_number = $db->loadResult();
		$last_invoice_number = str_replace($this->config->custom_invoice_prefix.'/',"",str_replace($this->config->custom_invoice_suffix.'/',"",$last_invoice_number));
		$seq = (int)$last_invoice_number + 1;  
		$seq = VaccountHelper::getCheckInvoice($seq,$this->config->custom_invoice_suffix,$this->config->custom_invoice_prefix);
		
		if($seq < (int)$this->config->custom_invoice_seq)
		{
		$seq = $this->config->custom_invoice_seq;	
		}
	}
	if($this->invoices->id) {
		$inv = $this->invoices->invoice_number;
	} else {
		$inv = $this->config->custom_invoice_prefix."/".$seq."/".$this->config->custom_invoice_suffix;
	}
	
	$invoice_html = '<th><label>'.JText::_("INVOICE_NO").'</label></th><td><span>'.$inv.'</span><input type="hidden" name="invoice_number" value="'.$inv.'" /></td>';
}

//echo'<pre>';print_r($this->invoices->tax);
?>

<?php 
$t_place = VaccountHelper::getThousandPlace();
$d_place = VaccountHelper::getDecimalPlace();
$js = '';
if(empty($t_place)) {
$js .=	'var thousand_formating = \'""\'';
}
else{ 
$js .= ' var thousand_formating = String('.VaccountHelper::getThousandPlace().');';	
}
if(empty($d_place))
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
				data: {"option":"com_vbizz", "view":"invoices", "task":"getItemVal", "tmpl":"component", "id":id},
				
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
						jQuery("input[name=actual_amount]").attr("readonly","readonly");
                        var ii =jQuery("div.multi-item").length;	
		
						var moreItem = jQuery("#more-item");  
						var newhtml = "<div class=multi-item><div class=title-block><span class=item_title>'.JText::_('TITLE').': </span><span class=item_value>"+data.itemtitle+ "</span></div><div class=amount-block><span class=item_amounts>'.JText::_('AMOUNT').': </span><span class=item_amount_value><input class=item_amount type=text autocomplete=off name=\'item_amount[]\' value="+accounting.formatNumber(data.amount,2,thousand_formating, decimal_formating)+" ></span></div><div class=quantity-block><span class=item_quantity>'.JText::_('QUANTITY').': </span><span class=item_quantity_value><input class=item_quantity type=text autocomplete=off name=item_quantity[] value="+accounting.formatNumber(1,2,thousand_formating, decimal_formating)+"><span style=color:#FF0000;>'.JText::_('AVAILABLE_QUANTITY').': "+data.stock+"</span></span></div>";';   
						if($this->config->enable_tax_discount==1)
						{     
		                 $js .= ' newhtml +="<div class=\"tax-block\"><span class=\"tax_label\">'.JText::_("TAX").': </span><span class=\"sel-tax\"><select class=\"tax\" name=\"tax["+data.itemid+"][]\" id=\"tax"+ii+"\"  multiple>'.$html.'</select></span></div><div class=\"discount-block\"><span class=\"discount_label\">'.JText::_('DISCOUNT').': </span><span class=\"sel-dis\"><select class=\"discount\" name=\"discount["+data.itemid+"][]\" id=\"discount"+ii+"\"  multiple>'.  $dhtml.'</select></span></div>";';        
		               }
						$js .= ' newhtml +="<div class=item_button><a class=remNew href=javascript:void() id=item_setting><i class=\"fa fa-remove\"></i></a></div><div id="+data.itemid+"></div><input type=hidden class=item_id name=item_id[] value="+data.itemid+" /><input type=hidden class=item_title name=item_title[] value="+data.itemtitle+" /></div>";
						
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
	
		var old_id = document.getElementById("customer").value;
		if (old_id != id) {
			document.getElementById("customer").value = id;
			document.getElementById("cust").value = name;
			document.getElementById("cust").className = document.getElementById("cust").className.replace(" invalid" , "");
		}
		SqueezeBox.close();
	
	}';
	
$jstask = '
		function getTask(id)
		{              
		
			jQuery.ajax(
			{
				url: "",
				type: "POST",
				dataType:"json",
				data: {"option":"com_vbizz", "view":"invoices", "task":"getProjectTask", "tmpl":"component", "id":id},
				
				success: function(data) 
				{
					if(data.result=="success"){
						
						if(jQuery("#"+data.itemid).length>0)
						{
							alert("'.JText::_('ALREADY_ADDED').'");
							SqueezeBox.close();
							return false;
						}
						var ii =jQuery("div.multi-item").length;							
						var moreItem = jQuery("#more-item");
						
						jQuery("<div class=multi-item><div class=title-block><span class=item_title>'.JText::_('TASK_DESC').': </span><span class=item_value>"+data.task_desc+ "</span></div><div class=amount-block><span class=item_amount>'.JText::_('AMOUNT').': </span><span class=item_amount_value><input class=item_amount type=text name=item_amount[] value= ></span></div><div class=quantity-block><span class=item_quantity>'.JText::_('QUANTITY').': </span><span class=item_quantity_value><input class=item_quantity type=text name=item_quantity[] value= ></span></div><div class=tax-block><span class=tax_label>'.JText::_('TAX').': </span><span class=sel-tax><select class=\"tax\" name=\"tax["+data.taskId+"][]\" id=\"tax"+ii+"\"  multiple>'.$html.'</select></span></div><div class=discount-block><span class=discount_label>'.JText::_('DISCOUNT').': </span><span class=sel-dis><select class=\"discount\" name=\"discount["+data.taskId+"][]\" id=\"discount"+ii+"\"  multiple>'.  $dhtml.'</select></span></div><div class=item_button><a class=\"remNew btn\" href=\"javascript:void();\" id=\"custom-setting\"><i class=\"fa fa-remove\"></i></a></div><div id="+data.taskid+"></div><input type=hidden class=task_id name=task_id[] value="+data.taskid+" /></div>").appendTo(moreItem);    
						
						jQuery("select").chosen({"disable_search_threshold":0,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
						
						jQuery(".remNew").addClass("btn");
						
						
					}    
					SqueezeBox.close();  
					
				}
				
			});
		
		}';

$document =  JFactory::getDocument();
$document->addScriptDeclaration($js);
$document->addScriptDeclaration($jscust);
$document->addScriptDeclaration($jstask);

$itemTitle = preg_replace('/\s+/', '', $this->invoices->project);
$itemName = strtolower($itemTitle);
						
 ?>
 <script>
 <?php if(!empty($this->invoices->id)) { ?>
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
				  data: {"option":"com_vbizz", "view":"invoices", "task":"addcomments", "tmpl":"component", "section":"invoices", "section_id":<?php echo (int)$this->invoices->id; ?>, "msg":tinymce.get('comments_message').getContent(), "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
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
             data: {"option":"com_vbizz", "view":"invoices", "task":"UpdateComments", "ptaskid":"<?php echo (int)$this->invoices->id; ?>", "tmpl":"component", "<?php echo JSession::getFormToken(); ?>":1, 'abase':1}
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
<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		
		var form = document.adminForm;
	
		/* if(form.project.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_PROJECT'); ?>");
			return false;
		}
		if(form.invoice_date.value == "")	{
			alert("<?php echo JText::_('PLZ_SELECT_INVOICE_DATE'); ?>");
			return false;
		}
		if(form.transaction_type.value == "")	{
			alert("<?php echo sprintf ( JText::_( 'ERRSELTERMTXT' ), $this->config->type_view_single); ?>");
			return false;
		} */
		/* <?php if($this->config->enable_cust==1 &&(VaccountHelper::checkOwnerGroup() || VaccountHelper::checkEmployeeGroup())) { ?>
		if(form.customer.value == "")	{
			alert("<?php echo sprintf ( JText::_( 'ERRSELTERMTXT' ), $this->config->customer_view_single); ?>");
			return false;
		}
	<?php } ?> */
	/* if(form.quantity.value == "" || form.quantity.value == 0)	{
			alert("<?php echo JText::_('PLZ_ENTER_QUANTITY'); ?>");
			return false;
		} */
        /*  */
		if(typeof(validateit) == 'function')	{
			
			if(!validateit())
				return false;
		}
		 var valids = document.formvalidator.isValid(document.id('adminForm'));
		
		if(valids)
		{
		if(parseInt(jQuery('div.multi-item').length) == 0 && parseInt(jQuery('div.custom-line').length) == 0)	{
			alert("<?php echo JText::_('PLZ_ENTER_ITEMS_ALERT'); ?>");
			jQuery('label.add_custom_information').addClass('invalid');
			return false;
		}
        jQuery('label.add_custom_information').removeClass('invalid');		
		Joomla.submitform(task, document.getElementById('adminForm'));	
		}
		
	}
}

jQuery(document).ready(function(){
	var invoice_date = '';
	jQuery(document).on('click','.remNew',function()
	{   
	var section = jQuery(this).attr('id')=='item_setting'?'multi-item':'custom-line';
	removeItemSection(this, section);  
	
});
	window.addEvent('domready', function(){  
		
		document.formvalidator.setHandler('datevalidate', function(value) {
		  var timestamp=Date.parse(value);
          invoice_date = timestamp;
		if (isNaN(timestamp)==true)
		{
		return false;

		}
		else
		{
			return true;
		}
		
		});
		document.formvalidator.setHandler('daterangevalidate', function(value) {
		var timestamp=Date.parse(value);
		var invoice_due = new Date(timestamp);
		var invoice_date = new Date(invoice_date);
		if (isNaN(timestamp)==true)
		{
		return false;

		}
		else
		{
			if(invoice_due>invoice_date)
			{
			
			return false;	
			}
			else
			return true;
		}
		
		});
	});
	if(parseInt(jQuery('#status').val())>0)
	{
		jQuery('.account_id, .mid').addClass('required');
		jQuery('.paid_status').show();	
	}
	else
	{
	jQuery('.paid_status').hide();	
	}
	jQuery('#status').on('change', function(){
		if(parseInt(jQuery(this).val())>0){
		jQuery('.account_id, .mid').addClass('required');
        jQuery('.paid_status').show();
		}
	    else
		{
		jQuery('.account_id, .mid').removeClass('required');
		jQuery('.paid_status').hide();		
		}
		
	});
	
});
function removeItemSection(removeSectionItems,section)
	{
	var qty = accounting.unformat(jQuery('input[name="quantity"]').val(),decimal_formating);
	var inputname = section=='item'?'item_quantity':'custom_quantity_val';
	var inputamount = section=='item'?'item_amount':'custom_amount_val';
	var section_name = section=='item'?'multi-item':'custom-line';
	var itemQty = accounting.unformat(jQuery(removeSectionItems).parents('.'+section).find('input.'+inputname).val(),decimal_formating);
	if(parseFloat(itemQty)==0) {
		itemQty = 0;
	}
	
	var new_qauntity = (parseFloat(qty)) - (parseFloat(itemQty));
	jQuery('input[name="quantity"]').val(accounting.formatNumber(new_qauntity,2,thousand_formating,decimal_formating));
	
	
	var amount = accounting.unformat(jQuery('input[name="actual_amount"]').val(),decimal_formating);
	var itemAmt = accounting.unformat(jQuery(removeSectionItems).parents('.'+section).find('input.'+inputamount).val(),decimal_formating);
	
	var new_amount = (parseFloat(amount)) - (parseFloat(itemAmt));
	jQuery('input[name="actual_amount"]').val(accounting.formatNumber(new_amount,2, thousand_formating, decimal_formating));
	
	
	
	jQuery(removeSectionItems).parents('.'+section).remove();
	
	var quantity = 0;
	var newItmAmt = 0;	
	}
jQuery(document).ready(function(){
	
jQuery(document).on('click','.remove',function() {
	
	var itemid = jQuery(this).attr('itid');
	var transaction_id = '<?php echo $this->invoices->id; ?>';
	var that=this;
	
	jQuery.ajax(
	{
		url: "",
		type: "POST",
		dataType:"json",
		data: {"option":"com_vbizz", "view":"invoices", "task":"removeItem", "tmpl":"component", "itemid":itemid, "transaction_id":transaction_id},
		
		beforeSend: function() {
			jQuery(that).find("span.loadingbox").show();
		},
		
		complete: function()      {
			jQuery(that).find("span.loadingbox").hide();
		},
		
		success: function(data) 
		{
			if(data.result=="success"){  
				var section = 'item'; 
				var qty = accounting.unformat(jQuery('input[name="quantity"]').val(),decimal_formating);
				var inputname = section=='item'?'item_quantity':'custom_quantity_val';
				var inputamount = section=='item'?'item_amount':'custom_amount_val';
				var section_name = section=='item'?'multi-item':'custom-line';
				var itemQty = accounting.unformat(jQuery(that).parents('.'+section).find('input.'+inputname).val(),decimal_formating);
				if(parseFloat(itemQty)==0) {
				itemQty = 0;
				}

				var new_qauntity = (parseFloat(qty)) - (parseFloat(itemQty));
				jQuery('input[name="quantity"]').val(accounting.formatNumber(new_qauntity,2,thousand_formating,decimal_formating));


				var amount = accounting.unformat(jQuery('input[name="actual_amount"]').val(),decimal_formating);
				var itemAmt = accounting.unformat(jQuery(that).parents('.'+section).find('input.'+inputamount).val(),decimal_formating);

				var new_amount = (parseFloat(amount)) - (parseFloat(itemAmt));
				jQuery('input[name="actual_amount"]').val(accounting.formatNumber(new_amount,2, thousand_formating, decimal_formating));
				jQuery(that).parents('.multi-item').remove();
				
				
			} 
		}
		
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
		if (c_amount != 0) {
			customAmt += (parseFloat(c_amount)) * (parseFloat(itQt));
		}
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
		if(parseFloat(allAmount)>0)
		newItmAmt += (parseFloat(allAmount)) * (parseInt(itmQty));
		
		if (itmQty > 0) {
			quantity += (itmQty);
		}
	});
  
	jQuery('input[name="actual_amount"]').val(accounting.formatNumber(newItmAmt,2,thousand_formating,decimal_formating));
	jQuery('input[name="quantity"]').val(accounting.formatNumber(quantity,2, thousand_formating, decimal_formating));
	
});
jQuery(document).on('change','input.item_amount',function() {
	jQuery(this).val(accounting.formatNumber(accounting.unformat(jQuery(this).val(),decimal_formating),2, thousand_formating, decimal_formating));
	var totalamount = 0;
	var customQnt = 0;
	jQuery("#more-item").find('input.item_amount').each(function(){
		
		var itmQty = parseInt(accounting.unformat(jQuery(this).parents('.multi-item').find('input.item_quantity').val(),decimal_formating));
		if(itmQty==0|| itmQty=="") { 
			itmQty = 0;
		}
		var allAmount = parseFloat(accounting.unformat(jQuery(this).val(),decimal_formating));
		if (allAmount> 0) {
			totalamount += (parseFloat(allAmount) *itmQty);
		}
		customQnt += itmQty;
	});
	
	jQuery("#custom-item").find('input.custom_quantity_val').each(function(){  
			
			var itmQty = parseFloat(accounting.unformat(jQuery(this).val(),decimal_formating));
			if(itmQty==0 || itmQty=="" ) {      
				itmQty = 0;
			}
			var allAmount = parseFloat(accounting.unformat(jQuery(this).parents('.custom-line').find('input.custom_amount_val').val(),decimal_formating));
			
			if (allAmount> 0) {
				totalamount += (allAmount * itmQty);
			}
			customQnt += itmQty;
		});
	
	  jQuery('input[name="actual_amount"]').val(accounting.formatNumber(totalamount,2,thousand_formating,decimal_formating));
	jQuery('input[name="quantity"]').val(accounting.formatNumber(customQnt,2, thousand_formating, decimal_formating));
});
});
jQuery(function() {  
	var i = jQuery('input[name="count"]').val();
	jQuery(document).on('click','#addcustom',function() {
		
		var customItem = jQuery("#custom-item");
		var i = jQuery('div.custom-line').length;	
		var newhtml = '<div class="custom-line"><div class="custom-title-block"><span class="custom_title"><label id="custom_title-msg" for="custom_title'+i+'" class="hasTip" title="<?php echo JText::_('TITLE'); ?>"><?php echo JText::_('TITLE'); ?>: </label></span><span class="custom_value"><input type="text" class="item_title_value required" name="custom_title[]" id="custom_title'+i+'" value="" /></span></div><div class="custom-amount-block"><span class="custom_amount"><label id="custom_amount-msg" for="custom_amount'+i+'" class="hasTip" title="<?php echo JText::_('AMOUNT'); ?>"><?php echo JText::_('AMOUNT'); ?>: </label></span><span class="custom_amount_value"><input type="text" autocomplete="off" class="custom_amount_val required" id="custom_amount'+i+'" name="custom_amount[]" value="" /><?php echo ' '.$this->config->currency; ?></span></div><div class="custom-quantity-block"><span class="custom_quantity"><label id="custom_quantity-msg" for="custom_quantity'+i+'" class="hasTip" title="<?php echo JText::_('AMOUNT'); ?>"><?php echo JText::_('QUANTITY'); ?>: </label></span><span class="custom_quantity_value"><input type="text"  autocomplete="off" class="custom_quantity_val required" name="custom_quantity[]" id="custom_quantity'+i+'" value="" /></span></div>';  
        <?php  if($this->config->enable_tax_discount==1){ ?>
		newhtml += '<div class="custom-tax-block"><span class="tax_label"><?php echo JText::_("TAX");?>: </span><span class=sel-tax><select class="tax" name="custom_tax['+i+'][]" id="custom_tax'+i+'"  multiple><?php echo $newcustom_tax;?></select></span></div><div class="custom-discount-block"><span class="discount_label"><?php echo JText::_('DISCOUNT');?>: </span><span class=sel-dis><select class="discount" name="custom_discount['+i+'][]" id="custom_discount'+i+'"  multiple><?php echo $newcustom_discount;?></select></span></div>';
		<?php } ?>    		
		
		newhtml += '<div class="custom_button"><a class="remNew btn" href="javascript:void();" id="custom-setting"><i class="fa fa-remove"></i> </a></div><div id="cust_'+i+'"></div></div>';
		
		jQuery(newhtml).appendTo(customItem);
		
		jQuery("select").chosen({"disable_search_threshold":0,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});    
	}); 
	jQuery(document).on('change','input.custom_amount_val',function() { 
		jQuery(this).val(accounting.formatNumber(accounting.unformat(jQuery(this).val(),decimal_formating),2, thousand_formating, decimal_formating));
		var itemAmt = 0;
		var itemQnt = 0;
		jQuery("#more-item").find('input.item_quantity').each(function(){     
			
			var itQt = parseFloat(accounting.unformat(jQuery(this).val(),decimal_formating));
			
			
			if(itQt==0 || itQt=="") {
				itQt = 0;
			}
			
			var allAmount = parseFloat(accounting.unformat(jQuery(this).parents('.more-item').find('input.item_amount').val(),decimal_formating));
			if(allAmount>0)
			itemAmt += (allAmount * itQt); 
		    
			
			itemQnt += itQt;
		});
		
		var totalamount = itemAmt;
		
		jQuery("#custom-item").find('input.custom_amount_val').each(function(){
			var allQt = jQuery(this).parents('.custom-line').find('input.custom_quantity_val').val(); 
			allQt = accounting.unformat(allQt,decimal_formating);
			if(allQt==0 || allQt=="") {
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
	});
	
	jQuery('.radio_button.btn-group label').addClass('btn');

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
</script>
<div id="system-message-container" style="width:78%;float:left;"></div>
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo isset($this->invoices->id)&&$this->invoices->id>0?JText::_('INVOICEEDIT'):JText::_('INVOICENEW'); ?></h1>
	</div>
</header>  

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=invoices'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

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
					<?php if($user->id!=$this->invoices->created_by && empty($this->invoices->approved) && $this->invoices->id>0) { ?>
					  <div class="btn-wrapper"  id="toolbar-mailing">
						<span onclick="Joomla.submitbutton('approve')" class="btn btn-small">
						  
						    <span class="fa fa-check-square-o"></span> <?php echo JText::_('APPROVE'); ?>
						  
						  </span>  
					 </div>
				  <?php } ?>
					<?php if($this->invoices->id) { ?>
					<div class="btn-wrapper"  id="toolbar-pdf">
						<a class="modal btn btn-small" id="modal1" title="Select" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=invoices&task=print_bill&tmpl=component&cid[]='.$this->invoices->id; ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><span class="fa fa-plus"></span> <?php echo JText::_('PRINT_BILL'); ?></span></a>
						
					</div>
				
					<div class="btn-wrapper"  id="toolbar-mailing">
						<span onclick="Joomla.submitbutton('mailing')" class="btn btn-small">
						<span class="fa fa-envelope-o"></span> <?php echo JText::_('EMAIL'); ?></span>
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
		<li><?php	echo JText::_('NEW_INVOICES_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">   
<fieldset class="adminform">
<legend><?php if($this->invoices->id) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW'); ?></legend>
<table class="adminform table table-striped">

<?php if($this->invoices->id) { ?>
<div class="v-pdf"><a href='<?php echo JURI::root() . 'components/com_vbizz/pdf/invoice/'.$itemName.$this->invoices->id.'invoice'.".pdf" ?>' class="pdf btn"  target="_blank"><label class="hasTip" title="<?php echo JText::_('PDFTXT'); ?>"><i class="fa fa-download"></i> <?php echo JText::_('DOWNLOAD_PDF'); ?></label></a></div>
<?php } ?>

	<tbody>
		
		<tr>
			<?php echo $invoice_html; ?>
		</tr>  
        <tr>
            <th width="200">
			<label id="project-msg" for="project" class="hasTip" title="<?php if($project_id){echo JText::_('PROJECTTXT');}else{echo JText::_('INVOICETTXT');} ?>">
                <?php if($project_id)
				{
					echo JText::_('PROJECT');
				}
				else
				{ 
				echo JText::_('INVOICETTXT');
				}  echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><input class="text_area required" type="text" name="project" id="project" value="<?php echo $this->invoices->project;?>"/></td>
        </tr>
        
        <tr>
            <th><label id="invoice_date-msg" for="invoice_date" class="hasTip" title="<?php echo JText::_('INVOICEDATETXT'); ?>">
                <?php echo JText::_('DATE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><?php echo JHTML::_('calendar', $invoice_date, "invoice_date" , "invoice_date", VaccountHelper::DateFormat_javascript($this->config->date_format), ' class="required validate-datevalidate"'); ?></td>
        </tr>
		
		<tr>
            <th><label id="due_date-msg" for="due_date" class="hasTip" title="<?php echo JText::_('DUEONTXT'); ?>"><?php echo JText::_('DUE_ON'); ?></label></th>
            <td><?php echo JHTML::_('calendar', $this->invoices->due_date, "due_date" , "due_date", VaccountHelper::DateFormat_javascript($this->config->date_format), ' class="required validate-daterangevalidate"'); ?></td>
        </tr>
        <tr>
            <th><label id="quantity-msg" for="quantity" class="hasTip" title="<?php echo JText::_('QTYTXT'); ?>">
                <?php echo JText::_('QUANTITY'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><input class="text_area required" type="text" name="quantity" readonly="readonly" id="quantity" value="<?php echo $this->invoices->quantity;?>"/></td>
        </tr>
        
        <tr>
            <th><label id="actual_amount-msg" for="actual_amount" class="hasTip" title="<?php echo JText::_('AMNTXT'); ?>">
                <?php echo JText::_('ACTUAL_AMOUNT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><input class="text_area required" type="text" name="actual_amount" id="actual_amount" readonly="readonly" value="<?php if($this->invoices->tax_inclusive==1){echo $this->invoices->amount+$this->invoices->tax_amount;} else {echo $this->invoices->amount;}?>"/><?php echo ' '.$this->config->currency; ?></td>  
        </tr>
        <tr>
            <th><label id="transaction_type-msg" for="transaction_type" class="hasTip" title="<?php echo printf ( JText::_( 'SELTRTYPEDESCTXT' ), $this->config->type_view_single); ?>">
                <?php echo $this->config->type_view_single; ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td>
                <select name="transaction_type" id="transaction_type" class="required">
                <option value=""><?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->type_view_single); ?></option>
                <?php	for($i=0;$i<count($this->type);$i++)	{	?>
                <option value="<?php echo $this->type[$i]->id; ?>" <?php if($this->type[$i]->id==$this->invoices->transaction_type) echo 'selected="selected"'; ?>> <?php echo JText::_($this->type[$i]->treename); ?> </option>
                <?php	}	?>
                </select> 
            </td>
        </tr>  
				
		<tr>
            <th width="200"><label class="hasTip" title="<?php echo JText::_('REFTXT'); ?>"><?php echo JText::_('REF'); ?></label></th>
            <td><input class="text_area" type="text" name="ref_no" id="ref_no" value="<?php echo $this->invoices->ref_no;?>"/></td> 
        </tr>
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
			echo '<option value="'.$value->id.'"'.($value->id==$this->invoices->salesman?' selected="selected"':'').'>'.$value->name.'</option>';
			?></select></td>
		<?php }
				  if(VaccountHelper::checkEmployeeGroup())
				  { ?>  
			    <input type="hidden" name="salesman" value="<?php echo $userId;?>"> <?php }  } ?>
		<?php if($this->config->enable_cust==1 &&(VaccountHelper::checkOwnerGroup() || VaccountHelper::checkEmployeeGroup())) { ?>    
		<tr>
            <th><label id="customer-msg" for="customer" class="hasTip" title="<?php echo sprintf ( JText::_( 'SELTERMDESCTXT' ), $this->config->customer_view_single); ?>">
                <?php echo $this->config->customer_view_single; ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td class="sel_customer">
			<input id="cust" type="text" readonly="" value="<?php if($customer){ echo $customer;} else {echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single);} ?>">
            <a class="btn btn-primary modal" id="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=customer&layout=modal&tmpl=component&for=income';?>" title="<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single); ?>">
            <i class="fa fa-user hasTip" title="<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single); ?>"></i>
            </a>
			<input id="customer" class="required" type="hidden" value="<?php echo $this->invoices->customer; ?>" name="customer" />
			</td>
            
        </tr>
		 <?php } ?>
		<?php if($this->config->enable_tax_discount==1){ ?>
		<tr>
			<th><label class="hasTip" title="<?php echo JText::_('TAXINCTXT'); ?>"><?php echo JText::_('TAX_INCL'); ?></label></th>
			<td>
				<fieldset class="radio_button btn-group" style="margin-bottom:9px;">
				<label for="tax_inclusive1" id="tax_inclusive-lbl" class="radio"><?php echo JText::_('YS'); ?></label>
				<input type="radio" name="tax_inclusive" id="tax_inclusive1" value="1" <?php if($this->invoices->tax_inclusive) echo 'checked="checked"';?> />
				<label for="tax_inclusive0" id="tax_inclusive-lbl" class="radio"><?php echo JText::_('NOS'); ?></label>
				<input type="radio" name="tax_inclusive" id="tax_inclusive0" value="0"  <?php if(!$this->invoices->tax_inclusive) echo 'checked="checked"';?>/>
				</fieldset>
			</td>
		</tr>
		<?php } ?> 
		
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('AMOUNTSTATTXT'); ?>"><?php echo JText::_('AMOUNT_STATUS'); ?></label></th>
            <td>
                <select name="status" id="status">
                <option value=""><?php echo JText::_('SELECT_AMOUNT_STATUS'); ?></option>
                <option value="1" <?php if($this->invoices->status==1) echo 'selected="selected"'; ?>><?php echo JText::_('PAID'); ?></option>
                <option value="0" <?php if($this->invoices->status==0) echo 'selected="selected"'; ?>><?php echo JText::_('UNPAID'); ?></option>
                </select>
            </td>
        </tr>
		<tr class="paid_status">
            <th><label id="mid-msg" for="mid" class="hasTip" title="<?php echo JText::_('MODTXT'); ?>">
                <?php echo JText::_('TRANSACTION_MODE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td>
                <select name="mid" id="mid" class="mid">
                <option value=""><?php echo JText::_('SELECT_TRANSACTION_MODE'); ?></option>
                <?php	for($i=0;$i<count($this->mode);$i++)	{	?>
                <option value="<?php echo $this->mode[$i]->id; ?>" <?php if($this->mode[$i]->id==$this->invoices->mid) echo 'selected="selected"'; ?>> <?php echo JText::_($this->mode[$i]->title); ?> </option>
                <?php	}	?>
                </select>
            </td>
        </tr>
		<?php if($this->config->enable_account==1) { ?>
        <tr class="paid_status">
            <th><label class="hasTip" id="account_id-msg" for="account_id" title="<?php echo JText::_('SELECTACCTXT'); ?>"><?php echo JText::_('SELECT_ACCOUNT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
            <td>
                <select name="account_id" id="account_id" class="account_id">
                <option value=""><?php echo JText::_('SELECT_ACCOUNT'); ?></option>
                <?php	for($i=0;$i<count($this->account);$i++)	{	?>
                <option value="<?php echo $this->account[$i]->id; ?>" <?php if($this->account[$i]->id==$this->invoices->account_id) echo 'selected="selected"'; ?>> <?php echo JText::_($this->account[$i]->account_name); ?> </option>
                <?php	}	?>
                </select>
            </td>
        </tr>
        <?php	}	?>
       <tr>
            <th><label class="hasTip" title="<?php echo JText::_('RCTUPLDTXT'); ?>"><?php echo JText::_('RECIEPT_UPLOAD'); ?></label></th>
            <td><input type="file" name="reciept" id="reciept" class="inputbox" size="50" value=""/>
			<?php if(isset($this->invoices->reciept)) {?>
                <a target="_blank" href="components/com_vbizz/uploads/reciept/<?php echo $this->invoices->reciept;?>"><?php echo $this->invoices->reciept;?></a>
			<?php } ?>
            </td>
        </tr>
        <tr>
        	<th colspan="0">
        		<?php if($project_id) { ?>
        		<a class="modal" id="modal1" title="Select" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=ptask&layout=modal&pro=inc&tmpl=component&projectid='.$project_id ;?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
        		<button id="addnew" class="btn btn-success">
				<i class="fa fa-plus"></i> <?php echo JText::_('ADD_TASK'); ?>
				</button>
                </a>
                <?php } else { ?>
                 <?php 
				   
				 if($this->config->enable_items==1) { ?>
            	<a class="modal" id="modal1" title="Select" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=items&layout=modal&pro=inc&tmpl=component';?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
        		<button id="addnew" class="btn btn-success">
				<i class="fa fa-plus"></i> <?php echo sprintf ( JText::_( 'ADDITEMTXT' ), $this->config->item_view_single); ?>
				</button>
                </a>
                <?php } ?>
				 <?php } ?>
            </th>
			<td id="more-item">
			 
            <?php 
			for( $i=0; $i < count($this->multi_item); $i++ ) { 
				$items = $this->multi_item[$i]; 
				$quantity2 = 0;    
				if(empty($project_id)){
				if($items->quantity2==0) {
					$quantity2 = 0;//JText::_('UNLIMITED');
				} else {
					$quantity2 = $items->quantity2;
				}}
			?>
            <div class="multi-item">
                <div class="title-block">
                	<span class="item_title"><?php echo JText::_('TITLE'); ?>: </span>
                    <span class="item_value"><?php echo $items->title; ?></span>
                </div>
                <div class="amount-block">
                	<span class="item_amounts"><?php echo JText::_('AMOUNT'); ?>: </span>
					
						<span class="item_amount_value"><input type="text" class="item_amount" name="item_amount[]" value="<?php echo VaccountHelper::getNumberFormatValue($items->amt); ?>" /></span>
					
                </div>
                <div class="quantity-block">
                	<span class="item_quantitys"><?php echo JText::_('QUANTITY'); ?>: </span>
                	<span class="item_quantity_value">
                	<input class="item_quantity" type="text" autocomplete="off" name="item_quantity[]" value="<?php echo VaccountHelper::getNumberFormatValue($items->quant); ?>">
					<?php if(!$project_id && VaccountHelper::checkOwnerGroup()) { ?>
						<span style="color:#FF0000;"><?php echo JText::_('AVAILABLE_QUANTITY') ; ?> : <?php echo $quantity2; ?></span>
					<?php } ?>
                </span>
                </div>
                
				<?php 
				
				if($this->config->enable_tax_discount==1){ ?>
                <div class="tax-block">
                	<span class="tax_label"><?php echo JText::_('TAX'); ?>: </span>
					<span class="sel-tax">
						<select class="tax" name="tax[<?php echo $items->id; ?>][]" multiple="multiple" >
						<?php	for($j=0;$j<count($this->tax);$j++)	{	?>
							<option value="<?php echo $this->tax[$j]->id; ?>" <?php if(in_array($this->tax[$j]->id,$items->tax)) { echo 'selected="selected"';}?>> <?php echo JText::_($this->tax[$j]->tax_name); ?> </option>
						
						
						<?php	}	?>
						</select>
					</span>
				</div>
				
				<div class="discount-block">
					<span class="discount_label"><?php echo JText::_('DISCOUNT'); ?>: </span>
					<span class="sel-dis">
						<select class="discount" name="discount[<?php echo $items->id; ?>][]" multiple="multiple" >
						<?php	for($j=0;$j<count($this->discount);$j++)	{	?>
							<option value="<?php echo $this->discount[$j]->id; ?>" <?php if(in_array($this->discount[$j]->id,$items->discount)) { echo 'selected="selected"';}?>> <?php echo JText::_($this->discount[$j]->discount_name); ?> </option>
							
						
						<?php	}	?>
						</select>
					</span>
				</div>
				<?php	}	?>
                
                <div class="item_button">
					<a class="<?php if(!$project_id) { ?>remove<?php } else { ?>remNew<?php } ?> btn" itId="<?php echo $items->id; ?>" href="javascript:void();"><span class="loadingbox" style="display:none;"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/load.gif" />' ?></span><i class="fa fa-remove"></i></a>
				</div>
				
                <div class="itId" id="<?php echo $items->id; ?>"></div>  
				<?php if($project_id) { ?>
					<input type="hidden" class="task_id" name="task_id[]" value="<?php echo $items->id; ?>" />  
				<?php } else { ?>
					<input type="hidden" class="item_id" name="item_id[]" value="<?php echo $items->id; ?>" />
				<?php } ?>
                <input type="hidden" class="item_title" name="item_title[]" value="<?php echo $items->title; ?>" />
				
            </div>
            <?php } ?>  
            
            
          </td>		   
		</tr>
		
		<tr>
			<td colspan="0">
        		<label class="add_custom_information"><input type="button" id="addcustom" value="<?php echo JText::_('ADD_CUSTOM'); ?>" class="btn btn-success" /></label>
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
							<option value="<?php echo $this->discount[$j]->id; ?>" <?php if(in_array($this->discount[$j]->id,$items->discount)) { echo 'selected="selected"';}?>> <?php echo $this->discount[$j]->discount_name; ?> </option>  
						
						<?php	}	?>
						</select>
					</span>
				</div>
				<?php	}	?>
				
				<div class="custom_button"><a class="remCust btn btn-success" href="javascript:void();"><i class="fa fa-remove"></i> </a></div>
				<div id="cust_<?php echo $c; ?>"></div>
			</div>
			
            <?php $c++;} ?>  
            </td>
		</tr>
        
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->invoices->id; ?>" />
<input type="hidden" name="transaction_id" value="<?php echo $this->invoices->transaction_id; ?>" />
<input type="hidden" name="projectid" value="<?php echo $project_id; ?>" />
<input type="hidden" name="count" value="<?php echo $c; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="invoices" />
</form>
<?php if(!empty($this->invoices->id)) { ?>
<div class="comment_section">
	<div class="comment_section_listing">
		<div class="discussion_title"></div>  
		<div class="discussion_messages">
					<?php 
					
					for($c = 0; $c<count($this->comments); $c++)
					{ 
				        $comment =  $this->comments[$c]; 
						$userdetails = VaccountHelper::UserDetails($comment->created_by);
				    ?>
					<div class="discussion_message" id="discussion_message<?php echo $c+1;?>">
                    <span class="msg_imag"><a  href="<?php echo JRoute::_('index.php?option=com_vbizz&view=invoices');?>"><img alt="<?php echo $userdetails->name;?>" class="avatar" src="<?php echo JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($userdetails->profile_pic) && !empty($userdetails->profile_pic)?$userdetails->profile_pic:"profile.png");?>" title="<?php echo $userdetails->name;?>" width="96" height="96"></a></span>
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
    
	<span class="msg_imag"><a  href="<?php echo JRoute::_('index.php?option=com_vbizz&view=invoices');?>"><img alt="<?php echo $userdetails->name;?>" class="avatar" src="<?php echo JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($userdetails->profile_pic) && !empty($userdetails->profile_pic)?$userdetails->profile_pic:"profile.png");?>" title="<?php echo $userdetails->name;?>" width="96" height="96"></a></span>
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