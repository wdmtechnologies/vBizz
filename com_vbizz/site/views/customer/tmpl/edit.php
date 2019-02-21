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
JHtml::_('behavior.modal');
//check joomla version
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion>='3.0') {
	JHtml::_('formbehavior.chosen', 'select');
}


$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();
//check acl for add, edit and delete access
$add_access = $this->config->customer_acl->get('addaccess');
$edit_access = $this->config->customer_acl->get('editaccess');
$delete_access = $this->config->customer_acl->get('deleteaccess');

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
    
	
	
	$js = 'function assign_profiles(userid)
     {
	jQuery.ajax({
		type: "POST",
		dataType:"JSON",
		data: {"option":"com_vbizz", "view":"customer", "task":"getUserProfile", "tmpl":"component","userid":userid},
		
		beforeSend: function() {
			jQuery(this).parent().find("span.loadingbox").show();
		},
		
		complete: function()      {
			jQuery(this).parent().find("span.loadingbox").hide();
		},

		success: function(data){  
			if(data.result=="success"){
				jQuery("input[name=\"id\"]").val(data.html.id);
				jQuery("input[name=\"userid\"]").val(data.html.id);
				jQuery("input[name=\"username\"]").val(data.html.username);
				jQuery("input[name=\"name\"]").val(data.html.name);
				jQuery("input[name=\"email\"]").val(data.html.email);
				SqueezeBox.close(); 
				
			}
		} 
	});
	
}';
	
	$document->addScriptDeclaration($js);
$html = '<select name="type">';
$html .='<option value="">'.JText::_('SELECT_ACTIVITY_TYPE').'</option>';
$html .='<option value="notification">'.JText::_('NOTIFICATION').'</option>';
$html .='<option value="data_manipulation">'.JText::_('DATA_MANIPULATION'). '</option>';
$html .='<option value="configuration">'.JText::_('CONFIGURATION').'</option>';
$html .='<option value="import_export">'.JText::_('IMPORT_EXPORT').'</option>';
$html .='<option value="recurring">'.JText::_('RECURRING').'</option>';
$html .='</select>';

