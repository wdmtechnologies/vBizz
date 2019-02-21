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

$input = JFactory::getApplication()->input;

$document = JFactory::getDocument();

$id = JRequest::getInt('id', 0);

$edit=JRequest::getInt('edit', 0);

$user = JFactory::getUser();
$groups = $user->getAuthorisedGroups();

$bug_access = $this->config->bug_acl->get('access_interface');
		
$add_access = $this->config->bug_acl->get('addaccess');

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
		
if($bug_access) {
	$bug_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$bug_access))
		{
			$bug_acl=true;
			break;
		}
	}
}
else {
	$bug_acl=true;
}

if(!$bug_acl || !$addaccess)
{
	echo JText::_( 'YOU_R_NOT_AUTHORISE' );
	jexit();
}

?>
<script type="text/javascript">
jQuery(document).on('click','.note-save',function() {
		
		var id = '<?php echo $id; ?>';
		var notes = jQuery(this).parent().parent().parent().find('textarea[name="notes"]').val();
		var edit = '<?php echo $edit; ?>';
		
		var that=this;


		jQuery.ajax({
			type: "POST",
			dataType:"JSON",
			data: {'option':'com_vbizz', 'view':'mail', 'task':'moveToBug', 'tmpl':'component','id':id, 'notes':notes },
			
			beforeSend: function() {
				jQuery(that).parent().find("span.loadingbox").show();
			},
			
			complete: function()      {
				jQuery(that).parent().find("span.loadingbox").hide();
			},

			success: function(data){
				if(data.result=="success"){
					
					jQuery('.alert-msg').css('color','red').css('display','block').text(data.msg);
					
					if(edit==1) {
						window.parent.jQuery("#bug-icon").remove();
					} else {
						
						var htm = '<span><i class="fa fa-star"></i></span>';
						
						window.parent.jQuery("#bug-icon-"+id).html(htm);
					}
					
					setTimeout(function() { window.parent.SqueezeBox.close();},3000);
				} else {
					jQuery('.alert-msg').css('color','red').css('display','block').text(data.msg);
					
					setTimeout(function() { window.parent.SqueezeBox.close();},3000);
				}
			}
		});
	});
</script>

<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=mail&layout=bugnotes&tmpl=component&id='.$id); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<table class="adminform table table-striped">
	<span class="alert-msg" style="display: none;"></span>
    <tbody>
		<tr>
			<th><label><?php echo JText::_('NOTES'); ?></label></th>
			<td><textarea class="text_area" name="notes" id="notes" rows="4" cols="50" style="margin: 0px; height: 160px; width: 380px;"></textarea></td>
		</tr>
		
		<tr>
			<th colspan="0">
				<input type="button" class="note-save" value="<?php echo JText::_('SAVE'); ?>" class="btn btn-success" style="margin-bottom:10px" />
				<span class="loadingbox" style="display:none;"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/loading.gif"   />' ?></span>
			</th>
		</tr>
		
    </tbody>
</table>

</form>