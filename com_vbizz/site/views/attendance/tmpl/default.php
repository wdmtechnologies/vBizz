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

//check joomla version
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion>='3.0') {
	JHtml::_('formbehavior.chosen', 'select');
}


$user = JFactory::getUser();

$count = count($this->items);

if($this->config->weekoffday==='null' || is_null($this->config->weekoffday) || $this->config->weekoffday=='null' || $this->config->weekoffday=='') {
	$weekoffday = "";
} else {
	$weekoffday = implode(',',json_decode($this->config->weekoffday));
}

?>
<script>

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
						
});


jQuery(document).on('click','.send',function() {
		
	var date = jQuery('input[type=hidden][name="date"]').val();
	var employee = jQuery(this).parents('#dialog').find('input[type=hidden][name="employee"]').val();
	var present = jQuery(this).parents('#dialog').find('input[type=radio][name="present"]:checked').val();
	var halfday = jQuery(this).parents('#dialog').find('input[type=radio][name="halfday"]:checked').val();
	var paid = jQuery(this).parents('#dialog').find('input[type=radio][name="paid"]:checked').val();
	var divNO = jQuery(this).parents('#dialog').find('input[type=hidden][name="divNO"]').val();
	
	var that=this;
	jQuery.ajax({
		url: 'index.php',
		type: "POST",
		dataType:"JSON",
		data: {'option':'com_vbizz', 'view':'attendance', 'task':'attendanceParams', 'tmpl':'component', 'date':date, 'present':present, 'halfday':halfday, 'paid':paid, 'employee':employee },
		
		beforeSend: function() {
			jQuery(that).parent().find("span.loadingbox").show();
		},
		
		complete: function()      {
			jQuery(that).parent().find("span.loadingbox").hide();
		},

		success: function(data){
			if(data.result=="success"){
				jQuery('#attendance_'+divNO).fullCalendar("destroy");
				var calFunction = window["initializeCalendar" + divNO];
				calFunction();
				jQuery( "#dialog" ).dialog( "close" );
				//setTimeout(function() { jQuery( "#dialog" ).dialog( "close" );},3000);
			} else {
				alert(data.msg);
				jQuery( "#dialog" ).dialog( "close" );
			}
		}
	});
});


</script>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('ATTENDANCE'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=attendance');?>" method="post" name="adminForm" id="adminForm">


<div class="adminlist filter">
	<div class="filet_left">
		<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
		<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT');?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
		<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
	</div>
</div>


<div id="editcell">
<div class="at_toolbar">
<div class="at_toolbar_left">
	<div class="prev-btn-wrapper" >
		<span class="btn btn-small" id="my-prev-button"><span class="fa fa-backward"></span></span>
	</div>
	
	<div class="next-btn-wrapper" >
		<span class="btn btn-small" id="my-next-button"><span class="fa fa-forward"></span></span>
	</div>
	
	<div class="today-btn-wrapper" >
		<span class="btn btn-small" id="my-today-button"><?php echo JText::_('TODAY'); ?></span>
	</div>
	</div>
	<div class="at_toolbar_right">
	<div class="btn-wrapper">
			<span onclick="Joomla.submitbutton('todayAttendance')" class="btn btn-small">
			<span class="fa fa-pencil"></span> <?php echo JText::_('MARK_ATTENDANCE'); ?></span>
		</div>
		</div>
		</div>
	
	<table class="adminlist table">
        <thead>
            <tr>
                <th width="10"><?php echo JText::_( 'SR_NO' ); ?></th>
                <th><?php echo JText::_( 'NAME' ); ?></th>
				<th></th>
            </tr>
        </thead>
        <?php
		
		$next = "";
		$prev = "";
		$today = "";
        $k = 0;
        for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
            $row = &$this->items[$i];
			
			$next .='jQuery("#attendance_'.$i.'").fullCalendar("next");';
			$prev .='jQuery("#attendance_'.$i.'").fullCalendar("prev");';
			$today .='jQuery("#attendance_'.$i.'").fullCalendar("today");';
			
            ?>
			
			<script type="text/javascript">

				jQuery(document).ready(function() {
					initializeCalendar<?php echo $i; ?>();
				});
				
				
					
					function initializeCalendar<?php echo $i; ?>() {
						jQuery('#attendance_<?php echo $i; ?>').fullCalendar({
							header: {
								left: '',
								center: 'title',
								right: ''
							},
							
							defaultView: 'basicWeek',
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
									data: { 'option':'com_vbizz', 'view':'attendance', 'task':'attendance', 'tmpl':'component', 'start':start.unix(), 'end':end.unix(), 'employee':<?php echo $row->userid; ?> },
									
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
							
							dayClick: function(date, jsEvent, view) { 
								 
								var that = this;
								jQuery.ajax({
									url: 'index.php',
									type: "POST",
									dataType:"JSON",
									data: {'option':'com_vbizz', 'view':'attendance', 'task':'attendValue', 'tmpl':'component', 'date':date.format(), 'employee':<?php echo $row->userid; ?>, 'divNO':<?php echo $i; ?> },
									
									success: function(data){
										if(data.result=="success"){
											jQuery(that).parent().append(data.htm);
											jQuery( "#dialog" ).dialog();
										} else {
											alert(data.msg);
										}
									}
								});
							}
							
						});
					}
				

			</script>
            <tr class="<?php echo "row$k"; ?>">
            
                <td align="center"><?php echo $this->pagination->getRowOffset($i); ?></td>
                                
                <td align="center"><?php echo $row->name; ?></td>
				
				<td><div id='attendance_<?php echo $i; ?>'></div></td>
				
				<input type="hidden" name="employee" value="<?php echo $row->userid; ?>" />
				
            </tr>
           
                
            <?php
            $k = 1 - $k;
        }
        ?>
    
        <tfoot>
            <tr>
              <td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td>
            </tr>
        </tfoot>
  
	</table>
</div>

<script>
jQuery(document).ready(function() {
	jQuery('#my-next-button').click(function() {
		<?php echo $next; ?>
	});
	jQuery('#my-prev-button').click(function() {
		<?php echo $prev; ?>
	});
	jQuery('#my-today-button').click(function() {
		<?php echo $today; ?>
	});
});
</script>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="attendance" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>
