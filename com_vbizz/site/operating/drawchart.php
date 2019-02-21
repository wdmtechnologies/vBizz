<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2016 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');


class vchart
{

	var $chart_script = '';			// the chart script to be returned
	var $chart_data = null;			// the chart data definition structure
	var $datasets = array();		// the dataset arrays
	var $total_rows = 0;			// total number of rows from all queries
	var $chart_title = '';
	var $font_style='';


//-------------------------------------------------------------------------------
// Constructor
//

	public static  function draw_view_chart($chart_data)
	{
		$obj = new stdClass();
		
		$db = JFactory::getDbo();
		
		$user = JFactory::getUser();
		
		if(VaccountHelper::checkOwnerGroup()) {
			$query = 'SELECT userid from #__vbizz_users where ownerid = '.$user->id;
			$db->setQuery($query);
			$u_list = $db->loadColumn();
			array_push($u_list,$user->id);
		} else {
			$query = 'SELECT ownerid from #__vbizz_users where userid = '.$user->id;
			$db->setQuery($query);
			$ownerid = $db->loadResult();
			
			$query = 'SELECT userid from #__vbizz_users where ownerid = '.$ownerid;
			$db->setQuery($query);
			$u_list = $db->loadColumn();
			array_push($u_list,$ownerid);
		}
		if(VaccountHelper::checkOwnerGroup() || VaccountHelper::checkEmployeeGroup())
		$cret = $user->id;//VaccountHelper::getUserListing();
	    else
		$cret = $user->id;
	
	    if(VaccountHelper::checkOwnerGroup() || VaccountHelper::checkEmployeeGroup())
		$vlist = $user->id;//VaccountHelper::getVendorListing();
	    else
	    $vlist = $user->id;
		$config = JFactory::getConfig();
		$uniqid 			= $chart_data->id;
			
		$chart_title		= $chart_data->chart_type;
		$width				= '400px';
		$height				= '400px';
		$chart_detail		= json_decode($chart_data->detail);
		if(empty($chart_detail->existing_database_table)&&(isset($chart_detail->user_write_query_value)&&$chart_detail->user_write_query_value=='') ){
			$obj->result = 'error';
			$obj->error = 'no_chart';;
			return $obj;
		}
				
		$chart_type = $chart_data->chart_type;
				
		$options = array();
		if($chart_type=='Line Chart')
			$chart_type='LineChart';
		if($chart_type=='Stepped AreaChart')
			$chart_type='SteppedAreaChart';
		if($chart_type=='Combo Charts')
			$chart_type='ComboChart';
		if($chart_type=='Area Chart')
			$chart_type='AreaChart';
		if($chart_type=='Bar Chart')
			$chart_type='BarChart';
		if($chart_type=='Column Chart')
			$chart_type='ColumnChart';
		if($chart_type=='Pie Chart')
			$chart_type='PieChart';
		if($chart_type=='Slice Pie Chart')
			$chart_type='PieChart';
		if($chart_type=='Geo Chart')
			$chart_type='GeoChart';
		if($chart_type=='Map Chart')
			$chart_type='Map';
		if($chart_type=='Table Chart')
			$chart_type='Table';
	 
		$option =  $opt =  $opt1 = $grid = array();
			$d_place = VaccountHelper::getDecimalPlace();
			$t_place = VaccountHelper::getThousandPlace();
			$decimal_symbol = !empty($d_place)?VaccountHelper::getDecimalPlace():'""';
            $thousand_symbol = !empty($t_place)?VaccountHelper::getThousandPlace():'""';			
		if(isset($chart_detail->x_axis) && $chart_detail->x_axis!='')
			array_push($options, 'hAxis: {title: "'.$chart_detail->x_axis.'", textPosition: "out"}');
		if(isset($chart_detail->y_axis) && $chart_detail->y_axis!='')
			array_push($options, 'vAxis: {title: "'.$chart_detail->y_axis.'", decimalSymbol: '.$decimal_symbol.' , groupingSymbol: '.$thousand_symbol.' }');
		if(isset($chart_detail->legend) && $chart_detail->legend!='')
			array_push($options, 'legend: {position:"'.$chart_detail->legend.'"}');
		           
		if(isset($chart_detail->pie_3d) && $chart_detail->pie_3d!='')
			array_push($options, 'is3D: true');
		if(isset($chart_detail->piehole) && $chart_detail->piehole!='')
			array_push($options, 'pieHole:'.$chart_detail->piehole); 
		
		array_push($options, 'width:"100%"');	
		array_push($options, 'height:"100%"');

		if(isset($chart_detail->legend) && $chart_detail->legend!='')
			array_push($options, 'chartArea:{top:30,height:"75%",width:"75%"}');
		else
			array_push($options, 'chartArea:{top:25,height:"80%",width:"80%"}');
					
		if(isset($chart_detail->series_column_color)&& $chart_detail->series_column_color!=''){
			$colors = explode(',',$chart_detail->series_column_color);
        
			
		}
		  
         array_push($options, 'colors: ["#67B7DC", "#FDD400", "#84B761", "#CC4748", "#cd82ad", "#2f4074", "#B5CBE9", "#FF9873", "#1D90FA", "#D77C7C"]');
		switch($chart_type)	{

			case 'AreaChart':
			case 'LineChart':
			case 'ComboChart':
			case 'ColumnChart':
			case 'SteppedAreaChart':
			case 'BarChart':
			$package = 'corechart';
			$series_display = array();
			
			if(isset($chart_detail->chart_churve) && $chart_detail->chart_churve!='')
				array_push($options, ' curveType: "function"');

			if(isset($chart_detail->isStacked) && $chart_detail->isStacked!='')
				array_push($options, ' isStacked: true');
			if(isset($chart_detail->connectSteps) && $chart_detail->connectSteps!='')
				array_push($options, ' connectSteps: true');


			if(isset($chart_detail->orientation) && $chart_detail->orientation!='')
				array_push($options, ' orientation: "'.$chart_detail->orientation.'"');

			$items = array();
			$data =array();

			if($chart_data->datatype_option=='predefined')
			{
				$da = JFactory::getConfig(); 

				$chart_detail->remote_query_value = preg_replace('{databasename}', '"'.$da->get('db').'"', $chart_detail->remote_query_value, 1); 
				preg_match_all('/{tablename\s(.*?)}/i', $chart_detail->remote_query_value, $matches);
				preg_match_all('/{as\s(.*?)}/i', $chart_detail->remote_query_value, $match);
				$matches_s = $matches[1];
				$text = $chart_detail->remote_query_value;
				
				for($r=0;$r<count($matches_s);$r++){
					$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
				}
				
				$query = str_replace('{','',str_replace('}','',$text));
				$ownerid = VaccountHelper::getOwnerId();
				$query = str_replace('wid', $ownerid, $query);
				$query = str_replace('vc_user', $cret, $query);
                $query = str_replace('v_user', $vlist, $query);
				if(isset($chart_detail->extra_condition) && !empty($chart_detail->extra_condition)){
					if(strpos(strtolower($query), 'where')!== false){
						$substr = 'where ';
						$attachment = $chart_detail->extra_condition.' AND ';
						$query  = str_replace($substr, $substr.$attachment, $query);
					}
					elseif(strpos(strtolower($query), 'group by')!== false){
						$substr = ' GROUP BY';
						$attachment = ' where '.$chart_detail->extra_condition.' ';
						$query = str_replace($substr, $attachment.$substr, $query);
					}
					else
						$query .= ' where '.$chart_detail->extra_condition;
				}
				if(isset($chart_detail->ordering_reference_column_name) && !empty($chart_detail->ordering_reference_column_name) && isset($chart_detail->ordering) && !empty($chart_detail->ordering))
					$query .= ' ORDER BY '.$chart_detail->ordering_reference_column_name.' '.$chart_detail->ordering;
				
				if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
					$query .= ' LIMIT '.$chart_detail->limit_value;
				
				$query = str_replace('informationschema','information_schema',$query);
				$query = str_replace('{databasename}',$config->get( 'db' ),$query);
				
				$db->setQuery($query);
				
				
				if($db->getErrorNum())	{
					$obj->result = 'error';
					$obj->error = $db->getErrorMsg();
					return $obj;
				}
				$chart_data = $db->loadObjectList();

				$label_array = isset($chart_data[0])?get_object_vars($chart_data[0]):array();

				$num =0;
				$new_label_array = array();
				if(strpos($query, 'information_schema')!== false)
				{
					$item = '[{type:"string", label:"'.$chart_detail->existing_database_table.'"}';
					$num =1;
					for($i=0;$i<count($chart_data);$i++)
					{
						$chart_datas = $chart_data[$i];

						$item .= ', "'.$chart_datas->Variable_name.'"';
						array_push($series_display,'{column: '.($num).',display: true}');
						$num++;		
					  
					}
					$item .= ']';
					array_push($items, $item);
					$item = '["'.$chart_detail->existing_database_table.'"';

					for($i=0;$i<count($chart_data);$i++)
					{
						$chart_datas = $chart_data[$i];
						$item .= ', '.$chart_datas->Value;
					}
					$item .= ']';
					array_push($items, $item);		
				}
				else
				{
					$item = '[';
					$num =0;
					$new_label_array = array();
					foreach($label_array as $key=>$value){
						$new_label_array[$num] = $key;
						if($num == 0)
							$item .= '{type:"string", label:"'.$key.'"}';
						else
						{
							$item .= ', "'.$key.'"';
							array_push($series_display,'{column: '.$num.',display: true}');
						}
						$num++;			
					}
					$item .= ']';
					array_push($items, $item);				

					$num =0;

					for($i=0;$i<count($chart_data);$i++){
						$chart_datas = $chart_data[$i];
                        $label_name = $new_label_array[0];
						$item = '["'.$chart_datas->$label_name.'"';	

						for($j=1;$j<count($new_label_array);$j++){
							 $label_name = $new_label_array[$j];
							$item .= ', '.$chart_datas->$label_name;	
						}
						$item .=']';
						array_push($items, $item);			 
					}
				}
				$data = implode(',', $items); 
			}
			elseif($chart_data->datatype_option=='writequery')
			{     
				if(isset($chart_detail->user_write_query_value) && $chart_detail->user_write_query_value!='')
				{
					$query  = $chart_detail->user_write_query_value;
				preg_match_all('/{tablename\s(.*?)}/i', $chart_detail->user_write_query_value, $matches);
				preg_match_all('/{as\s(.*?)}/i', $chart_detail->user_write_query_value, $match);
				$matches_s = $matches[1];
				$text = $chart_detail->user_write_query_value;
				
				for($r=0;$r<count($matches_s);$r++){
					$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
				}
				$query = str_replace('{','',str_replace('}','',$text));
				$ownerid = VaccountHelper::getOwnerId();
				$query = str_replace('wid', $ownerid, $query);
				}
				else
				{
					$series_array = explode(',', $chart_detail->series_column_name);
					$series_name = explode(',', $chart_detail->series);
					for($s =0; $s<count($series_array);$s++){
						if($chart_detail->column_name==$series_array[$s]){
							$series_array[$s] = $series_array[$s].' as '.$series_name[$s]; 
						}
					} 
					$query = 'select '.$chart_detail->column_name.','.implode(', ',$series_array).' from '.$chart_detail->existing_database_table;
					if(!empty($chart_detail->extra_condition))
						$query .= ' where '.$chart_detail->extra_condition;
					
					if(isset($chart_detail->ordering_reference_column_name) && !empty($chart_detail->ordering_reference_column_name) && isset($chart_detail->ordering) && !empty($chart_detail->ordering))
						$query .= ' ORDER BY '.$chart_detail->ordering_reference_column_name.' '.$chart_detail->ordering;
					if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
						$query .= ' LIMIT '.$chart_detail->limit_value;
				}
				
				$query = str_replace('vc_user', $cret, $query);
                 $query = str_replace('v_user', $vlist, $query);
				$db->setQuery($query);
				if($db->getErrorNum())	{
					$obj->result = 'error';
					$obj->error = $db->getErrorMsg();
					return $obj;
				}
				$chart_data = $db->loadObjectList();
				$label_array = isset($chart_data[0])?get_object_vars($chart_data[0]):array();
				$num =0;$label_arrays = array();
				if(strpos($query, 'information_schema')!== false)
				{
					$item = '[{type:"string", label:"'.$chart_detail->existing_database_table.'"}';
					$num =1;
					for($i=0;$i<count($chart_data);$i++)
					{
						$chart_datas = $chart_data[$i];
						$item .= ', "'.$chart_datas->Variable_name.'"';
						array_push($series_display,'{column: '.($num).',display: true}');
						$num++;		
					}
					$item .= ']';
					array_push($items, $item);
					$item = '["'.$chart_detail->existing_database_table.'"';

					for($i=0;$i<count($chart_data);$i++)
					{
						$chart_datas = $chart_data[$i];

						$item .= ', '.(float)$chart_datas->Value;
					}
					$item .= ']';
					array_push($items, $item);		
				}
				else{
					$item = '[';

					foreach($label_array as $key=>$value){
						$label_arrays[$num] = $key;
						if($num == 0)
							$item .= '{type:"string", label:"'.$key.'"}';
						else
						{
							$item .= ', "'.$key.'"';
							array_push($series_display,'{column: '.$num.',display: true}');
						}
						$num++;
					}
					$item .= ']';
					array_push($items, $item);
					$num =0;
					for($i=0;$i<count($chart_data);$i++){
						$chart_datas = $chart_data[$i];
                         $f_label = $label_arrays[0];
						$item = '["'.$chart_datas->$f_label.'"';	

						for($j=1;$j<count($label_arrays);$j++){
							$f_label = $label_arrays[$j];
							$item .= ', '.(float)$chart_datas->$f_label;	
						}
						$item .=']';
						array_push($items, $item);			 
					}
				}
				$data = implode(',', $items);
				
				//echo'<pre>';print_r($data);
			}

			$scripts = ' if(typeof google !== "undefined") 
			google.load("visualization", "1.1", {"packages":["'. $package.'", "controls"], callback: drawchart' . $uniqid . '});	

			function drawchart'.$uniqid.'() {
				var data'.$uniqid.' = google.visualization.arrayToDataTable(['.$data.']);
               for(var c=1;c<parseInt(data'.$uniqid.'.getNumberOfColumns());c++)
			   {
				   var formatter = new google.visualization.NumberFormat(
				  {decimalSymbol: '.$decimal_symbol.' , groupingSymbol: '.$thousand_symbol.' });
				   formatter.format(data'.$uniqid.', c);
			   }
				
				var chart'.$uniqid.' = new google.visualization.ChartWrapper({
					chartType: "'.$chart_type.'",
					containerId: "widget_' . $uniqid . '",
					dataTable: data'.$uniqid.',
					options : {'.implode(',', $options).'}
				})
				var columns'.$uniqid.' = [0];

				var seriesMap'.$uniqid.' = ['.implode(',', $series_display).'];
				var columnsMap'.$uniqid.' = {};
				var series'.$uniqid.' = [];
				for (var i = 0; i < seriesMap'.$uniqid.'.length; i++) {
					var col'.$uniqid.' = seriesMap'.$uniqid.'[i].column;
					columnsMap'.$uniqid.'[col'.$uniqid.'] = i;

					series'.$uniqid.'[i] = {};
					if (seriesMap'.$uniqid.'[i].display) {

						columns'.$uniqid.'.push(col'.$uniqid.');
					}
					else {

						columns'.$uniqid.'.push({
							label: data'.$uniqid.'.getColumnLabel(col'.$uniqid.'),
							type: data'.$uniqid.'.getColumnType(col'.$uniqid.'),
							sourceColumn: col'.$uniqid.',
							calc: function () {
								return null;
							}
						});

					if (typeof(series'.$uniqid.'[i].color) !== "undefined") {
						series'.$uniqid.'[i].backupColor = series'.$uniqid.'[i].color;
					}
					series'.$uniqid.'[i].color = "#CCCCCC";
					}

				}

				chart'.$uniqid.'.setOption("series", series'.$uniqid.');



				google.visualization.events.addListener(chart'.$uniqid.', "select", showHideSeries'.$uniqid.');
				function showHideSeries'.$uniqid.' () {
					var sel'.$uniqid.' = chart'.$uniqid.'.getChart().getSelection();

					if (sel'.$uniqid.'.length > 0) {

						if (sel'.$uniqid.'[0].row == null) {
							var col'.$uniqid.' = sel'.$uniqid.'[0].column;
							if (typeof(columns'.$uniqid.'[col'.$uniqid.']) == "number") {
								var src'.$uniqid.' = columns'.$uniqid.'[col'.$uniqid.'];


								columns'.$uniqid.'[col'.$uniqid.'] = {
									label: data'.$uniqid.'.getColumnLabel(src'.$uniqid.'),
									type: data'.$uniqid.'.getColumnType(src'.$uniqid.'),
									sourceColumn: src'.$uniqid.',
									calc: function () {
										return null;
									}
								};
								series'.$uniqid.'[columnsMap'.$uniqid.'[src'.$uniqid.']].color = "#CCCCCC";
							}
							else {
								var src'.$uniqid.' = columns'.$uniqid.'[col'.$uniqid.'].sourceColumn;


								columns'.$uniqid.'[col'.$uniqid.'] = src'.$uniqid.';
								series'.$uniqid.'[columnsMap'.$uniqid.'[src'.$uniqid.']].color = null;
							}
							var view'.$uniqid.' = chart'.$uniqid.'.getView() || {};
							view'.$uniqid.'.columns = columns'.$uniqid.';
							chart'.$uniqid.'.setView(view'.$uniqid.');
							chart'.$uniqid.'.draw();
						}
					}
				}

				var view'.$uniqid.' = {
					columns: columns'.$uniqid.'
				};
				chart'.$uniqid.'.draw();';

			$scripts .= '}';	

			break;
			case 'GeoChart':
			case 'PieChart':

			if(isset($chart_detail->region_label) && $chart_detail->region_label!='')
				array_push($options, ' region: "'.$chart_detail->region_label.'"');
			if(isset($chart_detail->displaymode) && $chart_detail->displaymode!='')
				array_push($options, ' displayMode: "'.$chart_detail->displaymode.'"');

			$items = array();
			$package = 'corechart';
			if($chart_data->datatype_option=='predefined')
			{

				$da = JFactory::getConfig(); 
				$chart_detail->remote_query_value = preg_replace('{databasename}', '"'.$da['db'].'"', $chart_detail->remote_query_value, 1);

				if(empty($chart_detail->remote_query_value))
					return ;
				preg_match_all('/{tablename\s(.*?)}/i', $chart_detail->remote_query_value, $matches);
				preg_match_all('/{as\s(.*?)}/i', $chart_detail->remote_query_value, $match);
				
				$matches_s = $matches[1];
				$text = $chart_detail->remote_query_value;
				
				for($r=0;$r<count($matches_s);$r++){
					$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
				}
				$query = str_replace('{','',str_replace('}','',$text));
				$ownerid = VaccountHelper::getOwnerId();
				$query = str_replace('wid', $ownerid, $query);
				$query = str_replace('vc_user', $cret, $query);
                 $query = str_replace('v_user', $vlist, $query);
				if(!empty($chart_detail->extra_condition))
					$query .= ' where '.$chart_detail->extra_condition;
				
				if(isset($chart_detail->ordering_reference_column_name) && !empty($chart_detail->ordering_reference_column_name) && isset($chart_detail->ordering) && !empty($chart_detail->ordering))
					$query .= ' ORDER BY '.$chart_detail->ordering_reference_column_name.' '.$chart_detail->ordering;
				
				if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
					$query .= ' LIMIT '.$chart_detail->limit_value;
				$query = str_replace('informationschema','information_schema',$query);
				$query = str_replace('{databasename}',$config->get( 'db' ),$query);
				$label_array = $chart_data = array();
				//echo $query;
				$db->setQuery($query);
				if($db->getErrorNum())	{
					$obj->result = 'error';
					$obj->error = $db->getErrorMsg();
					return $obj;
				}
				 $db->Query($query);
                if( $db->getNumRows() > 0 )
				{
				$chart_data = $db->loadObjectList();
				foreach($chart_data[0] as $key => $value){
					array_push($label_array, $key);
				}
				
				}
				else
				{
				$label_array = $chart_data = array();				
				}
				
				if(strpos($query, 'information_schema')!== false)
				{
					$item = '[{type:"string", label:"'.$chart_detail->existing_database_table.'"},"Value"]';
					array_push($items, $item);

					for($i=0;$i<count($chart_data);$i++)
					{
						$chart_datas = $chart_data[$i];
						$item = '["'.$chart_datas->Variable_name.'"';	
						$item .= ', '.$chart_datas->Value.']';

						array_push($items, $item);	
					  
					}

				}
				else
				{
					$item = '[';
					$num =0;
					$new_label_array = array();
					foreach($label_array as $key=>$value){
						$new_label_array[$num] = $key;
						if($num == 0)
							$item .= '{type:"string", label:"'.$key.'"}';
						else
							$item .= ', "'.$key.'"';
						$num++;			
					}
					$item .= ']';
					array_push($items, $item);
					$num =0;
					
					for($i=0;$i<count($chart_data);$i++){
						$chart_datas = $chart_data[$i];
						$f_label = $label_array[0];
						
                      
						$item = '["'.$chart_datas->$f_label.'"';

						for($j=1;$j<count($label_array);$j++){
							$f_label = $label_array[$j];
							$item .= ', '.$chart_datas->$f_label;  	
						}
						$item .=']';
						array_push($items, $item);			 
					}
				}
				$data = implode(',', $items); 

			}
			elseif($chart_data->datatype_option=='writequery')
			{

				if(isset($chart_detail->user_write_query_value) && $chart_detail->user_write_query_value!='')
				{
					$query  = $chart_detail->user_write_query_value;
				preg_match_all('/{tablename\s(.*?)}/i', $chart_detail->user_write_query_value, $matches);
				preg_match_all('/{as\s(.*?)}/i', $chart_detail->user_write_query_value, $match);
				$matches_s = $matches[1];
				$text = $chart_detail->user_write_query_value;
				
				for($r=0;$r<count($matches_s);$r++){
					$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
				}
				$query = str_replace('{','',str_replace('}','',$text));
				$ownerid = VaccountHelper::getOwnerId();
				$query = str_replace('wid', $ownerid, $query);
				}
				else
				{		 

					if($chart_detail->label_column_name!=$chart_detail->value_column_name)
						$query = 'select '.$chart_detail->label_column_name.','.$chart_detail->value_column_name.' from '.$chart_detail->existing_database_table;
					else
						$query = 'select '.$chart_detail->label_column_name.','.$chart_detail->value_column_name.' from '.$chart_detail->existing_database_table;
				}	

				if(!empty($chart_detail->extra_condition))
					$query .= ' where '.$chart_detail->extra_condition;
				
				if(isset($chart_detail->ordering_reference_column_name) && !empty($chart_detail->ordering_reference_column_name) && isset($chart_detail->ordering) && !empty($chart_detail->ordering))
					$query .= ' ORDER BY '.$chart_detail->ordering_reference_column_name.' '.$chart_detail->ordering;
				
				if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
					$query .= ' LIMIT '.$chart_detail->limit_value;

				$query = str_replace('vc_user', $cret, $query);
				 $query = str_replace('v_user', $vlist, $query);
				$db->setQuery($query);
				if($db->getErrorNum())	{
					$obj->result = 'error';
					$obj->error = $db->getErrorMsg();
					return $obj;
				}
				$chart_data = $db->loadObjectList();//print_r($data);
				$label_array = isset($chart_data[0])?get_object_vars($chart_data[0]):array();

				$num =0; 
				$item = '[';
				foreach($label_array as $key=>$value)
				{
					$label_arrays[$num] = $key;

					$item .= $num == 0?'{type:"string", label:"'.$key.'"}':', "'.$key.'"';

					$num++;
				}	
				$item .= ']';				

				array_push($items, $item);
				$num =0;

				for($i=0;$i<count($chart_data);$i++)
				{
					$chart_datas = $chart_data[$i];
					$item ='[';
					$num =0;
					foreach($chart_datas as $key=>$value)
					{
						$item .= $num ==0?'"'.$value.'"':', '.$value;	
						$num++;
					}
					$item .=']';
					array_push($items, $item);			 
				}
				$data = implode(',', $items);
			}				


			$scripts = ' if(typeof google !== "undefined") 
			google.load("visualization", "1.1", {"packages":["'. $package.'", "controls"]});	
			google.setOnLoadCallback(drawchart' . $uniqid . ');
			
			function drawchart'.$uniqid.'() {
				var data'.$uniqid.' = google.visualization.arrayToDataTable(['.$data.']);
				for(var c=1;c<parseInt(data'.$uniqid.'.getNumberOfColumns());c++)
			   {
				   var formatter = new google.visualization.NumberFormat(
				  {decimalSymbol: '.$decimal_symbol.' , groupingSymbol: '.$thousand_symbol.'});
				   formatter.format(data'.$uniqid.', c);
			   }
				var options'.$uniqid.' = {'.implode(',', $options).'};
				var chart'.$uniqid.' =  new google.visualization.'.$chart_type.'(document.getElementById("widget_' . $uniqid . '"));';



				$scripts .= ' chart'.$uniqid.'.draw(data'.$uniqid.', options'.$uniqid.');';

			$scripts .= '}';
			break;
			
			case 'Table':
			$package = 'table';
			$items = array(); 
			array_push($options, 'showRowNumber: true');
			array_push($options, 'allowHtml: true');
			
			if(isset($chart_detail->table_page) && $chart_detail->table_page!='')
				array_push($options, ' page: "enable"');
			if(isset($chart_detail->table_page_size) && $chart_detail->table_page_size!='')
				array_push($options, ' pageSize: '.$chart_detail->table_page_size);
			if(isset($chart_detail->table_page_button) && $chart_detail->table_page_button!='')
				array_push($options, ' pagingSymbols: "{prev: \'prev\', next: \'next\'}"');
			
			if($chart_data->datatype_option=='predefined')
			{
				$da = JFactory::getConfig(); 
				$chart_detail->remote_query_value = preg_replace('{databasename}', '"'.$da['db'].'"', $chart_detail->remote_query_value, 1);
				preg_match_all('/{tablename\s(.*?)}/i', $chart_detail->remote_query_value, $matches);
				preg_match_all('/{as\s(.*?)}/i', $chart_detail->remote_query_value, $match);
				$matches_s = $matches[1];
				$text = $chart_detail->remote_query_value;
				for($r=0;$r<count($matches_s);$r++){
					$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
				}
				$query = str_replace('{','',str_replace('}','',$text));
				$ownerid = VaccountHelper::getOwnerId();
				$query = str_replace('wid', $ownerid, $query);
				$query = str_replace('vc_user', $cret, $query);
                 $query = str_replace('v_user', $vlist, $query);
				if(isset($chart_detail->extra_condition) && !empty($chart_detail->extra_condition)){
					if(strpos(strtolower($query), 'where')!== false){
						$substr = 'where ';
						$attachment = $chart_detail->extra_condition.' AND ';
						$query  = str_replace($substr, $substr.$attachment, $query);
					}
					elseif(strpos(strtolower($query), 'group by')!== false){
						$substr = ' GROUP BY';
						$attachment = ' where '.$chart_detail->extra_condition.' ';
						$query = str_replace($substr, $attachment.$substr, $query);
					}
					else
						$query .= ' where '.$chart_detail->extra_condition;
				}
				if(isset($chart_detail->ordering_reference_column_name) && !empty($chart_detail->ordering_reference_column_name) && isset($chart_detail->ordering) && !empty($chart_detail->ordering))
					$query .= ' ORDER BY '.$chart_detail->ordering_reference_column_name.' '.$chart_detail->ordering;
				
				if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
					$query .= ' LIMIT '.$chart_detail->limit_value;
				
				$query = str_replace('informationschema','information_schema',$query);
				$query = str_replace('{databasename}',$config->get( 'db' ),$query);
				$db->setQuery($query);
				if($db->getErrorNum())	{
					$obj->result = 'error';
					$obj->error = $db->getErrorMsg();
					return $obj;
				}
				$chart_data = $db->loadObjectList();
				$label_array = isset($chart_data[0])?get_object_vars($chart_data[0]):array();

				$num =0;
				$new_label_array = array();
				if(isset($chart_detail->existing_database_table) && $chart_detail->existing_database_table=='Show Logs on Timeline'){
					$item = '[{type:"string"';	
					$item .= ', label:"Table Name"},"Message","Date"]';	
					array_push($items, $item);

					$new_label_array = array('table','message','op_start');	
					for($i=0;$i<count($chart_data);$i++){
						$chart_datas = $chart_data[$i];
						$link 		= JRoute::_( 'index.php?option=com_vbizz&view=logs&task=edit&cid[]='. $chart_datas->id );
						$item = '["<a href=\"'.$link.'\">'.$chart_datas->$new_label_array[0].'</a>"';	

						for($j=1;$j<count($new_label_array);$j++){
							$item .= ', "'.$chart_datas->$new_label_array[$j].'"';		
						}
						$item .=']';
						array_push($items, $item);			 
					}				
				}
				else
				{
					$item = '[';
					foreach($label_array as $key=>$value){
						$new_label_array[$num] = $key;
						if($num == 0)
							$item .= '{type:"string", label:"'.$key.'"}';
						else
							$item .= ', {type:"string",label:"'.$key.'"}';
						$num++;			
					}
					$item .= ']';

					array_push($items, $item); 
					$num =0;
					for($i=0;$i<count($chart_data);$i++){
						$chart_datas = $chart_data[$i];

						$item = '["'.$chart_datas->$new_label_array[0].'"';	

						for($j=1;$j<count($new_label_array);$j++){
							$item .= ', "'.$chart_datas->$new_label_array[$j].'"';	
						}
						$item .=']';
						array_push($items, $item);			 
					}
				}
				$data = implode(',', $items); 
			}
			elseif($chart_data->datatype_option=='writequery')
			{
				$series_array = explode(',', $chart_detail->series_column_name);
				$series_name = explode(',', $chart_detail->series);
				for($s =0; $s<count($series_array);$s++){

					if($chart_detail->column_name==$series_array[$s]){
						$series_array[$s] = $series_array[$s].' as '.$series_name[$s]; 
					}
				} 
				$query = 'select '.$chart_detail->column_name.','.implode(', ',$series_array).' from '.$chart_detail->existing_database_table;
				
				if(!empty($chart_detail->extra_condition))
					$query .= ' where '.$chart_detail->extra_condition;
				if(isset($chart_detail->ordering_reference_column_name) && !empty($chart_detail->ordering_reference_column_name) && isset($chart_detail->ordering) && !empty($chart_detail->ordering))
					$query .= ' ORDER BY '.$chart_detail->ordering_reference_column_name.' '.$chart_detail->ordering;
				if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
					$query .= ' LIMIT '.$chart_detail->limit_value;
				
				$query = str_replace('vc_user', $cret, $query);
				$query = str_replace('v_user', $vlist, $query);
				$ownerid = VaccountHelper::getOwnerId();
				$query = str_replace('wid', $ownerid, $query);
				$db->setQuery($query);
				if($db->getErrorNum())	{
					$obj->result = 'error';
					$obj->error = $db->getErrorMsg();
					return $obj;
				}
				$chart_data = $db->loadObjectList();//print_r($data);
				$label_array = isset($chart_data[0])?get_object_vars($chart_data[0]):array();
				$item = '[';
				$num =0;$label_arrays = array();
				foreach($label_array as $key=>$value){
					$label_arrays[$num] = $key;
					if($num == 0)
						$item .= '{type:"string", label:"'.$key.'"}';
					else
						$item .= ', "'.$key.'"';
					$num++;
				}
				$item .= ']';
				array_push($items, $item);
				$num =0;
				for($i=0;$i<count($chart_data);$i++){
					$chart_datas = $chart_data[$i];

					$item = '["'.$chart_datas->$label_arrays[0].'"';	

					for($j=1;$j<count($label_arrays);$j++){
						$item .= ', '.$chart_datas->$label_arrays[$j];	
					}
					$item .=']';
					array_push($items, $item);			 
				}
				$data = implode(',', $items);
			}
			$scripts = ' if(typeof google !== "undefined") 
			google.load("visualization", "1.1", {"packages":["'. $package.'", "controls"]});	
			google.setOnLoadCallback(drawchart' . $uniqid . ');
			function drawchart'.$uniqid.'() {
				var data'.$uniqid.' = google.visualization.arrayToDataTable(['.$data.']);
				var options'.$uniqid.' = {'.implode(',', $options).'};
				var chart'.$uniqid.' =  new google.visualization.'.$chart_type.'(document.getElementById("widget_' . $uniqid . '"));';

				$scripts .= ' chart'.$uniqid.'.draw(data'.$uniqid.', options'.$uniqid.');';

			$scripts .= '}';
			break;
			case 'Maps':
			$package = 'map';

			$chart_type = 'Map';
			$scripts = ' if(typeof google !== "undefined") 
			google.load("visualization", "1.1", {"packages":["'. $package.'", "controls"]});	
			google.setOnLoadCallback(drawchart' . $uniqid . ');
			function drawchart'.$uniqid.'() {
				var data'.$uniqid.' = google.visualization.arrayToDataTable(['.$data.']);
				var options'.$uniqid.' = {'.implode(',', $options).'};
				var chart'.$uniqid.' =  new google.visualization.'.$chart_type.'(document.getElementById("widget_' . $uniqid . '"));';

				$scripts .= ' chart'.$uniqid.'.draw(data'.$uniqid.', options'.$uniqid.');';

			$scripts .= '}';
			break;

		}			    
		$obj->result ='success';  
		$obj->scripts =$scripts;  		  
		return $obj;
	
	}
	
	static function widgetvalue($single_data)
	{
		
		$return_data = array(); 
		$config = JFactory::getConfig();
		$database_name = $config->get( 'db' );			   
		$db = JFactory::getDbo();
		
		$user = JFactory::getUser();
		
		if(VaccountHelper::checkOwnerGroup() || VaccountHelper::checkEmployeeGroup())
		$cret = $user->id;//VaccountHelper::getUserListing();
	    else
		$cret = $user->id;
	    if(VaccountHelper::checkOwnerGroup() || VaccountHelper::checkEmployeeGroup())
		$vlist = $user->id;//VaccountHelper::getVendorListing();
	    else
	    $vlist = $user->id;
		$obj = new stdClass();
		$obj->result = 'error';
		$obj->error = 'error';
		if(isset($single_data->style_layout) && $single_data->style_layout=='single_formate'){
			if(isset($single_data->remote_query_value) && $single_data->remote_query_value!='')
			{

				preg_match_all('/{tablename\s(.*?)}/i', $single_data->remote_query_value, $matches);
				preg_match_all('/{as\s(.*?)}/i', $single_data->remote_query_value, $match);
				$matches_s = $matches[1];
				$text = $single_data->remote_query_value;
				for($r=0;$r<count($matches_s);$r++){
					$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
				}
				$query = str_replace('{','',str_replace('}','',$text));
				$query = str_replace('vc_user', $cret, $query);
				$ownerid = VaccountHelper::getOwnerId();
				$query = str_replace('wid', $ownerid, $query);
				$query = str_replace('v_user', $vlist, $query);
				$query = str_replace('informationschema','information_schema',$query);
				$query = str_replace('databasename',$database_name,$query);
				if(!empty($single_data->extra_condition))
					$query .= ' where '.$single_data->extra_condition;
				if(isset($single_data->ordering) && !empty($single_data->ordering))
					$query .= ' ORDER BY '.$single_data->column_name.' '.$single_data->ordering;
				if(isset($single_data->limit_value) && !empty($single_data->limit_value))
					$query .= ' LIMIT '.$single_data->limit_value;
				try
				{
					$obj->result = 'success';	
					$obj->data = $db->setQuery( $query )->loadObject();

					return $obj;
				}
				catch (RuntimeException $e)
				{  
					$obj->result = 'error';
					$obj->error = $e->getMessage();
					return $obj;

				}
			}
			elseif(isset($single_data->user_write_query_value) && $single_data->user_write_query_value!='')
			{
			  preg_match_all('/{tablename\s(.*?)}/i', $single_data->user_write_query_value, $matches);
				preg_match_all('/{as\s(.*?)}/i', $single_data->user_write_query_value, $match);
				$matches_s = $matches[1];
				$text = $single_data->user_write_query_value;
				for($r=0;$r<count($matches_s);$r++){
					$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
				}
				$query = str_replace('{','',str_replace('}','',$text));
				$ownerid = VaccountHelper::getOwnerId();
				$query = str_replace('wid', $ownerid, $query);
				$query = str_replace('vc_user', $cret, $query);
				$query = str_replace('v_user', $vlist, $query);
			  			  
			  
			  $obj->result = 'success';	
			  $obj->data = $db->setQuery( $query )->loadObject();

					return $obj;
			}
		}
		elseif(isset($single_data->style_layout) && $single_data->style_layout=='listing_formate'){

			if(isset($single_data->remote_query_value) && $single_data->remote_query_value!='')
			{
				preg_match_all('/{tablename\s(.*?)}/i', $single_data->remote_query_value, $matches);
				preg_match_all('/{as\s(.*?)}/i', $single_data->remote_query_value, $match);
				$matches_s = $matches[1];
				$text = $single_data->remote_query_value;
				for($r=0;$r<count($matches_s);$r++){
					$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
				}
				$query = str_replace('{','',str_replace('}','',$text));
				$query = str_replace('vc_user', $cret, $query);
				$ownerid = VaccountHelper::getOwnerId();
				$query = str_replace('wid', $ownerid, $query);
				$query = str_replace('v_user', $vlist, $query);
				$query = str_replace('informationschema','information_schema',$query);
				$query = str_replace('databasename',$config->get( 'db' ),$query);
				if(!empty($single_data->extra_condition))
					$query .= ' where '.$single_data->extra_condition;
				if(isset($single_data->ordering) && !empty($single_data->ordering))
					$query .= ' ORDER BY '.$single_data->column_name.' '.$single_data->ordering;
				if(isset($single_data->limit_value) && !empty($single_data->limit_value))
					$query .= ' LIMIT '.$single_data->limit_value;
				
				try
				{
					$obj->result = 'success';	
					//echo $query;
					$obj->data = $db->setQuery( $query )->loadObjectList();

					return $obj;
				}
				catch (RuntimeException $e)
				{  
					$obj->result = 'error';
					$obj->error = $e->getMessage();
					return $obj;

				}						
			}
			elseif(isset($single_data->user_write_query_value) && $single_data->user_write_query_value!='')
			{
				preg_match_all('/{tablename\s(.*?)}/i', $single_data->user_write_query_value, $matches);
				preg_match_all('/{as\s(.*?)}/i', $single_data->user_write_query_value, $match);
				$matches_s = $matches[1];
				$text = $single_data->user_write_query_value;
				for($r=0;$r<count($matches_s);$r++){
					$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
				}
				$query = str_replace('{','',str_replace('}','',$text));
				$query = str_replace('vc_user', $cret, $query);
				$ownerid = VaccountHelper::getOwnerId();
				$query = str_replace('wid', $ownerid, $query);
				$query = str_replace('v_user', $vlist, $query);
				$query = str_replace('informationschema','information_schema',$query);
				$query = str_replace('databasename',$config->get( 'db' ),$query);
				if(!empty($single_data->extra_condition))
					$query .= ' where '.$single_data->extra_condition;
				if(isset($single_data->ordering) && !empty($single_data->ordering))
					$query .= ' ORDER BY '.$single_data->column_name.' '.$single_data->ordering;
				if(isset($single_data->limit_value) && !empty($single_data->limit_value))
					$query .= ' LIMIT '.$single_data->limit_value;

				try
				{
					$obj->result = 'success';	
					$obj->data = $db->setQuery( $query )->loadObjectList();

					return $obj;
				}
				catch (RuntimeException $e)
				{  
					$obj->result = 'error';
					$obj->error = $e->getMessage();
					return $obj;
				}						
			}
			elseif(isset($single_data->existing_database_table) && $single_data->existing_database_table!='')
			{
			
				$query = 'select `'.implode('`,`',$single_data->listing_column_name).'` from '.$single_data->existing_database_table;
				if(!empty($single_data->extra_condition))
					$query .= ' where '.$single_data->extra_condition;
				if(isset($single_data->ordering_reference_column_name) && !empty($single_data->ordering_reference_column_name))
					$query .= ' ORDER BY '.$single_data->ordering_reference_column_name; 

				$query .= isset($single_data->ordering)?' '.$single_data->ordering:' asc'; 
				if(isset($single_data->limit_value) && !empty($single_data->limit_value))
					$query .= ' LIMIT '.$single_data->limit_value;

				try
				{
					$obj->result = 'success';	
					$obj->data =  $db->setQuery( $query )->loadObjectList();	

					return $obj;
				}
				catch (RuntimeException $e)
				{  
					$obj->result = 'error';
					$obj->error = $e->getMessage();
					return $obj;

				}	
			}						
		}


		return $return_data;
	}
	
   static function formate_check($bytes)
   {
	$show = ''; $show_text='';
	if ($bytes > pow(1024, 5)) {
            $show .= round($bytes / pow(1024, 5), 2);
            $show_text .= JText::_('PiB');
        }
        else {
            if ($bytes > pow(1024, 4)) {
                $show .= round($bytes / pow(1024, 4), 2);
                $show_text .= JText::_('TiB');
            }
            else {
                if ($bytes > pow(1024, 3)) {
                    $show .= round($bytes / pow(1024, 3), 2);
                    $show_text .= JText::_('GiB');
                }
                else {
                    if ($bytes > pow(1024, 2)) {
                        $show .= round($bytes / pow(1024, 2), 2);
                        $show_text .= JText::_('MiB');
                    }
                    else {
                        if ($bytes > pow(1024, 1)) {
                            $show .= round($bytes / pow(1024, 1), 2);
                            $show_text .= JText::_('KiB');
                        }
                        else {
                            $show .= $bytes;
                            $show_text .= JText::_('B');
                        }
                    }
                }
            }
        }
	$show_array = array();
	$show_array[0] =$show;
	$show_array[1] =$show_text;
	return $show_array;
	
}

}

?>
