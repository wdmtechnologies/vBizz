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

$input = JFactory::getApplication()->input;
// Checking if loaded via index.php or component.php
$tmpl = $input->getCmd('tmpl', '');

$db = JFactory::getDbo();

$projectid = JRequest::getInt('projectid',0);

$query = 'SELECT * from #__vbizz_users where userid='.$this->invoices->customer;
$db->setQuery( $query );
$customer = $db->loadObject();

if($customer->state_id) {
	$query19 = 'select state_name from #__vbizz_states where id = '.$customer->state_id;
	$db->setQuery( $query19 );
	$state = $db->loadResult();
} else {
	$state = "";
}

if($customer->country_id) {
	$query21 = 'select country_name from #__vbizz_countries where id = '.$customer->country_id;
	$db->setQuery( $query21 );
	$country = $db->loadResult();
} else {
	$country = "";
}


$itemTitle = preg_replace('/\s+/', '', $this->invoices->project);
$itemName = strtolower($itemTitle);

$document =  JFactory::getDocument();  
$document->addStyleSheet(JURI::root().'components/com_vbizz/assets/css/vbizz.css');


$final_total = $this->invoices->amount - $this->invoices->discount_amount + $this->invoices->tax_amount;

//calculate total tax and discount
  $total_tax = array();
	$total_discount = array();
	$tex_total_details_value = array();
	$discount_total_details_value = array();
    $t_d_details = VaccountHelper::getDicountTaxValue($this->invoices->id);
    $tex_total_details_value = $t_d_details[0];
	$discount_total_details_value = $t_d_details[1];


$total_tax = array();
$total_discount = array();
for($s=0;$s<count($this->all_multi_item);$s++) {
	$discounts = $this->all_multi_item[$s]->discount;
	$taxs = $this->all_multi_item[$s]->tax;
	
	$discount_detail = array();
	for($k=0;$k<count($discounts);$k++)
	{
		
		$discountId = $discounts[$k];
		
		$query = 'select discount_value from #__vbizz_discount where published=1 and id='.$discountId;
		$db->setQuery($query);
		$discount_detail[] = $db->loadResult();

	}
	
	$total_discount[] = array_sum($discount_detail);
	
	$tax_detail = array();
	for($j=0;$j<count($taxs);$j++)
	{
		$taxId = $taxs[$j];
		
		$query = 'select tax_value from #__vbizz_tax where published=1 and id='.$taxId;
		$db->setQuery($query);
		$tax_detail[] = $db->loadResult();
		
	}
	
	$total_tax[] = array_sum($tax_detail);
}

$discount = array_sum($total_discount);
$tax = array_sum($total_tax);

//calculate all applicable tax and discount name
$all_discounts = array();
$all_taxs = array();
for($s=0;$s<count($this->all_multi_item);$s++) {
	$all_discounts[] = $this->all_multi_item[$s]->discount;
	$all_taxs[] = $this->all_multi_item[$s]->tax;
}

$all_discounts = array_filter($all_discounts);
$all_taxs = array_filter($all_taxs);

if(!empty($all_discounts)) {
	$all_discount = call_user_func_array("array_merge", $all_discounts);
	$all_discount = array_filter($all_discount);
	$applied_discount_id = array_values(array_unique($all_discount));
} else {
	$applied_discount_id = array();
}

if(!empty($all_taxs)) {
	$all_tax = call_user_func_array("array_merge", $all_taxs);
	$all_tax = array_filter($all_tax);
	$applied_tax_id = array_values(array_unique($all_tax));
} else {
	$applied_tax_id = array();
}



$discount_names = array();
for($i=0;$i<count($applied_discount_id);$i++) {
	
	$dId = $applied_discount_id[$i];
	$query = 'select discount_name from #__vbizz_discount where published=1 and id='.$dId;
	$db->setQuery($query);
	$discount_names[] = $db->loadResult();
}
$applicable_discount = implode(', ',$discount_names);

