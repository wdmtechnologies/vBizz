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
				  data: {"option":"com_vbizz", "view":"ptask", "task":"addcomments", "tmpl":"component", "section":"ptask", "section_id":<?php echo (int)$this->ptask->id; ?>, "msg":tinymce.get('comments_message').getContent(), "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
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
             data: {"option":"com_vbizz", "view":"ptask", "task":"UpdateComments", "ptaskid":"<?php echo $this->ptask->id;?>", "tmpl":"component", "<?php echo JSession::getFormToken(); ?>":1, 'abase':1}
        }).done(function(resp){
            console.log('REFRESH');
            jQuery('.discussion_messages').replaceWith(resp.html);
            
            
        });
    }
	
jQuery(document).ready(function(){
	 setInterval("refreshDashboard()", 5000);
	 jQuery('.update_status').on('click', function () {
        jQuery.noConflict();  
        
		var status = jQuery(this).is(':checked'); 
        var jqxhr = jQuery.ajax({
            type: "POST",
			dataType: "json",
            url: "index.php",
             data: {"option":"com_vbizz", "view":"ptask", "task":"Updatestatus", "ptaskid":"<?php echo $this->ptask->id;?>", "status":status, "tmpl":"component", "<?php echo JSession::getFormToken(); ?>":1, 'abase':1}
        }).done(function(resp){
			if(status==true)
           jQuery('.ptask_details_msg').css('text-decoration','line-through');
		   console.log('UPDATED');
           
        });
    });
});	
</script>

<?php

$user = JFactory::getUser();

$input = JFactory::getApplication()->input;
// Checking if loaded via index.php or component.php
$tmpl = $input->getCmd('tmpl', '');

$db = JFactory::getDbo();

?>
	
<div class="content_part">
<div id="toolbar" class="btn-toolbar">

	<div class="btn-wrapper"  id="toolbar-arrow-left-4">
	<a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=ptask');?>" class="btn btn-small">
	<span class="fa fa-arrow-left"></span> <?php echo JText::_('BACK_TO_TASKS'); ?></a>
	</div>
	<div class="btn-wrapper prstatus"  id="toolbar-arrow-left-4">
	<label class="hasTip" title="<?php echo $this->ptask->status==1?JText::_('COM_VBIZZ_PROJECT_TAST_STATUS_COMPLETE'):JText::_('COM_VBIZZ_PROJECT_TAST_STATUS_ONGOING'); ?>"><?php echo JText::_('COM_VBIZZ_PROJECT_TAST_STATUS'); ?></label>
			<fieldset class="checkboxes">
			<li>
			<input type="checkbox" id="status" class="update_status" name="status" value="1" <?php if($this->ptask->status) echo 'checked="checked"'; ?> />
			<label for="status"></label>
			</li>
    </div>
	</div>
<div class="ptask_details">
        <div class="ptask_details_msg" <?php if($this->ptask->status) echo 'style="text-decoration:line-through;"'; ?>>
				 <h3><strong>
					 <?php echo $this->ptask->task_desc;?>
				</strong></h3>
		</div>
		<div class="ptask_details_status"></div> 
	    <div class="ptask_details_user">
			<span class="ptask_details_user_due_date">
				  <span class="ptask_details_user_due_date_label"></span>
				  <span class="ptask_details_user_due_date_value"></span>
			</span>
			<span class="ptask_details_user_assign">
				  <span class="ptask_details_user_assign_label"></span>
				  <span class="ptask_details_user_assign_value"></span>
			</span>
		</div>	
</div>
<div class="comment_section">
	<div class="comment_section_listing">
		<div class="discussion_title"><h4><?php echo JText::_('DISCUSSION_THIS_TASK');?></h4></div>  
		<div class="discussion_messages">
					<?php 
					$userdetails = VaccountHelper::UserDetails();
					for($c = 0; $c<count($this->comments); $c++)
					{ 
				        $comment =  $this->comments[$c]; 
						$userdetails = VaccountHelper::UserDetails($comment->created_by);
				    ?>
					<div class="discussion_message" id="discussion_message<?php echo $c+1;?>">
                    <span class="msg_imag"><a  href="<?php echo JRoute::_('index.php?option=com_vbizz&view=users');?>"><img alt="<?php echo $userdetails->name;?>" class="avatar" src="<?php echo JURI::root().'components/com_vbizz/uploads/profile_pics/'.(isset($userdetails->profile_pic) && !empty($userdetails->profile_pic)?$userdetails->profile_pic:"profile.png");?>" title="<?php echo $userdetails->name;?>" width="96" height="96"></a></span>
					<span class="msg_detail_section">
                    <br>
					<span class="owner_name"><strong><?php echo $userdetails->name;?></strong></span><br><span class="write_msg"><?php echo $comment->msg;?></span><span class="msg_detail_post"><span class="datetime_label"><?php echo JText::_('POSTED_ON');?></span><?php echo VaccountHelper::calculate_time_span($comment->date);?></span> 	</span>				
					</div>	
					<?php } 

$userdetails = VaccountHelper::UserDetails();					
					?>
		</div>			
		</div>
  <div class="comment_section_add new">
    
	<span class="msg_imag"><a  href="<?php echo JRoute::_('index.php?option=com_vbizz&view=quotes');?>"><img alt="<?php echo $userdetails->name;?>" class="avatar" src="<?php echo JURI::root().'components/com_vbizz/uploads/profile_pics/'.(isset($userdetails->profile_pic) && !empty($userdetails->profile_pic)?$userdetails->profile_pic:"profile.png");?>" title="<?php echo $userdetails->name;?>" width="96" height="96"></a></span>
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
	  <button class="action_button green" onclick="AddMessageSection();" name="commit">Add this comment</button>
	</div>  
</div>
</span>
</div>
</div>  
<?php

?>
