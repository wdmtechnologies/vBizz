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
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

$db = JFactory::getDbo();
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_vbizz/assets/css/vaccount.css');

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->support_acl->get('addaccess');
$edit_access = $this->config->support_acl->get('editaccess');
$delete_access = $this->config->support_acl->get('deleteaccess');

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


?>

<script type="text/javascript">
jQuery(function() {
	
	 jQuery( "#dialog" ).dialog({
		autoOpen: false,
		width: 600,
		height: 300,
		show: {
			effect: "blind",
			//duration: 1000
		},
		hide: {
			effect: "explode",
			//duration: 1000
		},
		
	});
	
	
	jQuery(document).on('click','.send',function() {
		
		var title = jQuery('input[name="title"]').val();
		var description = jQuery('textarea[name="description"]').val();
		
		if(title == "")	{
			var errHtm = '<div class="err-msg" style="color:red;"><?php echo JText::_('ENTER_CATEGORY_NAME'); ?></div>';
			jQuery('#dialog').prepend(errHtm);
			setTimeout(function() { jQuery('.err-msg').remove();},3000);
			return false;
		}
		
		var that=this;


		jQuery.ajax({
			type: "POST",
			dataType:"JSON",
			data: { 'option':'com_vbizz', 'view':'support', 'task':'addCategory', 'tmpl':'component','title':title, 'description':description },
			
			beforeSend: function() {
				jQuery(that).parent().find("span.loadingbox").show();
			},
			
			complete: function()      {
				jQuery(that).parent().find("span.loadingbox").hide();
			},

			success: function(data){
				if(data.result=="success"){
					jQuery('.all-cat').append(data.htm);
					var errHtm = '<div class="err-msg" style="color:red;">'+data.msg+'</div>';
					jQuery('#dialog').prepend(errHtm);
					jQuery('input[name="title"]').val("");
					jQuery('textarea[name="description"]').val("");
					setTimeout(function() { jQuery('.err-msg').remove();},3000);
					setTimeout(function() { jQuery( "#dialog" ).dialog( "close" );},3000);
				} else {
					var errHtm = '<div class="err-msg" style="color:red;">'+data.msg+'</div>';
					jQuery('#dialog').prepend(errHtm);
				}
			}
		});
	});
	
});

jQuery(document).on('click','#add-cat',function() {
	jQuery( "#dialog" ).dialog( "open" );
});
</script>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('SUPPORT_FORUM'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=support'); ?>" method="post" name="adminForm" id="adminForm">


<?php if(VaccountHelper::checkOwnerGroup()) { ?>

<div class="v-pdf">
	<a  id="add-cat" class="add-cat btn btn-small btn-success pdf" href="javascript:void(0);">
		<span class="fa fa-plus"></span> <?php echo JText::_('ADD_CATEGORY'); ?>
	</a>
</div>
<?php } ?>

<div class="adminlist filter">
	<div class="filet_left">
		<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
		<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
		<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
	</div>
</div>


<div id="editcell">
    <table class="adminlist table all-cat">
        <thead>
            <tr>
                <th><?php echo JHTML::_('grid.sort', JText::_('CATEGORY'), 'title', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th><?php echo JHTML::_('grid.sort', JText::_('TOPICS'), 'topics', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th><?php echo JHTML::_('grid.sort', JText::_('REPLIES'), 'replies', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
            </tr>
        </thead>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=support&task=editCat&id='.$row->id );
		$topicLink 		= JRoute::_( 'index.php?option=com_vbizz&view=support&layout=topics&category='.$row->id );
		//$replyLink 		= JRoute::_( 'index.php?option=com_vbizz&view=support&layout=topics&category='.$row->id );
		
    ?>
        <tr class="<?php echo "row$k"; ?> cat-part">
            
            <td>
            <?php if ($editaccess) : ?>
            	<a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
            <?php else : ?>
            	<?php echo $row->title; ?>
            <?php endif; ?>
            </td>
            
            <td><a href="<?php echo $topicLink; ?>"><?php echo $row->topics; ?></a></td>
			
            <td><?php echo $row->replies; ?></td>
            
        </tr>
    <?php
    	$k = 1 - $k;
    }
    ?>
        <tfoot>
            <tr>
                <td colspan="4"><?php echo $this->pagination->getListFooter(); ?></td>
            </tr>
        </tfoot>
    </table>
</div>

<div id="dialog" title="<?php echo JText::_('ADD_CATEGORY'); ?>">
	<table class="adminform table table-striped">
		<tbody id="support-category">
			<tr class="admintable">
            <th width="200"><label class="hasTip" title="<?php echo JText::_('TITLETXT'); ?>">
            	<?php echo JText::_('TITLE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><input class="text_area" type="text" name="title" id="title" value=""/></td>
        </tr>
        
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('DESCRIPTIONTXT'); ?>"><?php echo JText::_('DESCRIPTION'); ?></label></th>
            <td><textarea class="text_area" name="description" id="description" rows="4" cols="50"></textarea></td>
        </tr>
		
		<tr>
			<th></th>
			<td>
				<input type="button" class="send btn" value="<?php echo JText::_('SAVE'); ?>" class="btn btn-success" style="margin-bottom:10px" />
				<span class="loadingbox" style="display:none;"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/loading.gif"   />' ?></span>
			</td>
		</tr>
			
		</tbody>
	</table>
</div>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>
