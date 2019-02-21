<?php
/*------------------------------------------------------------------------
# com_vbizz - vBizz
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.wdmtech.com
# Technical Support: Forum - https://www.wdmtech.com/support-forum
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

$user = JFactory::getUser();
$document = JFactory::getDocument();
$sent_mail=JRequest::getInt('sent',0);
$type_mail=JRequest::getInt('type_mail');
$uuid=$this->item->message_id;
	 
	
	$script = array();
	$script[] = 'jQuery( document ).ready(function(){';
	if($type_mail==2){
		$script[] = "$('.showcc').css('display','');";
		$script[] = "$('.addcc').html('".JText::_('REMOVE_TO_CC')."')";
		$script[] = "$('#email_cc').val('".$this->item->cc."')";
	}
	if($sent_mail==1){
		$script[] = 'alert("'.JText::_('Mail_sent').'")';
		$script[] = 'window.parent.SqueezeBox.close();';
	}
	$script[] = '});';
	JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
				
	if(isset($this->item->attachments)){
		
		$attachment=array();
		$attachment =array(
		'is_attachment' =>1,
		'filename' => ''.$this->item->attachments_name.'',
		'name' => ''.$this->item->attachments_name.'',
		'attachment' => '');
		$filename = $attachment['name'];
		if($this->item->encoding == 3) 
		{ 
		$attachment['attachment'] = base64_decode($this->item->attachments_files_path);
		}
		elseif($this->item->encoding == 4) 
		{ 
		$attachment['attachment'] = quoted_printable_decode($this->item->attachments_files_path);
		}

		$fp = fopen("./" . $uuid . "-" . $filename, "w+");
		fwrite($fp, $attachment['attachment']);
		fclose($fp);
		file_put_contents($attachment['filename'], $attachment['attachment']);
		
	}
			
	 $text =trim(quoted_printable_decode($this->item->body_messge));
	 $mail_subject=$this->item->subject;
		 
	 if(isset($this->item->attachments) && $this->item->attachments == 1){

		$allowed = array('.jpg', '.jpeg', '.gif', '.png');
		$ext = strrchr($attachment['name'], '.');
		$text .='<div class="message_attachment">';
		$text .='<b>'.JText::_('ATTACHEMENT').'</b>';		
		$text .='<a href="'.$attachment['filename'].'" download="'.$attachment['filename'].'">'.$attachment['filename'].'</a>';
		if(in_array($ext, $allowed)){
		$text .='<br/><br/><img src="'.$attachment['filename'].'"/>';
		}
	 }
			
	if($type_mail==1 or $type_mail==2){
		$to_message=$this->item->from_email;  
	}else{
		$to_message='';
	}
	
?>
<script>
var t_ggle=false;
jQuery(document).on('click','.addcc',function() {
	    
 		if(t_ggle==true){
			$(this).html('<?php echo JText::_("ADD_TO_CC")?>');
			$('.showcc').css('display','none');
			t_ggle=false;
		}else{
			$(this).html('<?php echo JText::_("REMOVE_TO_CC")?>');
			$('.showcc').css('display','');
			$('#email_cc').val('');
			t_ggle=true;
		}
});
	  jQuery(document).on('click','.send_mail',function() {
			 
			if($('input[name="email_to"]').val()==''){
				alert('Please Enter mail To the Send.');
				$('input[name="email_to"]').focus();
				return false;
			} else
			{    
				Joomla.submitform();
			}
		});
	$( document ).ready(function() {
		$('input:file').change(function(e){ 
		var files = e.target.files;
		var file;
		for (var i = 0; i < files.length; i++) {
			file = files.item(i);
			file = files[i];
			$('.attach_file_text').html(file);
 
		}
		});
		
 
	var availableTags=<?php echo $this->allmailid ?>; 
		
		function split( val ) {
		  return val.split( /,\s*/ );
		}
		function extractLast( term ) {
		  return split( term ).pop();
		}
 
    $( ".mail_fill_list" )
      // don't navigate away from the field on tab when selecting an item
      .bind( "keydown", function( event ) {
        if ( event.keyCode === $.ui.keyCode.TAB &&
            $( this ).autocomplete( "instance" ).menu.active ) {
          event.preventDefault();
        }
      })
      .autocomplete({
        minLength: 0,
        source: function( request, response ) {
          // delegate back to autocomplete, but extract the last term
          response( $.ui.autocomplete.filter(
            availableTags, extractLast( request.term ) ) );
        },
        focus: function() {
          // prevent value inserted on focus
          return false;
        },
        select: function( event, ui ) {
          var terms = split( this.value );
          // remove the current input
          terms.pop();
          // add the selected item
          terms.push( ui.item.value );
          // add placeholder to get the comma-and-space at the end
          terms.push( "" );
          this.value = terms.join( ", " );
          return false;
        }
      });
	
	});
</script>

<form action="<?php JRoute::_('index.php?option=com_vbizz&view=mail');?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div class="main-mail-header">
		<label class="header_unread min_header_bar">
			<input type="submit" title="Send Mail" class="send_mail btn" value="<?php echo JText::_("SEND");?>">
		</label>
		<label class="header_unread min_header_bar">
			<label><?php echo JText::_('ATTACHE_FILE')?></label>
			<input type="file" title="Attach File" name="attach_file[]" multiple="multiple" value="">
			<label style="color:#fff;" class="attach_file_text"></label>
		</label>
	</div>
	<table class="adminform table table-striped">
		<tbody id="mail-sent">
			<tr>
			<th><label><?php echo JText::_('FROM'); ?></label></th>
			<td><?php echo $user->email;?></td>
			</tr>
			<tr>
			<th><label><?php echo JText::_('TO'); ?></label></th>
			<td><input class="text_area mail_fill_list" type="text" name="email_to" id="email_to" style="width: 500px;" value="<?php echo $to_message;?>"  />
			<a href="javascript:void(0)" class="addcc"><?php echo JText::_("CC");?></a>
			</td>
			</tr>
			<tr class="showcc" style="display:none;">
			<th><label><?php echo JText::_('Cc'); ?></label></th>
			<td><input class="text_area mail_fill_list" type="text" name="email_cc" id="email_cc" style="width: 500px;" value="" /></td>
			</tr>
			<tr>
			<th><label><?php echo JText::_('SUBJECT'); ?></label></th>
			<td><textarea class="text_area" name="subject" id="subject" rows="4" cols="50" style="width: 500px;"><?php echo $mail_subject;?></textarea></td>
			</tr>

			<tr>
			<th></th>
			<td>
			<?php
			$editor =JFactory::getEditor();					
			echo $editor->display('body_text',$text, '400', '400', '20', '20', false, null, null, null,true );
			?> 
			</td>

			</tr>
		</tbody>
	</table>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="sendCustomEmail" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="type_mail" value="<?php echo $type_mail;?>" />
<input type="hidden" name="view" value="mail" />
</form>