<?php
/*------------------------------------------------------------------------
# mod_vbizz_Notification - vBizz Notification
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access'); 
JHTML::_('behavior.tooltip');
			$configuration = VaccountHelper::getConfig();
			$ownertimezone = VaccountHelper::getDateDefaultTimeZoneName();
			$config = JFactory::getConfig();
			$localtimezone = $config->get('offset');
			$localtimezone = 'GMT';
			$lang = JFactory::getLanguage();
			$tagname = $lang->getTag();
					
?>


<script type="text/javascript">
jQuery(function() {
	
	/* jQuery('body').not('#notify').click(function () {    		
		jQuery('#notifications').hide();
	}); */
	
	

	jQuery(document).on('click','#notify',function() {
		jQuery('#notifications').slideToggle('show-notify');
		jQuery(this).toggleClass('show-notify');
		jQuery('.count-notes').remove();
		var that = this;
		
		if( (jQuery(this).hasClass('show-notify')) && (!jQuery(this).hasClass('notify-update')) ){
			jQuery.ajax({
				type: "POST",
				dataType:"JSON",
				data: { 'option':'com_vbizz', 'task':'updateNotes', 'tmpl':'component' },
				
				success: function(data){
					if(data.result=="success"){
						jQuery(that).addClass('notify-update');
					}
				}

			});
		}
	});
	
	
	
	jQuery(document).click(function(e) {
		if (!jQuery(e.target).parents().andSelf().is('#notify')) {
			jQuery("#notify").removeClass("show-notify");
			//jQuery('#notifications').hide( "slide", { direction: "up" }, "slow" );
			jQuery('#notifications').slideUp();
		}

    });
	jQuery("#notifications").click(function (e) {
		e.stopPropagation();
	});
	
	
});
function clear_all() {
		
		jQuery.ajax({
			type: "POST",
			dataType:"JSON",
			data: { 'option':'com_vbizz', 'task':'clearNotes', 'tmpl':'component' },
			
			success: function(data){
				if(data.result=="success"){
					var ht = '<div class="notify-parts"><div class="notify-result"><div class="notify-title"><span><?php echo JText::_('NO_NOTIFICATIONS'); ?></span></div></div></div>';

					jQuery('#notifications').html(ht);
				}
			}

		});
		
	}
</script>

<form action="" method="post" name="vbNotifyForm" id="vbNotifyForm">

<div class="vbizz-notification">

	<div class="vb_notify">
		<a id="notify" class="notify <?php if(empty($notes)) { ?>notify-update<?php } ?> faa-parent animated-hover faa-slow" href="javascript:void(0);" title="<?php echo JText::_('NOTIFICATION'); ?>"><i class="fa fa-bell faa-ring faa-slow"></i><?php if($countNotes>0) { ?><span class="count-notes"><?php echo $countNotes; ?></span><?php } ?></a>
	</div>
	<div class="notifications" id="notifications">
	
		<?php if(!empty($notes)) { ?>
		<div class="clear-all"><span class="noti_title"><?php echo JText::_('NOTIFICATIONS'); ?></span><a id="clear_all" class="clear_all" href="javascript:void(0);" onclick="clear_all();"><i class="fa fa-close"></i> <?php echo JText::_('CLEAR_ALL'); ?></a></div>
		<?php } ?>
		
		<div class="sub-notification mCustomScrollbar">
		<?php for($i=0;$i<count($notes);$i++) {
			$row = $notes[$i];
			$link = JRoute::_('index.php?option=com_vbizz&view='.$row->views);
		?>
		<div class="notify-parts">
			<div class="notify-result">
				<div class="notify-title">
					<a href="<?php echo JRoute::_('index.php?option=com_vbizz&view='.$row->views); ?>"><span><?php
                      
					
					 $schedule_date = new DateTime($row->created, new DateTimeZone($localtimezone) );
                     $schedule_date->setTimeZone(new DateTimeZone($ownertimezone));$format = !empty($configuration->date_format)?$configuration->date_format.', g:i A':'Y-m-d, g:i A';
                     $triggerOn =  $schedule_date->format($format); 
					if(strpos($row->comments, ' on ')){
					$row->comments = substr($row->comments, 0, strpos($row->comments, ' on ')).' on '.$triggerOn;	
					}
					if(strpos($row->comments, ' en ')){
					$row->comments = substr($row->comments, 0, strpos($row->comments, ' en ')).' en '.$triggerOn;	
					}
					echo JText::_($row->comments); ?></span></a>
				</div>
			</div>
		</div>
		<?php } ?>
		<?php if(empty($notes)) { ?>
			<div class="notify-parts">
				<div class="notify-result">
					<div class="notify-title">
						<span><?php echo JText::_('NO_NOTIFICATIONS'); ?></span>
					</div>
				</div>
			</div>
		<?php } ?>
		</div>
	</div>

</div>

</form>