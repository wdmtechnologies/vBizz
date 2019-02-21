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


//check joomla version
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion>='3.0') {
	JHtml::_('formbehavior.chosen', 'select');
}

 ?>
 <script>
 jQuery(document).ready(function()

{

    jQuery('*[rel=tooltip]').tooltip()

 

    // Turn radios into btn-group

    jQuery('.radio.btn-group label').addClass('btn');

    jQuery(".btn-group label:not(.active)").click(function()

    {

        var label = jQuery(this);

        var input = jQuery('#' + label.attr('for'));

 

        if (!input.prop('checked')) {

            label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');

            if (input.val() == ''|| input.val() == 0) {

                label.addClass('active btn-danger');

            } else {

                label.addClass('active btn-success');

            }

            input.prop('checked', true);

        }

    });

    jQuery(".btn-group input[checked=checked]").each(function()

    {

        if (jQuery(this).val() == '' || jQuery(this).val() == 0) { 

            jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-danger');

        }  else {

            jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-success');

        }

    });

               

});
 </script>
 

	<div id="dialog" title="<?php echo JText::_('ATTENDANCE'); ?>">
		<table class="adminform table table-striped">
			<tbody id="attendance-params">
				<tr>
					<th><label><?php echo JText::_('PRESENT'); ?></label></th>
					<td>
						<fieldset class="radio btn-group" style="margin-bottom:9px;">
						<label for="present1" id="present-lbl" class="radio btn"><?php echo JText::_( 'YS' ); ?></label>
						<input type="radio" name="present" id="present1" value="1" <?php if($attend->present) echo 'checked="checked"';?>/>
						<label for="present0" id="present-lbl" class="radio btn"><?php echo JText::_( 'NOS' ); ?></label>
						<input type="radio" name="present" id="present0" value="0" <?php if(!$attend->present) echo 'checked="checked"';?>/>
						</fieldset>
					</td>
				</tr>
				
				<tr>
					<th><label><?php echo JText::_('HALFDAY'); ?></label></th>
					<td>
						<fieldset class="radio btn-group" style="margin-bottom:9px;">
						<label for="halfday1" id="halfday-lbl" class="radio btn"><?php echo JText::_( 'YS' ); ?></label>
						<input type="radio" name="halfday" id="halfday1" value="1" <?php if($attend->halfday) echo 'checked="checked"';?>/>
						<label for="halfday0" id="halfday-lbl" class="radio btn"><?php echo JText::_( 'NOS' ); ?></label>
						<input type="radio" name="halfday" id="halfday0" value="0" <?php if(!$attend->halfday) echo 'checked="checked"';?>/>
						</fieldset>
					</td>
				</tr>
				
				<tr>
					<th><label><?php echo JText::_('PAID'); ?></label></th>
					<td>
						<fieldset class="radio btn-group" style="margin-bottom:9px;">
						<label for="paid1" id="paid-lbl" class="radio btn"><?php echo JText::_( 'YS' ); ?></label>
						<input type="radio" name="paid" id="paid1" value="1" <?php if($attend->paid) echo 'checked="checked"';?>/>
						<label for="paid0" id="paid-lbl" class="radio btn"><?php echo JText::_( 'NOS' ); ?></label>
						<input type="radio" name="paid" id="paid0" value="0" <?php if(!$attend->paid) echo 'checked="checked"';?>/>
						</fieldset>
					</td>
				</tr>
				
				<tr>
					<th colspan="0">
						<input type="button" class="send btn" value="<?php echo JText::_('SEND'); ?>" class="btn btn-success" style="margin-bottom:10px" />
						<span class="loadingbox" style="display:none;"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/loading.gif"   />' ?></span>
					</th>
				</tr>
				<input type="hidden" name="date" value="<?php echo $date; ?>" />
				<input type="hidden" name="employee" value="<?php echo $employee; ?>" />
				<input type="hidden" name="divNO" value="<?php echo $divNO; ?>" />
			</tbody>
		</table>
	</div>
