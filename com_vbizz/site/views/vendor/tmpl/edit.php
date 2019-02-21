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
JHTML::_('behavior.modal');
$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->vendor_acl->get('addaccess');
$edit_access = $this->config->vendor_acl->get('editaccess');
$delete_access = $this->config->vendor_acl->get('deleteaccess');
$assign_access = VaccountHelper::AccessLevel('user_assign_acl', 'access_interface');

if($assign_access) {
	$assignaccess = false;
	foreach($groups as $group) {
		if(in_array($group,$assign_access))
		{
			$assignaccess=true;
			break;
		}
	}
} else {
	$assignaccess=true;
}
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

$input = JFactory::getApplication()->input;
// Checking if loaded via index.php or component.php
$tmpl = $input->getCmd('tmpl', '');

$document = JFactory::getDocument();

if($tmpl)
{
	$document->addStyleSheet(JURI::root().'components/com_vbizz/assets/css/vbizz.css');
	$document->addStyleSheet(JURI::root().'templates/vacount/css/style.css');
	$document->addStyleSheet(JURI::root().'templates/vacount/css/font-awesome.css');
}

$html = '<select name="type">';
$html .='<option value="">'.JText::_('SELECT_ACTIVITY_TYPE').'</option>';
$html .='<option value="notification">'.JText::_('NOTIFICATION').'</option>';
$html .='<option value="data_manipulation">'.JText::_('DATA_MANIPULATION'). '</option>';
$html .='<option value="configuration">'.JText::_('CONFIGURATION').'</option>';
$html .='<option value="import_export">'.JText::_('IMPORT_EXPORT').'</option>';
$html .='<option value="recurring">'.JText::_('RECURRING').'</option>';
$html .='</select>';

if(!empty($this->vendor->profile_pic)) { 
$profile_pic = $this->vendor->profile_pic;
} else {
	$profile_pic = "noimage.png";
}
	 
$js = 'function assign_profiles(userid, name, username, email)
     {     
	            jQuery("input[name=\"id\"]").val(userid);
				jQuery("input[name=\"userid\"]").val(userid);
				jQuery("input[name=\"username\"]").val(username);
				jQuery("input[name=\"name\"]").val(name);
				jQuery("input[name=\"email\"]").val(email);
				SqueezeBox.close(); 
	
}';
	
	$document->addScriptDeclaration($js);	
 ?>
 <style> 
