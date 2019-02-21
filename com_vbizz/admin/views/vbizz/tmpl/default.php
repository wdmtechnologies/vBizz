<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHtml::_('behavior.modal');
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion>='3.0') {
	JHtml::_('formbehavior.chosen', 'select');
}

$user = JFactory::getUser();
$userId = $user->id;

?>

<script type="text/javascript"> 
    
jQuery(function()	{
    if(typeof google !== "undefined")
	    google.setOnLoadCallback(drawExpenseChart);
		
	jQuery('.expensecharttype').on('change', 'select[name="duration"]', function()	{
		drawExpenseChart();
	});
	  
    function drawExpenseChart() 
	{
		var duration = jQuery('select[name="duration"]').val();
    		
		jQuery.ajax(
		{
			url: "index.php",
			type: "POST",
			dataType:"json",
			data: {'option':'com_vbizz', 'task':'drawExpenseChart', 'tmpl':'component', 'duration':duration},
			beforeSend: function()	{
				jQuery("#vbizzpanel .loadingblock").show();
			},
			complete: function()	{
				jQuery("#vbizzpanel .loadingblock").hide();
			},
			success: function(data)	
			{
				if(data.result == "success")
				{
					var expensedata = google.visualization.arrayToDataTable(data.expenses);
					
					var expenseoptions = 
					{
						title: '<?php JText::_('EXPENSES'); ?>',
						backgroundColor:'#ffffff',
						colors:['#67B7DC', '#FDD400', '#84B761', '#CC4748', '#cd82ad', '#2f4074', '#B5CBE9', '#FF9873', '#1D90FA', '#D77C7C'],
						height:200,chartArea:{left:5,top:0,width:'100%',height:'100%'}
					};
					var chart = new google.visualization.PieChart(document.getElementById('expense_chart_div'));
					chart.draw(expensedata, expenseoptions);
				}
				else
				{
				}
			},
			error: function(jqXHR, textStatus, errorThrown)	{
			}
		});
    }
});
</script>

<script type="text/javascript">
    
jQuery(function()	{
    if(typeof google !== "undefined")
	    google.setOnLoadCallback(drawIncomeChart);
		
		jQuery('.incomecharttype').on('change', 'select[name="periods"]', function()	{
		drawIncomeChart();
	});
	
		    
    function drawIncomeChart() 
	{
		var periods = jQuery('select[name="periods"]').val();
    		
		jQuery.ajax(
		{
			url: "index.php",
			type: "POST",
			dataType:"json",
			data: {'option':'com_vbizz', 'task':'drawIncomeChart', 'tmpl':'component', 'periods':periods},
			beforeSend: function()	{
				jQuery("#vbizzpanel .loadingblock").show();
			},
			complete: function()	{
				jQuery("#vbizzpanel .loadingblock").hide();
			},
			success: function(data)	
			{ 
				if(data.result == "success")
				{
					var incomedata = google.visualization.arrayToDataTable(data.incomes);
					
					var incomeoptions = 
					{
						title: '<?php JText::_('INCOMES'); ?>',
						backgroundColor:'#ffffff',
						colors:['#67B7DC', '#FDD400', '#84B761', '#CC4748', '#cd82ad', '#2f4074', '#B5CBE9', '#FF9873', '#1D90FA', '#D77C7C'],
						height:200,chartArea:{left:5,top:0,width:'100%',height:'100%'}
					};
					
					var chart = new google.visualization.PieChart(document.getElementById('income_chart_div'));
					chart.draw(incomedata, incomeoptions);
				}
				else {}
			},
			error: function(jqXHR, textStatus, errorThrown)	{
			}
		});
    }
});
</script>

<script type="text/javascript"> 
    
