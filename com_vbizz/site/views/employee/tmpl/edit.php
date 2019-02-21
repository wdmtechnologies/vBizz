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
JHtml::_('behavior.modal');

JHtml::_('formbehavior.chosen', 'select');

$db = JFactory::getDbo();

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_vbizz/assets/css/fullcalendar.css');
//$document->addStyleSheet('components/com_vbizz/assets/css/fullcalendar.print.css');
$document->addScript('components/com_vbizz/assets/js/moment.min.js');
$document->addScript('components/com_vbizz/assets/js/fullcalendar.min.js');

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->employee_acl->get('addaccess');
$edit_access = $this->config->employee_acl->get('editaccess');
$delete_access = $this->config->employee_acl->get('deleteaccess');

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

$html = '<select name="type">';
$html .='<option value="">'.JText::_('SELECT_ACTIVITY_TYPE').'</option>';
$html .='<option value="notification">'.JText::_('NOTIFICATION').'</option>';
$html .='<option value="data_manipulation">'.JText::_('DATA_MANIPULATION'). '</option>';
$html .='<option value="configuration">'.JText::_('CONFIGURATION').'</option>';
$html .='<option value="import_export">'.JText::_('IMPORT_EXPORT').'</option>';
$html .='<option value="recurring">'.JText::_('RECURRING').'</option>';
$html .='</select>';

if(!empty($this->item->profile_pic)) { 
$profile_pic = $this->item->profile_pic;
} else {
	$profile_pic = "noimage.png";
}

//get weekoff day from configuration
if($this->config->weekoffday==='null' || is_null($this->config->weekoffday) || $this->config->weekoffday=='null' || $this->config->weekoffday=='') {
	$weekoffday = "";
} else {
	$weekoffday = implode(',',json_decode($this->config->weekoffday));
}

$date = JFactory::getDate()->format('Y-m-d');

$salaryDate = $this->config->sal_date;
		
		
$monthCycle = $this->config->emp_month_cycle;

$today = date('j', strtotime($date));

$month = date('n', strtotime($date));

$year = date('Y', strtotime($date));

$givenDate = explode('-',$date);

if($month==1) {
	$givenDate[0] = $year-1; 
}

$givenDate[1] = $month-1; 
$givenDate[2] = $monthCycle; 

$monthStart = implode('-',$givenDate);

$monthStart = date("Y-m-d", strtotime($monthStart));

$monthEnd = date("Y-m-d", strtotime(date("Y-m-d", strtotime($monthStart)) . " +29 days"));
$salMonth = date('n', strtotime($monthStart));
$salYear = date('Y', strtotime($monthEnd));

$salBtn = false;
if($this->item->userid) {
	$query = 'SELECT count(*) from #__vbizz_transaction where employee='.$this->item->userid.' and month='.$db->quote($salMonth).' and year='.$db->quote($salYear);
	$db->setQuery($query);
	$salTransferred = $db->loadResult();
	
	if( ( !$salTransferred ) && ( strtotime($date) > strtotime($monthEnd) ) ) {
		$salBtn = true;
	}
}

//show salary structure of employee
$inc_html = '<table class="adminform table table-striped" style="margin-top: 20px;">';
$inc_html .= '<tbody>';
$inc_html .= 	'<thead>';
$inc_html .= 		'<tr>';
$inc_html .= 			'<th>'.JText::_('PAYHEADNAME') ;'</th>';
$inc_html .= 			'<th>'.JText::_('PAYHEAD_TYPE') ;'</th>';
$inc_html .= 			'<th>'.JText::_('AMOUNT') ;'</th>';
$inc_html .= 		'</tr>';

$k = 0;
for($i=0, $n=count( $this->sal_struct ); $i < $n; $i++) {
	
	$row = $this->sal_struct[$i];
	if(!empty($this->emp_sal))
	{
		
		
		if(!array_key_exists($i, $this->emp_sal)) {
			$empSal = new stdClass();
			$empSal->amount = null;
		} else {
			$empSal = $this->emp_sal[$i];
		}
		
	} else {
		$empSal = new stdClass();
		$empSal->amount = null;
	}
	
	
	//echo'<pre>';print_r($empSal);
	
	if($row->payhead_type=="earning") {
		$payhead_type = JText::_('EARNINGS');
	} else if($row->payhead_type=="std_deduction") {
		$payhead_type = JText::_('STANDARD_DEDUCTION');
	} else if($row->payhead_type=="other_deduction") {
		$payhead_type = JText::_('OTHER_DEDUCTION');
	}
	
	$inc_html .= '<tr class="'."row$k".'">';
	$inc_html .= 	'<td align="center">'.$row->name.'</td>';
	$inc_html .= 	'<td align="center">'.$payhead_type.'</td>';
	$inc_html .= 	'<td align="center"><input class="text_area" type="text" name="amount[]" value=""/></td>';
	$inc_html .= 	'<input class="text_area" type="hidden" name="payid[]" value="'.$row->id.'"/>';
	$inc_html .= '</tr>';
	
	$k = 1 - $k;
}

