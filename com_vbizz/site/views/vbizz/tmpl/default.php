<?php
/*------------------------------------------------------------------------
# com_vbizz - vBizz
# ------------------------------------------------------------------------
# author  Zaheer Abbas
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.wdmtech.com
# Technical Support:  Forum - https://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.formvalidation'); 
JHTML::_('behavior.modal');
JHtml::_('behavior.colorpicker');   
JHtml::_('formbehavior.chosen', 'select');
require_once JPATH_COMPONENT.'/operating/drawchart.php';
$document =  JFactory::getDocument();
$document->addScript("https://www.google.com/jsapi");
  $mem_usage = memory_get_usage(true); 
  $mem_usage = round($mem_usage/1024,2);  
  
$user = JFactory::getUser();
/* $db = JFactory::getDbo();  
$query = 'ALTER TABLE `#__vbizz_config` ADD `widget_acl` VARCHAR( 255 ) NOT NULL';
$db->setQuery($query);
$db->execute();
$query = 'ALTER TABLE `#__vbizz_configuration` ADD `widget_acl` VARCHAR( 255 ) NOT NULL';
$db->setQuery($query);
$db->execute();
jexit('test'); */    

 ?>

<script type="text/javascript">
var infor_memory_response=<?php echo  $mem_usage; ?>;
var infor_memory_response_text = <?php echo  $mem_usage; ?>;
var infor_cpu_response=0;
var infor_response_time=0;
var infor_cpu_name = '';
var server_cpu_load = '';
var thread_connected = 0;
var thread_running = 0;
var source;
var check_status = '';
var status_action = '';
var status_index = 0;

function eventsourcing()
{
	if(typeof(EventSource) !== "undefined") {

		source = new EventSource("<?php echo JURI::root(). "index.php?option=com_vbizz&view=vbizz&task=live_chart_data_test";?>");

		source.onmessage = function(event) {
           
			var res = jQuery.parseJSON(event.data); //alert(res);
            var single_value_display = res[0];//alert(single_value_display);
	  jQuery('.innser_single_trigger').each(function(index, value) {
      var st_text =	'';	 //alert(index);  
	  st_text = single_value_display[index];
	  jQuery(this).html(single_value_display[index]);
	  
	  });
		var listing_info_display = res[1]; //alert(listing_info_display);
	 
	  jQuery('.common_class_listing_format_new').each(function(index, value) { 
	 if(jQuery(this).hasClass('noupdate'))
		 return;  
	  if(jQuery(this).is('ul')){
		 jQuery('ul.'+jQuery(this).attr('id')).prepend(listing_info_display[index]);    
	  }
	  else if(jQuery(this).is('table')){
       jQuery(this).find('tbody').empty().append(listing_info_display[index]);
	  //alert(12);
		/* jQuery('table.'+jQuery(this).attr('id')+' tr').slice(1).remove();//alert(13);
	    jQuery('table.'+jQuery(this).attr('id')+' tbody').prepend(listing_info_display[index]);  */ 
	  }
	   else if(jQuery(this).is('div')){
		 jQuery('div.'+jQuery(this).attr('id')+' tbody').prepend(listing_info_display[index]);      
	   }
	  
	  
	  });		
		
		};
	} 
}

function livechart()
{
	var start_time = new Date().getTime();
	jQuery.getJSON( "<?php echo JRoute::_("index.php?option=com_vbizz&view=vbizz&task=live_chart_data",false);?>", function(data){

		var end_time = new Date().getTime();
		var request_time = end_time - start_time;
		infor_response_time = request_time;

		var json = jQuery.parseJSON(data.json);
		//data.info_memory;
		infor_memory_response = json.Memory.attributes.Percent;
		infor_memory_response_text = '%';
		infor_cpu_name = json.Hardware.CPU.CpuCore[0].attributes.Model; 
		infor_cpu_response = Math.round(json.Vitals.attributes.CPULoad);
		var memory_status = formate_check(json.Memory.attributes.Percent);//data.info_memory;
		infor_memory_response = Math.round(memory_status[0],1);
		infor_memory_response_text = "%";
	});
}


