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
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select'); 

$db = JFactory::getDbo();

$user = JFactory::getUser();


$document = JFactory::getDocument();
$document->addStyleSheet('components/com_vbizz/assets/css/fullcalendar.css');
//$document->addStyleSheet('components/com_vbizz/assets/css/fullcalendar.print.css');
$document->addScript('components/com_vbizz/assets/js/moment.min.js');
$document->addScript('components/com_vbizz/assets/js/fullcalendar.min.js');

//filter for owner
if(VaccountHelper::checkOwnerGroup()) {
	$text = JText::_('ALL_TRANSACTIONS');
	
	$html ='<h2 class="report-heading">'.$text.'</h2>';
	
	$html .=	'<div class="trantypecal">';
	$html .=		'<select name="types" id="types">';
	$html .=			'<option value="1">'.JText::_('ALL_TRANSACTIONS').'</option>';
	$html .=			'<option value="2">'.JText::_('INCOME').'</option>';
	$html .=			'<option value="3">'.JText::_('EXPENSE').'</option>';
	$html .=			'<option value="4">'.JText::_('DUE_INCOMES').'</option>';
	$html .=			'<option value="5">'.JText::_('DUE_EXPENSES').'</option>';
	$html .=			'<option value="6">'.JText::_('HOLIDAYS').'</option>';
	$html .=			'<option value="7">'.JText::_('PROJECTS').'</option>';
	$html .=			'<option value="8">'.JText::_('INVOICE_DUE_DATE').'</option>';
	$html .=		'</select>';
	$html .=	'</div>';
	
} else if(VaccountHelper::checkEmployeeGroup()) { //filter for employee
	$text = JText::_('LEAVES_N_HOLIDAYS');
	$html ='<h2 class="report-heading">'.$text.'</h2>';
	
	$html .=	'<div class="empreport">';
	$html .=		'<select name="types" id="types">';
	$html .=			'<option value="1">'.JText::_('LEAVES_N_HOLIDAYS').'</option>';
	$html .=			'<option value="2">'.JText::_('TASKS_DUE_DATE').'</option>';
	$html .=		'</select>';
	$html .=	'</div>';
} else if(VaccountHelper::checkClientGroup()) { //filter for client
	$text = JText::_('TRANSACTIONS');
	$html ='<h2 class="report-heading">'.$text.'</h2>';
	$html .=	'<div class="empreport">';
	$html .=		'<select name="types" id="types">';
	$html .=			'<option value="1">'.JText::_('TRANSACTIONS').'</option>';
	$html .=			'<option value="2">'.JText::_('INVOICE_DUE_DATE').'</option>';
	$html .=			'<option value="3">'.JText::_('PROJECTS').'</option>';
	$html .=		'</select>';
	$html .=	'</div>';
} else if(VaccountHelper::checkVenderGroup()) { //filter for vendor
	$text = JText::_('TRANSACTIONS');
	$html ='<h2 class="report-heading">'.$text.'</h2>';
}


$weekoffday = !empty($this->config->weekoffday)?implode(',',json_decode($this->config->weekoffday)):'';


?>


<link href='components/com_vbizz/assets/css/fullcalendar.print.css' rel='stylesheet' media='print' />
<script type="text/javascript">