$inc_html .= '<input class="text_area" type="hidden" name="lastIncrement" value=""/>';
$inc_html .= '<tbody>';
$inc_html .= '<table>';


$db = JFactory::getDbo();
if($this->item->userid) {
	$query = 'SELECT name from #__vbizz_employee_dept where id='.$this->item->department;
	$db->setQuery( $query );
	$dept = $db->loadResult();
	
	$query = 'SELECT title from #__vbizz_employee_desg where id='.$this->item->department;
	$db->setQuery( $query );
	$desg = $db->loadResult();
} else {
	$dept=JText::_('SELECT_DEPARTMENT');
	$desg=JText::_('SELECT_DESIGNATION');
}

$jsDept = '
		function getDept(id,name)
		{              
			var old_id = document.getElementById("department").value;
			if (old_id != id) {
				document.getElementById("department").value = id;
				document.getElementById("dept").value = name;
				document.getElementById("dept").className = document.getElementById("dept").className.replace(" invalid" , "");
				
			}
			SqueezeBox.close();
		
		}';
		
$jsDesg = '
		function getDesg(id,name)
		{              
			var old_id = document.getElementById("designation").value;
			if (old_id != id) {
				document.getElementById("designation").value = id;
				document.getElementById("desg").value = name;
				document.getElementById("desg").className = document.getElementById("desg").className.replace(" invalid" , "");
				
			}
			SqueezeBox.close();
		
		}';
	
	$document =  JFactory::getDocument();
	$document->addScriptDeclaration($jsDept); 
	$document->addScriptDeclaration($jsDesg); 

 ?>
 

<link href='<?php echo JURI::root().'components/com_vbizz/assets/css/fullcalendar.print.css'; ?>' rel='stylesheet' media='print' />