function formate_check(bytes){
	var show='';var show_text='';
	if (bytes > Math.pow(1024, 5)) {
		show += Math.round(bytes / Math.pow(1024, 5), 2);
		show_text += "<?php echo JText::_('PiB');?>";
	}
	else {
		if (bytes > Math.pow(1024, 4)) {
			show += Math.round(bytes / Math.pow(1024, 4), 2);
			show_text += "<?php echo JText::_('TiB');?>";
		}
		else {
			if (bytes > Math.pow(1024, 3)) {
				show += Math.round(bytes / Math.pow(1024, 3), 2);
				show_text += "<?php echo JText::_('GiB');?>";
			}
			else {
				if (bytes > Math.pow(1024, 2)) {
					show += Math.round(bytes / Math.pow(1024, 2), 2);
					show_text += "<?php echo JText::_('MiB');?>";
				}
				else {
					if (bytes > Math.pow(1024, 1)) {
						show += Math.round(bytes / Math.pow(1024, 1), 2);
						show_text += "<?php echo JText::_('KiB');?>";
					}
					else {
						show += bytes;
						show_text += "<?php echo JText::_('B');?>";
					}
				}
			}
		}
	}
	var show_array = new Array();
	show_array[0] =show; 
	show_array[1] =show_text; 
	return show_array;
}

jQuery(document).ready(function(){ 
	status_index = parseInt(jQuery('ul.connectedsortable > li').length)+1;
	var timer = '';
	// jQuery( ".dragable" ).draggable({containment: "parent"});
	eventsourcing();
	<?php if(VaccountHelper::checkOwnerGroup()) { ?>
	jQuery( "#sortable1, #sortable2" ).sortable({
		connectWith: ".connectedsortable",
		containment: "parent",
		cancel : '.widget_chart, .profile_mid_data_inner, .listing_layout_others',
		start: function(event, ui ){ 
		jQuery('li.ui-sortable-placeholder').css("width", (ui.item.width()-1)+"px"); 
			clearInterval(timer);
		if(typeof(EventSource) !== "undefined") {   
			if(source !== "undefined") { 
			source.close();}
		}},
		stop: function( event, ui ) {
		ui.item.width(ui.item.width+1);	
			timer = setTimeout(function() {
				var ordering = new Array();
				jQuery('.common_profile_main').each(function(index, value) { 
					ordering.push(jQuery(this).attr('data-ordering-profile'));	 
				});
				jQuery.ajax({

					url: "index.php",
					type: "POST",
					dataType: "json",
					data: {'option':'com_vbizz', 'view':'vbizz', 'task':'update_profile_ordering', 'new_ordering':ordering, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1, 'tmpl':'component'},
					success: function(res)	{
						eventsourcing();	
					},
					error: function(jqXHR, textStatus, errorThrown)	{
						alert(textStatus);				  
					}

				});
			},3000);
		}
	}).disableSelection();
	<?php } ?>


	SqueezeBox.loadModal = function(modalUrl,handler,x,y) {
		this.presets.size.x = 1024;
		this.initialize();      
		var options = { handler: 'iframe', size: {x: "100%", y: "100%"} };      
		this.setOptions(this.presets, options);
		this.assignOptions();
		this.setContent(handler,modalUrl);
	}; 
jQuery('.linecharttypes').on('click', '.btn', function()	{
	   if(jQuery(this).hasClass('select_type'))
		   return;
		 
		if(jQuery(this).hasClass('active'))
			return;
		else	{    
			jQuery(this).parents('.common_profile_main').find('.linecharttypes>span').removeClass('active');
			jQuery(this).addClass('active');
			
			drawChart(jQuery(this).parents('.common_profile_main'));
		}
	});
jQuery('.linecharttypeselect').on('change', 'select[name="transection_type"]', function()	{
		drawChart(jQuery(this).parents('.common_profile_main'));
	});
});
function drawChart(chartidss) 
	{   
	    chartid = chartidss.attr('data-options');
		var type = chartidss.find('.linecharttypes>span.active').data('type');
		var transection_type = chartidss.find('select[name="transection_type"]').val();
		 var option_list = chartid.split(":");
		 chartid = option_list[0];
		 var chart_type = check_chart_type(option_list[1], chartid);
         
		jQuery.ajax(
		{
			url: "index.php",
			type: "POST",
			dataType:"json",
			data: {'option':'com_vbizz', 'view':'vbizz', 'task':'drawChart', 'tmpl':'component', 'type':type, 'transection_type':transection_type, 'formate':option_list[2]},
			beforeSend: function()	{
				jQuery(".loadingblock").show();
			},
			complete: function()	{
				jQuery(".loadingblock").hide();  
			},
			success: function(data)	
			{ 
				if(data.result == "success")
				{
					var playedquiz=data.playedquiz;
					
                    if(option_list[2]=='listing_formate')
					{ 
				    chartidss.find('table.listing_info').html(playedquiz);
						
					}  
					if(option_list[2]=='charting_formate'){
					       var update_data = new Array();
							update_data[0] = [String(playedquiz[0][0]),String(playedquiz[0][1]),String(playedquiz[0][2])];

							for(var j=1;j<playedquiz.length;j++)	{

							update_data[j] = new Array();
							update_data[j][0] = String(playedquiz[j][0]);
							update_data[j][1] = Number(playedquiz[j][1]);
							update_data[j][2] = Number(playedquiz[j][2]);
							}
							var update_data = google.visualization.arrayToDataTable(update_data);
							//view code for animation
							var view = new google.visualization.DataView(update_data);
							view.setColumns([0, {
								type: 'number',
								label: update_data.getColumnLabel(1),
								calc: function () {return 0;}
							}]);

							var options = data.options;

							var chart = check_chart_type(option_list[1], chartid);
							function errorHandler(errorMessage) {
							console.log(errorMessage);
							google.visualization.errors.removeError(errorMessage.id);
							}
							google.visualization.events.addListener(chart, 'error', errorHandler);
							chart.draw(update_data, options);
							if(playedquiz==''){
							var ht='<?php echo JText::_('COM_VBIZZ_NODATA');?>';
							jQuery('#'+chartid).html(ht);
							}
                          }

						}

				}

		});

    }
	function check_chart_type(chartname,chartids){
		if(chartname=='Line Chart')
			 return new google.visualization.LineChart(document.getElementById(chartids));
			else if(chartname=='Area Chart')
				return new google.visualization.LineChart(document.getElementById(chartids));
				else if(chartname=='Stepped AreaChart')
					return new google.visualization.SteppedAreaChart(document.getElementById(chartids));
					else if(chartname=='Column Chart')
						return new google.visualization.ColumnChart(document.getElementById(chartids));
						else if(chartname=='Bar Chart')
							return new google.visualization.BarChart(document.getElementById(chartids));
							else if(chartname=='Geo Chart')
								return new google.visualization.GeoChart(document.getElementById(chartids));
								else if(chartname=='Table Chart')
									return new google.visualization.TableChart(document.getElementById(chartids));
									else if(chartname=='Pie Chart')
										return new google.visualization.PieChart(document.getElementById(chartids));
	}
