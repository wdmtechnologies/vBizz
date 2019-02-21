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
JHtml::_('formbehavior.chosen', 'select');


$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->vendor_acl->get('addaccess');
$edit_access = $this->config->vendor_acl->get('editaccess');
$delete_access = $this->config->vendor_acl->get('deleteaccess');

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
$document->addStyleSheet(JURI::root().'templates/vacount/css/style.css');
$document->addStyleSheet(JURI::root().'templates/vacount/css/font-awesome.css');

?>

<script type="text/javascript">
jQuery(function() {
	
	 jQuery( "#dialog" ).dialog({
		autoOpen: false,
		width: 700,
		height: 400,
		show: {
			effect: "blind",
			//duration: 1000
		},
		hide: {
			effect: "explode",
			//duration: 1000
		},
		open: function(event, ui) {
			var vendid = jQuery('#dialog').data('vendid');
			var htm = '<input type="hidden" name="vendid" value="'+vendid+'" />';
			jQuery('#mail-sent').append(htm);
		},
		close: function(event, ui) {
			jQuery('#mail-sent').find('input[name="vendid"]').remove();
		}
	});
	
	
	jQuery(document).on('click','.send',function() {
		
		var vendid = jQuery(this).parents('#dialog').find('input[name="vendid"]').val();
		var subject = jQuery(this).parents('#dialog').find('input[name="subject"]').val();
		var email = jQuery(this).parents('#dialog').find('textarea[name="email_content"]').val();
		
		var that=this;


		jQuery.ajax({
			type: "POST",
			dataType:"JSON",
			data: {'option':'com_vbizz', 'view':'vendor', 'task':'sendCustomEmail', 'tmpl':'component','vendid':vendid, 'subject':subject, 'email':email},
			
			beforeSend: function() {
				jQuery(that).parent().find("span.loadingbox").show();
			},
			
			complete: function()      {
				jQuery(that).parent().find("span.loadingbox").hide();
			},

			success: function(data){
				if(data.result=="success"){
					jQuery(that).parents('#dialog').find('input[name="subject"]').val('');
					jQuery(that).parents('#dialog').find('textarea[name="email_content"]').val('');
					var htm = '<tr class="removemail"><td></td><td><span><?php echo JText::_('MAIL_SENT_SUCCESSFULLY'); ?></span></td></tr>';
					jQuery(that).parents('#dialog').append(htm);
					
					setTimeout(function() { 
					jQuery('tr.removemail').remove();
					jQuery( "#dialog" ).dialog( "close" );},3000);
				}
			}
		});
	});
	
});
function vendormail(vid)
{
	jQuery( "#dialog" ).data('vendid',vid).dialog( "open" );
}

</script>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo $this->config->vendor_view; ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=vendor'); ?>" method="post" name="adminForm" id="adminForm">

<?php if( ($addaccess) || ($editaccess) || ($deleteaccess) ) { ?>
<div class="subhead">
	<div class="container-fluid">
        <div id="container-collapse" class="container-collapse"></div>
        <div class="row-fluid">
            <div class="span12">
                <div id="toolbar" class="btn-toolbar">
                    <?php if($addaccess) { ?>
                    <div class="btn-wrapper"  id="toolbar-new">
                        <span onclick="Joomla.submitbutton('add')" class="btn btn-small btn-success">
                        <span class="fa fa-plus"></span> <?php echo JText::_('NEW'); ?></span>
                    </div>
                    <?php } ?>
                    
                    <?php if($editaccess) { ?>
                    <div class="btn-wrapper"  id="toolbar-edit">
                        <span onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('edit')}" class="btn btn-small">
                        <span class="fa fa-edit"></span> <?php echo JText::_('EDIT'); ?></span>
                    </div>
                    <?php } ?>
                    
                    <?php if($deleteaccess) { ?>
                    <div class="btn-wrapper"  id="toolbar-delete">
                    <span onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('remove')}" class="btn btn-small">
                    <span class="fa fa-remove"></span> <?php echo JText::_('DELETE'); ?></span>
                    </div>
                    <?php } ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div class="adminlist filter">
