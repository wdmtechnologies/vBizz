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
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

$canDo = VaccountHelper::getActions();

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->document_acl->get('addaccess');
$edit_access = $this->config->document_acl->get('editaccess');
$delete_access = $this->config->document_acl->get('deleteaccess');
$upload_access = $this->config->document_acl->get('uploadaccess');
$download_access =$this->config->document_acl->get('downloadaccess');

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

if($upload_access) {
	$uploadaccess = false;
	foreach($groups as $group) {
		if(in_array($group,$upload_access))
		{
			$uploadaccess=true;
			break;
		}
	}
} else {
	$uploadaccess=false;
}

if($download_access) {
	$downloadaccess = false;
	foreach($groups as $group) {
		if(in_array($group,$download_access))
		{
			$downloadaccess=true;
			break;
		}
	}
} else {
	$downloadaccess=false;
}

$symbols = array("pdf"=>"icon-48-pdf.png", "txt"=>"icon-48-txt.png", "zip"=>"icon-48-zip.png", "xls"=>"icon-48-xls.png", "7zip"=>"icon-48-7zip.png", "archive"=>"icon-48-archive.png", "jpg"=>"icon-48-jpg.png","binary"=>"icon-48-binary.png", "doc"=>"icon-48-doc.png", "mp3"=>"icon-48-mp3.png", "rar"=>"icon-48-rar.png" );

$valid_ext = $this->config->document_type;
$valid_ext = explode(',', $valid_ext);
array_walk($valid_ext, function(&$val){
	$val = '"'.$val.'"';
});

 ?>
<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		var form = document.adminForm;
		
		if(form.title.value == "")	{
			alert("<?php echo JText::_('ENTER_TITLE'); ?>");
			return false;
		}
		
		if(typeof(validateit) == 'function')	{
			if(!validateit())
				return false;
		}
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
jQuery(document).ready(function(){
	
	jQuery(document).on('change', 'select[name="thumb3"]', function(){
		if(jQuery(this).val()==''){
			jQuery('div#symbol_extra').hide();
			jQuery('img#symbolic').attr("src", "");
		}
		else if(jQuery(this).val()=='symbol_extra'){
			jQuery('div#symbol_extra').show();
			jQuery('img#symbolic').attr("src", "");
		}
		else{
			jQuery('div#symbol_extra').hide();
			var sym_fold = "<?php echo JRoute::_(JUri::root().'components/com_vbizz/assets/images/');?>";
			var symbol = sym_fold+"icon-48-"+jQuery(this).val()+".png";
			// jQuery('img#symbolic').attr("src", sym_fold+jQuery(this).val());
			jQuery('img#symbolic').fadeOut(300, function(){
				jQuery(this).attr('src',symbol).fadeIn(300);
			});
			
		}
	});
	
	jQuery("#doc").on('change', function(e){
		var ext = jQuery(this).val().split('.').pop().toLowerCase();
		var valid_ext = [<?php echo implode(',', $valid_ext);?>];
		if(jQuery.inArray(ext, valid_ext) == -1) {
			jQuery("#doc").val("");
			alert('invalid extension!');
			return false;
		}
	});
	
	
});