function insert()
{
	jQuery.ajax({
		url: "index.php",
		type: "POST",
		dataType: "json",
		data: {'option':'com_vbizz', 'view':'vbizz', 'task':'check_login_status', "<?php echo JSession::getFormToken(); ?>":1, 'abase':1, 'tmpl':'component'},

		beforeSend: function()	{ 
			jQuery('.loading').show();	
		},
		complete: function()	{
			jQuery('.loading').hide();	
		},
		success: function(res)	{
			if(res.result == "success"){
				if(res.state){
					var url = "<?php echo JRoute::_("index.php?option=com_vbizz&view=widget&tmpl=component",false);?>";
					SqueezeBox.loadModal(url,"iframe",'95%','95%');

				}
				if(!res.state){
					window.location = '<?php echo JRoute::_('index.php?option=com_users&view=login',false);?>';
				}
			}
		},
		error: function(jqXHR, textStatus, errorThrown)	{
			alert(textStatus);
			SqueezeBox.close();
			window.location = '<?php echo JRoute::_('index.php?option=com_users&view=login');?>';
		}
	});
}

SqueezeBox.initialize({
	onOpen:function(){
		jQuery( "#system-message-container" ).html('');
		jQuery("html, body").animate({scrollTop : 0}, "slow");
	},
	onClose: function() {
		jQuery('#system-message-container').html(''); 
		if(status_index<parseInt(jQuery('ul.connectedsortable > li').length)+1){
			jQuery('html,body').animate({ scrollTop: jQuery('ul.connectedsortable li:nth-child('+(parseInt(status_index)+1)+'n)').offset().top-170}, 2000);	
		}
		else if(check_status=='action_saved')
			jQuery('html,body').animate({ scrollTop: jQuery('ul.connectedsortable li:nth-child('+parseInt(jQuery('ul.connectedsortable > li').length)+'n)').offset().top}, 2000); 

		if(check_status=='action_saved'){
			jQuery.ajax({        
				url: "index.php",
				type: "POST",
				dataType: "json",
				data: {'option':'com_vbizz', 'view':'vbizz', 'task':'update_dashboard_new','status_index':status_index,'status_action':status_action, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1, 'tmpl':'component'},

				beforeSend: function()	{
					jQuery('ul.connectedsortable li:nth-child('+(parseInt(status_index)+1)+')').children(':first').before('<div class="vbizz_overlay" style="display:block;"><img alt="" src="<?php echo JURI::root();?>media/com_vbizz/images/loading_second.gif" class="vbizz-loading"></div>');
				},
				complete: function()	{
				},
				success: function(res)	{
					if(res.result == "success"){

						if(status_index<parseInt(jQuery('ul.connectedsortable > li').length)+1){
							jQuery('ul.connectedsortable').children('li:eq('+(parseInt(status_index))+')').html('');
							var style = res.style.split(":");
							
							jQuery('ul.connectedsortable').children('li:eq('+(parseInt(status_index))+')').width( style[0]).height(style[1]);
							
							jQuery('ul.connectedsortable').children('li:eq('+(parseInt(status_index))+')').html(res.html+res.script);
						}
						else{
							jQuery('ul.connectedsortable').append(res.li+res.html+res.script+'</li>');
						}
						var status_text_action = 'drawchart'+status_action;

						jQuery('.widget_chart').each(function(index,value){
							if(status_text_action==jQuery(this).attr('data-profile-id')){
								eval(status_text_action+"()");
							}
						}); 

					}
					else
					alert(res.error);

				},
				error: function(jqXHR, textStatus, errorThrown)	{
					alert(textStatus);				  
				}
			});
		}
	}
});
  