#sbox-window {
	left: 5% !important; top:5% !important;
	width:87% !important;
	padding:1.5% !important;
}
</style>
<script type="text/javascript">
jQuery(document).ready(function(){
	SqueezeBox.loadModal = function(modalUrl,handler,x,y) {
        this.presets.size.x = 1024;
        this.initialize();      
        var options = {handler: 'iframe', size: {x: "95%", y: "95%"}
		};      
        this.setOptions(this.presets, options);
        this.assignOptions();
        this.setContent(handler,modalUrl);
    };

	 
});
SqueezeBox.initialize({ 
	 onOpen:function(){
			
			
			jQuery("html, body").animate({scrollTop : 0}, "slow"); 
			check_status= ''; 
		 }
}); 
	Joomla.submitbutton = function(task) {
		if (task == 'cancel') {
			
			Joomla.submitform(task, document.getElementById('adminForm'));
		} 
		else if(task == 'assign')
		{ 
		var url = "<?php echo JRoute::_("index.php?option=com_vbizz&view=vendor&layout=users&tmpl=component",false);?>";
            jQuery.ajax({
				    url: "index.php",
					type: "POST",
					dataType: "JSON",
			        data: {'option':'com_vbizz','view':'vbizz', 'task':'check_login_status','tmpl':'component', "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
					beforeSend: function()	{   
						jQuery('.loading').show();	
					},
					complete: function()	{
						jQuery('.loading').hide();	
					}, 
					success: function(res)	{
						if(res.result == "success"){
							if(res.state){
							SqueezeBox.loadModal(url,"iframe",'95%','95%'); 
								
							}
							if(!res.state){
							 window.location = '<?php echo JRoute::_('index.php?option=com_vbizz&view=vendor',false);?>';
							}
						}
					},
					error: function(jqXHR, textStatus, errorThrown)	{
					
						window.location = '<?php echo JRoute::_('index.php?option=com_vbizz&view=vendor',false);?>';
					}
			});  	  
		}
		else {
			
			var form = document.adminForm;
		
			if(form.name.value == "")	{
				alert("<?php echo JText::_('PLZ_ENTER_NAME'); ?>");
				return false;
			}
			
			if(form.email.value == "")	{
			
				alert("<?php echo JText::_('ENTER_EMAIL'); ?>");
				return false;
			}
			var email = form.email.value;
			
			if(email)
			{
				var emailReg = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
				
				var valid = emailReg.test(email); 
				if(!valid) {
					alert("<?php echo JText::_('ENTER_VALID_EMAIL'); ?>");
					return false;
				}
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
			data: {'option':'com_vbizz','view':'vendor', 'task':'getState', 'tmpl':'component', 'country_id':country_id},
			
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

jQuery(document).on('click','#addnew',function() {
	
	if(jQuery('#act_text').length==0)	{
		var html = '<tr id="act_text"><th><label><?php echo JText::_('COMMENT'); ?></label></th><td><textarea class="text_area" name="comments" id="comment" rows="4" cols="50"></textarea></td></tr><tr id="act_typ"><th><label><?php echo JText::_('ACTIVITY_TYPE'); ?></label></th><td><?php echo $html; ?></td></tr><tr id="act_sub"><td><input type="button" id="submit_act" value="<?php echo JText::_('SUBMIT'); ?>" class="btn btn-success" /><span class="loadingbox" style="display:none;"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/load.gif"   />' ?></span></td></tr>'
		
		jQuery('#add_activity').after(html);
		jQuery('select').chosen({"disable_search_threshold":0,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
	}
	
});

jQuery(document).on('click','#submit_act',function() {
	
	var vendid	= '<?php echo $this->vendor->userid ?>';
	var comments = jQuery('#comment').val();
	var type = jQuery('select[name="type"]').val();
	if(comments=="")
	{
		alert('<?php echo JText::_('ENTER_COMMENTS') ?>');
		return false;
	}
	if(type=="")
	{
		alert('<?php echo JText::_('SELECT_ACTIVITY_TYPE') ?>');
		return false;
	}
	var that=this;
	
	
	jQuery.ajax({
			type: "POST",
			dataType:"JSON",
			data: {'option':'com_vbizz', 'view':'vendor', 'task':'addActivity', 'tmpl':'component','vendid':vendid, 'comments':comments, 'type':type},
			
			beforeSend: function() {
				jQuery(that).parent().find("span.loadingbox").show();
			},
			
			complete: function()      {
				jQuery(that).parent().find("span.loadingbox").hide();
			},

			success: function(data){
				if(data.result=="success"){
					jQuery('#no_activity').remove();
					
					var htm = '<tr class="activity"><td align="center">'+data.tareekh+'</td><td>'+data.comments+'</td></tr>';
					jQuery('#activity_head').after(htm);
					
					jQuery('#act_text').remove();
					jQuery('#act_typ').remove();
					jQuery('#act_sub').remove();
					
				}
			}
		});
	
});

</script>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><h1 class="page-title"><?php echo isset($this->vendor->userid)&&$this->vendor->userid>0?JText::_('VENDOREDIT'):JText::_('VENDORNEW'); ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=vendor'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<?php if($tmpl) { ?>
<div class="row-fluid">
    <div class="span12">
        <div class="btn-toolbar" id="toolbar">
            <?php if($editaccess) { ?>
            <div class="btn-wrapper"  id="toolbar-save">
            <span onclick="Joomla.submitbutton('save')" class="btn btn-small">
            <span class="fa fa-check"></span> <?php echo JText::_('SAVE'); ?></span>
            </div>
            <?php } ?>
            <div class="btn-wrapper"  id="toolbar-cancel">
                <span onclick="Joomla.submitbutton('cancel')" class="btn btn-small">
                <span class="fa fa-close"></span> <?php echo JText::_('CLOSE'); ?></span>
            </div>
			
			
        </div>
    </div>
</div>
<?php } else { ?>

<div class="row-fluid">
    <div class="span12">
        <div class="btn-toolbar" id="toolbar">
            <?php if($editaccess) { ?>
            <div class="btn-wrapper"  id="toolbar-apply">
				<span onclick="Joomla.submitbutton('apply')" class="btn btn-small btn-success">
				<span class="fa fa-check"></span> <?php echo JText::_('SAVE'); ?></span>
            </div>
            <div class="btn-wrapper"  id="toolbar-save">
				<span onclick="Joomla.submitbutton('save')" class="btn btn-small">
				<span class="fa fa-check"></span> <?php echo JText::_('SAVE_N_CLOSE'); ?></span>
            </div>
			<div class="btn-wrapper"  id="toolbar-save-new">
				<span onclick="Joomla.submitbutton('saveNew')" class="btn btn-small">
				<span class="fa fa-plus"></span> <?php echo JText::_('SAVE_N_NEW'); ?></span>
			</div>
			
            <?php } ?>  
            <div class="btn-wrapper"  id="toolbar-cancel">
                <span onclick="Joomla.submitbutton('cancel')" class="btn btn-small">
                <span class="fa fa-close"></span> <?php echo JText::_('CLOSE'); ?></span>
            </div>
			<?php if($assignaccess) { ?>  
			<div class="btn-wrapper"  id="toolbar-assign">
				<span class="btn btn-small">		
					<a class="modal" id="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=users&layout=users&tmpl=component&assign_user=1';?>"> 
					<i class="fa fa-user"></i><?php echo JText::_( 'COM_VBIZZ_ASSIGN_VENDOR' ); ?>
					</a></span>
           </div>
			<?php } ?>
        </div>
    </div>
</div>
<?php } ?>

<div class="overview">
<fieldset class="adminform">  
	<legend><?php echo JText::_( 'OVERVIEW' ); ?></legend>
	<ul>
		<li><?php	echo JText::_('NEW_VENDOR_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>

<div class="col100 leftdiv">
<fieldset class="adminform">
<legend><?php if($this->vendor->userid) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW'); ?></legend>
<table class="adminform table table-striped">
    <tbody>
	
	<tr>
		<th><label class="hasTip" title="<?php echo JText::_('PROFILE_PICS'); ?>"><?php echo JText::_('PROFILE_PICS'); ?></label></th>
		<td><input type="file" name="profile_pic" id="profile_pic" class="inputbox required" size="50" value=""/>
		<?php echo '<img src="'.JURI::root().'components/com_vbizz/uploads/profile_pics/'.$profile_pic.'"  style="width:20%;"' ;?>
		</td>
	</tr>
	
    <tr>
        <th width="200"> <label class="hasTip" title="<?php echo JText::_('VNDNAMETXT'); ?>"><?php echo JText::_('NAME'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
        <td><input class="text_area" type="text" name="name" id="name" size="32" maxlength="250" value="<?php echo $this->vendor->name;?>" /></td>
    </tr>
	
	<tr>
		<th width="200">
			<label class="hasTip" title="<?php echo JText::_('VNDUSERNAMETXT'); ?>">
			<?php echo JText::_('USERNAME'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
		</th>
		<td><input class="text_area" type="text" name="username" id="username" value="<?php echo $this->vendor->username;?>"/></td>
	</tr>
	
	<tr>
    	<th width="200"> <label class="hasTip" title="<?php echo JText::_('VNDEMAILTXT'); ?>">
			<?php echo JText::_('EMAIL'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
		</th>
    	<td><input class="text_area" type="text" name="email" id="email" size="32" maxlength="250" value="<?php echo $this->vendor->email;?>" /></td>
    </tr>
	
	<tr>
		<th><label class="hasTip" title="<?php echo JText::_('PASSWORD'); ?>"><?php echo JText::_('PASSWORD'); ?></label></th>
		<td><input type="password" class="text_area" name="password" autocomplete="false" value="" cols="39" rows="5" /></td>
	</tr>
	
	<tr id="morebtn">
		<th colspan="0">
		<input type="button" id="more" value="<?php echo JText::_('MORE_OPTION'); ?>" class="btn btn-success" style="margin-bottom:10px" />
		</th>
		<td></td>
	</tr>
    
    <tr class="tohide" style="display:none;">
        <th width="200"> <label class="hasTip" title="<?php echo JText::_('VNDCOMPTXT'); ?>"><?php echo JText::_('COMPANY'); ?></label></th>
        <td><input class="text_area" type="text" name="company" id="company" size="32" maxlength="250" value="<?php echo $this->vendor->company;?>" /></td>
    </tr>
    
    <tr class="tohide" style="display:none;">
    	<th width="200"> <label class="hasTip" title="<?php echo JText::_('VNDCONTACTTXT'); ?>"><?php echo JText::_('CONTACT_NO'); ?></label></th>
    	<td><input class="text_area" type="text" name="phone" id="phone" size="32" maxlength="250" value="<?php echo $this->vendor->phone;?>" /></td>
    </tr>
        
    <tr class="tohide" style="display:none;">
    	<th width="200"><label class="hasTip" title="<?php echo JText::_('CUSTWEBSITETXT'); ?>"><?php echo JText::_('WEBSITE'); ?></label></th>
    	<td><input class="text_area" type="text" name="website" id="website" value="<?php echo $this->vendor->website;?>" /></td>
    </tr>
    
    <tr class="tohide" style="display:none;">
    <th><label class="hasTip" title="<?php echo JText::_('IMTEXT'); ?>"><?php echo JText::_('INST_MANAGER'); ?></label></th>
    <td>
    <select name="instant_messenger" id="instant_messenger">
    <option value=""><?php echo JText::_('SELECT_IM'); ?></option>
    <option value="skype" <?php if($this->vendor->instant_messenger=="skype") echo 'selected="selected"'; ?>><?php echo JText::_('Skype');?></option>
    <option value="gtalk" <?php if($this->vendor->instant_messenger=="gtalk") echo 'selected="selected"'; ?>><?php echo JText::_('GTalk');?></option>
    <option value="yahoo" <?php if($this->vendor->instant_messenger=="yahoo") echo 'selected="selected"';?>><?php echo JText::_('Yahoo');?></option>
    <option value="nimbuzz" <?php if($this->vendor->instant_messenger=="nimbuzz") echo 'selected="selected"';?>><?php echo JText::_('Nimbuzz');?></option>
    <option value="ebuddy" <?php if($this->vendor->instant_messenger=="ebuddy") echo 'selected="selected"';?>><?php echo JText::_('eBuddy');?></option>
    <option value="aim" <?php if($this->vendor->instant_messenger=="aim") echo 'selected="selected"';?>><?php echo JText::_('AIM');?></option>
    <option value="ichat" <?php if($this->vendor->instant_messenger=="ichat") echo 'selected="selected"'; ?>><?php echo JText::_('iChat');?></option>
    <option value="myspace" <?php if($this->vendor->instant_messenger=="myspace") echo 'selected="selected"'; ?>><?php echo JText::_('MySpace');?></option>
    <option value="meebo" <?php if($this->vendor->instant_messenger=="meebo") echo 'selected="selected"'; ?>><?php echo JText::_('Meebo');?></option>
    <option value="icq" <?php if($this->vendor->instant_messenger=="icq") echo 'selected="selected"'; ?>><?php echo JText::_('ICQ');?></option>
    <option value="digsby" <?php if($this->vendor->instant_messenger=="digsby") echo 'selected="selected"';?>><?php echo JText::_('Digsby');?></option>
    <option value="msn" <?php if($this->vendor->instant_messenger=="msn") echo 'selected="selected"';?>><?php echo JText::_('MSN');?></option>
    <option value="trillian" <?php if($this->vendor->instant_messenger=="trillian") echo 'selected="selected"';?>><?php echo JText::_('Trillian');?></option>
    <option value="pidgin" <?php if($this->vendor->instant_messenger=="pidgin") echo 'selected="selected"';?>><?php echo JText::_('Pidgin');?></option>
    <option value="jitsi" <?php if($this->vendor->instant_messenger=="jitsi") echo 'selected="selected"';?>><?php echo JText::_('Jitsi');?></option>
    <option value="miranda" <?php if($this->vendor->instant_messenger=="miranda") echo 'selected="selected"';?>><?php echo JText::_('Miranda');?></option>
    <option value="qnext" <?php if($this->vendor->instant_messenger=="qnext") echo 'selected="selected"';?>><?php echo JText::_('Qnext');?></option>
    <option value="adium" <?php if($this->vendor->instant_messenger=="adium") echo 'selected="selected"';?>><?php echo JText::_('Adium');?></option>
    <option value="empathy" <?php if($this->vendor->instant_messenger=="empathy") echo 'selected="selected"';?>><?php echo JText::_('Empathy');?></option>
    </select>
    </td>
    </tr>
    
    <tr class="tohide" style="display:none;">
    	<th width="200"><label class="hasTip" title="<?php echo JText::_('IMIDTXT'); ?>"><?php echo JText::_('IM_ID'); ?></label></th>
    	<td><input class="text_area" type="text" name="im_id" id="im_id" value="<?php echo $this->vendor->im_id;?>" /></td>
    </tr>
    
    <tr class="tohide" style="display:none;">
        <th><label class="hasTip" title="<?php echo JText::_('COUNTRYTXT'); ?>"><?php echo JText::_('COUNTRY'); ?></label></th>
        <td>
            <select name="country_id" id="country_id">
            <option value=""><?php echo JText::_('SELECT_COUNTRY'); ?></option>
            <?php	for($i=0;$i<count($this->countries);$i++)	{	?>
            <option value="<?php echo $this->countries[$i]->id; ?>" <?php if($this->countries[$i]->id==$this->vendor->country_id) echo 'selected="selected"'; ?>> <?php echo JText::_($this->countries[$i]->country_name); ?></option>
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
            <option value="<?php echo $this->states[$i]->id; ?>"<?php if($this->states[$i]->id==$this->vendor->state_id) echo 'selected="selected"'; ?>> <?php echo JText::_($this->states[$i]->state_name); ?></option>
            <?php	}	?>
            </select>
        </td>
    </tr>
    
    <tr class="tohide" style="display:none;">
        <th width="200"> <label class="hasTip" title="<?php echo JText::_('VNDADDTXT'); ?>"><?php echo JText::_('ADDRESS'); ?></label></th>
        <td><input class="text_area" type="text" name="address" id="address" size="32" maxlength="250" value="<?php echo $this->vendor->address;?>" /></td>
    </tr>
    
    <tr class="tohide" style="display:none;">
    	<th width="200"> <label class="hasTip" title="<?php echo JText::_('VNDCITYTXT'); ?>"><?php echo JText::_('CITY'); ?></label></th>
    	<td><input class="text_area" type="text" name="city" id="city" size="32" maxlength="250" value="<?php echo $this->vendor->city;?>" /></td>
    </tr>
    
    <tr class="tohide" style="display:none;">
    	<th width="200"> <label class="hasTip" title="<?php echo JText::_('ZIPTXT'); ?>"><?php echo JText::_('ZIPCODE'); ?></label></th>
    	<td><input class="text_area" type="text" name="zip" id="zip" size="32" maxlength="250" value="<?php echo $this->vendor->zip;?>" /></td>
    </tr>
    
    <tr class="tohide" style="display:none;">
    	<th width="200"><label class="hasTip" title="<?php echo JText::_('CMNTXT'); ?>"><?php echo JText::_('COMMENTS'); ?></label></th>
    	<td><textarea class="text_area" name="comments" id="comments" rows="4" cols="50"><?php echo $this->vendor->comments;?></textarea></td>
    </tr>
    
</tbody>
</table>
</fieldset>
</div>

<?php if($this->vendor->userid) { ?>
<div class="rightdiv">
<fieldset class="adminform">
<legend style="border: medium none; margin: 0px 0px 5px;"><?php echo JText::_('RECENT_ACTIVITY'); ?></legend>
<table class="adminform table table-striped a_activity">
<tbody>
<tr id="add_activity">
	<th colspan="0">
		<input type="button" id="addnew" value="<?php echo JText::_('ADD_ACTIVITY'); ?>" class="btn btn-success" style="margin-bottom:10px" />
	</th>
</tr>
</tbody>
</table>
<table class="adminform table table-striped">
<tbody id="activity">
	<thead>
		<tr id="activity_head">
			<th width="200"><?php echo JText::_( 'DATE' ); ?></th>
			<th><?php echo JText::_( 'ACTIVITY' ); ?></th>
		</tr>
	</thead>
	
	<?php if((count( $this->activity ))<1) { ?>
	<tr id="no_activity">
		<td><span><?php echo JText::_('NO_ACTIVITY_TO_SHOW'); ?></span></td>
	</tr>
	<?php } else { ?>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->activity ); $i < $n; $i++)	{
		$activity = &$this->activity[$i];
		
		$format = $this->config->date_format.', g:i A';
		$datetime = strtotime($activity->created);
		$created = date($format, $datetime );
		
	?>
		<tr class="activity <?php echo "row$k"; ?>">
			<td align="center"><?php echo $created ;?></td>
			
			<td><?php echo $activity->comments; ?></td>
		</tr>
	<?php
		$k = 1 - $k;
	} 
} ?>
</tbody>
</table>
</fieldset>
</div>
<?php } ?>


<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->vendor->userid; ?>" />
<input type="hidden" name="userid" value="<?php echo $this->vendor->userid; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="vendor" />
<?php if($tmpl) { ?>
<input type="hidden" name="tmpl" value="component" />
<?php } ?>
</form>
</div>
</div>
</div>
