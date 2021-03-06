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
	 
$uuid=$this->item->message_id;

if($this->item->attachments){

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
			
?>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('BUG_TRACKER'); ?></h1>
	</div>
</header>

 <div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=bug');?>" method="post" name="adminForm" id="adminForm">
<div class="edit_mail">
	<div class="row-fluid">
		<div class="span12">
			<div class="btn-toolbar" id="toolbar">
				<div class="btn-wrapper"  id="toolbar-cancel">
					<span onclick="Joomla.submitform('cancel', document.getElementById('adminForm'));" class="btn btn-small">
					<span class="fa fa-close"></span> <?php echo JText::_('CLOSE'); ?></span>
				</div>
			</div>
		</div>
	</div>
	<div class="header_edit" style="display:inline-block; width:100%">
	<div class="header_left" style="float:left">
		 
		    <p>
			<?php 
			echo JText::_('FROM').' : ';
			echo $this->item->from_name?$this->item->from_name:$this->item->from_email;
			?>
			</p>
			<p>
			<?php		
			echo  JText::_('SUBJECT').' : ';
			echo '<b>'.$this->item->subject.'</b>';
			?>
			</p>
			
			 <p>
			<?php
			echo "".JText::_('TO').' : ';
			echo $this->item->to_name?$this->item->to_name:$this->item->to_email;
			?>
			</p>
		  <?php
				if(!empty($this->item->cc)){
					echo "<p>";
					echo JText::_('CC').' ';
					echo $this->item->cc;
					echo "</p>";
				}
			?>
			
	</div>
	
</div><br/><hr>
<div class="messages_div" >

		<div class="contet_message">
			<?php
			$text=quoted_printable_decode($this->item->body_messge);
			if($this->item->subtype=='PLAIN')
				$text = nl2br($text);
			echo $text;
			?>
		</div><br/><hr>
	
		
		 <div class="message_attachment">
		<?php	
			if($this->item->attachments == 1){
				$allowed = array('.jpg', '.jpeg', '.gif', '.png');
				$ext = strrchr($attachment['name'], '.');
				
				echo '<b>'.JText::_('ATTACHEMENT').'</b>&nbsp;&nbsp;&nbsp;';		
				echo '<a href="'.$attachment['filename'].'" download="'.$attachment['filename'].'">'.$attachment['filename'].'</a>';
				
				if(in_array($ext, $allowed)){
					echo '<br/><br/><img src="'.$attachment['filename'].'"/>';
				}
			}
			?>	
		</div>
		
		<div class="bug-notes">
		<?php echo JText::_('NOTES').' : '.$this->item->notes; ?>	
		</div>

</div>

<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="bug" />
</form>
</div> 