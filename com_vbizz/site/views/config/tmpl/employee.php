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
defined( '_JEXEC' ) or die( 'Restricted access' ); 
 $ot = JRequest::getInt('ot',0); 

?>

<link rel="stylesheet" href="<?php echo JURI::root(); ?>components/com_vbizz/assets/css/vbizz-new.css" type="text/css" />
<?php if($ot) { ?>

  <link rel="stylesheet" href="<?php echo JURI::root(); ?>media/system/css/modal.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo JURI::root(); ?>media/jui/css/chosen.css" type="text/css" />
  <script src="<?php echo JURI::root(); ?>media/system/js/mootools-core.js" type="text/javascript"></script>
  <script src="<?php echo JURI::root(); ?>media/jui/js/jquery.min.js" type="text/javascript"></script>
  <script src="<?php echo JURI::root(); ?>media/jui/js/jquery-noconflict.js" type="text/javascript"></script>
  <script src="<?php echo JURI::root(); ?>media/jui/js/jquery-migrate.min.js" type="text/javascript"></script>
  <script src="<?php echo JURI::root(); ?>media/system/js/core.js" type="text/javascript"></script>
  <script src="<?php echo JURI::root(); ?>media/system/js/mootools-more.js" type="text/javascript"></script>
  <script src="<?php echo JURI::root(); ?>media/system/js/modal.js" type="text/javascript"></script>
  <script src="<?php echo JURI::root(); ?>components/com_vbizz/assets/js/jquery.1.10.js" type="text/javascript"></script>
  <script src="<?php echo JURI::root(); ?>components/com_vbizz/assets/js/jquery-ui.js" type="text/javascript"></script>
  <script src="<?php echo JURI::root(); ?>media/jui/js/chosen.jquery.min.js" type="text/javascript"></script>
  <script type="text/javascript">
jQuery(function($) {
	 $('.hasTip').each(function() {
		var title = $(this).attr('title');
		if (title) {
			var parts = title.split('::', 2);
			$(this).data('tip:title', parts[0]);
			$(this).data('tip:text', parts[1]);
		}
	});
	var JTooltips = new Tips($('.hasTip').get(), {"maxTitleChars": 50,"fixed": false});
});
jQuery(function($) {
	SqueezeBox.initialize({});
	SqueezeBox.assign($('a.modal').get(), {
		parse: 'rel'
	});
});

jQuery(document).ready(function (){
	jQuery('select').chosen({"disable_search_threshold":10,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
});
			
window.setInterval(function(){var r;try{r=window.XMLHttpRequest?new XMLHttpRequest():new ActiveXObject("Microsoft.XMLHTTP")}catch(e){}if(r){r.open("GET","./",true);r.send(null)}},3540000);
  </script>
  
<?php } ?>


<script type="text/javascript">
jQuery(function() {
	
	jQuery(document).on('click','#save-config',function() {
		
		var month = jQuery('select[name="emp_month_cycle"]').val();
		var sal_date = jQuery('select[name="sal_date"]').val();
		var weekoffday = jQuery('select[name="weekoffday[]"]').val();
		var type = jQuery('select[name="sal_transaction_type"]').val();
		var mode = jQuery('select[name="sal_transaction_mode"]').val();
		var account = jQuery('select[name="sal_account"]').val();
		
		var that=this;

		jQuery.ajax({
			type: "POST",
			dataType:"JSON",
			data: {'option':'com_vbizz', 'view':'config', 'task':'updateEmployee', 'tmpl':'component','month':month, 'sal_date':sal_date, 'type':type, 'mode':mode, 'account':account, 'weekoffday':weekoffday, 'ot':<?php echo $ot; ?> },
			
			beforeSend: function() {
				jQuery(that).find("span.loadingbox").show();
			},
			
			complete: function()      {
				jQuery(that).find("span.loadingbox").hide();
			},

			success: function(data){
				jQuery('.alert-msg').text(data.msg);
			}
		});
	});  
	jQuery(".emp_commission_payhead_add").on("click", function(){
		
		jQuery.ajax({
			type: "POST",
			dataType:"JSON",
			data: {'option':'com_vbizz', 'view':'config', 'task':'updateEmployeePayhead', 'tmpl':'component','payhead':jQuery(this).val()},
			
			beforeSend: function() {
				jQuery(that).find("span.loadingbox").show();
			},
			
			complete: function()      {
				jQuery(that).find("span.loadingbox").hide();
			},

			success: function(data){
				jQuery('.alert-msg').text(data.msg);
			}
		});
	});
	
});

</script>
<div id="vbizz">
<div class="content_part">
<table class="adminform table table-striped">
<span class="alert-msg" style="color:red;"></span>
	<tbody> 
		
		<tr>
			<th width="200"><label class="hasTip" title="<?php echo JText::_('MONTHCYCLETXT'); ?>"><?php echo JText::_('MONTHSTART');?></label></th>
			<td>
				<select class="emp_month_cycle" name="emp_month_cycle" >
					<option value=""><?php echo JText::_("SELECT_DAY"); ?></option>
					<?php for($i=1;$i<32;$i++) { ?>
						<option value="<?php echo $i; ?>" <?php if($i==$config->emp_month_cycle) echo 'selected="selected"'; ?>><?php echo $i; ?></option>
					<?php }  ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<th><label class="hasTip" title="<?php echo JText::_('SALDATETXT'); ?>"><?php echo JText::_('SAL_DATE');?></label></th>
			<td>
				<select class="sal_date" name="sal_date" >
					<option value=""><?php echo JText::_("SELECT_DAY"); ?></option>
					<?php for($i=1;$i<32;$i++) { ?>
						<option value="<?php echo $i; ?>" <?php if($i==$config->sal_date) echo 'selected="selected"'; ?>><?php echo $i; ?></option>
					<?php }  ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<th><label class="hasTip" title="<?php echo JText::_('WEEKOFFTXT'); ?>"><?php echo JText::_('WEEKOFFDAY');?></label></th>
			<td>
				<select class="week-off" name="weekoffday[]" multiple="multiple" size="7" style="width:200px">
					 
					<option value="fc-mon" <?php if(in_array('fc-mon',$config->weekoffday)) echo 'selected="selected"';?>><?php echo JText::_('MONDAY');?></option>
					<option value="fc-tue" <?php if(in_array('fc-tue',$config->weekoffday)) echo 'selected="selected"';?>><?php echo JText::_('TUESDAY');?></option>
					<option value="fc-wed" <?php if(in_array('fc-wed',$config->weekoffday)) echo 'selected="selected"';?>><?php echo JText::_('WEDNESDAY');?></option>
					<option value="fc-thu" <?php if(in_array('fc-thu',$config->weekoffday)) echo 'selected="selected"';?>><?php echo JText::_('THURSDAY');?></option>
					<option value="fc-fri" <?php if(in_array('fc-fri',$config->weekoffday)) echo 'selected="selected"';?>><?php echo JText::_('FRIDAY');?></option>
					<option value="fc-sat" <?php if(in_array('fc-sat',$config->weekoffday)) echo 'selected="selected"';?>><?php echo JText::_('SATURDAY');?></option>
					<option value="fc-sun" <?php if(in_array('fc-sun',$config->weekoffday)) echo 'selected="selected"';?>><?php echo JText::_('SUNDAY');?></option>
				</select>
			</td>
		</tr>
		<tr>
        <th><label class="hasTip" title="<?php echo JText::_('ENABLED_EMPLOYEE_COMMISSION_TXT');?>"><?php echo JText::_('ENABLED_EMPLOYEE_COMMISSION');?></label></th>
        <td><select name="employeecommission" id="employeecommission"><option value="0"><?php echo JText::_("SELECT_OPTION");?></option>
		<option value="1"<?php echo $config->employeecommission==1?' selected="selected"':''?>><?php echo JText::_( 'ENABLE' );?></option>
		<option value="2"<?php echo $config->employeecommission==2?' selected="selected"':''?>><?php echo JText::_( 'DISABLE' );?></option></select>
            
        </td>
    </tr>
	<tr>
			<th width="200"><label class="hasTip" title="<?php echo JText::_('EMPLOYEE_COMMISSION_PAYHEAD'); ?>"><?php echo JText::_('EMPLOYEE_COMMISSION_PAYHEAD');?></label></th>
			<td>
				<input type="text" name="emp_commission_payhead" id="emp_commission_payhead" value="<?php echo !empty($config->emp_commission_payhead)?$config->emp_commission_payhead:'';?>" /><span class="emp_commission_payhead_add btn"><?php echo JText::_("EMPLOYEE_COMMISSION_PAYHEAD_ADD");?></span>
			</td>
		</tr>
		<tr>
			<th><label class="hasTip" title="<?php echo JText::_('TYPTXT'); ?>"><?php echo $config->type_view; ?></label></th>
			<td>
				<select name="sal_transaction_type" id="sal_transaction_type">
				<option value=""><?php echo JText::_('SELECT').' '.$config->type_view; ?></option>
				<?php	for($i=0;$i<count($type);$i++)	{	?>
				<option value="<?php echo $type[$i]->id; ?>" <?php if($type[$i]->id==$config->sal_transaction_type) echo 'selected="selected"'; ?>> <?php echo JText::_($type[$i]->treename); ?> </option>
				<?php	}	?>
				</select>
			</td>
		</tr>
	
		<tr>
			<th><label class="hasTip" title="<?php echo JText::_('MODTXT'); ?>"><?php echo JText::_('TRANSACTION_MODE'); ?></label></th>
			<td>
				<select name="sal_transaction_mode" id="sal_transaction_mode">
				<option value=""><?php echo JText::_('SELECT_TRANSACTION_MODE'); ?></option>
				<?php	for($i=0;$i<count($mode);$i++)	{	?>
				<option value="<?php echo $mode[$i]->id; ?>" <?php if($mode[$i]->id==$config->sal_transaction_mode) echo 'selected="selected"'; ?>> <?php echo JText::_($mode[$i]->title); ?> </option>
				<?php	}	?>
				</select>
			</td>
		</tr>
		
		<tr>
			<th><label class="hasTip" title="<?php echo JText::_('SELECTACCTXT'); ?>"><?php echo JText::_('SELECT_ACCOUNT'); ?></label></th>
			<td>
				<select name="sal_account" id="sal_account">
				<option value=""><?php echo JText::_('SELECT_ACCOUNT'); ?></option>
				<?php	for($i=0;$i<count($account);$i++)	{	?>
				<option value="<?php echo $account[$i]->id; ?>" <?php if($account[$i]->id==$config->sal_account) echo 'selected="selected"'; ?>> <?php echo JText::_($account[$i]->account_name); ?> </option>
				<?php	}	?>
				</select>
			</td>
		</tr>
		
		<?php if($ot) { ?>
        <tr>
		<td></td>
		<td>
			<div class="config-save">
				<a id="save-config" href='javascript:void(0);' class="btn btn-success">
					<span class="fa fa-check"></span> <?php echo JText::_('SAVE'); ?>
					<span class="loadingbox" style="display:none;"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/loading.gif"   />' ?></span>
				</a>
			</div>
		</td>
        </tr>
        <?php } ?>
		
	</tbody>
</table>
</div>
</div>

