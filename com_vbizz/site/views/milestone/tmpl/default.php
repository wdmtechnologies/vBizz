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
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

$db = JFactory::getDbo();
$document = JFactory::getDocument(); 
$document->addScript(JUri::root(true).'/components/com_vbizz/assets/js/account.js');


$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->milestone_acl->get('addaccess');
$edit_access = $this->config->milestone_acl->get('editaccess');
$delete_access = $this->config->milestone_acl->get('deleteaccess');

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

/* $query = 'SELECT customer from #__vbizz_users where userid='.$userId;
$db->setQuery($query);
$client = $db->loadResult(); */

$projectid = JRequest::getInt('projectid',0);

$query = 'SELECT count(*) from #__vbizz_project_milestone_temp where projectid='.$projectid;
$db->setQuery($query);
$count = $db->loadResult();

$format = $this->config->date_format;

//get currency and currency format from configuration		
$currency_format = $this->config->currency_format;
$currency = $this->config->currency;

		
$query = 'SELECT estimated_cost FROM #__vbizz_projects where id='.$db->Quote($projectid);
$db->setQuery( $query );
$estimated_cost = $db->loadResult();

if( (count($this->mt) > 0) && (!count($this->tmt)) ) {
	
	$temp = 0;
	$tblClass = 'Milestone';
} else {
	$temp = 1;
	$tblClass = 'Milestonetemp';
}

?>

<script type="text/javascript">

</script>
<?php 
$t_palace = VaccountHelper::getThousandPlace();
$d_palace = VaccountHelper::getDecimalPlace();
$js = '';
if(empty($t_palace)) {
$js .=	'var thousand_formating = \'""\'';
}
else{ 
$js .= ' var thousand_formating = String('.VaccountHelper::getThousandPlace().');';	
}
if(empty($d_palace))
{
	$js .= 'var decimal_formating = \'""\'';    
}
else 
{
	$js .= 'var decimal_formating = String('.VaccountHelper::getDecimalPlace().');';
}
$document->addScriptDeclaration($js);

?>
<script type="text/javascript">