<?php if(VaccountHelper::checkOwnerGroup()) { ?>
jQuery(document).ready(function() {  
	var types=1;
	initializeCalendar(types);
});

jQuery(document).on('change', 'select[name="types"]', function()	{  
	jQuery('#calendarcheck').fullCalendar("destroy");
	//jQuery('#calendar').fullCalendar("destroy");
	var types = jQuery('select[name="types"]').val(); 
	initializeCalendar(types);
});

function initializeCalendar(types) { 
    jQuery('#calendarcheck').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'year,month,basicWeek,basicDay'
		},
		defaultView: 'year',
		yearColumns: 3,
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
				data: {'option':'com_vbizz', 'view':'reports', 'task':'ownerTransaction', 'tmpl':'component', 'start':start.unix(), 'end':end.unix(), 'types':types},
				
				beforeSend: function() {
					jQuery(".vbizz_overlay").css('display','inline-block');
				},
				complete: function() {
					jQuery(".vbizz_overlay").hide();
				},

												
				success: function(data) { 
					if(types==1) {
						jQuery('.report-heading').text('<?php echo JText::_('ALL_TRANSACTIONS'); ?>');
					}else if(types==2) {
						jQuery('.report-heading').text('<?php echo JText::_('INCOME'); ?>');
					} else if(types==3) {
						jQuery('.report-heading').text('<?php echo JText::_('EXPENSE'); ?>');
					} else if(types==4) {
						jQuery('.report-heading').text('<?php echo JText::_('DUE_INCOMES'); ?>');
					} else if(types==5) {
						jQuery('.report-heading').text('<?php echo JText::_('DUE_EXPENSES'); ?>');
					} else if(types==6) {
						jQuery('.report-heading').text('<?php echo JText::_('HOLIDAYS'); ?>');
					} else if(types==7) {
						jQuery('.report-heading').text('<?php echo JText::_('PROJECTS'); ?>');
					} else if(types==8) {
						jQuery('.report-heading').text('<?php echo JText::_('INVOICE_DUE_DATE'); ?>');
					} else {
						jQuery('.report-heading').text('<?php echo JText::_('ALL_TRANSACTIONS'); ?>');
					}  
					var events = data.transactions;
					callback(events);
				}
			});
		},
		
		eventRender: function(event, element) { 
			element.find('.fc-title').append("<br/>" + event.amount); 
		},
		
		dayRender: function (date, cell) {
			<?php if(!empty($weekoffday)) { ?>
			var weekoff = '<?php echo $weekoffday; ?>';
			var weekoffday = weekoff.split(',');
			
			for(var i=0; i<weekoffday.length; i++) {
				var cl = weekoffday[i];
				jQuery('.'+cl).css('color','red');
			}
			<?php } ?>
		}
	
    });
	
}
<?php } ?>

<?php if(VaccountHelper::checkEmployeeGroup() && $this->config->enable_employee) { ?>

jQuery(document).ready(function() {
	var types=1;
	initializeCalendar(types);
});

jQuery(document).on('change', 'select[name="types"]', function()	{
	jQuery('#calendarcheck').fullCalendar("destroy");
	var types = jQuery('select[name="types"]').val();
	initializeCalendar(types);
});

function initializeCalendar(types) {
	
	jQuery('#calendarcheck').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'year,month,basicWeek,basicDay'
		},
		
		defaultView: 'year',
		yearColumns: 3,
		firstDay: 0,
		contentHeight: 'auto',
		selectable: true,
		selectHelper: true,
		editable: false,
		eventLimit: false, // allow "more" link when too many events

        events: function(start, end, timezone, callback) {
			jQuery.ajax({
				url: 'index.php',
				type: "POST",
				dataType:"json",
				data: {'option':'com_vbizz', 'view':'reports', 'task':'employeeReport', 'tmpl':'component', 'start':start.unix(), 'end':end.unix(), 'types':types },
				
				beforeSend: function() {
					jQuery(".vbizz_overlay").css('display','inline-block');
				},
				complete: function() {
					jQuery(".vbizz_overlay").hide();
				},
				
				success: function(data) {
					if(types==1) {
						jQuery('.report-heading').text('<?php echo JText::_('LEAVES_N_HOLIDAYS'); ?>');
					} else if(types==2) {
						jQuery('.report-heading').text('<?php echo JText::_('TASKS_DUE_DATE'); ?>');
					} else {
						jQuery('.report-heading').text('<?php echo JText::_('LEAVES_N_HOLIDAYS'); ?>');
					}  
					var events = data.employee;
					callback(events);
				}
			});
		},
		
		dayRender: function (date, cell) {
			<?php if(!empty($weekoffday)) { ?>
			var weekoff = '<?php echo $weekoffday; ?>';
			var weekoffday = weekoff.split(',');
			
			for(var i=0; i<weekoffday.length; i++) {
				var cl = weekoffday[i];
				jQuery('.'+cl).css('color','red');
			}
			<?php } ?>
		}
		
	});
}
<?php } ?>

