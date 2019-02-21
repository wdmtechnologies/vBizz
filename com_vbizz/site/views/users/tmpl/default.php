<?php
/*------------------------------------------------------------------------
# com_vbizz - vReview
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.wdmtech.com
# Technical Support: Forum - https://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');

?>

<script type="text/javascript">
	
	Joomla.submitbutton = function(task) {
		if (task == 'cancel') {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			var form = document.adminForm;
	
			if(form.name.value == "")	{
				alert("<?php echo JText::_('PLZ_ENTER_NAME'); ?>");
				return false;
			}
			
			if(form.username.value == "")	{
				alert("<?php echo JText::_('ENTER_USERNAME'); ?>");
				return false;
			}
			
			if(form.email.value == "")	{
				alert("<?php echo JText::_('PLZ_ENTER_EMAIL'); ?>");
				return false;
			}
			
			if(typeof(validateit) == 'function')	{
				if(!validateit())
					return false;
			}
			
			Joomla.submitform(task, document.getElementById('adminForm'));
			
		}
	}
	
</script>

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

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('USER_PROFILE'); ?></h1>
	</div>
</header>

<div class="content_part">

<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=users');?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="row-fluid">
<div class="span12">
	<div class="btn-toolbar" id="toolbar">
		<div class="btn-wrapper"  id="toolbar-apply">
			<span onclick="Joomla.submitbutton('save')" class="btn btn-small btn-success">
			<span class="fa fa-check"></span> <?php echo JText::_('SAVE'); ?></span>
		</div>
		
		<div class="btn-wrapper"  id="toolbar-cancel">
			<span onclick="Joomla.submitbutton('cancel')" class="btn btn-small">
			<span class="fa fa-close"></span> <?php echo JText::_('CANCEL'); ?></span>
		</div>
	</div>
</div>
</div>

<div class="overview">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'OVERVIEW' ); ?></legend>
	<ul>
		<li><?php	echo JText::_('NEW_USERS_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">

<fieldset class="adminform">
<legend><?php echo JText::_( 'EDIT_USER' ); ?></legend>
	<table class="adminform table table-striped">
		<tr>
			<th width="200"><label class="hasTip" title="<?php echo JText::_('NAMETXT');?>">
				<?php echo JText::_('NAME');?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
			</td>
			<td><input class="text_area" type="text" name="name" id="name" value="<?php echo $this->item->uname;?>"/></td>
		</tr>
		
		<tr>
			<th>
				<label class="hasTip" title="<?php echo JText::_('USERNAME'); ?>">
					<?php echo JText::_('USERNAME'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>
				</label>
			</th>
			<td><input type="text" name="username" id="username" class="text_area" value="<?php echo $this->item->username; ?>" /></td>
		</tr>
		
		<tr>
			<th width="200"><label class="hasTip" title="<?php echo JText::_('EMAILTXT'); ?>">
				<?php echo JText::_('EMAIL'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
			</th>
			<td><input class="text_area" type="text" name="email" id="email" value="<?php echo $this->item->uemail;?>" /></td>
		</tr>
		
		<tr>
			<th><label class="hasTip" title="<?php echo JText::_('PASSWORD'); ?>"><?php echo JText::_('PASSWORD'); ?></label></th>
			<td><input type="password" class="text_area" name="password" autocomplete="false" value="" cols="39" rows="5" /></td>
		</tr>
		
		<tr>
			<th><label class="hasTip" title="<?php echo JText::_('CONF_PASSWORD'); ?>"><?php echo JText::_('CONF_PASSWORD'); ?></label></th>
			<td><input type="password" class="text_area" name="password2" autocomplete="false" value="" cols="39" rows="5" /></td>
		</tr>
		
		<tr id="morebtn">
			<th colspan="0">
			<input type="button" id="more" value="<?php echo JText::_('MORE_OPTION'); ?>" class="btn btn-success" style="margin-bottom:10px" />
			</th>
			<td></td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<th width="200"><label class="hasTip" title="<?php echo JText::_('COMPTXT'); ?>"><?php echo JText::_('COMPANY'); ?></label></th>
			<td><input class="text_area" type="text" name="company" id="company" value="<?php echo $this->item->company;?>" /></td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<th width="200"><label class="hasTip" title="<?php echo JText::_('CONTACTTXT'); ?>"><?php echo JText::_('CONTACT_NO'); ?></label></th>
			<td><input class="text_area" type="text" name="phone" id="phone" value="<?php echo $this->item->phone;?>"/></td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<th width="200"><label class="hasTip" title="<?php echo JText::_('WEBSITETXT'); ?>"><?php echo JText::_('WEBSITE'); ?></label></th>
			<td><input class="text_area" type="text" name="website" id="website" value="<?php echo $this->item->website;?>" /></td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<th><label class="hasTip" title="<?php echo JText::_('IMTEXT'); ?>"><?php echo JText::_('INST_MANAGER'); ?></label></th>
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
			<th width="200"><label class="hasTip" title="<?php echo JText::_('IMIDTXT'); ?>"><?php echo JText::_('IM_ID'); ?></label></th>
			<td><input class="text_area" type="text" name="im_id" id="im_id" value="<?php echo $this->item->im_id;?>" /></td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<th><label class="hasTip" title="<?php echo JText::_('COUNTRYTXT'); ?>"><?php echo JText::_('COUNTRY'); ?></label></th>
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
			<th><label class="hasTip" title="<?php echo JText::_('STATETXT'); ?>"><?php echo JText::_('STATE'); ?></label></th>
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
			<th width="200"><label class="hasTip" title="<?php echo JText::_('ADDTXT'); ?>"><?php echo JText::_('ADDRESS'); ?></label></th>
			<td><input class="text_area" type="text" name="address" id="address" value="<?php echo $this->item->address;?>" /></td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<th width="200"><label class="hasTip" title="<?php echo JText::_('CITYTXT'); ?>"><?php echo JText::_('CITY'); ?></label></th>
			<td><input class="text_area" type="text" name="city" id="city" value="<?php echo $this->item->city;?>" /></td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<th width="200"><label class="hasTip" title="<?php echo JText::_('ZIPTXT'); ?>"><?php echo JText::_('ZIPCODE'); ?></label></th>
			<td><input class="text_area" type="text" name="zip" id="zip" value="<?php echo $this->item->zip;?>"/></td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<th width="200"><label class="hasTip" title="<?php echo JText::_('CMNTXT'); ?>"><?php echo JText::_('COMMENTS'); ?></label></th>
			<td><textarea class="text_area" name="comments" id="comments" rows="4" cols="50"><?php echo $this->item->comments;?></textarea></td>
		</tr>
		
		<tr class="tohide" style="display:none;">
			<th><label class="hasTip" title="<?php echo JText::_('PROFILE_PICS'); ?>"><?php echo JText::_('PROFILE_PICS'); ?> </label></th>
			<td><input type="file" name="profile_pic" id="profile_pic" class="inputbox required" size="50" value=""/>
			<?php if($this->item->profile_pic) echo '<img src="'.JURI::root().'components/com_vbizz/uploads/profile_pics/'.$this->item->profile_pic.'" style="height:60px;vertical-align: middle;" />'?>
			</td>
		</tr>
		<?php if(VaccountHelper::checkOwnerGroup()|| VaccountHelper::checkVenderGroup()) { ?>
		<tr class="tohide" style="display:none;">
			<th><label class="hasTip" title="<?php echo JText::_('COMAPNY_PICS'); ?>"><?php echo JText::_('COMAPNY_PICS'); ?> </label></th>
			<td><input type="file" name="company_pic" id="profile_pic" class="inputbox required" size="50" value=""/>
			<?php if($this->item->company_pic) echo '<img src="'.JURI::root().'components/com_vbizz/uploads/profile_pics/'.$this->item->company_pic.'" style="height:60px;vertical-align: middle;" />'?>
			</td>
		</tr>
		<?php } ?>
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
</div>
</div>