<script>
jQuery(function() {
	jQuery('base').remove();
	jQuery( "#tabs" ).tabs({
		activate: function(event, ui) {
			jQuery('#attendance').fullCalendar('render');
		}
	});
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

jQuery(function() {
	
	 jQuery( "#dialog" ).dialog({
		autoOpen: false,
		width: 350,
		modal: true,

		show: {
			effect: "blind",
		},
		hide: {
			effect: "explode",
		},
		open: function(event, ui) {
			
		},
		close: function(event, ui) {
			jQuery('#attendance-params').find('input[name="date"]').remove();
		}
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

<?php if($this->item->userid) { ?>
jQuery(document).ready(function() {
	initializeCalendar();
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
	
function initializeCalendar() {
	jQuery('#attendance').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: ''
		},
		
		firstDay: 0,
		contentHeight: 'auto',
		selectable: true,
		selectHelper: true,
		editable: false,
		eventLimit: false,

		events: function(start, end, timezone, callback) {
			jQuery.ajax({
				url: 'index.php',
				type: "POST",
				dataType:"json",
				data: {'option':'com_vbizz', 'view':'employee', 'task':'attendance', 'tmpl':'component', 'start':start.unix(), 'end':end.unix(), 'employee':<?php echo $this->item->userid; ?>  },
				
				success: function(data) {
					var events = data.attendance;
					callback(events);
				}
			});
		},
		
		eventRender: function(event, element) {
			element.find('.fc-title').append("<br/>" + event.htitle); 
			element.find('.fc-title').append("<br/>" + event.ltitle); 
			
		},
		
		dayRender: function (date, cell) {
			var weekoff = '<?php echo $weekoffday; ?>';
			if(weekoff != "") {
				var weekoffday = weekoff.split(',');
				
				for(var i=0; i<weekoffday.length; i++) {
					var cl = weekoffday[i];
					jQuery('.'+cl).css('color','red');
				}
			}
		},
		
		viewRender: function (view, element) {
			
			var attended = '<a class="hasTip attended" title="<?php echo JText::_("ATTENDED"); ?>" href="javascript:void(0);"><i class="fa fa-check"></i> <?php echo JText::_("ATTENDED"); ?></a>'; 
			jQuery(".fc-day-number").append(attended);
			
			var not_attended = '<a class="hasTip not_attended" title="<?php echo JText::_("NOT_ATTENDED"); ?>" href="javascript:void(0);"><i class="fa fa-remove"></i> <?php echo JText::_("NOT_ATTENDED"); ?></a>'; 
			jQuery(".fc-day-number").append(not_attended);
		 }
		
	});
}


jQuery(document).on('click','.attended',function() {
	
	var date = jQuery(this).parent().data('date');
	var that = this;
			
	jQuery.ajax({
		url: 'index.php',
		type: "POST",
		dataType:"json",
		data: {'option':'com_vbizz', 'view':'employee', 'task':'markAttendance', 'tmpl':'component', 'date':date, 'employee':<?php echo $this->item->userid; ?> },
		
		success: function(data) {
			
			if(data.result=="success") {
				
				jQuery('#attendance').fullCalendar("destroy");
				initializeCalendar();
				
			} else {
				alert(data.msg);
			}
			
		}
	});
	
});

jQuery(document).on('click','.not_attended',function() {
	var date = jQuery(this).parent().data('date');
	var employee = '<?php echo $this->item->userid; ?>';
	var that = this;
	jQuery.ajax({
		url: 'index.php',
		type: "POST",
		dataType:"JSON",
		data: {'option':'com_vbizz', 'view':'employee', 'task':'attendValue', 'tmpl':'component', 'date':date, 'employee':<?php echo $this->item->userid; ?> },
		
		success: function(data){
			if(data.result=="success"){
				jQuery(that).parent().append(data.htm);
				jQuery( "#dialog" ).dialog(  );
			} else {
				alert(data.msg);
			}
		}
	});
	//jQuery( "#dialog" ).data('date',date).dialog( "open" );
});

jQuery(document).on('click','.send',function() {
		
	var date = jQuery('input[type=hidden][name="date"]').val();
	var present = jQuery(this).parents('#dialog').find('input[type=radio][name="present"]:checked').val();
	var halfday = jQuery(this).parents('#dialog').find('input[type=radio][name="halfday"]:checked').val();
	var paid = jQuery(this).parents('#dialog').find('input[type=radio][name="paid"]:checked').val();
	
	
	
	var that=this;


	jQuery.ajax({
		url: 'index.php',
		type: "POST",
		dataType:"JSON",
		data: {'option':'com_vbizz', 'view':'employee', 'task':'attendanceParams', 'tmpl':'component', 'date':date, 'present':present, 'halfday':halfday, 'paid':paid, 'employee':<?php echo $this->item->userid; ?> },
		
		beforeSend: function() {
			jQuery(that).parent().find("span.loadingbox").show();
		},
		
		complete: function()      {
			jQuery(that).parent().find("span.loadingbox").hide();
		},

		success: function(data){
			if(data.result=="success"){
				
				jQuery('#attendance').fullCalendar("destroy");
				initializeCalendar();
				
				jQuery( "#dialog" ).dialog( "close" );
				
				//setTimeout(function() { jQuery( "#dialog" ).dialog( "close" );},3000);
			} else {
				alert(data.msg);
				jQuery( "#dialog" ).dialog( "close" );
			}
		}
	});
});

<?php } ?>


</script>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		var form = document.adminForm;
		
		if(form.name.value == "")	{
			alert("<?php echo JText::_('ENTER_EMP_NAME'); ?>");
			return false;
		}
		
		if(form.username.value == "")	{
			alert("<?php echo JText::_('ENTER_USERNAME'); ?>");
			return false;
		}
		
		
		if(form.user_role.value == "")	{
			alert("<?php echo JText::_('SELECT_USER_ROLE'); ?>");
			return false;
		}
		
		if(form.empid.value == "")	{
			alert("<?php echo JText::_('ENTER_EMP_ID'); ?>");
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
		
		if(form.department.value == "")	{
			alert("<?php echo JText::_('SELECT_DEPARTMENT'); ?>");
			return false;
		}
		
		if(form.designation.value == "")	{
			alert("<?php echo JText::_('SELECT_DESIGNATION'); ?>");
			return false;
		}
		
		if(typeof(validateit) == 'function')	{
			if(!validateit())
				return false;
		}
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}


jQuery(document).on('click','.approve',function() {
		
		var id = jQuery(this).attr('id');
		var that=this;
		
		jQuery.ajax({
			type: "POST",
			dataType:"JSON",
			data: {'option':'com_vbizz', 'task':'leaveApprove', 'tmpl':'component','id':id},
			
			beforeSend: function() {
				jQuery(that).parent().find("span.loadingbox").show();
			},
			
			complete: function()      {
				jQuery(that).parent().find("span.loadingbox").hide();
			},

			success: function(data){
				if(data.result=="success"){
					var htmTxt = "<?php echo JText::_('APPROVED') ?>";
					var htm = '<span>'+htmTxt+'</span>';
					jQuery(that).parent().parent().append(htm);
					
					jQuery(that).parent().remove();
				}
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
	
	var empid	= '<?php echo $this->item->userid ?>';
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
			data: {'option':'com_vbizz', 'view':'employee', 'task':'addActivity', 'tmpl':'component','empid':empid, 'comments':comments, 'type':type},
			
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

jQuery(document).on('click','#add-inc',function() {
	jQuery(this).remove();
	jQuery('#increment').after('<?php echo $inc_html; ?>');
	
});
	
</script>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo isset($this->item->userid)&&$this->item->userid>0?JText::_('EMPLOYEEEDIT'):JText::_('EMPLOYEENEW'); ?></h1>
	</div>
</header>
  
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=employee');?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

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
		
		<?php if($salBtn) { ?>
			<div class="btn-wrapper"  id="toolbar-transferSalary">
				<span onclick="Joomla.submitbutton('transferSalary')" class="btn btn-small">
				<span class="fa fa-send"></span> <?php echo JText::_('TRANSFER_SALARY'); ?></span>
			</div>
		<?php } ?>
		
	</div>
</div>
</div>

<div class="overview">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'OVERVIEW' ); ?></legend>
	<ul>
		<li><?php	echo JText::_('NEW_EMPLOYEE_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">
<fieldset class="adminform">

	<div id="tabs">
		<ul>
			<li><a href="#tabs-1"><?php	echo JText::_('BASIC_DETAILS');?></a></li>
			<li><a href="#tabs-2"><?php	echo JText::_('SALARY_STRUCT_FOR_CURR_MONTH'); ?></a></li>
			<?php if($this->item->userid) { ?>
			<li><a href="#tabs-3"><?php	echo JText::_('INCREMENT'); ?></a></li>
			<li><a href="#tabs-4"><?php	echo JText::_('LEAVE_DETAILS'); ?></a></li>
			<li><a href="#tabs-5"><?php	echo JText::_('ATTENDANCE'); ?></a></li>
			<li><a href="#tabs-6"><?php	echo JText::_('RECENT_ACTIVITY'); ?></a></li>
			<?php } ?>
		</ul>
		
		<div id="tabs-1">

			<table class="adminform table table-striped">
				<tbody>
				
				<tr>
					<th><label class="hasTip" title="<?php echo JText::_('PROFILE_PICS'); ?>"><?php echo JText::_('PROFILE_PICS'); ?></label></th>
					<td><input type="file" name="profile_pic" id="profile_pic" class="inputbox required" size="50" value=""/>
					<?php echo '<img src="'.JURI::root().'components/com_vbizz/uploads/profile_pics/'.$profile_pic.'"  style="width:20%;"' ;?>
					</td>
				</tr>
				
				<tr>
					<th width="200">
						<label class="hasTip" title="<?php echo JText::_('EMPNAMETXT'); ?>">
						<?php echo JText::_('NAME'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
					</th>
					<td><input class="text_area" type="text" name="name" id="name" value="<?php echo $this->item->name;?>"/></td>
				</tr>
				
				<tr>
					<th width="200">
						<label class="hasTip" title="<?php echo JText::_('EMPUSERNAMETXT'); ?>">
						<?php echo JText::_('USERNAME'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
					</th>
					<td><input class="text_area" type="text" name="username" id="username" value="<?php echo $this->item->username;?>"/></td>
				</tr>
				
				<tr>
					<th><label class="hasTip" title="<?php echo JText::_('PASSWORD'); ?>"><?php echo JText::_('PASSWORD'); ?></label></th>
					<td><input type="password" class="text_area" name="password" autocomplete="false" value="" /></td>
				</tr>
				
				<tr>
					<th><label class="hasTip" title="<?php echo JText::_('USERROLETXT'); ?>">
						<?php echo JText::_('USER_ROLE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
					</th>
					<td>
						<select name="user_role" id="user_role">
						<option value=""><?php echo JText::_('SELECT_USER_GROUP'); ?></option>
						<?php	for($i=0;$i<count($this->user_role);$i++)	{	?>
						
						<option value="<?php echo $this->user_role[$i]->id; ?>" <?php if($this->user_role[$i]->id==$this->item->user_role) echo'selected="selected"'; ?>> <?php echo str_repeat('<span class="gi">&mdash;</span>', $this->user_role[$i]->level).JText::_($this->user_role[$i]->title); ?> </option> 
						<?php	}	?>
						</select>
					</td>
				</tr>
							
				
				<tr>
					<th width="200"><label class="hasTip" title="<?php echo JText::_('EMPEMAILTXT'); ?>">
						<?php echo JText::_('EMAIL'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
					</th>
					<td><input class="text_area" type="text" name="email" id="email" value="<?php echo $this->item->email;?>"/></td>
				</tr>
				
				<tr>
					<th width="200">
						<label class="hasTip" title="<?php echo JText::_('EMPIDTXT'); ?>">
						<?php echo JText::_('EMPLOYEE_ID'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
					</th>
					<td><input class="text_area" type="text" name="empid" id="empid" value="<?php echo $this->item->empid;?>"/></td>
				</tr>
				
				<tr>
					<th><label class="hasTip" title="<?php echo JText::_('DEPARTMENTTXT'); ?>">
						<?php echo JText::_('DEPARTMENT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
					</th>
					<td class="sel_customer">
						<input id="dept" type="text" readonly="" value="<?php echo $dept; ?>">
						<a class="btn btn-primary modal" id="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_vbizz&view=edept&layout=modal&tmpl=component');?>" title="<?php echo JText::_('SELECT_DEPARTMENT'); ?>"><i class="fa fa-user hasTip" title="<?php echo JText::_('SELECT_DEPARTMENT'); ?>"></i></a>
					</td>
					<input id="department" type="hidden" value="<?php echo $this->item->department; ?>" name="department" />
				</tr>
				
				<tr>
					<th><label class="hasTip" title="<?php echo JText::_('DESIGNATIONTXT'); ?>">
						<?php echo JText::_('DESIGNATION'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
					</th>
					<td class="sel_customer">
						<input id="desg" type="text" readonly="" value="<?php echo $desg; ?>">
						<a class="btn btn-primary modal" id="modal2" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_vbizz&view=edesg&layout=modal&tmpl=component');?>" title="<?php echo JText::_('SELECT_DESIGNATION'); ?>"><i class="fa fa-user hasTip" title="<?php echo JText::_('SELECT_DESIGNATION'); ?>"></i></a>
					</td>
					<input id="designation" type="hidden" value="<?php echo $this->item->designation; ?>" name="designation" />
				</tr>
				
				<tr id="morebtn">
					<th colspan="0">
					<input type="button" id="more" value="<?php echo JText::_('MORE_OPTION'); ?>" class="btn btn-success" style="margin-bottom:10px" />
					</th>
					<td></td>
				</tr>
				
				<tr class="tohide" style="display:none;">
					<th width="200"><label class="hasTip" title="<?php echo JText::_('EMPCONTACTTXT'); ?>"><?php echo JText::_('CONTACT_NO'); ?></label></th>
					<td><input class="text_area" type="text" name="phone" id="phone" value="<?php echo $this->item->phone;?>"/></td>
				</tr>
				
				<tr class="tohide" style="display:none;">
					<th><label class="hasTip" title="<?php echo JText::_('GENDERTXT');?>"><?php echo JText::_('GENDER');?></label></th>
					<td>
						<fieldset class="radio btn-group" style="margin-bottom:9px;">
						<label for="gender1" id="gender-lbl" class="radio"><?php echo JText::_( 'MALE' ); ?></label>
						<input type="radio" name="gender" id="gender1" value="1" <?php if($this->item->gender) echo 'checked="checked"';?>/>
						<label for="gender0" id="gender-lbl" class="radio"><?php echo JText::_( 'FEMALE' ); ?></label>
						<input type="radio" name="gender" id="gender0" value="0" <?php if(!$this->item->gender) echo 'checked="checked"';?>/>
						</fieldset>
					</td>
				</tr>
				
				<tr class="tohide" style="display:none;">
					<th><label class="hasTip" title="<?php echo JText::_('BLOODGROUPTXT'); ?>"><?php echo JText::_('BLOOD_GROUP'); ?></label></th>
					<td>
						<select name="blood_group">
						<option value="A+" <?php if($this->item->blood_group=="A+") echo 'selected="selected"'; ?>><?php echo JText::_('APOSITIVE'); ?></option>
						<option value="A-" <?php if($this->item->blood_group=="A-") echo 'selected="selected"'; ?>><?php echo JText::_('ANEGATIVE'); ?></option>
						<option value="B+" <?php if($this->item->blood_group=="B+") echo 'selected="selected"'; ?>><?php echo JText::_('BPOSITIVE'); ?></option>
						<option value="B-" <?php if($this->item->blood_group=="B-") echo 'selected="selected"'; ?>><?php echo JText::_('BNEGATIVE'); ?></option>
						<option value="AB+" <?php if($this->item->blood_group=="AB+") echo 'selected="selected"'; ?>><?php echo JText::_('ABPOSITIVE'); ?></option>
						<option value="AB-" <?php if($this->item->blood_group=="AB-") echo 'selected="selected"'; ?>><?php echo JText::_('ABNEGATIVE'); ?></option>
						<option value="O+" <?php if($this->item->blood_group=="O+") echo 'selected="selected"'; ?>><?php echo JText::_('OPOSITIVE'); ?></option>
						<option value="O-" <?php if($this->item->blood_group=="O-") echo 'selected="selected"'; ?>><?php echo JText::_('ONEGATIVE'); ?></option>
						</select>
					</td>
				</tr>
				
				<tr class="tohide" style="display:none;">
					<th><label class="hasTip" title="<?php echo JText::_('DOBTXT'); ?>"><?php echo JText::_('DOB'); ?></label></th>
					<td><?php echo JHTML::_('calendar', $this->item->dob, "dob" , "dob", '%Y-%m-%d'); ?></td>
				</tr>
				
				<tr class="tohide" style="display:none;">
					<th><label class="hasTip" title="<?php echo JText::_('PRESENTADDTXT'); ?>"><?php echo JText::_('PRESENT_ADDRESS'); ?></label></th>
					<td>
						<textarea class="text_area" name="present_address" id="present_address" rows="4" cols="50"><?php echo $this->item->present_address;?></textarea>
					</td>
				</tr>
				
				<tr class="tohide" style="display:none;">
					<th><label class="hasTip" title="<?php echo JText::_('PERMANENTADDTXT'); ?>"><?php echo JText::_('PERMANENT_ADDRESS'); ?></label></th>
					<td>
					<textarea class="text_area" name="permanent_address" id="permanent_address" rows="4" cols="50"><?php echo $this->item->permanent_address;?></textarea>
					</td>
				</tr>
				
				<tr class="tohide" style="display:none;">
					<th><label class="hasTip" title="<?php echo JText::_('JOINDATETXT'); ?>"><?php echo JText::_('JOINING_DATE'); ?></label></th>
					<td><?php echo JHTML::_('calendar', $this->item->joining_date, "joining_date" , "joining_date", '%Y-%m-%d'); ?></td>
				</tr>
				
				<tr class="tohide" style="display:none;">
					<th><label class="hasTip" title="<?php echo JText::_('WORKTYPETXT'); ?>"><?php echo JText::_('WORK_TYPE'); ?></label></th>
					<td>
						<select name="work_type">
						<option value="permanent" <?php if($this->item->work_type=="permanent") echo 'selected="selected"'; ?>><?php echo JText::_('PERMANENT');?></option>
						<option value="contract" <?php if($this->item->work_type=="contract") echo 'selected="selected"'; ?>><?php echo JText::_('CONTRACT'); ?></option>
						<option value="other" <?php if($this->item->work_type=="other") echo 'selected="selected"'; ?>><?php echo JText::_('OTHER'); ?></option>
						
						</select>
					</td>
				</tr>
				
				<tr class="tohide" style="display:none;">
					<th><label class="hasTip" title="<?php echo JText::_('PAYMENTTYPETXT'); ?>"><?php echo JText::_('PAYMENT_TYPE'); ?></label></th>
					<td>
						<select name="payment_type">
						<option value="bank" <?php if($this->item->payment_type=="bank") echo 'selected="selected"'; ?>><?php echo JText::_('BANK_TRANSFER');?></option>
						<option value="checque" <?php if($this->item->payment_type=="checque") echo 'selected="selected"'; ?>><?php echo JText::_('CHECQUE'); ?></option>
						<option value="cash" <?php if($this->item->payment_type=="cash") echo 'selected="selected"'; ?>><?php echo JText::_('CASH'); ?></option>
						
						</select>
					</td>
				</tr>
				
				<tr class="tohide" style="display:none;">
					<th width="200"><label class="hasTip" title="<?php echo JText::_('PANTXT'); ?>"><?php echo JText::_('PAN'); ?></label></th>
					<td><input class="text_area" type="text" name="pan" id="pan" value="<?php echo $this->item->pan;?>"/></td>
				</tr>
				
				<tr class="tohide" style="display:none;">
					<th width="200"><label class="hasTip" title="<?php echo JText::_('PFACTXT'); ?>"><?php echo JText::_('PF_AC'); ?></label></th>
					<td><input class="text_area" type="text" name="pf_ac" id="pf_ac" value="<?php echo $this->item->pf_ac;?>"/></td>
				</tr>
				
				<tr class="tohide" style="display:none;">
					<th width="200"><label class="hasTip" title="<?php echo JText::_('BANKACNOTXT'); ?>"><?php echo JText::_('BANK_AC_NO'); ?>:</label></th>
					<td><input class="text_area" type="text" name="bank_ac" id="bank_ac" value="<?php echo $this->item->bank_ac;?>"/></td>
				</tr>
				
				<tr class="tohide" style="display:none;">
					<th width="200"><label class="hasTip" title="<?php echo JText::_('BANKNAMETXT'); ?>"><?php echo JText::_('BANK_NAME'); ?></label></th>
					<td><input class="text_area" type="text" name="bank_name" id="bank_name" value="<?php echo $this->item->bank_name;?>"/></td>
				</tr>
				
				<tr class="tohide" style="display:none;">
					<th width="200"><label class="hasTip" title="<?php echo JText::_('BANKBRANCHTXT'); ?>"><?php echo JText::_('BANK_BRANCH'); ?></label></th>
					<td><input class="text_area" type="text" name="bank_branch" id="bank_branch" value="<?php echo $this->item->bank_branch;?>"/></td>
				</tr>
				
				<tr class="tohide" style="display:none;">
					<th width="200"><label class="hasTip" title="<?php echo JText::_('IFSCTXT'); ?>"><?php echo JText::_('IFSC_CODE'); ?></label></th>
					<td><input class="text_area" type="text" name="ifsc" id="ifsc" value="<?php echo $this->item->ifsc;?>"/></td>
				</tr>
				
				<tr class="tohide" style="display:none;">
					<th><label class="hasTip" title="<?php echo JText::_('LEAVINGDATETXT'); ?>"><?php echo JText::_('LEAVING_DATE'); ?></label></th>
					<td><?php echo JHTML::_('calendar', $this->item->leaving_date, "leaving_date" , "leaving_date", '%Y-%m-%d'); ?></td>
				</tr>
				
				</tbody>
			</table>
		</div>
		
		<div id="tabs-2">
				
			<table class="adminform table table-striped">
				<tbody>
				
					<?php if($this->item->userid) { ?>
						<th width="200"><label><?php echo JText::_('EFFECTIVE_FROM_DATE'); ?></label></th>
						<td><?php echo $this->item->sal_effective_date; ?></td>
					<?php } else { ?>
						<tr>
							<th><label class="hasTip" title="<?php echo JText::_('EFECTIVEDATETXT'); ?>"><?php echo JText::_('EFFECTIVE_FROM_DATE'); ?></label></th>
							<td><?php echo JHTML::_('calendar', JFactory::getDate()->format('Y-m-d'), "sal_effective_date" , "sal_effective_date", '%Y-%m-%d'); ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			
			<table class="adminform table table-striped">
				<tbody>				
					<thead>
						<tr>
							<th><?php echo JText::_('PAYHEADNAME') ; ?></th>
							<th><?php echo JText::_('PAYHEAD_TYPE') ; ?></th>
							<th><?php echo JText::_('AMOUNT') ; ?></th>
						</tr>
					</thead>
					
					<?php 
					
					$k = 0;
					for($i=0, $n=count( $this->sal_struct ); $i < $n; $i++) {
						
						$row = $this->sal_struct[$i];
						if(!empty($this->emp_sal))
						{
							
							
							if(!array_key_exists($i, $this->emp_sal)) {
								$empSal = new stdClass();
								$empSal->amount = null;
							} else {
								$empSal = $this->emp_sal[$i];
							}
							
						} else {
							$empSal = new stdClass();
							$empSal->amount = null;
						}
						
						
						//echo'<pre>';print_r($empSal);
						
						if($row->payhead_type=="earning") {
							$payhead_type = JText::_('EARNINGS');
						} else if($row->payhead_type=="std_deduction") {
							$payhead_type = JText::_('STANDARD_DEDUCTION');
						} else if($row->payhead_type=="other_deduction") {
							$payhead_type = JText::_('OTHER_DEDUCTION');
						}

					?>
					<tr class="<?php echo "row$k"; ?>">
						
						<td align="center"><?php echo $row->name; ?></td>
						
						<td align="center"><?php echo $payhead_type; ?></td>
						
						<?php if($this->increment_transfer) { ?>
						<td align="center"><span><?php echo $empSal->amount;?></span></td>
						<input class="text_area" type="hidden" name="amount[]" value="<?php echo $empSal->amount;?>"/>
						<?php } else { ?>
						<td align="center"><input class="text_area" type="text" name="amount[]" value="<?php echo $empSal->amount;?>"/></td>
						<?php } ?>
						<input class="text_area" type="hidden" name="payid[]" value="<?php echo $row->id;?>"/>
					
					</tr>
					
					<?php
						$k = 1 - $k;
					}
					?>
					<input class="text_area" type="hidden" name="lastIncrement" value="<?php echo $this->lastIncrement;?>"/>
				</tbody>
			</table>
		</div>
		
		<?php if($this->item->userid) { ?>
		<div id="tabs-3">
		
			<?php if($this->increment_transfer) { ?>
			<div id="add_increment">
				<input type="button" id="add-inc" value="<?php echo JText::_('ADD_INCREMENT'); ?>" class="btn btn-success" style="margin-bottom:10px" />
			</div>
			<?php } ?>
				
			
			<table class="adminform table table-striped" id="increment">
				<tbody>				
					<thead>
						<tr>
							<th><?php echo JText::_('ID') ; ?></th>
							<th><?php echo JText::_('CTC') ; ?></th>
							<th><?php echo JText::_('INCREMENT_DATE') ; ?></th>
						</tr>
					</thead>
					
					<?php 
					$k = 0;
					for($i=0, $n=count( $this->increment ); $i < $n; $i++) {
						$row = $this->increment[$i];
						
						
						$created = strtotime($row->created);
						$created_date = date('Y-m-d', $created );
						
						$format = $this->config->date_format;
						$saved_date = $created_date;
						$datetime = strtotime($saved_date);
						if($format)
						{
							$increment_date = date($format, $datetime );
							
						} else {
							$increment_date = $saved_date;
						}

					?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center"><?php echo $row->id; ?></td>
						<td align="center"><?php echo $row->ctc; ?></td>
						<td align="center"><?php echo $increment_date; ?></td>
					</tr>
					
					<?php
						$k = 1 - $k;
					}
					?>
				</tbody>
			</table>
		</div>
		
		
		<div id="tabs-4">
			<?php if(empty($this->request)) { ?>
				<span><?php echo JText::_('NO_RECORDS_TO_SHOW'); ?></span>
			<?php } else { ?>
			<table class="adminform table table-striped">
				<tbody>
					<thead>
						<tr>
							<th><?php echo JText::_( 'SR_NO' ); ?></th>
							<th><?php echo JText::_('FROM'); ?></th>
							<th><?php echo JText::_('TO'); ?></th>
							<th><?php echo JText::_('DAYS'); ?></th>
							<th><?php echo JText::_('APPROVE'); ?></th>
						</tr>
					</thead>
					<?php
					$k = 0;
					for ($i=0, $n=count( $this->request ); $i < $n; $i++)	{
						$row = &$this->request[$i];
						
						$format = $this->config->date_format;
						
						$begin=strtotime($row->start_date);
						$end=strtotime($row->end_date);
						
						$leave_start = date($format, $begin );
			
						$leave_end = date($format, $end );
						
						$total_days=0;
						while($begin<=$end){
							$total_days++; // no of days in the given interval
							$begin+=86400;
						};

						?>
						<tr class="<?php echo "row$k"; ?>">
							<td align="center"><?php echo $this->pagination->getRowOffset($i); ?></td>

							<td align="center"><?php echo $leave_start; ?></td>

							<td align="center"><?php echo $leave_end; ?></td>

							<td align="center"><?php echo $total_days; ?></td>
							
							<td align="center">
								<?php if($row->approved==1) { ?>
									<span><?php echo JText::_('APPROVED') ?></span>
								<?php } else { ?>
									<div class="who-woe">
										<span class="loadingbox" style="display:none;">
										<?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/load.gif"   />' ?>
										</span>
										<a class="approve" id="<?php echo $row->id; ?>" href="javascript:void(0);"><i class="fa fa-check"></i></a>
									</div>
								<?php } ?>
							</td>
						</tr>

					<?php
						$k = 1 - $k;
					}
					?>
				</tbody>
			</table>
			<?php } ?>
		</div>
		
		<div id="tabs-5">
			<div id='attendance'></div>
		</div>
		
		<div id="tabs-6">
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
		</div>
		
		<?php } ?>
	</div>


</fieldset>
</div>

<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->item->userid; ?>" />
<input type="hidden" name="userid" value="<?php echo $this->item->userid; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="employee" />
</form>
</div>
</div>
</div>