if(!empty($this->customer->profile_pic)) { 
$profile_pic = $this->customer->profile_pic;
} else {
	$profile_pic = "noimage.png";
}
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
		/* if(form.country_id.value == "")	{
			alert("<?php echo JText::_('SELECT_COUNTRY'); ?>");
			return false;
		} */
		
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
			data: {'option':'com_vbizz','view':'customer', 'task':'getState','tmpl':'component', 'country_id':country_id},
			
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
	
	var custid	= '<?php echo $this->customer->userid ?>';
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
		data: {'option':'com_vbizz', 'view':'customer', 'task':'addActivity', 'tmpl':'component','custid':custid, 'comments':comments, 'type':type},
		
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
		<h1 class="page-title"><?php echo isset($this->customer->userid)&&$this->customer->userid>0?JText::_('CUSTOMEREDIT'):JText::_('CUSTOMERNEW'); ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=customer');?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

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
					<i class="fa fa-user"></i> <?php echo JText::_( 'COM_VBIZZ_ASSIGNED_USER' ); ?>
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
		<li><?php	echo JText::_('NEW_CUSTOMER_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100 leftdiv">
<fieldset class="adminform">
<legend><?php if($this->customer->userid) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW'); ?></legend>
<table class="adminform table table-striped">
    <tbody>
	
	<tr>
		<th><label class="hasTip" title="<?php echo JText::_('PROFILE_PICS'); ?>"><?php echo JText::_('PROFILE_PICS'); ?></label></th>
		<td><input type="file" name="profile_pic" id="profile_pic" class="inputbox required" size="50" value=""/>
		<?php echo '<img src="'.JURI::root().'components/com_vbizz/uploads/profile_pics/'.$profile_pic.'"  style="height:60px;vertical-align:middle;margin-top:5px;"' ;?>
		</td>
	</tr>
	
    <tr>
        <th width="200"><label class="hasTip" title="<?php echo JText::_('CUSTNAMETXT');?>">
        	<?php echo JText::_('NAME');?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
        </th>
    	<td><input class="text_area" type="text" name="name" id="name" value="<?php echo $this->customer->name;?>"/></td>
    </tr>
	
	<tr>
		<th width="200">
			<label class="hasTip" title="<?php echo JText::_('CUSTUSERNAMETXT'); ?>">
			<?php echo JText::_('USERNAME'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
		</th>
		<td><input class="text_area" type="text" name="username" id="username" value="<?php echo $this->customer->username;?>"/></td>
	</tr>
	
	<tr>
        <th width="200"><label class="hasTip" title="<?php echo JText::_('CUSTEMAILTXT'); ?>">
			<?php echo JText::_('EMAIL'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
		</th>
        <td><input class="text_area" type="text" name="email" id="email" value="<?php echo $this->customer->email;?>" /></td>
    </tr>
	
	<tr>
		<th><label class="hasTip" title="<?php echo JText::_('PASSWORD'); ?>"><?php echo JText::_('PASSWORD'); ?></label></th>
		<td><input type="password" class="text_area" name="password" autocomplete="false" value="" cols="39" rows="5" /></td>
	</tr>
	
	<tr id="morebtn">
		<th colspan="0">
		<input id="more" class="btn" type="button" value="<?php echo JText::_('MORE_OPTION'); ?>">
		</th>
		<td></td>
	</tr>
    
    <tr class="tohide" style="display:none;">
        <th width="200"><label class="hasTip" title="<?php echo JText::_('CUSTCOMPTXT'); ?>"><?php echo JText::_('COMPANY'); ?></label></th>
    	<td><input class="text_area" type="text" name="company" id="company" value="<?php echo $this->customer->company;?>" /></td>
    </tr>
    
    <tr class="tohide" style="display:none;">
        <th width="200"><label class="hasTip" title="<?php echo JText::_('CUSTCONTACTTXT'); ?>"><?php echo JText::_('CONTACT_NO'); ?></label></th>
    	<td><input class="text_area" type="text" name="phone" id="phone" value="<?php echo $this->customer->phone;?>"/></td>
    </tr>
    
    <tr class="tohide" style="display:none;">
        <th width="200"><label class="hasTip" title="<?php echo JText::_('CUSTWEBSITETXT'); ?>"><?php echo JText::_('WEBSITE'); ?></label></th>
        <td><input class="text_area" type="text" name="website" id="website" value="<?php echo $this->customer->website;?>" /></td>
    </tr>
    
    <tr class="tohide" style="display:none;">
        <th><label class="hasTip" title="<?php echo JText::_('IMTEXT'); ?>"><?php echo JText::_('INST_MANAGER'); ?></label></th>
        <td>
            <select name="instant_messenger" id="instant_messenger">
            <option value=""><?php echo JText::_('SELECT_IM'); ?></option>
            <option value="skype" <?php if($this->customer->instant_messenger=="skype") echo 'selected="selected"'; ?>><?php echo JText::_('Skype');?></option>
            <option value="gtalk" <?php if($this->customer->instant_messenger=="gtalk") echo 'selected="selected"'; ?>><?php echo JText::_('GTalk');?></option>
            <option value="yahoo" <?php if($this->customer->instant_messenger=="yahoo") echo 'selected="selected"';?>><?php echo JText::_('Yahoo');?></option>
            <option value="nimbuzz" <?php if($this->customer->instant_messenger=="nimbuzz") echo 'selected="selected"';?>><?php echo JText::_('Nimbuzz');?></option>
            <option value="ebuddy" <?php if($this->customer->instant_messenger=="ebuddy") echo 'selected="selected"';?>><?php echo JText::_('eBuddy');?></option>
            <option value="aim" <?php if($this->customer->instant_messenger=="aim") echo 'selected="selected"';?>><?php echo JText::_('AIM');?></option>
            <option value="ichat" <?php if($this->customer->instant_messenger=="ichat") echo 'selected="selected"'; ?>><?php echo JText::_('iChat');?></option>
            <option value="myspace" <?php if($this->customer->instant_messenger=="myspace") echo 'selected="selected"'; ?>><?php echo JText::_('MySpace');?></option>
            <option value="meebo" <?php if($this->customer->instant_messenger=="meebo") echo 'selected="selected"'; ?>><?php echo JText::_('Meebo');?></option>
            <option value="icq" <?php if($this->customer->instant_messenger=="icq") echo 'selected="selected"'; ?>><?php echo JText::_('ICQ');?></option>
            <option value="digsby" <?php if($this->customer->instant_messenger=="digsby") echo 'selected="selected"';?>><?php echo JText::_('Digsby');?></option>
            <option value="msn" <?php if($this->customer->instant_messenger=="msn") echo 'selected="selected"';?>><?php echo JText::_('MSN');?></option>
            <option value="trillian" <?php if($this->customer->instant_messenger=="trillian") echo 'selected="selected"';?>><?php echo JText::_('Trillian');?></option>
            <option value="pidgin" <?php if($this->customer->instant_messenger=="pidgin") echo 'selected="selected"';?>><?php echo JText::_('Pidgin');?></option>
            <option value="jitsi" <?php if($this->customer->instant_messenger=="jitsi") echo 'selected="selected"';?>><?php echo JText::_('Jitsi');?></option>
            <option value="miranda" <?php if($this->customer->instant_messenger=="miranda") echo 'selected="selected"';?>><?php echo JText::_('Miranda');?></option>
            <option value="qnext" <?php if($this->customer->instant_messenger=="qnext") echo 'selected="selected"';?>><?php echo JText::_('Qnext');?></option>
            <option value="adium" <?php if($this->customer->instant_messenger=="adium") echo 'selected="selected"';?>><?php echo JText::_('Adium');?></option>
            <option value="empathy" <?php if($this->customer->instant_messenger=="empathy") echo 'selected="selected"';?>><?php echo JText::_('Empathy');?></option>
            </select>
        </td>
    </tr>
    
    <tr class="tohide" style="display:none;">
        <th width="200"><label class="hasTip" title="<?php echo JText::_('IMIDTXT'); ?>"><?php echo JText::_('IM_ID'); ?></label></th>
        <td><input class="text_area" type="text" name="im_id" id="im_id" value="<?php echo $this->customer->im_id;?>" /></td>
    </tr>
    
    <tr class="tohide" style="display:none;">
        <th><label class="hasTip" title="<?php echo JText::_('COUNTRYTXT'); ?>"><?php echo JText::_('COUNTRY'); ?></label></td>
        <td>
            <select name="country_id" id="country_id">
            <option value=""><?php echo JText::_('SELECT_COUNTRY'); ?></option>
            <?php	for($i=0;$i<count($this->countries);$i++)	{	?>
            <option value="<?php echo $this->countries[$i]->id; ?>" <?php if($this->countries[$i]->id==$this->customer->country_id) echo 'selected="selected"'; ?>> <?php echo JText::_($this->countries[$i]->country_name); ?></option>
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
            <option value="<?php echo $this->states[$i]->id; ?>"<?php if($this->states[$i]->id==$this->customer->state_id) echo 'selected="selected"'; ?>> <?php echo JText::_($this->states[$i]->state_name); ?></option>
            <?php	}	?>
            </select>
        </td>
    </tr>
    
    <tr class="tohide" style="display:none;">
    	<th width="200"><label class="hasTip" title="<?php echo JText::_('CUSTADDTXT'); ?>"><?php echo JText::_('ADDRESS'); ?></label></th>
    	<td><input class="text_area" type="text" name="address" id="address" value="<?php echo $this->customer->address;?>" /></td>
    </tr>
    
    <tr class="tohide" style="display:none;">
    	<th width="200"><label class="hasTip" title="<?php echo JText::_('CUSTCITYTXT'); ?>"><?php echo JText::_('CITY'); ?></label></th>
    	<td><input class="text_area" type="text" name="city" id="city" value="<?php echo $this->customer->city;?>" /></td>
    </tr>
    
    <tr class="tohide" style="display:none;">
        <th width="200"><label class="hasTip" title="<?php echo JText::_('ZIPTXT'); ?>"><?php echo JText::_('ZIPCODE'); ?></label></th>
        <td><input class="text_area" type="text" name="zip" id="zip" value="<?php echo $this->customer->zip;?>"/></td>
    </tr>
    
    <tr class="tohide" style="display:none;">
    	<th width="200"><label class="hasTip" title="<?php echo JText::_('CMNTXT'); ?>"><?php echo JText::_('COMMENTS'); ?></label></th>
    	<td><textarea class="text_area" name="comments" id="comments" rows="4" cols="50"><?php echo $this->customer->comments;?></textarea></td>
    </tr>
    
    </tbody>
</table>

</fieldset>
</div>


<?php if($this->customer->userid) { ?>
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
<input type="hidden" name="id" value="<?php echo $this->customer->userid; ?>" />
<input type="hidden" name="userid" value="<?php echo $this->customer->userid; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="customer" />
<?php if($tmpl) { ?>
<input type="hidden" name="tmpl" value="component" />
<?php } ?>
</form>
</div>
</div>
</div>