$tax_names = array();
for($i=0;$i<count($applied_tax_id);$i++) {
	
	$tax_id = $applied_tax_id[$i];
	$query = 'select tax_name from #__vbizz_tax where published=1 and id='.$tax_id;
	$db->setQuery($query);
	$tax_names[] = $db->loadResult();
}
$applicable_tax = implode(', ',$tax_names);
					
  ?>
<script>
function showMessageSection(){
	jQuery('.comment_section_add').addClass('expanded');
	jQuery('.collapsed_content').remove();
	jQuery('.editor').remove();
	tinyMCE.init({selector: "textarea",
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
function refreshDashboard() {
        jQuery.noConflict();
        
        var jqxhr = jQuery.ajax({
            type: "POST",
			dataType: "json",
            url: "index.php",
             data: {"option":"com_vbizz", "view":"invoices", "task":"UpdateComments", "section":"invoices", "ptaskid":"<?php echo (int)$this->invoices->id; ?>", "tmpl":"component", "<?php echo JSession::getFormToken(); ?>":1, 'abase':1}
        }).done(function(resp){
            console.log('REFRESH');
            jQuery('.discussion_messages').replaceWith(resp.html);
            
            
        });
    }
jQuery(document).ready(function(){  
	 setInterval("refreshDashboard()", 10000);
});
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

</script>
 

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=invoices&layout=detail&tmpl=component&cid[]='.$this->invoices->id.'&projectid='.$projectid); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">


<div class="col100">
<fieldset class="adminform">

<table class="adminform table table-striped">

<?php if($this->invoices->id) { ?>
<div class="v-pdf"><a href='<?php echo JURI::root() . 'components/com_vbizz/pdf/invoice/'.$itemName.$this->invoices->id.'invoice'.".pdf" ?>' class="pdf btn btn-success"  target="_blank"><label class="hasTip" title="<?php echo JText::_('PDFTXT'); ?>"><?php echo JText::_('DOWNLOAD_PDF'); ?></label></a></div>
<?php } ?>

</table>

<div class="invoicehtml"><?php echo $this->inoiceHtml;?></div>
<!--<table style="width: 100%;" border="0" width="100%">
<tbody>
	<tr bgcolor="#f2f2f2">
		<td>
			<table>
				<tbody>
					<tr><td></td><td></td><td></td></tr>
					<tr><td></td><td></td><td></td></tr>
				</tbody>
			</table>
			
			<table style="width: 100%;" border="0" width="100%">
				<tbody>
					<tr><td><img alt="" border="0" /></td><td align="right"> </td></tr>
					
					<tr bgcolor="#f2f2f2">
						<td><strong><?php echo JText::_('TITLE'); ?> : </strong><?php echo $this->invoices->project; ?></td>
						<td align="right"></td><td align="right"><?php echo $customer->name; ?></td>
					</tr>
					
					<tr bgcolor="#f2f2f2">
						<td><strong><?php echo JText::_('INVOICE_NO'); ?> : </strong><?php echo $this->invoices->invoice_number; ?></td>
						<td align="right"></td><td align="right"><?php echo $customer->address; ?></td>
					</tr>
					
					<tr bgcolor="#f2f2f2">
						<td><strong><?php echo JText::_('CUSTOMER_NOTES'); ?> : </strong><?php echo $this->invoices->customer_notes; ?></td>
						<td align="right"></td><td align="right"><?php echo $state; ?></td>
					</tr>
					
					
					<tr bgcolor="#f2f2f2"><td></td><td align="right"></td><td align="right"><?php echo $country; ?></td></tr>
					
					<tr bgcolor="#f2f2f2"><td></td><td align="right"></td><td align="right"><?php echo $customer->zip; ?></td></tr>

					
					<tr bgcolor="#f2f2f2"><td></td><td></td></tr>
				</tbody>
			</table>
			
			<table border="1" width="100%">
				<tbody>
					<tr bgcolor="#f2f2f2">
						
						<th><?php echo JText::_('ITEM'); ?></th>
						
						<th align="center"><?php echo JText::_('QUANTITY'); ?></th>
						
						<th align="center"><?php echo JText::_('ACTUAL_AMOUNT'); ?></th>
						
						<th align="center"><?php echo JText::_('FINAL_AMOUNT'); ?></th>
					</tr>
				
			
			<?php 
			
				
			for($i=0;$i<count($this->all_multi_item);$i++) {
				
				$row = $this->all_multi_item[$i];
			
				$final_amount = $row->amount - $row->discount_amount + $row->tax_amount;

			?>
			
					
			<tr bgcolor="#f2f2f2">
				<td align="center"><?php echo $row->title; ?></td>
				<td align="center" valign="top"><?php echo $row->quantity; ?></td>
				<td align="center" valign="top"><?php echo VaccountHelper::getValueFormat($row->amount); ?></td>
				<td align="center" valign="top"><?php 
				$config = VaccountHelper::getConfig();
				if($config->enable_items==1)
				    echo VaccountHelper::getValueFormat($row->amount*$row->quantity);
                else
					echo VaccountHelper::getValueFormat($row->amount);
           			?></td>
			</tr>			
																
			<?php } ?>
				<?php 
				$config = VaccountHelper::getConfig();  
				if(!$config->enable_items==1) { ?>
     	             <tr>
						<td valign="top"> </td>
						<td align="center"><strong><?php echo $this->invoices->quantity; ?></strong></td>
						<td align="center" valign="top"><?php echo VaccountHelper::getValueFormat($this->invoices->amount); ?></td>
						<td align="center" valign="top"><?php echo VaccountHelper::getValueFormat($this->invoices->amount); ?></td>
					</tr>
				<?php } ?>   
					 <tr>
						<td valign="top"> </td>
						<td align="right"><strong></strong></td>
						<td align="right" valign="top"><strong><?php echo JText::_('SUBTOTAL');?></strong></td>
						<td align="center" valign="top"><?php echo VaccountHelper::getValueFormat($this->invoices->amount); ?></td>
					</tr>
					<?php foreach($discount_total_details_value as $key => $value) { 
				       $d_detail = explode(':', $key);
				   
				     ?>
					<tr>
						<td valign="top"> </td>
						<td align="right"></td>
						<td align="right" valign="top"><?php echo $d_detail[0].' '.$d_detail[1].'%'; ?></td>
						<td align="center" valign="top"><?php echo VaccountHelper::getValueFormat($value); ?></td>
					</tr>
				<?php } ?>
				<?php foreach($tex_total_details_value as $key => $value) { 
				 $t_detail = explode(':', $key);
				?>
					<tr>
						<td valign="top"> </td>
						<td align="right"></td>
						<td align="right" valign="top"><?php echo $t_detail[0].' '.$t_detail[1].'%'; ?></td>
						<td align="center" valign="top"><?php echo VaccountHelper::getValueFormat($value); ?></td>
					</tr>
				<?php } ?>
				 <tr>
						<td valign="top"> </td>
						<td align="right"><strong></strong></td>
						<td align="right" valign="top"><strong><?php echo JText::_('TOTAL'); ?></strong></td>
						<td align="center" valign="top"><?php echo VaccountHelper::getValueFormat($final_total); ?></td>
					</tr>
</tbody>
			</table>
			
		</td>
	</tr>
</tbody>
</table>-->


</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->invoices->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="invoices" />
<input type="hidden" name="tmpl" value="component" />
</form>
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
	<textarea name="comments_message" id="comments_message" cols="5" rows="5" style="width: 600px; height:300px;" ></textarea> 
	
     <div class="submit">
	  <button class="action_button green" onclick="AddMessageSection();" name="commit"><?php echo JText::_('VACCOUNT_ADD_CMMENTS');?></button>
	</div>  
</div>
</span>
</div>
</div> 
</div>

<div class="copyright" align="center">
	<?php echo JText::_( 'WDM' );?>
</div>