<?php if(VaccountHelper::checkClientGroup() && $this->config->enable_cust) { ?>

jQuery(document).ready(function() {
	var types=1;
	initializeCalendar(types);
});

jQuery(document).on('change', 'select[name="types"]', function()	{
	jQuery('#calendarcheck').fullCalendar("destroy");
	var types = jQuery('select[name="types"]').val();
	initializeCalendar(types);
});

function initializeCalendar(types) {
	
	jQuery('#calendarcheck').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'year,month,basicWeek,basicDay'
		},
		
		defaultView: 'year',
		yearColumns: 3,
		firstDay: 0,
		contentHeight: 'auto',
		selectable: true,
		selectHelper: true,
		editable: false,
		eventLimit: false, // allow "more" link when too many events

        events: function(start, end, timezone, callback) {
			jQuery.ajax({
				url: 'index.php',
				type: "POST",
				dataType:"json",
				data: {'option':'com_vbizz', 'view':'reports', 'task':'customerReport', 'tmpl':'component', 'start':start.unix(), 'end':end.unix(), 'types':types },
				
				beforeSend: function() {
					jQuery(".vbizz_overlay").css('display','inline-block');
				},
				complete: function() {
					jQuery(".vbizz_overlay").hide();
				},
				
				success: function(data) {
					
				if(types==1) {
						jQuery('.report-heading').text('<?php echo JText::_('TRANSACTIONS'); ?>');
					} else if(types==2) {
						jQuery('.report-heading').text('<?php echo JText::_('INVOICE_DUE_DATE'); ?>');
					} else if(types==3) {
						jQuery('.report-heading').text('<?php echo JText::_('PROJECTS'); ?>');
					} else {
						jQuery('.report-heading').text('<?php echo JText::_('TRANSACTIONS'); ?>');
					}  
					var events = data.customer;
					callback(events);
				}
			});
		},
		
		eventRender: function(event, element) { 
			element.find('.fc-title').append("<br/>" + event.amount); 
		},
		
		dayRender: function (date, cell) {
			<?php if(!empty($weekoffday)) { ?>
			var weekoff = '<?php echo $weekoffday; ?>';
			var weekoffday = weekoff.split(',');
			
			for(var i=0; i<weekoffday.length; i++) {
				var cl = weekoffday[i];
				jQuery('.'+cl).css('color','red');
			}
			<?php } ?>
		}
		
	});
}
<?php } ?>

<?php if(VaccountHelper::checkVenderGroup() && $this->config->enable_vendor) { ?>

jQuery(document).ready(function() {
	var types=1;
	initializeCalendar(types);
});

jQuery(document).on('change', 'select[name="types"]', function()	{
	jQuery('#calendarcheck').fullCalendar("destroy");
	var types = jQuery('select[name="types"]').val();
	initializeCalendar(types);
});

function initializeCalendar(types) {
	
	jQuery('#calendarcheck').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'year,month,basicWeek,basicDay'
		},
		
		defaultView: 'year',
		yearColumns: 3,
		firstDay: 0,
		contentHeight: 'auto',
		selectable: true,
		selectHelper: true,
		editable: false,
		eventLimit: false, // allow "more" link when too many events

        events: function(start, end, timezone, callback) {
			jQuery.ajax({
				url: 'index.php',
				type: "POST",
				dataType:"json",
				data: {'option':'com_vbizz', 'view':'reports', 'task':'vendorReport', 'tmpl':'component', 'start':start.unix(), 'end':end.unix(), 'types':types },
				
				beforeSend: function() {
					jQuery(".vbizz_overlay").css('display','inline-block');
				},
				complete: function() {
					jQuery(".vbizz_overlay").hide();
				},
				
				success: function(data) {
					
					var events = data.vendor;
					callback(events);
				}
			});
		},
		
		eventRender: function(event, element) { 
			element.find('.fc-title').append("<br/>" + event.amount); 
		},
		
		dayRender: function (date, cell) {
			<?php if(!empty($weekoffday)) { ?>
			var weekoff = '<?php echo $weekoffday; ?>';
			var weekoffday = weekoff.split(',');
			
			for(var i=0; i<weekoffday.length; i++) {
				var cl = weekoffday[i];
				jQuery('.'+cl).css('color','red');
			}
			<?php } ?>
		} 
		
	});
}
<?php } ?>

</script>


<header class="header">
		<div class="container-title">
				<h1 class="page-title"><?php echo JText::_('REPORTS'); ?></h1>
		</div>
</header>

<div class="content_part cont_reports">
	<div class="vbizz_overlay"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/loading_second.gif"   />' ?></div>

	<div class="calendar_blocks">
	
		<div class="calendar_block expenses">
			<div class="calendar_box">
				<div class="calendar_x_tool">
					<?php echo $html; ?>
					<div id='calendarcheck'></div>
				</div>
			</div>
		</div>
		
	</div>
	
</div>
</div>
</div>