function edit_widget(e){
	status_action = jQuery(e).attr('data-widget-id');
	status_index = jQuery(e).parents('li').index();
	var url = "<?php echo JURI::root().'index.php?option=com_vbizz&view=widget&tmpl=component';?>&cid[]="+jQuery(e).attr('data-widget-id'); 
	SqueezeBox.loadModal(url,"iframe",'95%','95%');
}

function delete_widget(e){
	var to_delete_li = jQuery(e).parents('li');
	
	status_action = jQuery(e).attr('data-widget-id');
	
	var to_delete_li_loading = jQuery(e).parents('li :first-child');
	
	if(jQuery(e).attr('data-widget-id')==''){
		alert("<?php echo Jtext::_('COM_VBIZZ_WIDGET_PLEASE_SELECT');?>");
		return;
	}	else {
		var tester = confirm("<?php echo Jtext::_('COM_VBIZZ_WIDGET_WANT_TO_DELETE');?> !");
	}
	if(tester==true){
		jQuery.ajax({
			url: "index.php",
			type: "POST",
			dataType: "json",
			data: {'option':'com_vbizz', 'view':'vbizz', 'task':'delete_widget','id':jQuery(e).attr('data-widget-id'), "<?php echo JSession::getFormToken(); ?>":1, 'abase':1, 'tmpl':'component'},

			beforeSend: function()	{
				to_delete_li_loading.before('<div class="vbizz_overlay" style="display:block;"><img alt="" src="<?php echo JURI::root();?>media/com_vbizz/images/loading_second.gif" class="vbizz-loading"></div>');
			},
			complete: function()	{
				to_delete_li.find('div:first').remove(); 
			},
			success: function(res)	{
				if(res.result == "success"){
					to_delete_li.remove();
				}
				else
					alert(res.error);
			},
			error: function(jqXHR, textStatus, errorThrown)	{
				alert(textStatus);				  
			}	
		});	
	}

}

</script>

<style>
#sbox-window {
	left: 5% !important;
    padding: 1.5% !important;
    top: 3% !important;
    width: 87% !important;
}
</style>
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('DASHBOARD'); ?></h1>
		<div class="hx_dash_button">
<?php if(VaccountHelper::checkOwnerGroup()) { ?>  

		<a href="javascript:void(0);" class="btn btn-small btn-success" onclick="insert();">
		   <span class="fa fa-plus"></span> <?php echo JText::_( 'COM_VBIZZ_ADD_WIDGET' );?>
		</a>
		<?php if(VaccountHelper::checkOwnerGroup()) { ?> 
			<a href="javascript:void(0);" class="btn btn-small" onclick="Joomla.submitbutton('checkin');">
			<span class="fa fa-check"></span> <?php echo JText::_( 'COM_VBIZZ_CHECKED_IN' );?>
			</a>		
		<?php } ?>
<?php } ?>
</div>
	</div>
