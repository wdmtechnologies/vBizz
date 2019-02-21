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
JHtml::_('formbehavior.chosen', 'select');

$db = JFactory::getDbo();

$user = JFactory::getUser(); 
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->employee_acl->get('addaccess');
$edit_access = $this->config->employee_acl->get('editaccess');
$delete_access = $this->config->employee_acl->get('deleteaccess');

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
			var empid = jQuery('#dialog').data('empid');
			var htm = '<input type="hidden" name="empid" value="'+empid+'" />';
			jQuery('#mail-sent').append(htm);
		},
		close: function(event, ui) {
			jQuery('#mail-sent').find('input[name="empid"]').remove();
		}
	});
	
	
	jQuery(document).on('click','.send',function() {
		
		var empid = jQuery(this).parent().parent().parent().find('input[name="empid"]').val();
		var subject = jQuery(this).parent().parent().parent().find('input[name="subject"]').val();
		var email = jQuery(this).parent().parent().parent().find('textarea[name="email_content"]').val();
		
		var that=this;


		jQuery.ajax({
			type: "POST",
			dataType:"JSON",
			data: {'option':'com_vbizz', 'view':'employee', 'task':'sendCustomEmail', 'tmpl':'component','empid':empid, 'subject':subject, 'email':email},
			
			beforeSend: function() {
				jQuery(that).parent().find("span.loadingbox").show();
			},
			
			complete: function()      {
				jQuery(that).parent().find("span.loadingbox").hide();
			},

			success: function(data){
				if(data.result=="success"){
					jQuery(that).parent().parent().parent().find('input[name="subject"]').val('');
					jQuery(that).parent().parent().parent().find('textarea[name="email_content"]').val('');
					var htm = '<tr><td></td><td><span><?php echo JText::_('MAIL_SENT_SUCCESSFULLY'); ?></span></td></tr>';
					jQuery(that).parent().parent().parent().append(htm);
					
					setTimeout(function() { jQuery( "#dialog" ).dialog( "close" );},3000);
				}
			}
		});
	});
	
});

jQuery(document).on('click','.send_mail',function() {
	var empid = jQuery(this).attr('empid');
	jQuery( "#dialog" ).data('empid',empid).dialog( "open" );
});
</script>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('EMPLOYEE'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=employee');?>" method="post" name="adminForm" id="adminForm">

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
					
					<?php if(VaccountHelper::checkOwnerGroup()) { ?>
					<div class="btn-wrapper"  id="toolbar-setting"style="float: right;">
						<a class="modal btn" id="modal1" title="Select" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=config&task=loadEmployeeSetting&tmpl=component&ot=1';?>" rel="{handler: 'iframe', size: {x: 550, y: 400}}">
						<span class="fa fa-cog"></span>
						</a>
                    </div>
					<?php } ?>
					
                </div>
            </div>
        </div>
    </div>
</div>

<div class="adminlist filter filter_emp">
<div class="filet_left">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT');?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();
document.getElementById('filter_dept').value='';this.form.submit();
document.getElementById('filter_desg').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
</div>

<div class="filter_right">
<?php
echo $this->lists['department'];
echo $this->lists['designation'];
?>
</div>
</div>

<div id="editcell">
	<table class="adminlist table">
        <thead>
            <tr>
                <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
                <th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
                <th><?php echo JHTML::_('grid.sort', 'NAME', 'i.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				<th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'EMPLOYEE_ID', 'i.empid', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'DEPARTMENT', 'd.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'DESIGNATION', 'p.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				<th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'EMAIL', 'i.email', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				<th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'CTC', 'i.ctc', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				<th class="hidden-phone"></th>
            </tr>
        </thead>
        <?php
        $k = 0;
        for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
            $row = &$this->items[$i];
            $checked 	= JHTML::_('grid.id',   $i, $row->userid );
            $link 		= JRoute::_( 'index.php?option=com_vbizz&view=employee&task=edit&cid[]='.$row->userid );
			
			//get currency format from configuration
			$currency_format = $this->config->currency_format;
		
			//convert amount value into given format
			if($currency_format==1)
			{
				$ctc = $row->ctc;
			} else if($currency_format==2) {
				$ctc = number_format($row->ctc, 2, '.', ',');
			} else if($currency_format==3) {
				$ctc = number_format($row->ctc, 2, ',', ' ');
			} else if($currency_format==4) {
				$ctc = number_format($row->ctc, 2, ',', '.');
			} else {
				$ctc = $row->ctc;
			}
			
			$workingDays = 30;
			
			$sal_per_day = $ctc/$workingDays;
			
			$query = 'SELECT count(*) from #__vbizz_attendance where present=0 and paid=0 and month(date)=month(curdate()) and employee='.$row->userid;
			$db->setQuery($query);
			$absent = $db->loadResult();
			
			$totalPresent = $workingDays - $absent;
			
			$query = 'SELECT count(*) from #__vbizz_attendance where halfday=1 and paid=0 and month(date)=month(curdate()) and employee='.$row->userid;
			$db->setQuery($query);
			$halfday = $db->loadResult();
			
			$totalHafday = $halfday * .5;
			
			
			$actualPresent = $totalPresent - $totalHafday;
			
			$salary = $sal_per_day * $actualPresent;
			
			
		
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
				
				<td align="center" class="hidden-phone"><?php echo $row->empid; ?></td>
				
				<td align="center"><?php echo $row->department; ?></td>
                
                <td align="center" class="hidden-phone"><?php echo $row->designation; ?></td>
                
                
                <td align="center" class="hidden-phone"><?php echo "<a href='mailto:".$row->email."'><div> ".$row->email."</div></a>"; ?></td>
                
                <td align="center" class="hidden-phone"><?php echo $this->config->currency.' '.$ctc; ?></td>
				
				
                <td align="center" class="hidden-phone"><a href="javascript:void(0);" class="send_mail btn" empid="<?php echo $row->userid; ?>"><?php echo JText::_('SEND_EMAIL'); ?></td>

				
            </tr>
           
                
            <?php
            $k = 1 - $k;
        }
        ?>
    
        <tfoot>
            <tr>
              <td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td>
            </tr>
        </tfoot>
  
	</table>
</div>

<div id="dialog" title="<?php echo JText::_('SEND_EMAIL'); ?>">
	<table class="adminform table table-striped">
		<tbody id="mail-sent">
			<tr>
				<th><label><?php echo JText::_('SUBJECT'); ?></label></th>
				<td><input class="text_area" type="text" name="subject" id="subject"value="" /></td>
			</tr>
			
			<tr>
				<th><label><?php echo JText::_('MESSAGE'); ?></label></th>
				<td><textarea class="text_area" name="email_content" id="email_content" rows="6" cols="50"></textarea>
				<input type="button" class="send btn btn-small" value="<?php echo JText::_('SEND_EMAIL'); ?>" class="btn btn-success" />
					<span class="loadingbox" style="display:none;"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/loading.gif"   />' ?></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="employee" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>