jQuery(function()	{
    if(typeof google !== "undefined")
	    google.setOnLoadCallback(drawMostValuedCustomerChart);
		
		    
    function drawMostValuedCustomerChart() 
	{
		
    		
		jQuery.ajax(
		{
			url: "index.php",
			type: "POST",
			dataType:"json",
			data: {'option':'com_vbizz', 'task':'drawMostValuedCustomerChart', 'tmpl':'component'},
			beforeSend: function()	{
				jQuery("#vbizzpanel .loadingblock").show();
			},
			complete: function()	{
				jQuery("#vbizzpanel .loadingblock").hide();
			},
			success: function(data)	
			{
				if(data.result == "success")
				{
					var customerdata = google.visualization.arrayToDataTable(data.customers);
					
					var customeroptions = 
					{
						title: '<?php JText::_('CUSTOMERS'); ?>',
						backgroundColor:'#ffffff',
						colors:['#67B7DC', '#FDD400', '#84B761', '#CC4748', '#cd82ad', '#2f4074', '#B5CBE9', '#FF9873', '#1D90FA', '#D77C7C'],
						height:200,chartArea:{left:5,top:0,width:'100%',height:'100%'}
					};
					var chart = new google.visualization.PieChart(document.getElementById('customer_chart_div'));
					chart.draw(customerdata, customeroptions);
				}
				else{}
			},
			error: function(jqXHR, textStatus, errorThrown)	{
			}
		});
    }
});
</script>

<script type="text/javascript">
    
jQuery(function()	{
    if(typeof google !== "undefined")
	    google.setOnLoadCallback(drawMostValuedVendorChart);
		
	   
    function drawMostValuedVendorChart() 
	{
    		
		jQuery.ajax(
		{
			url: "index.php",
			type: "POST",
			dataType:"json",
			data: {'option':'com_vbizz', 'task':'drawMostValuedVendorChart', 'tmpl':'component'},
			beforeSend: function()	{
				jQuery("#vbizzpanel .loadingblock").show();
			},
			complete: function()	{
				jQuery("#vbizzpanel .loadingblock").hide();
			},
			success: function(data)	
			{ 
				if(data.result == "success")
				{
					var vendordata = google.visualization.arrayToDataTable(data.vendors);
					
					var vendoroptions = 
					{
						title: '<?php JText::_('VENDORS'); ?>',
						backgroundColor:'#ffffff',
						colors:['#67B7DC', '#FDD400', '#84B761', '#CC4748', '#cd82ad', '#2f4074', '#B5CBE9', '#FF9873', '#1D90FA', '#D77C7C'],
						height:200,chartArea:{left:5,top:0,width:'100%',height:'100%'}
					};
					
					var chart = new google.visualization.PieChart(document.getElementById('vendor_chart_div'));
					chart.draw(vendordata, vendoroptions);
				}
				else {}
			},
			error: function(jqXHR, textStatus, errorThrown)	{
			}
		});
    }
});
</script>

<script type="text/javascript"> 
    
jQuery(function()	{
    if(typeof google !== "undefined")
	    google.setOnLoadCallback(drawMostAddedItemChart);
		
	
	jQuery('.itemcharttype').on('change', 'select[name="item_types"]', function()	{
		drawMostAddedItemChart();
	});
		    
    function drawMostAddedItemChart() 
	{
		var item_types = jQuery('select[name="item_types"]').val();
    		
		jQuery.ajax(
		{
			url: "index.php",
			type: "POST",
			dataType:"json",
			data: {'option':'com_vbizz', 'task':'drawMostAddedItemChart', 'tmpl':'component', 'item_types':item_types},
			beforeSend: function()	{
				jQuery("#vbizzpanel .loadingblock").show();
			},
			complete: function()	{
				jQuery("#vbizzpanel .loadingblock").hide();
			},
			success: function(data)	
			{
				if(data.result == "success")
				{
					var itemdata = google.visualization.arrayToDataTable(data.items);
					
					var itemoptions = 
					{
						title: '<?php JText::_('ITEMS'); ?>',
						backgroundColor:'#ffffff',
						colors:['#67B7DC', '#FDD400', '#84B761', '#CC4748', '#cd82ad', '#2f4074', '#B5CBE9', '#FF9873', '#1D90FA', '#D77C7C'],
						height:200,chartArea:{left:5,top:0,width:'100%',height:'100%'}
					};
					var chart = new google.visualization.PieChart(document.getElementById('item_chart_div'));
					chart.draw(itemdata, itemoptions);
				}
				else{}
			},
			error: function(jqXHR, textStatus, errorThrown)	{
			}
		});
    }
});
</script>

