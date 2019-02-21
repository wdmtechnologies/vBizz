<?php
/*------------------------------------------------------------------------
# com_vbizz - vReview
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');

JHtml::_('formbehavior.chosen', 'select');

?>

<script type="text/javascript">
jQuery(function() {
	
	jQuery("#country_id").change(function(){
		var country_id = jQuery("#country_id").val();
		jQuery.ajax({
			type: "POST",
			dataType:"JSON",
			data: {'option':'com_vbizz','view':'users', 'task':'getState','tmpl':'component', 'country_id':country_id},
			
			beforeSend: function() {
				jQuery(".loadingbox").show();
			},
			complete: function()      {
				jQuery(".loadingbox").hide();
			},
			success: function(data){
				if(data.result=="success"){
					jQuery("#states").html(data.htm);
					jQuery('select').chosen({"disable_search_threshold":0,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
				}
			}
		});
	});
	
	jQuery(document).on('click','#more',function() {
		jQuery('.tohide').toggle();
		jQuery(this).toggleClass('more-opt');
		if(jQuery(this).hasClass('more-opt')){
			jQuery(this).val('<?php echo JText::_('LESS_OPTION'); ?>');         
		} else {
			jQuery(this).val('<?php echo JText::_('MORE_OPTION'); ?>');
		}
	});

});
</script>

<script type="text/javascript">
	
	Joomla.submitbutton = function(task) {
		if (task == 'cancel') {
			
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			
			 
			if(!jQuery('input[name="name"]').val()){
				alert('<?php echo JText::_('PLZ_ENTER_OWNER_NAME', true); ?>');
				document.adminForm.name.focus();
				return false;
			}
			if(!jQuery('input[name="username"]').val()){
				alert('<?php echo JText::_('PLZ_ENTER_USERNAME', true); ?>');
				document.adminForm.username.focus();
				return false;
			}
			if(!jQuery('input[name="email"]').val()){
				alert('<?php echo JText::_('PLZ_ENTER_OWNER_EMAIL', true); ?>');
				 document.adminForm.email.focus();
				return false;
			}
						
			Joomla.submitform(task, document.getElementById('adminForm'));
			
		}
	}
	
</script>

<div id="dabpanel">

<form action="index.php?option=com_vbizz&view=users" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="col100">
<fieldset class="adminform">
<legend><?php echo JText::_( 'DETAILS' ); ?></legend>
	<table class="adminform table table-striped">
		<tr>
			<td width="200"><label class="hasTip" title="<?php echo JText::_('OWNERNAMETXT');?>">
				<?php echo JText::_('NAME');?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
			</td>
			<td><input class="text_area" type="text" name="name" id="name" value="<?php echo $this->item->uname;?>"/></td>
		</tr>
		
		<tr>
			<td>
				<label class="hasTip" title="<?php echo JText::_('USERNAME'); ?>">
					<?php echo JText::_('USERNAME'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>
				</label>
			</td>
			<td><input type="text" name="username" id="username" class="text_area" value="<?php echo $this->item->username; ?>" /></td>
		</tr>
		
		<tr>
			<td width="200"><label class="hasTip" title="<?php echo JText::_('OWNEREMAILTXT'); ?>">
				<?php echo JText::_('EMAIL'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
			</td>
			<td><input class="text_area" type="text" name="email" id="email" value="<?php echo $this->item->uemail;?>" /></td>
		</tr>
		
		<tr>
			<td><label class="hasTip" title="<?php echo JText::_('PASSWORD'); ?>"><?php echo JText::_('PASSWORD'); ?></label></td>
			<td><input type="password" class="text_area" name="password" autocomplete="false" value="" cols="39" rows="5" /></td>
		</tr>
		
		<tr>
			<td><label class="required"><?php echo JText::_('BLOCK'); ?></label></td>
			<td>
				<fieldset class="radio btn-group" style="margin-bottom:9px;">
					<label for="block1" id="block-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
					<input type="radio" name="block" id="block1" value="1" <?php if($this->item->block) echo 'checked="checked"';?>/>
					<label for="block0" id="block-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
					<input type="radio" name="block" id="block0" value="0" <?php if(!$this->item->block) echo 'checked="checked"';?>/>
				</fieldset>
			</td>
		</tr>
		
		<tr id="morebtn">
            <th colspan="0">
				<input type="button" id="more" value="<?php echo JText::_('MORE_OPTION'); ?>" class="btn btn-success" style="margin-bottom:10px" />
			</th>
			<td></td>
        </tr>
		
		<tr class="tohide" style="display:none;">
			<td width="200"><label class="hasTip" title="<?php echo JText::_('COMPTXT'); ?>"><?php echo JText::_('COMPANY'); ?></label></td>
			<td><input class="text_area" type="text" name="company" id="company" value="<?php echo $this->item->company;?>" /></td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<td width="200"><label class="hasTip" title="<?php echo JText::_('CONTACTTXT'); ?>"><?php echo JText::_('CONTACT_NO'); ?></label></td>
			<td><input class="text_area" type="text" name="phone" id="phone" value="<?php echo $this->item->phone;?>"/></td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<td width="200"><label class="hasTip" title="<?php echo JText::_('WEBSITETXT'); ?>"><?php echo JText::_('WEBSITE'); ?></label></td>
			<td><input class="text_area" type="text" name="website" id="website" value="<?php echo $this->item->website;?>" /></td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<td><label class="hasTip" title="<?php echo JText::_('IMTEXT'); ?>"><?php echo JText::_('INST_MANAGER'); ?></label></td>
			<td>
				<select name="instant_messenger" id="instant_messenger">
				<option value=""><?php echo JText::_('SELECT_IM'); ?></option>
				<option value="skype" <?php if($this->item->instant_messenger=="skype") echo 'selected="selected"'; ?>><?php echo JText::_('Skype');?></option>
				<option value="gtalk" <?php if($this->item->instant_messenger=="gtalk") echo 'selected="selected"'; ?>><?php echo JText::_('GTalk');?></option>
				<option value="yahoo" <?php if($this->item->instant_messenger=="yahoo") echo 'selected="selected"';?>><?php echo JText::_('Yahoo');?></option>
				<option value="nimbuzz" <?php if($this->item->instant_messenger=="nimbuzz") echo 'selected="selected"';?>><?php echo JText::_('Nimbuzz');?></option>
				<option value="ebuddy" <?php if($this->item->instant_messenger=="ebuddy") echo 'selected="selected"';?>><?php echo JText::_('eBuddy');?></option>
				<option value="aim" <?php if($this->item->instant_messenger=="aim") echo 'selected="selected"';?>><?php echo JText::_('AIM');?></option>
				<option value="ichat" <?php if($this->item->instant_messenger=="ichat") echo 'selected="selected"'; ?>><?php echo JText::_('iChat');?></option>
				<option value="myspace" <?php if($this->item->instant_messenger=="myspace") echo 'selected="selected"'; ?>><?php echo JText::_('MySpace');?></option>
				<option value="meebo" <?php if($this->item->instant_messenger=="meebo") echo 'selected="selected"'; ?>><?php echo JText::_('Meebo');?></option>
				<option value="icq" <?php if($this->item->instant_messenger=="icq") echo 'selected="selected"'; ?>><?php echo JText::_('ICQ');?></option>
				<option value="digsby" <?php if($this->item->instant_messenger=="digsby") echo 'selected="selected"';?>><?php echo JText::_('Digsby');?></option>
				<option value="msn" <?php if($this->item->instant_messenger=="msn") echo 'selected="selected"';?>><?php echo JText::_('MSN');?></option>
				<option value="trillian" <?php if($this->item->instant_messenger=="trillian") echo 'selected="selected"';?>><?php echo JText::_('Trillian');?></option>
				<option value="pidgin" <?php if($this->item->instant_messenger=="pidgin") echo 'selected="selected"';?>><?php echo JText::_('Pidgin');?></option>
				<option value="jitsi" <?php if($this->item->instant_messenger=="jitsi") echo 'selected="selected"';?>><?php echo JText::_('Jitsi');?></option>
				<option value="miranda" <?php if($this->item->instant_messenger=="miranda") echo 'selected="selected"';?>><?php echo JText::_('Miranda');?></option>
				<option value="qnext" <?php if($this->item->instant_messenger=="qnext") echo 'selected="selected"';?>><?php echo JText::_('Qnext');?></option>
				<option value="adium" <?php if($this->item->instant_messenger=="adium") echo 'selected="selected"';?>><?php echo JText::_('Adium');?></option>
				<option value="empathy" <?php if($this->item->instant_messenger=="empathy") echo 'selected="selected"';?>><?php echo JText::_('Empathy');?></option>
				</select>
			</td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<td width="200"><label class="hasTip" title="<?php echo JText::_('IMIDTXT'); ?>"><?php echo JText::_('IM_ID'); ?></label></td>
			<td><input class="text_area" type="text" name="im_id" id="im_id" value="<?php echo $this->item->im_id;?>" /></td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<td><label class="hasTip" title="<?php echo JText::_('COUNTRYTXT'); ?>"><?php echo JText::_('COUNTRY'); ?></label></td>
			<td>
				<select name="country_id" id="country_id">
				<option value=""><?php echo JText::_('SELECT_COUNTRY'); ?></option>
				<?php	for($i=0;$i<count($this->countries);$i++)	{	?>
				<option value="<?php echo $this->countries[$i]->id; ?>" <?php if($this->countries[$i]->id==$this->item->country_id) echo 'selected="selected"'; ?>> <?php echo JText::_($this->countries[$i]->country_name); ?></option>
				<?php	}	?>
				</select>
			</td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<td><label class="hasTip" title="<?php echo JText::_('STATETXT'); ?>"><?php echo JText::_('STATE'); ?></label></td>
			<td id="states">
				<select name="state_id" id="state_id">
				<option value=""><?php echo JText::_('SELECT_STATE'); ?></option>
				<?php	for($i=0;$i<count($this->states);$i++)	{	?>
				<option value="<?php echo $this->states[$i]->id; ?>"<?php if($this->states[$i]->id==$this->item->state_id) echo 'selected="selected"'; ?>> <?php echo JText::_($this->states[$i]->state_name); ?></option>
				<?php	}	?>
				</select>
			</td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<td width="200"><label class="hasTip" title="<?php echo JText::_('ADDTXT'); ?>"><?php echo JText::_('ADDRESS'); ?></label></td>
			<td><input class="text_area" type="text" name="address" id="address" value="<?php echo $this->item->address;?>" /></td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<td width="200"><label class="hasTip" title="<?php echo JText::_('CITYTXT'); ?>"><?php echo JText::_('CITY'); ?></label></td>
			<td><input class="text_area" type="text" name="city" id="city" value="<?php echo $this->item->city;?>" /></td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<td width="200"><label class="hasTip" title="<?php echo JText::_('ZIPTXT'); ?>"><?php echo JText::_('ZIPCODE'); ?></label></td>
			<td><input class="text_area" type="text" name="zip" id="zip" value="<?php echo $this->item->zip;?>"/></td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<td width="200"><label class="hasTip" title="<?php echo JText::_('CMNTXT'); ?>"><?php echo JText::_('COMMENTS'); ?></label></td>
			<td><textarea class="text_area" name="comments" id="comments" rows="4" cols="50"><?php echo $this->item->comments;?></textarea></td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<td><label class="hasTip" title="<?php echo JText::_('PROFILE_PICS'); ?>"><?php echo JText::_('PROFILE_PICS'); ?></label></td>
			<td><input type="file" name="profile_pic" id="profile_pic" class="inputbox required" size="50" value=""/>
			<?php if($this->item->profile_pic) echo '<img src="'.JURI::root().'components/com_vbizz/uploads/profile_pics/'.$this->item->profile_pic.'" style="width:15%; height:15%;" />' ?>
			</td>
		</tr>
		
	</table>
</fieldset>
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="userid" value="<?php echo $this->item->userid; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="users" />
</form>
</div>



