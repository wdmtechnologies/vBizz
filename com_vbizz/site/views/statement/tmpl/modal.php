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

$user = JFactory::getUser();

$db = JFactory::getDbo();

$query='select initial_balance FROM `#__vbizz_accounts` where id='.JRequest::getInt('accountid',0) ;
$db->setQuery($query);
$initial_balance = $db->loadResult();

$accountid = JRequest::getInt('accountid',0);
?>

<script type="text/javascript">
    
jQuery(function()	{
    if(typeof google !== "undefined")
	    google.setOnLoadCallback(drawLineChart);
	
	jQuery('.linecharttype').on('click', 'span.btn', function()	{
		if(jQuery(this).hasClass('active'))
			return;
		else	{
			jQuery('.linecharttype>span').removeClass('active');
			jQuery(this).addClass('active');
			drawLineChart();
		}
	});
	
	jQuery('.linecharttype').on('change', 'select[name="duration"]', function()	{ 
		drawLineChart();
	});
		
	
    function drawLineChart() 
	{
		
		var accountid = <?php echo JRequest::getInt('accountid',0) ?>;
		var duration = jQuery('select[name="duration"]').val(); 
		
		jQuery.ajax(
		{
			url: "index.php",
			type: "POST",
			dataType:"json",
			data: {'option':'com_vbizz', 'view':'statement', 'task':'drawLineChart', 'tmpl':'component', 'accountid':accountid, 'duration':duration},
			beforeSend: function()	{
				jQuery("#line_chart_div .loadingbox").show();
			},
			complete: function()	{
				jQuery("#line_chart_div .loadingbox").hide();
			},
			success: function(data)	
			{ 
				if(data.result == "success")
				{
					var transactiondata = google.visualization.arrayToDataTable(data.lines);
					
					var transactionoptions = 
					{
						title: '<?php JText::_('ACCOUNT_BAL_DURING_TIME'); ?>',
						backgroundColor:'#ffffff',
						height:250,
						colors: ['#67b7dc', '#fdd400']

					};
					
					var chart = new google.visualization.LineChart(document.getElementById('line_chart_div'));
					chart.draw(transactiondata, transactionoptions);
				}
				else{
					var htm = '<span style="display:inline-block; line-height:200px; text-align:center; width:100%;">No Data</span>';
					jQuery('#line_chart_div').html(htm);
				}
			},
			error: function(jqXHR, textStatus, errorThrown)	{
			}
		});
    }
});
</script>


<!--Line Chart Start -->   
<div class="cp_block line_chart">
    <div class="chart_box">
        <div class="linecharttype chart_x_tool">
        	<h2><?php echo JText::_('ACCOUNT_BAL_DURING_TIME'); ?></h2>
			<div class="select_type">
                <select name="duration" id="duration">
                <option value=""><?php echo JText::_('ALL'); ?></option>
                <option value="current_month"><?php echo JText::_('CURRENT_MONTH'); ?></option>
                <option value="current_year"><?php echo JText::_('CURRENT_YEAR'); ?></option>
                </select>
			</div>
        </div>
		
        <div id="line_chart_div"><span class="loadingbox" style="display:none;"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/spinner.gif" />' ?></span></div>
    </div>
</div>
<!--Line Chart End -->