jQuery(function() {
	jQuery(document).on('change','input.milestoneamount',function(){
		jQuery(this).val(accounting.formatNumber(accounting.unformat(jQuery(this).val(),decimal_formating),2, thousand_formating, decimal_formating));
	});
	jQuery(document).ready(function(){
		jQuery( 'input[name="eddelivery_date[]"]' ).datepicker();
		jQuery( 'input[name="eddelivery_date[]"]' ).datepicker( "option", "dateFormat", "<?php echo VaccountHelper::DateFormat_bizz($this->config->date_format);?>" );
	});
	 
	jQuery(document).on('click','.del-miles',function() {
		jQuery(this).parents('tr').remove();
		if(jQuery('.tmpMl').length==0) {
			jQuery('#toolbar-apply').remove();
		}
		return false;
	});
	
	jQuery(document).on('click','.del',function() {
	
		var id = jQuery(this).attr('id');
		//var tblClass = jQuery('input[name="tblClass"]').val();
		var temp = jQuery('input[name="temp"]').val();
		if(temp==1) {
			var tblClass = 'Milestonetemp';
		} else {
			var tblClass = 'Milestone';
		}
		var that=this;
		
		jQuery.ajax(
		{
			url: "",
			type: "POST",
			dataType:"json",
			data: { "option":"com_vbizz", "view":"milestone", "task":"delete", "tmpl":"component", "id":id, "tblClass":tblClass },
			
			beforeSend: function() {
				jQuery(that).parents('tr').find("span.loadingbox").show();
			},
			
			complete: function()      {
				jQuery(that).parents('tr').find("span.loadingbox").hide();
			},
			
			success: function(data) 
			{
				if(data.result=="success"){
					
					jQuery(that).parents('tr').remove();
				
					if(jQuery('.temp-miles').length==0) {
						jQuery('#toolbar-approve').remove();
						jQuery('#toolbar-reject').remove();
					}
					
				} 
				
				var errHtm = '<div class="err-msg" style="color:red;">'+data.msg+'</div>';
			    jQuery('#editcell').prepend(errHtm);
			    setTimeout(function() { jQuery('.err-msg').remove();},3000);
				//alert(data.msg);
				
			}
			
		});
		
		return false;
		
	});
	
	jQuery(document).on('click','.newMiles',function() {
		
		var htm = '<tr class="temp-miles tmpMl"><td><input type="text" class="text_area" name="title[]" value=""></input></td><td><input type="text" class="text_area" name="delivery_date[]" value=""></td><td><input type="text" class="text_area milestoneamount" name="amount[]" value=""></input></td><td><select name="status[]"><option value="ongoing"><?php echo JText::_('ONGOING');?></option><option value="completed"><?php echo JText::_('COMPLETED'); ?></option><option value="paid"><?php echo JText::_('PAID');?></option><option value="due"><?php echo JText::_('DUE');?></option><option value="overdue"><?php echo JText::_('OVERDUE');?></option></select><span class="miles-comments"><a class="add-comments" href="javascript:void(0);"><?php echo JText::_('ADD_COMMENTS');?></a></span></td><td><a class="del-miles" href="javascript:void();"><i class="icon-delete"></i></a></td></tr>';
		
		jQuery('.all-miles').append(htm);
		
		jQuery( 'input[name="delivery_date[]"]' ).datepicker();
		jQuery( 'input[name="delivery_date[]"]' ).datepicker( "option", "dateFormat", "<?php echo VaccountHelper::DateFormat_bizz($this->config->date_format);?>" );
		
		jQuery('#toolbar-apply').remove();
		
		/* var appTask = "'saveTempMiles'";
		
		var tool = '<div class="btn-wrapper"  id="toolbar-apply"><span onclick="Joomla.submitbutton('+appTask+')" class="btn btn-small btn-success"><span class="icon-apply icon-white"></span> <?php echo JText::_('SAVE'); ?></span></div>'; */
		
		var tool = '<div class="btn-wrapper"  id="toolbar-apply"><a href="javascript:void(0);" class="createNew btn btn-small btn-success"><span class="icon-apply icon-white"></span> <?php echo JText::_('SAVE'); ?></span></div>';
					
		jQuery('#toolbar').append(tool);
		
		jQuery('select').chosen({"disable_search_threshold":0,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
		
		return false;
	});
	
	jQuery(document).on('click','.add-comments',function() {
		var commentHtml = '<span class="miles-desc"><textarea class="description" name="description[]" rows="4" cols="50"></textarea></span>';
		
		jQuery(this).parent().after(commentHtml);
		jQuery(this).removeClass('add-comments');
		jQuery(this).addClass('remove-comments').text('<?php echo JText::_('REMOVE_COMMENTS');?>');
		
		return false;
	});
	
	jQuery(document).on('click','.remove-comments',function() {
		
		jQuery(this).parent().parent().find('.miles-desc').remove();
		jQuery(this).removeClass('remove-comments');
		jQuery(this).addClass('add-comments').text('<?php echo JText::_('ADD_COMMENTS');?>');
		
		return false;
	});
	
	jQuery(document).on('click','.createNew',function() {
		
		var valid = true;
		jQuery('select[name="status[]"]').each(function() {
			if( (this.value=="NaN") || (this.value=="") ) {
				var errHtm = '<div class="err-msg" style="color:red;"><?php echo JText::_('SELECT_STATUS'); ?></div>';
				jQuery('#editcell').prepend(errHtm);
				setTimeout(function() { jQuery('.err-msg').remove();},3000);
				return valid = false;
			}
		});
		
		jQuery('input[name="amount[]"]').each(function() {
			var amount_val = parseFloat(accounting.unformat(this.value,decimal_formating));
			
			if(amount_val==0) {
				var errHtm = '<div class="err-msg" style="color:red;"><?php echo JText::_('PLZ_ENTER_AMOUNT'); ?></div>';
			   jQuery('#editcell').prepend(errHtm);
			   setTimeout(function() { jQuery('.err-msg').remove();},3000);
			   return valid =  false;
			}
		});
		
		jQuery('input[name="delivery_date[]"]').each(function() {
			if( (this.value=="NaN") || (this.value=="") ) {
				var errHtm = '<div class="err-msg" style="color:red;"><?php echo JText::_('ENTER_DELIVERY_DATE'); ?></div>';
			   jQuery('#editcell').prepend(errHtm);
			   setTimeout(function() { jQuery('.err-msg').remove();},3000);
			   return valid = false;
			}
		});
		
		jQuery('input[name="title[]"]').each(function() {
			if( (this.value=="NaN") || (this.value=="") ) {
				var errHtm = '<div class="err-msg" style="color:red;"><?php echo JText::_('PLZ_ENTER_TITLE'); ?></div>';
			   jQuery('#editcell').prepend(errHtm);
			   setTimeout(function() { jQuery('.err-msg').remove();},3000);
			   return valid = false;
			}
		});
		
		if(valid) {
			var amount = [];
			var totalAmount = 0;
			jQuery('input[name="amount[]"]').each(function() {
				var amount_val = parseFloat(accounting.unformat(this.value,decimal_formating));
				amount.push(amount_val);
				if (amount_val != 0) {
					totalAmount += amount_val;
				}
			});
			
			var estimated_cost = '<?php echo $estimated_cost; ?>';
			
			if( totalAmount > estimated_cost ) {
				if ( confirm("<?php echo JText::_('AMOUNT_GREATER_NOTIFY_ALERT'); ?>") ){
					Joomla.submitform('saveTempMiles', document.getElementById('adminForm'));
				}
			} else {
				Joomla.submitform('saveTempMiles', document.getElementById('adminForm'));
			}
		} else {
					
			return false;
		}
		
		
	});
	
	jQuery(document).on('click','.edt',function() {
		
		jQuery(this).parent().parent().parent().find('.tempMilesI').css('display','none');
		jQuery(this).parent().parent().parent().find('.tempMilesN').css('display','block');
		
		var del_date = jQuery(this).parent().parent().parent().find( 'input[name="delivery"]' ).val();
		jQuery(this).parent().parent().parent().find( 'input[name="eddelivery_date[]"]' ).datepicker().val(del_date);
		
		return false;
	});
	
	jQuery(document).on('click','.cncl',function() {
		jQuery(this).parent().parent().parent().find('.tempMilesI').css('display','block');
		jQuery(this).parent().parent().parent().find('.tempMilesN').css('display','none');
		return false;
	});
	
	jQuery(document).on('click','.updt',function() {
	
		var id = jQuery(this).attr('id');
		var title = jQuery(this).parent().parent().parent().find( 'input[name="edtitle[]"]' ).val();
		var delivery_date = jQuery(this).parent().parent().parent().find( 'input[name="eddelivery_date[]"]' ).val();
		var amount = jQuery(this).parent().parent().parent().find( 'input[name="edamount[]"]' ).val();
		var status = jQuery(this).parent().parent().parent().find( 'select[name="edstatus[]"]' ).val();
		var description = jQuery(this).parent().parent().parent().find( 'textarea[name="eddescription[]"]' ).val();
		
		var projectid = '<?php echo $projectid; ?>';
		
		var temp = jQuery('input[name="temp"]').val();
		var that=this;
		
		jQuery.ajax(
		{
			url: "",
			type: "POST",
			dataType:"json",
			data: { "option":"com_vbizz", "view":"milestone", "task":"updateMiles", "tmpl":"component", "id":id, "title":title, "delivery_date":delivery_date, "amount":amount, "status":status, "description":description, 'temp':temp, 'projectid':projectid },
			
			beforeSend: function() {
				if(temp==0) {
					jQuery(".poploadingbox").css('display','inline-block');
				} else {
					jQuery(that).parent().parent().find("span.loadingbox").show();
				}
			},
			
			complete: function()      {
				if(temp==0) {
					jQuery(".poploadingbox").hide();
				} else {
					jQuery(that).parent().parent().find("span.loadingbox").hide();
					
				}
			},
			
			success: function(data) 
			{
				if(data.result=="success"){
					
					if(data.temp==1) {
					
						jQuery(that).parent().parent().parent().html(data.htm);
						
						jQuery( 'input[name="delivery_date[]"]' ).datepicker();
						jQuery( 'input[name="delivery_date[]"]' ).datepicker( "option", "dateFormat", "<?php echo VaccountHelper::DateFormat_bizz($this->config->date_format);?>" );
						
						
						jQuery('select').chosen({"disable_search_threshold":0,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
						
						var errHtm = '<div class="err-msg" style="color:red;">'+data.msg+'</div>';
						jQuery('#editcell').prepend(errHtm);
						setTimeout(function() { jQuery('.err-msg').remove();},3000);
					} else {
						
						if(data.tableUpdate==1) {
							jQuery('.temp-miles').remove();
							jQuery('.all-miles').append(data.htm);
			
							jQuery( 'input[name="delivery_date[]"]' ).datepicker();
							jQuery( 'input[name="delivery_date[]"]' ).datepicker( "option", "dateFormat", "<?php echo VaccountHelper::DateFormat_bizz($this->config->date_format);?>" );
							
							jQuery('.prev-miles').remove();
							
         
							
							var hrf = '<?php echo JURI::root().'index.php?option=com_vbizz&view=milestone&layout=modal&tmpl=component&projectid='.$projectid;?>';
							
							
							
							var handler = "'iframe'";
							
							var prevMileBtn = '<div class="prev-miles"><a class="modal" id="modal" title="Select" href="'+hrf+'" rel="{handler: '+handler+', size: {x: 800, y: 500}}"><input type="button" id="addnew" value="<?php echo JText::_('VIEW_PRE_MILESTONE'); ?>" class="btn btn-success" style="margin-bottom:10px" /></a></div>';
										
							jQuery('.subhead').after(prevMileBtn);
							jQuery( 'input[name="temp"]' ).val(1);
							jQuery('input[name="tblClass"]').val('Milestonetemp');
							
							SqueezeBox.initialize({});
							
							SqueezeBox.assign($('a.modal').get(), {
								parse: 'rel'
							});
							$$('a.modal').each(function(el) {
								el.addEvent('click', function(e) {
									new Event(e).stop();
									SqueezeBox.fromElement(el);
								});
							});
							
						} else {
							jQuery(that).parent().parent().parent().html(data.htm);
						
							jQuery( 'input[name="delivery_date[]"]' ).datepicker();
							jQuery( 'input[name="delivery_date[]"]' ).datepicker( "option", "dateFormat", "<?php echo VaccountHelper::DateFormat_bizz($this->config->date_format);?>" );
							
							
							jQuery('select').chosen({"disable_search_threshold":0,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
							
							var errHtm = '<div class="err-msg" style="color:red;">'+data.msg+'</div>';
							jQuery('#editcell').prepend(errHtm);
							setTimeout(function() { jQuery('.err-msg').remove();},3000);
						}
						
						jQuery('select').chosen({"disable_search_threshold":0,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
						
						
					}
					
				}else {
					var errHtm = '<div class="err-msg" style="color:red;">'+data.msg+'</div>';
				    jQuery('#editcell').prepend(errHtm);
				    setTimeout(function() { jQuery('.err-msg').remove();},3000);
				}
				
			}
			
		});
		
		return false;
		
	});
	
	
	
});

</script>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('MANAGE_MILESTONE'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=milestone&projectid='.$projectid); ?>" method="post" name="adminForm" id="adminForm">

<div class="subhead">
	<div class="container-fluid">
        <div id="container-collapse" class="container-collapse"></div>
        <div class="row-fluid">
            <div class="span12">
                <div id="toolbar" class="btn-toolbar">
					
					<div class="btn-wrapper"  id="toolbar-arrow-left-4">
						<a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=projects');?>" class="btn btn-small">
						<span class="icon-arrow-left-4"></span> <i class="fa fa-arrow-left"></i> <?php echo JText::_('BACK_TO_PROJECTS'); ?></a>
					</div>
			
					<?php if($addaccess) { ?>
					<div class="btn-wrapper"  id="toolbar-new">
						<a href="javascript:void(0);" class="newMiles btn btn-small btn-success">
						<span class="fa fa-plus"></span> <?php echo JText::_('NEW'); ?></a>
					</div>
					<?php } ?>
                    
					<?php if( (count($this->mt) > 0)) { ?>
<div class="prev-miles btn-wrapper">
<a class="modal" id="modal" title="Select" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=milestone&layout=modal&tmpl=component&projectid='.$projectid;?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
<input type="button" id="addnew" value="<?php echo JText::_('VIEW_PRE_MILESTONE'); ?>" class="btn" />
</a>
</div>
<?php } ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php if($temp==0) { ?>
<div class="poploadingbox"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/spinner.gif"   />' ?></div>
<?php } ?>

<div id="editcell">
    <table class="adminlist table all-miles">
        <thead>
            <tr>
                <th><?php echo JText::_('TITLE');?></th>
                <th><?php echo JText::_('DELIVERY_DATE');?></th>
                <th><?php echo JText::_('AMOUNT');?></th>
                <th><?php echo JText::_('STATUS');?></th>
				<?php if( ($deleteaccess) || ($editaccess) ) { ?>
				<th width="90"><?php echo JText::_('ACTION');?></th>
				<?php } ?>
            </tr>
        </thead>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->temp ); $i < $n; $i++)	{
		$row = &$this->temp[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		
		if(count($this->mt) > 0) {
			$task = 'edit';
		} else {
			$task = 'editTemp';
		}
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=milestone&task='.$task.'&tmpl=component&cid[]='.$row->id.'&projectid='. $projectid );
		
		//convert amount into currency format
		if($currency_format==1)
		{
			$amount = $row->amount;
		} else if($currency_format==2) {
			$amount = number_format($row->amount, 2, '.', ',');
		} else if($currency_format==3) {
			$amount = number_format($row->amount, 2, ',', ' ');
		} else if($currency_format==4) {
			$amount = number_format($row->amount, 2, ',', '.');
		} else {
			$amount = $row->amount;
		}
		
		//get date format from configuration
		$saved_to_date = $row->delivery_date;
		$todatetime = strtotime($saved_to_date);
		if($format)
		{
			$to_date = date($format, $todatetime );
		} else {
			$to_date = $saved_to_date;
		}
		
		//get milestone status
		$milestone_status = $row->status;
		
		if($milestone_status == "ongoing") {
			$status = JText::_('ONGOING');
		} else if($milestone_status == "completed") {
			$status = JText::_('COMPLETED');
		} else if($milestone_status == "paid") {
			$status = JText::_('PAID');
		} else if($milestone_status == "due") {
			$status = JText::_('DUE');
		} else if($milestone_status == "overdue") {
			$status = JText::_('OVERDUE');
		} else {
			$status = "";
		}
		
		
    ?>
        <tr class="temp-miles <?php echo "row$k"; ?>">
            
            <!--<td>
			
			<a class="modal" id="modal1" title="Select" href="<?php echo $link; ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
			<?php echo $row->title; ?>
			</a>
            
            </td>-->
            
            <td class="mile-title">
				<span class="tempMilesI"><?php echo $row->title; ?></span>
				<span class="tempMilesN">
					<input type="text" class="text_area" name="edtitle[]" value="<?php echo $row->title; ?>"></input>
				</span>
			</td>
			
            <td class="mile-date">
				<span class="tempMilesI"><?php echo $to_date; ?></span>
				<span class="tempMilesN">
					<input type="date" class="text_area" name="eddelivery_date[]" value="<?php echo $row->delivery_date; ?>"></input>
					<input type="hidden" class="text_area" name="delivery" value="<?php echo $row->delivery_date; ?>"></input>
				</span>
			</td>
            
            <td class="mile-amount">
				<span class="tempMilesI"><?php echo $currency.' '.$amount; ?></span>
				<span class="tempMilesN">
					<input type="text" class="text_area milestoneamount" name="edamount[]" value="<?php echo $row->amount; ?>"></input>
				</span>
			</td>
			
            <td class="mile-status">
				<span class="tempMilesI"><?php echo $status; ?></span>
				<span class="tempMilesN">
					<select name="edstatus[]">
						<option value="ongoing" <?php if($row->status=="ongoing") echo 'selected="selected"'; ?>><?php echo JText::_('ONGOING');?></option>
						<option value="completed" <?php if($row->status=="completed") echo 'selected="selected"'; ?>><?php echo JText::_('COMPLETED'); ?></option>
						<option value="paid" <?php if($row->status=="paid") echo 'selected="selected"'; ?>><?php echo JText::_('PAID');?></option>
						<option value="due" <?php if($row->status=="due") echo 'selected="selected"'; ?>><?php echo JText::_('DUE');?></option>
						<option value="overdue" <?php if($row->status=="overdue") echo 'selected="selected"'; ?>><?php echo JText::_('OVERDUE');?></option>
					</select>
				</span>
				<span class="tempMilesN"><?php echo JText::_('COMMENTS');?></span>
				<span class="tempMilesN miles-desc"><textarea class="description" name="eddescription[]" rows="4" cols="50"><?php echo $row->description; ?></textarea></span>
			</td>
			
			<?php if( ($deleteaccess) || ($editaccess) ) { ?>
            <td class="mile-status-buttons">
				<span class="loadingbox" style="display:none;">
					<?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/loading.gif" />' ?>
				</span>
				<?php if( $deleteaccess ) { ?>
				<span class="del-bt hasTip" title="<?php echo JText::_('DELETE'); ?>"><a class="del btn btn-danger" id="<?php echo $row->id; ?>" href="javascript:void();"><i class="fa fa-remove"></i> <?php echo JText::_('DELETE'); ?></a></span>
				<?php } ?>
				
				<?php if( $editaccess ) { ?>
				<span class="edit-bt hasTip" title="<?php echo JText::_('EDIT'); ?>"><a class="edt btn" href="javascript:void();"><i class="fa fa-edit"></i> <?php echo JText::_('EDIT'); ?></a></span>
				
				<span class="update-bt tempMilesN hasTip" title="<?php echo JText::_('UPDATE'); ?>"><a class="updt btn btn-success" id="<?php echo $row->id; ?>" href="javascript:void();"><i class="fa fa-check"></i> <?php echo JText::_('UPDATE'); ?></a></span>
				<?php } ?>
				
				<span class="cncl-bt tempMilesN"><a class="cncl btn btn-danger" href="javascript:void();"><i class="fa fa-remove"></i> <?php echo JText::_('CANCEL'); ?></a></span>
				
			</td>
			<?php } ?>
            
        </tr>
    <?php
    	$k = 1 - $k;
    }
    ?>
        
    </table>
</div>

<?php if( ($count) && (count($this->tmt)>0) && ($this->notApproved) ) { ?>
	<div class="btn-wrapper approveBtn"  id="toolbar-approve">
		<span onclick="Joomla.submitbutton('approve')" class="btn btn-small btn-success">
		<span class="fa fa-check"></span> <?php echo JText::_('APPROVE'); ?></span>
	</div>
	
	<div class="btn-wrapper rejectBtn"  id="toolbar-reject">
		<span onclick="Joomla.submitbutton('reject')" class="btn btn-small btn-danger">
		<span class="fa fa-remove"></span> <?php echo JText::_('REJECT'); ?></span>
	</div>
<?php } ?>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="milestone" />
<input type="hidden" name="projectid" value="<?php echo $projectid; ?>" />
<input type="hidden" name="tblClass" value="<?php echo $tblClass; ?>" />
<input type="hidden" name="temp" value="<?php echo $temp; ?>" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
