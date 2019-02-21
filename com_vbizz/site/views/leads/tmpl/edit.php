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


$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();   

//check acl for add, edit and delete access
$add_access = $this->config->leads_acl->get('addaccess');
$edit_access = $this->config->leads_acl->get('editaccess');
$delete_access = $this->config->leads_acl->get('deleteaccess');
$project_access = $this->config->project_acl->get('addaccess');
$invoice_access = $this->config->invoice_acl->get('addaccess');
$income_access = $this->config->income_acl->get('addaccess');
if($project_access) {
	$projectaccess = false;
	foreach($groups as $group) {
		if(in_array($group,$project_access))
		{
			$projectaccess=true;
			break;
		}
	}
} else {
	$projectaccess=true;
}
if($invoice_access) {
	$invoiceaccess = false;
	foreach($groups as $group) {
		if(in_array($group,$invoice_access))
		{
			$invoiceaccess=true;
			break;
		}
	}
} else {
	$invoiceaccess=true;
}
if($income_access) {
	$incomeaccess = false;
	foreach($groups as $group) {
		if(in_array($group,$income_access))
		{
			$incomeaccess=true;
			break;
		}
	}
} else {
	$incomeaccess=true;
}
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
	if( $this->leads->created_by == $user->id ) {
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


if($this->leads->userid) {
	$query = 'SELECT name from #__vbizz_users where userid='.$this->leads->userid;
	$db->setQuery( $query );
	$customer = $db->loadResult();
} else {
	$customer='';
}

$date = JFactory::getDate();
$curr_date = $date->format('Y-m-d');

if($this->leads->id)
{
	$quote_date = $this->leads->lead_date;
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
//var_dump($this->leads); exit;
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
				data: {"option":"com_vbizz", "view":"leads", "task":"getItemVal", "tmpl":"component", "id":id},
				
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
						
						jQuery("select").chosen({"disable_search_threshold":0,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
						
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
	
		var old_id = document.getElementById("userid").value;
		if (old_id != id) {
			document.getElementById("userid").value = id;
			document.getElementById("cust").value = name;
			document.getElementById("cust").className = document.getElementById("cust").className.replace(" invalid" , "");
		}
		SqueezeBox.close();
	
	}';
	

$document =  JFactory::getDocument();
$document->addScriptDeclaration($js);
$document->addScriptDeclaration($jscust);

$itemTitle = preg_replace('/\s+/', '', $this->leads->title);
$itemName = strtolower($itemTitle);
						
 ?>
<script type="text/javascript">


	
Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		var form = document.adminForm;
	
		
		if(form.amount.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_AMOUNT'); ?>");
			return false;
		}
		
		<?php if(!VaccountHelper::checkVenderGroup()) { ?>
		<?php if($this->config->enable_cust==1) { ?>
		if(form.userid.value == 0)	{
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


<?php if(!empty($this->leads->id)) { ?>       
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
				  data: {"option":"com_vbizz", "view":"leads", "task":"addcomments", "tmpl":"component", "section":"leads", "section_id":<?php echo (int)$this->leads->id; ?>, "msg":tinymce.get('comments_message').getContent(), "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
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
             data: {"option":"com_vbizz", "view":"leads", "task":"UpdateComments", "ptaskid":"<?php echo (int)$this->leads->id; ?>", "tmpl":"component", "<?php echo JSession::getFormToken(); ?>":1, 'abase':1}
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
		<h1 class="page-title"><?php echo isset($this->leads->id)&&$this->leads->id>0?JText::_('LEADEDIT'):JText::_('LEADNEW'); ?></h1>
	</div>
</header>
<div class="content_part">


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
                <?php } ?>
            <div class="btn-wrapper"  id="toolbar-cancel">
                <span onclick="Joomla.submitbutton('cancel')" class="btn btn-small">
                <span class="fa fa-close"></span> <?php echo JText::_('CLOSE'); ?></span>
            </div>  
			       <?php if($incomeaccess && $this->leads->id) { ?> 
						 <div class="btn-wrapper"  id="toolbar-mail">
						<span onclick="Joomla.submitbutton('create_sale');" class="btn btn-small">
						<span class="fa fa-plus"></span> <?php echo JText::_('COM_VBIZZ_CREATE_SALES_ORDER'); ?></span>
						</div>
						 <?php } ?>
						  <?php if($invoiceaccess && $this->leads->id) { ?>  
						 <div class="btn-wrapper"  id="toolbar-mail">
						<span onclick="Joomla.submitbutton('creat_invoice');" class="btn btn-small">
						<span class="fa fa-plus"></span> <?php echo JText::_('COM_VBIZZ_CREATE_INVOICE'); ?></span> 
						</div>
						 <?php } ?>
						  <?php if($projectaccess && $this->leads->id) { ?>
						 <div class="btn-wrapper"  id="toolbar-mail">
						<span onclick="Joomla.submitbutton('create_project');" class="btn btn-small">
						<span class="fa fa-plus"></span> <?php echo JText::_('COM_VBIZZ_CREATE_PROJECT'); ?></span>
						</div>
						 <?php } ?>
        </div>
    </div>
</div>
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=leads'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="overview">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'OVERVIEW' ); ?></legend>
	<ul>
		<li><?php	echo JText::_('NEW_LEADS_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">  
<fieldset class="adminform">
<legend><?php if($this->leads->id) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW'); ?></legend>
<table class="adminform table table-striped">



	<tbody>
		
        <tr>
            <th width="200"><label class="hasTip" title="<?php echo JText::_('QUOTETITLETXT'); ?>">
                <?php echo JText::_('TITLE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><input class="text_area" type="text" name="title" id="title" value="<?php echo $this->leads->title;?>"/></td>
        </tr>
		
		<tr>
        <th><label class="hasTip" title="<?php echo JText::_('QTYTXT'); ?>">
                <?php echo JText::_('QUANTITY'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><input class="text_area" type="text" name="quantity"  id="quantity" value="<?php echo $this->leads->quantity;?>"/></td>  
        </tr>
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('AMNTXT'); ?>">
                <?php echo JText::_('AMOUNT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>  
            </th>
            <td><input class="text_area" type="text" name="amount" id="amount" value="<?php echo $this->leads->amount;?>"/><?php echo ' '.$this->config->currency; ?></td>
        </tr>
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('COM_BIZZ_LEAD_SOURCE_TITLE_DESC'); ?>">
                <?php echo JText::_('COM_BIZZ_LEAD_SOURCE_TITLE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td>
			<?php 
			echo JHTML::_('select.genericlist', $this->lead_source_html, 'lead_source', 'class="inputbox" size="1"', 'value', 'text', $this->leads->lead_source );?>   
			</td>
        </tr>
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('COM_BIZZ_LEAD_INDUSTRY_TITLE_DESC'); ?>">
                <?php echo JText::_('COM_BIZZ_LEAD_INDUSTRY_TITLE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');
				?></label>
            </th>
            <td>
			<?php 
			
			echo JHTML::_('select.genericlist', $this->lead_industry_html, 'lead_industry', 'class="inputbox" size="1"', 'value', 'text', $this->leads->lead_industry);
			?>
			</td>
        </tr>
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('COM_BIZZ_LEAD_STATUS_DESC'); ?>">
                <?php echo JText::_('COM_BIZZ_LEAD_STATUS'); ?></label>
            </th>
           
			<td>
                <?php echo JHTML::_('select.genericlist', $this->lead_status_html, 'lead_status', 'class="inputbox" size="1"', 'value', 'text', $this->leads->lead_status); ?>
            </td>
			
        </tr>
		
		
		<?php if(VaccountHelper::checkOwnerGroup() && $this->config->enable_cust) { ?>
		<tr>
            <th><label class="hasTip" title="<?php echo sprintf ( JText::_( 'SELTERMDESCTXT' ), $this->config->customer_view_single); ?>">
                <?php echo $this->config->customer_view_single; ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td class="sel_customer"><input id="cust" type="text" readonly="" value="<?php if($customer){ echo $customer;} else {echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single);} ?>">
            <a class="btn btn-primary modal" id="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=customer&layout=modal&tmpl=component';?>" title="<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single); ?>">
            <i class="fa fa-user hasTip" title="<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single); ?>"></i>
            </a>
			</td>
            <input id="userid" type="hidden" value="<?php echo $this->leads->userid; ?>" name="userid" />
        </tr>
		<?php } ?>
		
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('DESCRIPTIONTXT'); ?>"><?php echo JText::_('DESCRIPTION'); ?></label></th>
            <td><textarea class="text_area" name="description" id="description" rows="4" cols="50"><?php echo $this->leads->description;?></textarea></td>
        </tr>
		
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('CUSTCMNTTXT'); ?>"><?php echo JText::_('CUSTOMER_NOTES'); ?></label></th>
            <td><textarea class="text_area" name="customer_notes" id="customer_notes" rows="4" cols="50"><?php echo $this->leads->customer_notes;?></textarea></td>
        </tr>
       
        
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->leads->id; ?>" />
<input type="hidden" name="count" value="<?php echo isset($c)?$c:0; ?>" /> 
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="leads" />
<input type="hidden" name="tmpl" value="" />
</form>
<?php if(!empty($this->leads->id)) { ?> 
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
                    <span class="msg_imag"><a  href="<?php echo JRoute::_('index.php?option=com_vbizz&view=leads');?>"><img alt="<?php echo $userdetails->name;?>" class="avatar" src="<?php echo JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($userdetails->profile_pic) && !empty($userdetails->profile_pic)?$userdetails->profile_pic:"profile.png");?>" title="<?php echo $userdetails->name;?>" width="96" height="96"></a></span>
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
    
	<span class="msg_imag"><a  href="<?php echo JRoute::_('index.php?option=com_vbizz&view=leads');?>"><img alt="<?php echo $userdetails->name;?>" class="avatar" src="<?php echo JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($userdetails->profile_pic) && !empty($userdetails->profile_pic)?$userdetails->profile_pic:"profile.png");?>" title="<?php echo $userdetails->name;?>" width="96" height="96"></a></span>
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