</header>
<div class="content_part dashboard_page">  
<div id="vbizzpanel">
<div class="vbizz_overlay" style="display:none;"> 
<img class="vbizz-loading" src="<?php echo JURI::root();?>/media/com_vbizz/images/loading_second.gif" alt="">
</div>
<form action="<?php echo JRoute::_("index.php?option=com_vbizz&view=vbizz");?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="clr">
</div>
<div class="clr" style="clear:both;">
</div>

<div class="profile-section">
<ul id="sortable1" class="connectedsortable">
<?php  
    $k = 0;
	$lang = JFactory::getLanguage();
	$prev_current_width ='';
	$future_width ='';
	$live_data_query = array('Server Response Monitoring','Server CPU Monitoring','Server Monitoring','Thread Status','Queries Status');
	$row_siz = 150;
	$one_column_size = 100/12;
	$groups = $user->getAuthorisedGroups();
	//echo '<pre>';print_r($this->profiles); jexit();
	
	for($j=0;$j<count($this->profiles);$j++){
		$profile = json_decode($this->profiles[$j]->detail);
			$tran_registry = new JRegistry;
			$tran_registry->loadString($this->profiles[$j]->access);
			$allow_access = $tran_registry->get('access_interface'); 
			
			$allow_check = false;
			if(is_array($allow_access) && count($allow_access)>0)
			{  
				foreach($groups as $group)
				{
					if(in_array($group,$allow_access))
					{
					$allow_check=true;
					
					}
				}
			}
			elseif(!empty($allow_access))
			{
				if(in_array($allow_access, $groups))
				{
				$allow_check=true;    

				}	
			}
			 if(!$allow_check)
			continue;  
		$current_width ='';
		$sub_current_width =0;$box_class_name='';
		if(isset($profile->box_layout) && $profile->box_layout=="onebox"){$sub_current_width=1;$box_class_name=' onebox';}
		elseif(isset($profile->box_layout) && $profile->box_layout=="twobox"){$sub_current_width=2;$box_class_name=' twobox';}
		elseif(isset($profile->box_layout) && $profile->box_layout=="threebox"){$sub_current_width=3;$box_class_name=' threebox';}
		elseif(isset($profile->box_layout) && $profile->box_layout=="fourbox"){$sub_current_width=4;$box_class_name=' fourbox';}
		elseif(isset($profile->box_layout) && $profile->box_layout=="fivebox"){$sub_current_width=5;$box_class_name=' fivebox';}
		elseif(isset($profile->box_layout) && $profile->box_layout=="sixbox"){$sub_current_width=5;$box_class_name=' sixbox';}
			
			$column_widget_width_value = '';
		    $row_widget_height_value = '';
		    $row_widget_height_value_chart = '';
		if(isset($profile->box_column) && $profile->box_column)
		{
		 $column_widget_width_value = (($profile->box_column*$one_column_size)-2).'%';	
		}
		
	    if(isset($profile->box_row) && $profile->box_row)
		{
		$row_widget_height_value =	(($profile->box_row*$row_siz)-20).'px';
		$row_widget_height_value_chart = (($profile->box_row*$row_siz)-20);
		}
			
			if(empty($prev_current_width))
			{
			$prev_current_width = $current_width;	
			}
			$current_width ='';   
	  
	  if(isset($profile->style_layout) && $profile->style_layout=='single_formate')
	  { ?>
	  <li class="common_profile_main single_formate num<?php echo ($j+1);if($this->profiles[$j]->datatype_option=='profile'){echo ' profile_widget';} echo $box_class_name;?>" data-ordering-profile="<?php echo $this->profiles[$j]->id.':'.$this->profiles[$j]->ordering; ?>" style="<?php if($column_widget_width_value!='' && $row_widget_height_value!='') 
		  echo 'width:'.$column_widget_width_value.';height:'.$row_widget_height_value.';';?>">
		
		<div class="panel_header">
		<?php if(isset($this->profiles[$j]->name)&& $this->profiles[$j]->name!='')
		echo '<span class="profile_name">'.$this->profiles[$j]->name.'</span>';?>
	    <?php if(VaccountHelper::WidgetAccess('widget_acl', 'deleteaccess')) { ?>
		<span class="delete-widget" onclick="delete_widget(this);" data-widget-id="<?php echo $this->profiles[$j]->id;?>"><i class="fa fa-remove"></i></span>
		<?php } ?>
		<?php if(VaccountHelper::WidgetAccess('widget_acl', 'editaccess')) { ?>
		<span class="edit-widget" onclick="edit_widget(this);" data-widget-id="<?php echo $this->profiles[$j]->id;?>"><i class="fa fa-edit"></i></span>
		<?php } ?>
		</div>
		
		<div class="profile_mid_data">
	  <?php 
		
		  $single_data = vchart::widgetvalue($profile);
		  //echo'<pre>'; print_r($single_data);
		  
		if(isset($single_data->result)&&$single_data->result=='error'){
			  echo $single_data->error;
		  }
		  else
		  {
		  $single_data = isset($single_data->data)?$single_data->data:array();
		  
		
		  $test_v = isset($profile->style_layout_editor)?$profile->style_layout_editor:'';
		    $regex		= '/{(.*?)}/i';
		    preg_match_all($regex, $profile->style_layout_editor, $matches, PREG_SET_ORDER);
			//print_r($matches);
			 foreach ($matches as $match)
			{
			foreach ($single_data as $key=>$value)
			{
				if($value=='') $value=0;   
			  
			     $value = (float)$value>0?VaccountHelper::getNumberFormatValue($value):$value;
				 if(isset($profile->style_layout_editor) && $profile->style_layout_editor!=''&&$value!=null&&$match[1]==$key)
				 {	
				$test_v = preg_replace("|$match[0]|", '<span class="innser_single_trigger" data-profile-ids="profile_'.$this->profiles[$j]->id.'" id="inner_single_'.trim(preg_replace('/\s*\([^)]*\)/', '', $key)).'">'.$value.'</span>', $test_v, 1);
	 
				$test_v = str_replace('{cur}', $this->config->currency, $test_v);
				}
				elseif(isset($profile->style_layout_editor) && $profile->style_layout_editor!=''&&$value==null)
				 {	
				$test_v = preg_replace("|$match[0]|", '<span class="innser_single_trigger" data-profile-ids="profile_'.$this->profiles[$j]->id.'" id="inner_single_'.trim(preg_replace('/\s*\([^)]*\)/', '', $key)).'">'.$value.'</span>', $test_v, 1);
				
				$test_v = str_replace('{cur}', $this->config->currency, $test_v);
			
				}
            }
		  }
			
           echo $test_v;
		  }
		 ?>
		 </div>
		  </li>
		  <?php
	  }
	  elseif(isset($profile->style_layout) && $profile->style_layout=='listing_formate')
	  {
	  
	  ?>
	   <li class="common_profile_main listing_formate num<?php echo ($j+1);if($this->profiles[$j]->datatype_option=='profile'){echo ' profile_widget';}echo $box_class_name; ?>" data-ordering-profile="<?php echo $this->profiles[$j]->id.':'.$this->profiles[$j]->ordering; ?>" style="<?php if($column_widget_width_value!='' && $row_widget_height_value!='') 
		  echo 'width:'.$column_widget_width_value.';height:'.$row_widget_height_value.';';?>" data-options="widget_<?php echo $this->profiles[$j]->id.':'.$this->profiles[$j]->chart_type.':'.$profile->style_layout;?>">
		<div class="panel_header">
		<?php if($profile->existing_database_table==JText::_('COM_VBIZZ_OVER_ALL_INCOME_EXPENSE_OWNER')) { ?>
		<span class="profile_name"> 
		<span class="" style="float:left;width: 40%">
		   <?php if(isset($this->profiles[$j]->name)&& $this->profiles[$j]->name!='')
			echo $this->profiles[$j]->name;
			$column_widget_width_value = '';
			$row_widget_height_value = '';  
			
			if(isset($profile->box_column) && $profile->box_column)
			{
			 $column_widget_width_value = (($profile->box_column*$one_column_size)-2).'%';	
			}
			
			if(isset($profile->box_row) && $profile->box_row)
			{
			$row_widget_height_value =	(($profile->box_row*$row_siz)-80).'px';
			
			}?></span>
			    <span class="linecharttypes" style="width:60%;">
	            
		          <span class="btn" data-type="day"><?php echo JText::_('COM_VIBIZZ_DAILY'); ?></span> 
                  <span class="btn" data-type="week"><?php echo JText::_('COM_VIBIZZ_WEEKLY'); ?></span> 
                  <span class="active btn" data-type="month"><?php echo JText::_('COM_VIBIZZ_MONTHLY'); ?></span> 
                  <span class="btn" data-type="year"><?php echo JText::_('COM_VIBIZZ_YEARLY'); ?></span> 
                  <span class="select_type linecharttypeselect" style="width:35%; float:left;">
                  <?php echo $this->transection;?></span>
				</span>
				
		</span>  
		<?php } else {
		echo '<span class="profile_name">';
		if(isset($this->profiles[$j]->name)&& $this->profiles[$j]->name!='')
		 echo $this->profiles[$j]->name;
	     echo '</span>';
		 } ?>
		<?php if(VaccountHelper::WidgetAccess('widget_acl', 'deleteaccess')) { ?> 
		<span class="delete-widget" onclick="delete_widget(this);" data-widget-id="<?php echo $this->profiles[$j]->id;?>"><i class="fa fa-remove"></i></span>
		<?php } ?>
		<?php if(VaccountHelper::WidgetAccess('widget_acl', 'editaccess')) { ?>
		<span class="edit-widget" onclick="edit_widget(this);" data-widget-id="<?php echo $this->profiles[$j]->id;?>"><i class="fa fa-edit"></i></span>
		<?php } ?>
		</div>
		<div class="profile_mid_data common_class_listing_format">
	  <?php 
		  
		  $single_data = vchart::widgetvalue($profile);
		   if(isset($single_data->result)&&$single_data->result=='error'){ 
			  echo $single_data->error;
		  }
		  else
		  {
	      $single_data = $single_data->data;     
		  $object_array = isset($single_data[0])?get_object_vars($single_data[0]):array();
		  $object_array = count($object_array)>0?array_keys($object_array):array();
		  echo '<div class="listing_layout_others" style="height:'.($row_widget_height_value_chart-75).'px;">';
		   echo '<table class="common_class_listing_format_new adminlist table table-hover listing_info listing_info_'.$this->profiles[$j]->id.($profile->existing_database_table==JText::_('COM_VBIZZ_OVER_ALL_INCOME_EXPENSE_OWNER')?' noupdate':'').'" width="100%"><thead><tr>';
			for ($s = 0;$s<count($object_array);$s++) 
			{
			    
				  echo '<th>'.$object_array[$s].'</th>';
				
			
            }			
           echo '</thead>';  
		   $g = 0;
			for ($l=0; $l < count( $single_data ); $l++)
			{
				$listing = $single_data[$l];
				echo '<tr>';
				foreach($listing as $listings){
				if(VaccountHelper::getValidateDate($listings))	
				echo '<td>'.VaccountHelper::getDate($listings).'</td>';	
				 else			
				echo '<td>'.((float)$listings>0?VaccountHelper::getNumberFormatValue((float)$listings):$listings).'</td>';	
				}
			echo '</tr>';    	
			}
		  echo '</table>';
		  echo '</div>';
		  }
		 ?>
		 </div>
		  </li>
		<?php 
       	
	  }
	 else
	 { 
			
		?>
	<li class="common_profile_main num<?php echo ($j+1);if($this->profiles[$j]->datatype_option=='profile'){echo ' profile_widget';}echo $box_class_name; ?>" data-ordering-profile="<?php echo $this->profiles[$j]->id.':'.$this->profiles[$j]->ordering; ?>" style="<?php if($column_widget_width_value!='' && $row_widget_height_value!='') 
		  echo 'width:'.$column_widget_width_value.';height:'.$row_widget_height_value.';';?>" data-options="widget_<?php echo $this->profiles[$j]->id.':'.$this->profiles[$j]->chart_type.':'.$profile->style_layout;?>">
	<div class="panel_header">
	
	<?php if($profile->existing_database_table==JText::_('COM_VBIZZ_OVER_ALL_INCOME_EXPENSE_OWNER')) { ?>
		<span class="profile_name">
		<span class="" style="float:left;width: 40%">
		   <?php if(isset($this->profiles[$j]->name)&& $this->profiles[$j]->name!='')
			echo $this->profiles[$j]->name;
			$column_widget_width_value = '';
			$row_widget_height_value = '';  
			
			if(isset($profile->box_column) && $profile->box_column)
			{
			 $column_widget_width_value = (($profile->box_column*$one_column_size)-2).'%';	
			}
			
			if(isset($profile->box_row) && $profile->box_row)
			{
			$row_widget_height_value =	(($profile->box_row*$row_siz)-80).'px';
			
			}?></span>
			    <span class="linecharttypes" style="width:60%;">
	            
		          <span class="btn" data-type="day" style="width:15%;"><?php echo JText::_('COM_VIBIZZ_DAILY'); ?></span> 
                  <span class="btn" data-type="week" style="width:15%;"><?php echo JText::_('COM_VIBIZZ_WEEKLY'); ?></span> 
                  <span class="active btn" data-type="month" style="width:15%;"><?php echo JText::_('COM_VIBIZZ_MONTHLY'); ?></span> 
                  <span class="btn" data-type="year" style="width:15%;"><?php echo JText::_('COM_VIBIZZ_YEARLY'); ?></span>
                  <span class="select_type linecharttypeselect">
                  <?php echo $this->transection;?></span>    
				</span>
			   </span>	
		   
		<?php } else { 
	echo '<span class="profile_name">';
	if(isset($this->profiles[$j]->name)&& $this->profiles[$j]->name!='')
		echo $this->profiles[$j]->name.'';
	echo '</span>';
		}
	?>
	<?php if(VaccountHelper::WidgetAccess('widget_acl', 'deleteaccess')) { ?> 
	<span class="delete-widget" onclick="delete_widget(this);" data-widget-id="<?php echo $this->profiles[$j]->id;?>"><i class="fa fa-remove"></i></span>
	<?php } ?>
	<?php if(VaccountHelper::WidgetAccess('widget_acl', 'editaccess')) { ?> 
	<span class="edit-widget" onclick="edit_widget(this);" data-widget-id="<?php echo $this->profiles[$j]->id;?>"><i class="fa fa-edit"></i></span>
	<?php } ?>
	</div>
	<div class="profile_mid_data">
	
	<ul>
	
	<?php
	if($this->profiles[$j]->datatype_option=='predefined')
   {  
    
	 $style= $row_widget_height_value_chart!=''?' style="height:'.($row_widget_height_value_chart-73).'px"':'';
	  if(isset($this->profiles[$j]->chart_type) && $this->profiles[$j]->chart_type!=''){
	      
			 $script_data = vchart::draw_view_chart($this->profiles[$j]);
			 if($script_data->result=='success'){ 
			  echo ' <li class="chart_profile num'.($j+1).'" data-ordering-profile="'.$this->profiles[$j]->id.':'.$this->profiles[$j]->ordering.'"'.$style.'>';
			 echo ' <div id="widget_'.$this->profiles[$j]->id.'" class="widget_chart" data-profile-id="drawchart'.$this->profiles[$j]->id.'" '.$style.'></div></li>';
			
			 echo '<script type="text/javascript"> ';
			 echo $script_data->scripts;
			 echo '</script>';
			 
			 
			  } 
			  elseif($script_data->result=='error'){  
				 $script_data->error; 
			  }
          }
		 
	  else
		{ 
		  echo ' <li class="chart_profile num'.($j+1).'" data-ordering-profile="'.$this->profiles[$j]->id.':'.$this->profiles[$j]->ordering.'"'.$style.'>'.$this->log_information($this->profiles[$j],$response_data).'</li>';
		}	  
      
   }
   elseif($this->profiles[$j]->datatype_option=='writequery')
   {
		$style= $row_widget_height_value_chart!=''?' style="height:'.($row_widget_height_value_chart-73).'px"':'';
		$script_data = vchart::draw_view_chart($this->profiles[$j]);
			if($script_data->result=='success')
			{ 
				echo ' <li class="chart_profile num'.($j+1).'" data-ordering-profile="'.$this->profiles[$j]->id.':'.$this->profiles[$j]->ordering.'" '.$style.'>';
				echo ' <div id="widget_'.$this->profiles[$j]->id.'" class="widget_chart" data-profile-id="drawchart'.$this->profiles[$j]->id.'"  '.$style.'></div></li>';

				echo '<script type="text/javascript"> ';
				echo $script_data->scripts;
				echo '</script>'; 
			}	
			elseif($script_data->result=='error')
			{
				echo $script_data->error; 
			}	
   }
   
   ?></ul></li><?php
     }
	
	}
 ?>
 
 </ul>
</div>

<div class="clr" style="clear:both;"></div>
<input type="hidden" name="option" value="com_vbizz" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="view" value="vbizz" />
	</form>
</div>
</div>
</div>
</div>