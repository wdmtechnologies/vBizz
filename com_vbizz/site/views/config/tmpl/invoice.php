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
 $ot = JRequest::getInt('ot',0); ?>
<link rel="stylesheet" href="<?php echo JURI::root(); ?>components/com_vbizz/assets/css/vbizz-new.css" type="text/css" />
<link rel="stylesheet" href="<?php echo JURI::root(); ?>components/com_vbizz/assets/css/font-awesome.css" type="text/css" />
<link rel="stylesheet" href="<?php echo JURI::root(); ?>templates/vbizz/css/style.css" type="text/css" />

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
		
		var invoice_setting = jQuery('input[name="invoice_setting"]:checked').val();
		var custom_invoice_prefix = jQuery('input[name="custom_invoice_prefix"]').val();
		var custom_invoice_seq = jQuery('input[name="custom_invoice_seq"]').val();
		var custom_invoice_suffix = jQuery('input[name="custom_invoice_suffix"]').val();
		
		var that=this;

		jQuery.ajax({
			type: "POST",
			dataType:"JSON",
			data: {'option':'com_vbizz', 'view':'config', 'task':'updateInvoice', 'tmpl':'component','invoice_setting':invoice_setting, 'custom_invoice_prefix':custom_invoice_prefix, 'custom_invoice_seq':custom_invoice_seq, 'custom_invoice_suffix':custom_invoice_suffix, 'ot':<?php echo $ot; ?> },
			
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
<table class="adminform table table-striped">
<span class="alert-msg"></span>
	<tbody>
		
		<tr>
			<td><fieldset class="bradio">
            <ul>
                <li><input type="radio" name="invoice_setting" value="1" <?php if($config->invoice_setting==1) echo 'checked="checked"';?> >
				<label><?php echo JText::_('INV_RAND');?></label></li></ul></fieldset></td>
		</tr>
		
		<tr>
			<td><fieldset class="bradio">
            <ul>
                <li><input type="radio" name="invoice_setting" value="2" <?php if($config->invoice_setting==2) echo 'checked="checked"';?> ><label><?php echo JText::_('INV_DATE_SEQ');?></label></li></ul></fieldset></td>
		</tr>
		
		<tr>
			<td><fieldset class="bradio">
            <ul>
                <li><input type="radio" name="invoice_setting" value="3" <?php if($config->invoice_setting==3) echo 'checked="checked"';?> ><label><?php echo JText::_('INPUT_OWN_INV_NO');?></label></li></ul></fieldset></td>
		</tr>
		
		<tr>
			<td><fieldset class="bradio">
            <ul>
                <li><input type="radio" name="invoice_setting" value="4" <?php if($config->invoice_setting==4) echo 'checked="checked"';?> ><label><?php echo JText::_('INV_SEQ');?></label></li></ul></fieldset></td>
		</tr>
		
		<tr>
			<td><fieldset class="bradio">
            <ul>
                <li><input type="radio" name="invoice_setting" value="5" <?php if($config->invoice_setting==5) echo 'checked="checked"';?> ><label><?php echo JText::_('CUST_INV_PRE_SUF');?></label></li></ul></fieldset></td>
		</tr>
		
		<tr>
			<td>
				
				<div class="custom-prefix">
					<label><?php echo JText::_('PREFIX');?>:</label>
					<input class="text_area" type="text" name="custom_invoice_prefix" value="<?php echo $config->custom_invoice_prefix;?>"/>
				</div>
				<div class="custom-seq">
					<label><?php echo JText::_('STARTING_SEQ');?>:</label>
					<input class="text_area" type="text" name="custom_invoice_seq" value="<?php echo $config->custom_invoice_seq;?>"/>
				</div>
				<div class="custom-suffix">
					<label><?php echo JText::_('SUFFIX');?>:</label>
					<input class="text_area" type="text" name="custom_invoice_suffix" value="<?php echo $config->custom_invoice_suffix;?>"/>
				</div>
			</td>
				
		</tr>
		
		<?php if($ot) { ?>
        <tr>
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