<script type="text/javascript">
    
jQuery(function()	{
    if(typeof google !== "undefined")
	    google.setOnLoadCallback(drawColumnChart);
		
		jQuery('.columncharttype').on('click', 'span.btn', function()	{
		if(jQuery(this).hasClass('active'))
			return;
		else	{
			jQuery('.columncharttype>span').removeClass('active');
			jQuery(this).addClass('active');
			drawColumnChart();
		}
	});
	
	
    function drawColumnChart() 
	{
		var type = jQuery('.columncharttype>span.active').data('type');
    		
		jQuery.ajax(
		{
			url: "index.php",
			type: "POST",
			dataType:"json",
			data: {'option':'com_vbizz', 'task':'drawColumnChart', 'tmpl':'component', 'type':type},
			beforeSend: function()	{
				jQuery("#vbizzpanel .loadingblock").show();
			},
			complete: function()	{
				jQuery("#vbizzpanel .loadingblock").hide();
			},
			success: function(data)	
			{ 
				if(data.result == "success")
				{
					var transactiondata = google.visualization.arrayToDataTable(data.columns);
					
					var transactionoptions = 
					{
						title: '<?php JText::_('COLUMN_CHART'); ?>',
						backgroundColor:'#ffffff',
						colors: ['#67B7DC', '#FDD400'],
						height:250
					};
					var chart = new google.visualization.ColumnChart(document.getElementById('transaction_chart_div'));
					chart.draw(transactiondata, transactionoptions);
				}
				else {
					var htm = '<span style="display:inline-block; line-height:200px; text-align:center; width:100%;">No Data</span>';
					jQuery('#transaction_chart_div').html(htm);
				}
			},
			error: function(jqXHR, textStatus, errorThrown)	{
			}
		});
    }
});
</script>

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
	
	jQuery('.linecharttype').on('change', 'select[name="mode"]', function()	{
		drawLineChart();
	});
	
	jQuery('.linecharttype').on('change', 'select[name="types"]', function()	{
		drawLineChart();
	});
	
		    
    function drawLineChart() 
	{
		var type = jQuery('.linecharttype>span.active').data('type');
		var mode = jQuery('select[name="mode"]').val();
		var types = jQuery('select[name="types"]').val();
		
		jQuery.ajax(
		{
			url: "index.php",
			type: "POST",
			dataType:"json",
			data: {'option':'com_vbizz', 'task':'drawLineChart', 'tmpl':'component', 'type':type, 'mode':mode, 'types':types},
			beforeSend: function()	{
				jQuery("#vbizzpanel .loadingblock").show();
			},
			complete: function()	{
				jQuery("#vbizzpanel .loadingblock").hide();
			},
			success: function(data)	
			{ 
				if(data.result == "success")
				{
					var transactiondata = google.visualization.arrayToDataTable(data.lines);
					
					var transactionoptions = 
					{
						title: '<?php JText::_('LINE_CHART'); ?>',
						backgroundColor:'#ffffff',
						height:250,
						colors: ['#67B7DC', '#FDD400']

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

<script type="text/javascript"> 
jQuery(function()	{
    if(typeof google !== "undefined")
	    google.setOnLoadCallback(drawIncomeBudgetChart);
		
		jQuery('.incomebarcharttype').on('click', 'span.btn', function()	{
		if(jQuery(this).hasClass('active'))
			return;
		else	{
			jQuery('.incomebarcharttype>span').removeClass('active');
			jQuery(this).addClass('active');
			drawIncomeBudgetChart();
		}
	});
	   
    function drawIncomeBudgetChart() 
	{
		var type = jQuery('.incomebarcharttype>span.active').data('type');
    		
		jQuery.ajax(
		{
			url: "index.php",
			type: "POST",
			dataType:"json",
			data: {'option':'com_vbizz', 'task':'drawIncomeBudgetChart', 'tmpl':'component', 'type':type},
			beforeSend: function()	{
				jQuery("#vbizzpanel .loadingblock").show();
			},
			complete: function()	{
				jQuery("#vbizzpanel .loadingblock").hide();
			},
			success: function(data)	
			{
				if(data.result == "success")
				{
					var incomebudgetdata = google.visualization.arrayToDataTable(data.income_budget);
					
					var incomebudgetoptions = 
					{
						title: '<?php JText::_('INCOME_BUDGET_STATS'); ?>',
						backgroundColor:'#ffffff',
						colors: ['#67B7DC', '#FDD400'],
						height:250
					};
					var chart = new google.visualization.BarChart(document.getElementById('income_bar_chart_div'));
					chart.draw(incomebudgetdata, incomebudgetoptions);
				}
				else{
					var htm = '<span style="display:inline-block; line-height:200px; text-align:center; width:100%;">No Data</span>';
					jQuery('#income_bar_chart_div').html(htm);
				}
			},
			error: function(jqXHR, textStatus, errorThrown)	{
			}
		});
    }
});
</script>


<script type="text/javascript"> 
jQuery(function()	{
    if(typeof google !== "undefined")
	    google.setOnLoadCallback(drawExpenseBudgetChart);
		
	
	jQuery('.expensebarcharttype').on('click', 'span.btn', function()	{
		if(jQuery(this).hasClass('active'))
			return;
		else	{
			jQuery('.expensebarcharttype>span').removeClass('active');
			jQuery(this).addClass('active');
			drawExpenseBudgetChart();
		}
	});
		    
    function drawExpenseBudgetChart() 
	{
		var type = jQuery('.expensebarcharttype>span.active').data('type');
    		
		jQuery.ajax(
		{
			url: "index.php",
			type: "POST",
			dataType:"json",
			data: {'option':'com_vbizz', 'task':'drawExpenseBudgetChart', 'tmpl':'component', 'type':type},
			beforeSend: function()	{
				jQuery("#vbizzpanel .loadingblock").show();
			},
			complete: function()	{
				jQuery("#vbizzpanel .loadingblock").hide();
			},
			success: function(data)	
			{
				if(data.result == "success")
				{
					var expensebudgetdata = google.visualization.arrayToDataTable(data.expense_budget);
					
					var expensebudgetoptions = 
					{
						title: '<?php JText::_('EXPENSE_BUDGET_STATS'); ?>',
						backgroundColor:'#ffffff',
						colors: ['#67B7DC', '#FDD400'],
						height:250
					};
					var chart = new google.visualization.BarChart(document.getElementById('expense_bar_chart_div'));
					chart.draw(expensebudgetdata, expensebudgetoptions);
				}
				else{
					var htm = '<span style="display:inline-block; line-height:200px; text-align:center; width:100%;">No Data</span>';
					jQuery('#expense_bar_chart_div').html(htm);
				}
			},
			error: function(jqXHR, textStatus, errorThrown)	{
			}
		});
    }
});
</script>

<script type="text/javascript">
jQuery(function() {
	
	jQuery(document).on('click','.move',function() {
		
		var id = jQuery(this).attr('id');
		var that=this;
		
		jQuery.ajax({
			type: "POST",
			dataType:"JSON",
			data: {'option':'com_vbizz', 'task':'moveExpense', 'tmpl':'component','id':id},
			
			beforeSend: function() {
				jQuery(that).parent().find("span.loadingbox").show();
			},
			
			complete: function()      {
				jQuery(that).parent().find("span.loadingbox").hide();
			},

			success: function(data){
				if(data.result=="success"){
					jQuery(that).parent().parent().parent().remove();
				}
			}
		});
	
	});
	
	jQuery(document).on('click','.moveIncome',function() {
		
		var id = jQuery(this).attr('id');
		var that=this;
		
		jQuery.ajax({
			type: "POST",
			dataType:"JSON",
			data: {'option':'com_vbizz', 'task':'moveIncome', 'tmpl':'component','id':id},
			
			beforeSend: function() {
				jQuery(that).parent().find("span.loadingbox").show();
			},
			
			complete: function()      {
				jQuery(that).parent().find("span.loadingbox").hide();
			},

			success: function(data){
				if(data.result=="success"){
					jQuery(that).parent().parent().parent().remove();
				}
			}
		});
	
	});
	
	
	jQuery(document).on('click','#expense-export',function() {
		location.href='index.php?option=com_vbizz&view=vbizz&task=exportExpense';
	
	});
	
	jQuery(document).on('click','#income-export',function() {
		location.href='index.php?option=com_vbizz&view=vbizz&task=exportIncome';
	
	});
	
	jQuery(document).on('click','#cust-export',function() {
		location.href='index.php?option=com_vbizz&view=vbizz&task=exportCustomer';
	
	});
	
	jQuery(document).on('click','#vend-export',function() {
		location.href='index.php?option=com_vbizz&view=vbizz&task=exportVendor';
	
	});
	
	jQuery(document).on('click','#item-export',function() {
		var tid = jQuery('select[name="item_types"]').val();
		location.href='index.php?option=com_vbizz&view=vbizz&task=exportItem&types='+tid;
	
	});
	
	jQuery(document).on('click','#income-budget-export',function() {
		var tid = jQuery('.incomebarcharttype>span.active').data('type');
		location.href='index.php?option=com_vbizz&view=vbizz&task=exportIncomeBudget&type='+tid;
	
	});
	
	jQuery(document).on('click','#expense-budget-export',function() {
		var tid = jQuery('.expensebarcharttype>span.active').data('type');
		location.href='index.php?option=com_vbizz&view=vbizz&task=exportExpenseBudget&type='+tid;
	
	});
	
	jQuery(document).on('click','#line-export',function() {
		var type = jQuery('.linecharttype>span.active').data('type');
		var mode = jQuery('select[name="mode"]').val();
		var types = jQuery('select[name="types"]').val();
		
		location.href='index.php?option=com_vbizz&view=vbizz&task=exportLine&type='+type+'&mode='+mode+'&types='+types;
	
	});
	
	jQuery(document).on('click','#column-export',function() {
		
		var type = jQuery('.columncharttype>span.active').data('type');
		
		location.href='index.php?option=com_vbizz&view=vbizz&task=exportGrowth&type='+type;
	
	});
	jQuery( "#sortable1" ).sortable({
      connectWith: ".connectedSortable",
	  distance: 2,
	  stop: function( event, ui ) {
		 var ordering = new Array();
		 jQuery('.common_profile_main').each(function(index, value) { 
		   ordering.push(jQuery(this).attr('data-ordering-profile'));	 
		 });
		   jQuery.ajax({
			
						url: "index.php",
						type: "POST",
						dataType: "json",
						data: {'option':'com_vbizz', 'view':'vbizz', 'task':'update_widget_ordering', 'new_ordering':ordering, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
						
						error: function(jqXHR, textStatus, errorThrown)	{
						alert(textStatus);				  
						}
					
		  }); 
		  }
    }).disableSelection();
 

});
</script>  
<div id="vbizz">
<?php if (!empty( $this->sidebar)) : ?>   
		<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
		<?php endif;?>
<div class="profile-section">
<!-- <div class="hx_top_bar">       
<div class="hx_dash_title"><img src="<?php echo JURI::root();?>administrator/components/com_vbizz/assets/images/vbizz-logo.png" alt="vBizz"> <span class="hx_main_title"><?php echo JText::_( 'VBIZZ_DASHBOARD' );?></span></div>
<div class="hx_dash_buttons">
<div class="hx_contact hx_box"><a href="https://www.wdmtech.com/contact-us" target="_blank"><i class="icon-mail"></i> <?php echo JText::_( 'VBIZZ_CONTACT' );?></a></div>
<div class="hx_support hx_box"><a href="https://www.wdmtech.com/support-forum" target="_blank"><i class="icon-support"></i> <?php echo JText::_( 'VBIZZ_SUPPORT' );?></a></div>
<div class="hx_social hx_box">
<span class="facebook"><a href="https://www.facebook.com/wdmtechnologies" target="_blank"><img src="<?php echo JURI::root();?>administrator/components/com_vbizz/assets/images/facebook.png" alt="Facebook"></a></span>
<span class="twitter"><a href="http://www.twitter.com/wdmtechnologies" target="_blank"><img src="<?php echo JURI::root();?>administrator/components/com_vbizz/assets/images/twitter.png" alt="Twitter"></a></span>
<span class="google-plus"><a href="https://plus.google.com/+Wdmtechnologies" target="_blank"><img src="<?php echo JURI::root();?>administrator/components/com_vbizz/assets/images/google-plus.png" alt="Google Plus"></a></span>
<span class="linkedin"><a href="http://www.linkedin.com/company/wdmtech" target="_blank"><img src="<?php echo JURI::root();?>administrator/components/com_vbizz/assets/images/linkedin.png" alt="Linkedin"></a></span></div> 
</div>
</div> -->
 <!-- <a class="modal" id="modal1" title="Select" href="<?php echo JURI::root().'administrator/index.php?option=com_vbizz&view=vbizz&layout=edit&task=add&tmpl=component';?>" rel="{handler: 'iframe', size: {x: 800, y: 600}}">
            <span class="addNew btn btn-small btn-success hasTip" title="<?php echo JText::_('COM_VBIZZ_ADD_WIDGET'); ?>"><i class="icon-new icon-white"></i> <?php echo JText::_('COM_VBIZZ_ADD_WIDGET'); ?></span></a> -->
<ul id="sortable1" class="cp_blocks connectedSortable">

<!--Expense Chart Start -->   
<li class="cp_block expenses common_profile_main">
    <div class="chart_box">
        <div class="chart_x_tool">
            <h2><?php echo JText::_('EXPENSES'); ?></h2>
			<div class="chrt_rbtn">
            <div id="expense-export" class="btn btn-small btn-success hasTip" title="<?php echo JText::_('EXPORT_REPORT');?>"><i class="icon-export"></i> <?php echo JText::_('COM_VBIZZ_EXPORT_REPORT');?></div></div>
            <div class="expensecharttype select_type">
                
                <select name="duration" id="duration">
					<option value=""><?php echo JText::_('TOTAL'); ?></option>
					<option value="current_month"><?php echo JText::_('CURRENT_MONTH'); ?></option>
					<option value="current_year"><?php echo JText::_('CURRENT_YEAR'); ?></option>
                </select>
               
            </div>
        </div>
        <div id="expense_chart_div" class="profile_mid_data"></div>
    </div>
</li>
<!--Expense Chart End -->   
    
<!--Income Chart Start -->   
<li class="cp_block incomes common_profile_main">
    <div class="chart_box">
        <div class="chart_x_tool">
            <h2><?php echo JText::_('INCOMES'); ?></h2>
			<div class="chrt_rbtn">
            <div id="income-export" class="btn btn-small btn-success hasTip" title="<?php echo JText::_('EXPORT_REPORT');?>"><i class="icon-export"></i> <?php echo JText::_('COM_VBIZZ_EXPORT_REPORT');?></div></div>
            <div class="incomecharttype select_type">
                
                <select name="periods" id="periods">
					<option value=""><?php echo JText::_('TOTAL'); ?></option>
					<option value="current_month"><?php echo JText::_('CURRENT_MONTH'); ?></option>
					<option value="current_year"><?php echo JText::_('CURRENT_YEAR'); ?></option>
                </select>
                
            </div>
        </div>
        <div id="income_chart_div" class="profile_mid_data"></div>
    </div>
</li>
<!--Income Chart End -->

<!--Most Valued Customer Chart Start -->   
<li class="cp_block expenses common_profile_main">
    <div class="chart_box">
        <div class="chart_x_tool">
            <h2><?php echo JText::_('MOST_VALUED_CUSTOMERS'); ?></h2>
			<div class="chrt_rbtn">
            <div id="cust-export" class="btn btn-small btn-success hasTip" title="<?php echo JText::_('EXPORT_REPORT');?>"><i class="icon-export"></i> <?php echo JText::_('COM_VBIZZ_EXPORT_REPORT');?></div></div>
            
        </div>
        <div id="customer_chart_div" class="profile_mid_data"></div>
    </div>
</li>
<!--Most Valued Customer Chart End -->

<!--Most Valued Vendor Chart Start -->   
<li class="cp_block incomes common_profile_main">
    <div class="chart_box">
        <div class="chart_x_tool">
            <h2><?php echo JText::_('MOST_VALUED_VENDORS'); ?></h2>
			<div class="chrt_rbtn">
            <div id="vend-export" class="btn btn-small btn-success hasTip" title="<?php echo JText::_('EXPORT_REPORT');?>"><i class="icon-export"></i> <?php echo JText::_('COM_VBIZZ_EXPORT_REPORT');?></div></div>
            
        </div>
        <div id="vendor_chart_div" class="profile_mid_data"></div>
    </div>
</li>
<!--Most Valued Vendor Chart End -->

<!--Most Valued Customer Chart Start -->   
<li class="cp_block expenses common_profile_main">
    <div class="chart_box">
        <div class="chart_x_tool">
            <h2><?php echo JText::_('MOST_ADDED_ITEMS'); ?></h2>
			<div class="chrt_rbtn">
            <div id="item-export" class="btn btn-small btn-success hasTip" title="<?php echo JText::_('EXPORT_REPORT');?>"><i class="icon-export"></i> <?php echo JText::_('COM_VBIZZ_EXPORT_REPORT');?></div></div>
            <div class="itemcharttype select_type">
            
            	<select name="item_types" id="item_types">
                <option value="0"><?php echo JText::_('SELECT_TRANSACTION_TYPE'); ?></option>
                <?php	for($i=0;$i<count($this->types);$i++)	{	?>
                <option value="<?php echo $this->types[$i]->id; ?>">
                <?php echo JText::_($this->types[$i]->treename); ?>
                </option>
                <?php	}	?>
                </select>
                
            </div>
        </div>
        <div id="item_chart_div" class="profile_mid_data"></div>
    </div>
</li>
<!--Most Valued Customer Chart End -->

<!--Line Chart Start -->   
<li class="cp_block line_chart common_profile_main">
    <div class="chart_box">
        <div class="linecharttype chart_x_tool">
        	<h2><?php echo JText::_('LINE_CHART'); ?></h2>
			<div class="chrt_rbtn">
            <div id="line-export" class="btn btn-small btn-success hasTip" title="<?php echo JText::_('EXPORT_REPORT');?>"><i class="icon-export"></i> <?php echo JText::_('COM_VBIZZ_EXPORT_REPORT');?></div>
			</div>
        	<div class="tab_btns"><span class="btn" data-type="day"><?php echo JText::_('DAILY'); ?></span><span class="btn" data-type="week"><?php echo JText::_('WEEKLY');?></span><span class="active btn" data-type="month"><?php echo JText::_('MONTHLY'); ?></span> <span class="btn" data-type="year"><?php echo JText::_('YEARLY');?></span></div>
        
            <div class="select_type">
                <select name="mode" id="mode">
                <option value="0"><?php echo JText::_('SELECT_MODE'); ?></option>
                <?php	for($i=0;$i<count($this->modes);$i++)	{	?>
                <option value="<?php echo $this->modes[$i]->id; ?>">
                <?php echo JText::_($this->modes[$i]->title); ?>
                </option>
                <?php	}	?>
                </select>
                
                
                <select name="types" id="types">
                <option value="0"><?php echo JText::_('SELECT_TRANSACTION_TYPE'); ?></option>
                <?php	for($i=0;$i<count($this->types);$i++)	{	?>
                <option value="<?php echo $this->types[$i]->id; ?>">
                <?php echo JText::_($this->types[$i]->treename); ?>
                </option>
                <?php	}	?>
                </select>
                
            </div>
        </div>
        <div id="line_chart_div" class="profile_mid_data"></div>
    </div>
</li>
<!--Line Chart End -->   
    
<!--Column Chart Start -->   
<li class="cp_block column_chart common_profile_main">
    <div class="chart_box">
    	<div class="columncharttype chart_x_tool">
    		<h2><?php echo JText::_('COLUMN_CHART'); ?></h2>
			<div class="chrt_rbtn">
            <div id="column-export" class="btn btn-small btn-success hasTip" title="<?php echo JText::_('EXPORT_REPORT');?>"><i class="icon-export"></i> <?php echo JText::_('COM_VBIZZ_EXPORT_REPORT');?></div></div>
        	<div class="tab_btns"><span class="btn" data-type="day"><?php echo JText::_('DAILY'); ?></span> <span class="btn" data-type="week"><?php echo JText::_('WEEKLY'); ?></span><span class="active btn" data-type="month"><?php echo JText::_('MONTHLY'); ?></span> <span class="btn" data-type="year"><?php echo JText::_('YEARLY');?></span></div>
            
        </div>
    
    	<div id="transaction_chart_div" class="profile_mid_data"></div>
    </div>
</li>
<!--Column Chart End -->   

<!--Income Bar Chart Start -->   
<li class="cp_block income_bar_chart common_profile_main">
    <div class="chart_box">
        <div class="incomebarcharttype chart_x_tool">
            <h2><?php echo JText::_('INCOME_BUDGET_STATS'); ?></h2>
			<div class="chrt_rbtn">
            <div id="income-budget-export" class="btn btn-small btn-success hasTip" title="<?php echo JText::_('EXPORT_REPORT');?>"><i class="icon-export"></i> <?php echo JText::_('COM_VBIZZ_EXPORT_REPORT');?></div></div>
            <div class="tab_btns"><span class="btn" data-type="week"><?php echo JText::_('WEEKLY');?></span><span class="active btn" data-type="month"><?php echo JText::_('MONTHLY');?></span><span class="btn" data-type="quater"><?php echo JText::_('QUATERLY');?></span><span class="btn" data-type="year"><?php echo JText::_('YEARLY');?></span></div>
            
        </div>
        <div id="income_bar_chart_div" class="profile_mid_data"></div>
    </div>
</li>
<!--Income Bar Chart End -->

<!--Expense Bar Chart Start -->   
<li class="cp_block expense_bar_chart common_profile_main">
    <div class="chart_box">
        <div class="expensebarcharttype chart_x_tool">
            <h2><?php echo JText::_('EXPENSE_BUDGET_STATS'); ?></h2>
			<div class="chrt_rbtn">
            <div id="expense-budget-export" class="btn btn-small btn-success hasTip" title="<?php echo JText::_('EXPORT_REPORT');?>"><i class="icon-export"></i> <?php echo JText::_('COM_VBIZZ_EXPORT_REPORT');?></div></div>
            <div class="tab_btns"><span class="btn" data-type="week"><?php echo JText::_('WEEKLY'); ?></span><span class="active btn" data-type="month"><?php echo JText::_('MONTHLY'); ?></span><span class="btn" data-type="quater"><?php echo JText::_('QUATERLY'); ?></span><span class="btn" data-type="year"><?php echo JText::_('YEARLY');?></span></div>
            
        </div>
        <div id="expense_bar_chart_div" class="profile_mid_data"></div>
    </div>
</li>
<!--Expense Bar Chart End -->   
  
</ul>
</div>
</div>