<div class="filet_left">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT');?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
</div>
</div>

<div id="editcell">
	<table class="adminlist table">
	<thead>
		<tr>
        	<th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
			<th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
			<th><?php echo JHTML::_('grid.sort', 'NAME', 'i.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'COMPANY', 'i.company', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'CONTACT_NO', 'i.phone', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'EMAIL', 'i.email', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <th class="hidden-phone"><?php echo JText::_( 'ADDRESS' ); ?></th>
			<th><?php echo JHTML::_('grid.sort', 'TOTAL_AMOUNT', 'total_amount', @$this->lists['order_Dir'], @$this->lists['order']);?></th>
			<th class="hidden-phone"></th>
		</tr>
	</thead>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->userid );
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=vendor&task=edit&cid[]='.$row->userid );
		$linkVid 	= JRoute::_( 'index.php?option=com_vbizz&view=expense&vid='.$row->userid );
		
		//get currency format from configuration
		$currency_format = $this->config->currency_format;
		
		//convert amount into given format
		if($currency_format==1)
		{
			$total_amount = $row->total_amount;
		} else if($currency_format==2) {
			$total_amount = number_format($row->total_amount, 2, '.', ',');
		} else if($currency_format==3) {
			$total_amount = number_format($row->total_amount, 2, ',', ' ');
		} else if($currency_format==4) {
			$total_amount = number_format($row->total_amount, 2, ',', '.');
		} else {
			$total_amount = $row->total_amount;
		}
		
		?>
		<tr class="<?php echo "row$k"; ?>">
        	<td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i); ?></td>
			
			<td><?php echo $checked; ?></td>
            
			<td align="center">
                <?php if ($editaccess) : ?>
					<a href="<?php echo $link; ?>"><?php echo $row->name; ?></a>
                <?php else : ?>
                	<?php echo $row->name; ?>
                <?php endif; ?>
			</td>
            
            <td align="center" class="hidden-phone"><?php echo $row->company; ?></td>
            
            <td align="center" class="hidden-phone"><?php echo $row->phone; ?></td>
            
            <td align="center" class="hidden-phone"><?php echo "<a href='mailto:".$row->email."'><div> ".$row->email."</div></a>"; ?></td>
            
            <td align="center" class="hidden-phone"><?php echo $row->address; ?>  <?php echo ($row->city); ?>  <?php echo ($row->state); ?>  <?php echo ($row->country); ?></td>
            
            <td align="center"><a href="<?php echo $linkVid; ?>"><?php echo $total_amount; ?></td>
						
            <td align="center" class="send_email hidden-phone"><a class="btn" href="javascript:void(0);" onclick="vendormail(<?php echo $row->userid; ?>);" class="send_mail" vendid="<?php echo $row->userid; ?>"><?php echo JText::_('SEND_EMAIL'); ?></td>

		</tr>
       
        	
		<?php
		$k = 1 - $k;
	}
	?>
    
    <tfoot>
    <tr>
      <td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
  </tfoot>
  
	</table>
 </div>

<div id="dialog" title="<?php echo JText::_('SEND_EMAIL'); ?>">
	<table class="adminform table table-striped">
		<tbody id="mail-sent">
			<tr>
				<th><label><?php echo JText::_('SUBJECT'); ?> :</label></th>
				<td><input class="text_area" type="text" name="subject" id="subject" style="width: 500px;" value="" /></td>
			</tr>
			
			<tr>
				<th><label><?php echo JText::_('EMAIL'); ?> :</label></th>
				<td><textarea class="text_area" name="email_content" id="email_content" rows="4" cols="50" style="width: 500px;"></textarea></td>
			</tr>
			
			<tr>
				<th>
				</th>
				<td><input type="button" class="send btn" value="<?php echo JText::_('SEND_EMAIL'); ?>" class="btn btn-success" style="margin-bottom:10px" />
					<span class="loadingbox" style="display:none;"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/loading.gif"   />' ?></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="vendor" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>