</script>
    

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=documents'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="row-fluid">
    <div class="span12">
        <div class="btn-toolbar" id="toolbar">
				<?php if($editaccess) { ?>
                    <div class="btn-wrapper"  id="toolbar-apply">
                    <span onclick="Joomla.submitbutton('apply')" class="btn btn-small btn-success">
                    <span class="icon-apply icon-white"></span> <?php echo JText::_('SAVE'); ?></span>
                    </div>
                    <div class="btn-wrapper"  id="toolbar-save">
                    <span onclick="Joomla.submitbutton('save')" class="btn btn-small">
                    <span class="icon-save"></span> <?php echo JText::_('SAVE_N_CLOSE'); ?></span>
                    </div>
                <?php } ?>
            <div class="btn-wrapper"  id="toolbar-cancel">
                <span onclick="Joomla.submitbutton('cancel')" class="btn btn-small">
                <span class="icon-cancel"></span> <?php echo JText::_('CLOSE'); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="overview">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'OVERVIEW' ); ?></legend>
	<ul>
		<li><?php	echo JText::_('NEW_DOCUMENT_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">
<fieldset class="adminform">

<table class="adminform table table-striped">
    <tbody>
    
    <tr>
        <td width="200">
        	<label class="hasTip" title="<?php echo JText::_('DOCUMENT_TITLE_DESC'); ?>">
        	<?php echo JText::_('DOCUMENT_TITLE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</label>
        </td>
    	<td><input class="text_area" type="text" name="title" id="title" value="<?php echo $this->item->title;?>"/></td>
    </tr>
	<tr>
		<td width="200">
        	<label class="hasTip" title="<?php echo JText::_('DOCUMENT_FILE_DESC'); ?>">
        	<?php echo JText::_('DOCUMENT_FILE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</label>
        </td>
		<td>
			
			<?php if($uploadaccess){?>
			<input type="file" name="doc" id="doc" class="inputbox required" size="50" value=""/>
			<span id=""><?php echo JText::_('VALID_DOC')." : ".$this->config->document_type;?></span>
			<?php }?>
			
			<?php if($downloadaccess && $this->item->id>0 && !empty($this->item->doc)){?>
				<a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=documents&task=download&document='.(int)$this->item->id);?>"><span id="download"><i class="fa fa-download"></i><?php echo JText::_('DOWNLOAD');?></span></a>
			<?php }?>
			
			<?php if($deleteaccess && $this->item->id>0 && !empty($this->item->doc)){?>
				<a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=documents&task=delete&document='.(int)$this->item->id);?>"><span id="delete"><i class="fa fa-remove"></i><?php echo JText::_('DELETE');?></span></a>
			<?php }?>
			
		</td>
	</tr>
	<tr>
		<td width="200">
        	<label class="hasTip" title="<?php echo JText::_('DOCUMENT_SYMBOL_DESC'); ?>">
        	<?php echo JText::_('DOCUMENT_SYMBOL_TITLE'); ?>:</label>
        </td>
		<td>
			<select name="thumb3">
				<option value=""><?php echo JText::_('SELECT_SYMBOL');?></option>
				<?php foreach($symbols as $ext=>$symbol){?>
				<option value="<?php echo $ext;?>" <?php if($this->item->thumb3==$ext){echo ' selected="selected"';}?>><?php echo $ext;?></option>
				<?php }?>
				<option value="symbol_extra" <?php if($this->item->thumb3=='symbol_extra'){echo ' selected="selected"';}?>><?php echo JText::_('OTHER_SYMBOL');?></option>
			</select>
			<span><img src="<?php if(!empty($this->item->thumb3)){if($this->item->thumb3!='symbol_extra'){echo JRoute::_(JUri::root().'components/com_vbizz/assets/images/'.$symbols[$this->item->thumb3]);}else{echo JRoute::_(JUri::root().'components/com_vbizz/uploads/documents/'.$this->item->thumb2);}}?>" id="symbolic" alt="<?php echo JText::_('NO_SYMBOL_FOUND');?>" width="32" height="32" /></span>
			<div id="symbol_extra" <?php if($this->item->thumb3!='symbol_extra'){echo ' style="display:none;"';}?>><input type="file" name="symbol_extra" class="inputbox required" size="50" value=""/></div>
		</td>
	</tr>
	<tr>
		<td><label class="hasTip" title="<?php echo JText::_('DESCRIPTIONXT'); ?>"><?php echo JText::_('DESCRIPTION'); ?> :</label></td>
		<td><textarea class="text_area" name="description" id="description" rows="4" cols="50"><?php echo $this->item->description;?></textarea></td>
	</tr>
	<tr>
		<td><label class="hasTip" title="<?php echo JText::_('DOCUMENT_ACCESS_DESC');?>"><?php echo JText::_('DOCUMENT_ACCESS'); ?></label></td>
		<td>
			<?php echo JHtmlAccess::level('access', $this->item->access, '', false);?>
		</td>
	</tr>
	<tr>
		<td><label class="hasTip" title="<?php echo JText::_('DOCUMENT_DOWNLOADS_DESC');?>"><?php echo JText::_('DOCUMENT_DOWNLOADS'); ?></label></td>
		<td>
			<?php echo intval($this->item->hits);?>
		</td>
	</tr>
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="documents" />
</form>
</div>
<div class="copyright" align="center">
	<?php echo JText::_( 'WDM' );?>
</div>