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
<link rel="stylesheet" href="<?php echo JURI::root(); ?>components/com_vbizz/assets/css/vbizz.css" type="text/css" />

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
		
		var cobrandLogin = jQuery('input[name="cobrandLogin"]').val();
		var cobrandPassword = jQuery('input[name="cobrandPassword"]').val();
		var restUrl = jQuery('input[name="restUrl"]').val();
		var cob_uname = jQuery('input[name="cob_uname"]').val();
		var cob_password = jQuery('input[name="cob_password"]').val();
		
		var that=this;

		jQuery.ajax({
			url:"index.php",
			type: "POST",
			dataType:"JSON",
			data: {'option':'com_vbizz', 'view':'config', 'task':'updateYodlee', 'tmpl':'component','cobrandLogin':cobrandLogin, 'cobrandPassword':cobrandPassword, 'restUrl':restUrl, 'cob_uname':cob_uname, 'cob_password':cob_password, 'ot':<?php echo $ot; ?> },
			
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

<table class="adminform table table-striped">
<span class="alert-msg" style="color:red;"></span>
	<tbody> 
		<tr>
			<th width="200"><label class="hasTip" title="<?php echo JText::_('COBRANDLOGINTXT');?>"><?php echo JText::_('COBRAND_LOGIN');?></label></th>
			<td><input class="span text_area" type="text" title="cobrandLogin" name="cobrandLogin" id="cobrandLogin" placeholder="<?php echo JText::_('ENTER_COBRAND_LOGIN');?>" value="<?php echo $config->cobrandLogin;?>"/></td>
		</tr>
		
		<tr>
			<th><label class="hasTip" title="<?php echo JText::_('COBRANDPWDTXT');?>"><?php echo JText::_('COBRAND_PWD');?></label></th>
			<td><input class="span text_area" type="text" title="cobrandPassword" name="cobrandPassword" id="cobrandPassword" placeholder="<?php echo JText::_('ENTER_COBRAND_PASSWORD');?>" value="<?php echo $config->cobrandPassword;?>"/></td>
		</tr>
		
		<tr>
			<th><label class="hasTip" title="<?php echo JText::_('RESTURLTXT');?>"><?php echo JText::_('REST_URL');?></label></th>
			<td><input class="span text_area" type="text" title="restUrl" name="restUrl" id="restUrl" placeholder="<?php echo JText::_('ENTER_REST_URL');?>" value="<?php echo $config->restUrl;?>" /></td>
		</tr>
		
		<tr>
			<th><label class="hasTip" title="<?php echo JText::_('YODLEELOGINTXT');?>"><?php echo JText::_('YODLEE_USERNAME');?></label></th>
			<td><input class="span text_area" type="text" title="cob_uname" name="cob_uname" id="cob_uname" placeholder="<?php echo JText::_('ENTER_YODLEE_LOGIN');?>" value="<?php echo $config->cob_uname;?>"/></td>
		</tr>
		
		<tr>
			<th><label class="hasTip" title="<?php echo JText::_('YODLEEPWDTXT');?>"><?php echo JText::_('YODLEE_PWD');?></label></th>
			<td><input class="span text_area" type="text" title="cob_password" name="cob_password" id="cob_password" placeholder="<?php echo JText::_('ENTER_YODLEE_PASSWORD');?>" value="<?php echo $config->cob_password;?>"/></td>
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
