<?php 
/*------------------------------------------------------------------------
# com_vChart - Virtual Google Chart
# ------------------------------------------------------------------------
# author    Zaheer Abbas
# copyright Copyright (C) 2013 wwww.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech..com
# Technical Support:  Forum - http://www.wdmtech..com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted Access');

function vchart($path){
	require_once(JPATH_ADMINISTRATOR.'/components/com_vchart/helper/'.str_replace( '.', '/', $path).'.php');
}
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
		
			$uniqid 			= $chart_data->id;
			
			$chart_title		= $chart_data->chart_type;
			$width				= '400px';
			$height				= '400px';
			$chart_detail		= json_decode($chart_data->detail);
			if(empty($chart_detail->existing_database_table)){
				return 'no_chart';
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
	
	
					
				//if(isset($chart_title_option->backgroundColor))
			    //array_push($options, 'backgroundColor:{fill:"'.$chart_title_option->backgroundColor.'",stroke:"#666",strokeWidth:"10px"}'); 
				 $option =  $opt =  $opt1 = $grid = array();
					
					
					if(isset($chart_detail->x_axis) && $chart_detail->x_axis!='')
					array_push($options, 'hAxis: {title: "'.$chart_detail->x_axis.'"}');
				   if(isset($chart_detail->y_axis) && $chart_detail->y_axis!='')
					array_push($options, 'vAxis: {title: "'.$chart_detail->y_axis.'"}');
					if(isset($chart_detail->legend) && $chart_detail->legend!='')
					array_push($options, 'legend: {position:"'.$chart_detail->legend.'"}');
		           
					array_push($options, 'chartArea:{top:10}');
			if(isset($chart_detail->series_column_color)&& $chart_detail->series_column_color!=''){
			$colors = explode(',',$chart_detail->series_column_color);

			if(count($colors)>0)
			array_push($options, 'colors: ["'.implode('","',$colors).'"]');
			}
		  

	switch($chart_type)	{
	
	case 'AreaChart':
	case 'LineChart':
	case 'ComboChart':
	case 'ColumnChart':
	case 'SteppedAreaChart':
	case 'BarChart':
	$package = 'corechart';

			if(isset($chart_detail->chart_churve) && $chart_detail->chart_churve!='')
			array_push($options, ' curveType: "function"');
			
			if(isset($chart_detail->isStacked) && $chart_detail->isStacked!='')
			array_push($options, ' isStacked: true');
			if(isset($chart_detail->connectSteps) && $chart_detail->connectSteps!='')
			array_push($options, ' connectSteps: true');
			
			
			if(isset($chart_detail->orientation) && $chart_detail->orientation!='')
			array_push($options, ' orientation: "'.$chart_detail->orientation.'"');
			
			$items = array();
		
		if($chart_data->datatype_option=='predefined')
		 {
			    
                $data = array('drawchart' => 'drawchart','id' => $chart_detail->existing_database_table);
                $postString = http_build_query($data, '', '&');
				/* $ch = curl_init(); 

				if (!$ch){die("Couldn't initialize a cURL handle");}
				$ret = curl_setopt($ch, CURLOPT_URL,"http://okhlites.com/demo/CustomQuizWebsite/new_stockdata.php");
                 
				curl_setopt($ch,CURLOPT_POST,1);

				curl_setopt ($ch, CURLOPT_POST, true);
				curl_setopt ($ch, CURLOPT_POSTFIELDS, $postString); 
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
                $response_data = json_decode(curl_exec($ch)); */				
				//$response_data = (curl_exec($ch)); 
				preg_match_all('/{tablename\s(.*?)}/i', $chart_detail->remote_query_value, $matches);
                preg_match_all('/{as\s(.*?)}/i', $chart_detail->remote_query_value, $match);
				$matches_s = $matches[1];
				$text = $chart_detail->remote_query_value;
				for($r=0;$r<count($matches_s);$r++){
				$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
				}
				$query = str_replace('{','',str_replace('}','',$text));
				
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
			if(isset($chart_detail->ordering) && !empty($chart_detail->ordering))
				$query .= ' ORDER BY '.$chart_detail->ordering;
			if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
				$query .= ' LIMIT '.$chart_detail->limit_value;
			   
		        $db->setQuery($query);
				if($db->getErrorNum())	{
				$obj->result = 'error';
				$obj->error = $db->getErrorMsg();
				return $obj;
				}
				$chart_data = $db->loadObjectList();
				$label_array = get_object_vars($chart_data[0]);
				 $item = '[{type:"string"';
				 $num =0;
				 $new_label_array = array();
				foreach($label_array as $key=>$value){
					$new_label_array[$num] = $key;
					if($num == 0)
				$item .= ', label:"'.$key.'"}';
                else
                $item .= ', "'.$key.'"';
                 $num++;			
				}
	            $item .= ']';
				array_push($items, $item);
				$num =0;
			for($i=0;$i<count($chart_data);$i++){
				$chart_datas = $chart_data[$i];
			
				$item = '["'.$chart_datas->$new_label_array[0].'"';	
				
			  	for($j=1;$j<count($new_label_array);$j++){
					$item .= ', '.$chart_datas->$new_label_array[$j];	
				}
               $item .=']';
              array_push($items, $item);			 
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
			if(isset($chart_detail->ordering) && !empty($chart_detail->ordering))
				$query .= ' ORDER BY '.$chart_detail->column_name.' '.$chart_detail->ordering;
			if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
				$query .= ' LIMIT '.$chart_detail->limit_value;
			
				
				$db->setQuery($query);
				if($db->getErrorNum())	{
				$obj->result = 'error';
				$obj->error = $db->getErrorMsg();
				return $obj;
				}
				$chart_data = $db->loadObjectList();//print_r($data);
				$label_array = get_object_vars($chart_data[0]);
				 $item = '[{type:"string"';
				 $num =0;$label_arrays = array();
				foreach($label_array as $key=>$value){
					$label_arrays[$num] = $key;
					if($num == 0)
				$item .= ', label:"'.$key.'"}';
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
		
				break;
				
				case 'PieChart':
				$items = array();
				$package = 'corechart';
				if($chart_data->datatype_option=='predefined')
		        {
			    
                $data = array('drawchart' => 'drawchart','id' => $chart_detail->existing_database_table);
                $postString = http_build_query($data, '', '&');
				/* $ch = curl_init(); 

				if (!$ch){die("Couldn't initialize a cURL handle");}
				$ret = curl_setopt($ch, CURLOPT_URL,"http://okhlites.com/demo/CustomQuizWebsite/new_stockdata.php");
                 
				curl_setopt($ch,CURLOPT_POST,1);

				curl_setopt ($ch, CURLOPT_POST, true);
				curl_setopt ($ch, CURLOPT_POSTFIELDS, $postString); 
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
                $response_data = json_decode(curl_exec($ch)); */				
				//$response_data = (curl_exec($ch)); 
				if(empty($chart_detail->remote_query_value))
					return ;
				$query = $chart_detail->remote_query_value;
				 echo $query; jexit();
			if(!empty($chart_detail->extra_condition))
				$query .= ' where '.$chart_detail->extra_condition;
			if(isset($chart_detail->ordering) && !empty($chart_detail->ordering))
				$query .= ' ORDER BY '.$chart_detail->ordering;
			if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
				$query .= ' LIMIT '.$chart_detail->limit_value;
		        $db->setQuery($query);
				if($db->getErrorNum())	{
				$obj->result = 'error';
				$obj->error = $db->getErrorMsg();
				return $obj;
				}
				$chart_data = $db->loadObjectList();
				$label_array = get_object_vars($chart_data[0]);
				 $item = '[{type:"string"';
				 $num =0;
				 $new_label_array = array();
				foreach($label_array as $key=>$value){
					$new_label_array[$num] = $key;
					if($num == 0)
				$item .= ', label:"'.$key.'"}';
                else
                $item .= ', "'.$key.'"';
                 $num++;			
				}
	            $item .= ']';
				array_push($items, $item);
				$num =0;
			for($i=0;$i<count($chart_data);$i++){
				$chart_datas = $chart_data[$i];
			
				$item = '["'.$chart_datas->$new_label_array[0].'"';	
				
			  	for($j=1;$j<count($new_label_array);$j++){
					$item .= ', '.$chart_datas->$new_label_array[$j];	
				}
               $item .=']';
              array_push($items, $item);			 
			}
			$data = implode(',', $items); 
		    
			
			
		}
		elseif($chart_data->datatype_option=='writequery')
		{
			  
			 if($chart_detail->label_column_name!=$chart_detail->value_column_name)
			 $query = 'select '.$chart_detail->label_column_name.','.$chart_detail->value_column_name.' from '.$chart_detail->existing_database_table;
		 else
			 $query = 'select '.$chart_detail->label_column_name.','.$chart_detail->value_column_name.' from '.$chart_detail->existing_database_table;
		 
			if(!empty($chart_detail->extra_condition))
				$query .= ' where '.$chart_detail->extra_condition;
			if(isset($chart_detail->ordering) && !empty($chart_detail->ordering))
				$query .= ' ORDER BY '.$chart_detail->column_name.' '.$chart_detail->ordering;
			if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
				$query .= ' LIMIT '.$chart_detail->limit_value;
			
				
				$db->setQuery($query);
				if($db->getErrorNum())	{
				$obj->result = 'error';
				$obj->error = $db->getErrorMsg();
				return $obj;
				}
				$chart_data = $db->loadObjectList();//print_r($data);
				$label_array = get_object_vars($chart_data[0]);
				 $item = '[{type:"string"';
				
				
				$item .= ', label:"'.$chart_detail->label_column_name.'"},"'.$chart_detail->value_column_name.'"]';
               
	          
				array_push($items, $item);
				$num =0;
				$lable_name = $chart_detail->label_column_name;
				$value_name = $chart_detail->value_column_name;
			for($i=0;$i<count($chart_data);$i++){
				$chart_datas = $chart_data[$i];
			
				$item = '["'.$chart_datas->$lable_name.'"';	
				 $item .= ', '.$chart_datas->$value_name;	
			
				   $item .=']';
				  array_push($items, $item);			 
				}
				$data = implode(',', $items);
			  }				
				break;
				case 'Table':
				$package = 'table';
				$items = array();
		        array_push($options, 'showRowNumber: true');
				array_push($options, 'allowHtml: true');
				
		if($chart_data->datatype_option=='predefined')
		 {
			   
                $data = array('drawchart' => 'drawchart','id' => $chart_detail->existing_database_table);
                $postString = http_build_query($data, '', '&');
				/* $ch = curl_init(); 

				if (!$ch){die("Couldn't initialize a cURL handle");}
				$ret = curl_setopt($ch, CURLOPT_URL,"http://okhlites.com/demo/CustomQuizWebsite/new_stockdata.php");
                 
				curl_setopt($ch,CURLOPT_POST,1);

				curl_setopt ($ch, CURLOPT_POST, true);
				curl_setopt ($ch, CURLOPT_POSTFIELDS, $postString); 
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
                $response_data = json_decode(curl_exec($ch)); */				
				//$response_data = (curl_exec($ch)); 
				preg_match_all('/{tablename\s(.*?)}/i', $chart_detail->remote_query_value, $matches);
                preg_match_all('/{as\s(.*?)}/i', $chart_detail->remote_query_value, $match);
				$matches_s = $matches[1];
				$text = $chart_detail->remote_query_value;
				for($r=0;$r<count($matches_s);$r++){
				$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
				}
				$query = str_replace('{','',str_replace('}','',$text));
				
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
			if(isset($chart_detail->ordering) && !empty($chart_detail->ordering))
				$query .= ' ORDER BY '.$chart_detail->ordering;
			if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
				$query .= ' LIMIT '.$chart_detail->limit_value;
			    //echo $query; jexit();
		        $db->setQuery($query);
				if($db->getErrorNum())	{
				$obj->result = 'error';
				$obj->error = $db->getErrorMsg();
				return $obj;
				}
				$chart_data = $db->loadObjectList();
				$label_array = get_object_vars($chart_data[0]);
				 
				 $num =0;
				 $new_label_array = array();
				 if(isset($chart_detail->existing_database_table) && $chart_detail->existing_database_table=='Show Logs on Timeline'){
				$item = '[{type:"string"';	
                $item .= ', label:"Table Name"},"Message","Date"]';	
				array_push($items, $item);
				
                $new_label_array = array('table','message','op_start');	
                for($i=0;$i<count($chart_data);$i++){
				$chart_datas = $chart_data[$i];
			    $link 		= JRoute::_( 'index.php?option=com_hexdata&view=logs&task=edit&cid[]='. $chart_datas->id );
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
				 $item = '[{type:"string"';
				foreach($label_array as $key=>$value){
					$new_label_array[$num] = $key;
					if($num == 0)
				$item .= ', label:"'.$key.'"}';
                else
                $item .= ', "'.$key.'"';
                 $num++;			
				}
	            $item .= ']';
				 
				array_push($items, $item);
				$num =0;
			for($i=0;$i<count($chart_data);$i++){
				$chart_datas = $chart_data[$i];
			
				$item = '["'.$chart_datas->$new_label_array[0].'"';	
				
			  	for($j=1;$j<count($new_label_array);$j++){
					$item .= ', '.$chart_datas->$new_label_array[$j];	
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
			if(isset($chart_detail->ordering) && !empty($chart_detail->ordering))
				$query .= ' ORDER BY '.$chart_detail->column_name.' '.$chart_detail->ordering;
			if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
				$query .= ' LIMIT '.$chart_detail->limit_value;
			
				
				$db->setQuery($query);
				if($db->getErrorNum())	{
				$obj->result = 'error';
				$obj->error = $db->getErrorMsg();
				return $obj;
				}
				$chart_data = $db->loadObjectList();//print_r($data);
				$label_array = get_object_vars($chart_data[0]);
				 $item = '[{type:"string"';
				 $num =0;$label_arrays = array();
				foreach($label_array as $key=>$value){
					$label_arrays[$num] = $key;
					if($num == 0)
				$item .= ', label:"'.$key.'"}';
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
		
				break;
				case 'GeoChart':
				$package = 'corechart';
					
				break;
				
				case 'Maps':
				$package = 'map';
				
				$chart_type = 'Map';
				break;
	
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
          return $scripts;
	
	}
	
	static function draw_live_chart($chart_data){
		$obj = new stdClass();
		
       $output = new WebpageXML(false, null);
	   if (defined('PSI_JSON_ISSUE') && (PSI_JSON_ISSUE)) {
            $json = json_encode(simplexml_load_string(str_replace(">", ">\n", $output->getXMLString()))); // solving json_encode issue
        } else {
            $json = json_encode(simplexml_load_string($output->getXMLString()));
        }
			
			$json = json_decode(str_replace('@attributes','attributes',$json)); 
			$db = JFactory::getDbo();
		
			$uniqid 			= $chart_data->id;
			
			$chart_title		= $chart_data->chart_type;
			$width				= '400px';
			$height				= '400px';
			$chart_detail		= json_decode($chart_data->detail);
			if(empty($chart_detail->existing_database_table)){
				return 'no_chart';
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
	
	
					
				//if(isset($chart_title_option->backgroundColor))
			    //array_push($options, 'backgroundColor:{fill:"'.$chart_title_option->backgroundColor.'",stroke:"#666",strokeWidth:"10px"}'); 
				 $option =  $opt =  $opt1 = $grid = array();
					
					
					if(isset($chart_detail->x_axis) && $chart_detail->x_axis!='')
					array_push($options, 'hAxis: {title: "'.$chart_detail->x_axis.'"}');
				   if(isset($chart_detail->y_axis) && $chart_detail->y_axis!='')
					array_push($options, 'vAxis: {title: "'.$chart_detail->y_axis.'"}');
					if(isset($chart_detail->legend) && $chart_detail->legend!='')
					array_push($options, 'legend: {position:"'.$chart_detail->legend.'"}');
		           
					array_push($options, 'chartArea:{top:10}');
			if(isset($chart_detail->series_column_color)&& $chart_detail->series_column_color!=''){
			$colors = explode(',',$chart_detail->series_column_color);

			if(count($colors)>0)
			array_push($options, 'colors: ["'.implode('","',$colors).'"]');
			}
		  

	
			$package = 'corechart';
			$scripts = '';
			if($chart_detail->existing_database_table=='Server Response Monitoring'){
				
		     $data_range_limit = isset($chart_detail->data_range_limit)&&$chart_detail->data_range_limit!=''&&$chart_detail->data_range_limit!=0?$chart_detail->data_range_limit:20;
		    $scripts .= ' 
			
		    var live_chart_data_server_response' . $uniqid . ' = new Array();
            var res' . $uniqid . '=0;	
            var ress' . $uniqid . '=3;
            var chart' . $uniqid . '=0;	
            var options'.$uniqid.'=0;
            var timer'.$uniqid.'=0;
            var timer'.$uniqid.'=0;	

            var process_section'.$uniqid.'=0;
            var memory_section'.$uniqid.'=0;				
		function livechart'.$uniqid.'()
		{
	   var start_time = new Date().getTime();
	   jQuery.getJSON( "'.Juri::root().'index.php?option=com_hexdata&view=hexdata&task=live_chart_data", function(data'.$uniqid.'){
               
			var end_time = new Date().getTime();
             var request_time = end_time - start_time;
			 infor_response_time = request_time;
			 infor_memory_response = data'.$uniqid.'.info_memory;
			 var json = jQuery.parseJSON(data'.$uniqid.'.json);
			 infor_cpu_name = json.Hardware.CPU.CpuCore.attributes.Model+" Cpu Speed"+json.Hardware.CPU.CpuCore.attributes.CpuSpeed; 
			 infor_cpu_response = json.Vitals.attributes.CPULoad;
			
			 });
          }
			
			if(typeof google !== "undefined") 
				google.load("visualization", "1.1", {"packages":["'. $package.'", "controls"]});	
				google.setOnLoadCallback(drawcharts' . $uniqid . ');
				
				function drawcharts'.$uniqid.'() 
				{
				live_chart_data_server_response'.$uniqid.' = new google.visualization.DataTable();
				live_chart_data_server_response'.$uniqid.'.addColumn("number", "Server Respone Time");
				live_chart_data_server_response'.$uniqid.'.addColumn("number", "Server Respone Time");
				live_chart_data_server_response'.$uniqid.'.addColumn({type: "string", role: "tooltip"});
				live_chart_data_server_response'.$uniqid.'.addRow([1,  230, "Server Respone Time 230ms"]);
				drawChart'.$uniqid.'(); 
				}
				function drawChart'.$uniqid.'()
				{
				options'.$uniqid.' = {
				title: "wdmtech.com Response Time",
				curveType: "function",
				hAxis: { textPosition: "none" },
				vAxis: {format: "#ms"},
				width: "100%",
        };
			chart'.$uniqid.' = new google.visualization.'.$chart_type.'(document.getElementById("widget_'.$uniqid.'"));
				    google.visualization.events.addListener(chart'.$uniqid.', "ready", function(){ 
					
					setTimeout(function(){
					livechart'.$uniqid.'();
					ress' . $uniqid . ' = ress' . $uniqid . '+1;
					 if(live_chart_data_server_response' . $uniqid . '.getNumberOfRows()>'.$data_range_limit.'){
			          live_chart_data_server_response' . $uniqid . '.removeRow(0); 
		               }
					  var tooltips_data'.$uniqid.' = "Server Respone Time "+parseInt(infor_response_time)+" ms";
             live_chart_data_server_response' . $uniqid . '.addRow([parseInt(ress' . $uniqid . '),  parseInt(infor_response_time),tooltips_data'.$uniqid.']); 
					   
					chart'.$uniqid.'.draw(live_chart_data_server_response'.$uniqid.', options'.$uniqid.');
				}, 120000);
			   });
     
	        chart'.$uniqid.'.draw(live_chart_data_server_response'.$uniqid.', options'.$uniqid.');
            			
	        }';
			}
			elseif($chart_detail->existing_database_table=='Server Monitoring'){
			    $data_range_limits = isset($chart_detail->data_range_limit)&&$chart_detail->data_range_limit!=''&&$chart_detail->data_range_limit!=0?$chart_detail->data_range_limit:20;
                $data = array('memory' => 'memory');
                $postString = http_build_query($data, '', '&');
				
	
				
		        $memory_info = exec('free');
		      
                $infor_memory =  explode(':',$memory_info);
				/* $array = preg_split("/\r\n|\n|\r/", $infor_memory[1]);print_r($array); jexit(); */
				 $infor_memory = explode(" ", trim(preg_replace('/\s\s+/', ' ', $infor_memory[1])));
                  $mem_usage = memory_get_usage(true); 
		          $mem_usage = round($mem_usage/1024,2);
                  if (stristr(PHP_OS, 'win')) {
       
            $wmi = new COM("Winmgmts://");
            $server = $wmi->execquery("SELECT * FROM Win32_Processor");
           
            $cpu_num = 0;
            $load_total = 0;
           
            foreach($server as $cpu){
                $cpu_num++;
				// print_r($cpu->loadpercentage);
                $load_total += $cpu->loadpercentage;
            }
           
            $load = round($load_total/$cpu_num);
           
        } else {
         
			$coreCount = 2;$interval = 1;
			$rs = sys_getloadavg();
			$core_nums=trim(shell_exec("grep -P '^physical id' /proc/cpuinfo|wc -l"));
			$load=$rs[0]/$core_nums;
			
        }

			 $scripts .= '
			 var live_chart_data_server' . $uniqid . ' = new Array();
			  var live_chart_cpu_load' . $uniqid . ' = new Array();
			 var chart_server' . $uniqid . '=0;
             var chart_cpu' . $uniqid . '=0;				 
             var options_server'.$uniqid.'=0;
             var option_cpu'.$uniqid.'="";			 
			  var ram_info' . $uniqid . '=2;
			  var cpu_info' . $uniqid . '=2;
			  var server_memory_response = '.$mem_usage.';
			  var server_cpu_load = '.$load.';
			 jQuery(document).ready(function() {
				 jQuery("base").remove();
                jQuery( "#tabs" ).tabs({
				activate: function (event, ui) { 
				jQuery( "#tabs" ).tabs( "refresh" );
				var active = jQuery("#tabs").tabs("option", "active");
				
				if(jQuery("#tabs ul>li").eq(active).attr("data-show-chart")=="yes"){
                        if(jQuery("#tabs ul>li").eq(active).attr("data-chart-for")=="ram"){ 
							chart_server'.$uniqid.'.draw(live_chart_data_server'.$uniqid.', options'.$uniqid.');
						}
                         else if(jQuery("#tabs ul>li").eq(active).attr("data-chart-for")=="cpu"){
							 chart_cpu'.$uniqid.'.draw(live_chart_cpu_load'.$uniqid.', option_cpu'.$uniqid.');
						 }						
					
				}	
				}	
				});
              });';
			  $scripts .= 'if(typeof google !== "undefined") 
				google.load("visualization", "1.1", {"packages":["'. $package.'", "controls"]});	
				google.setOnLoadCallback(drawcharts' . $uniqid . ');
				
				function drawcharts'.$uniqid.'()
				{
				live_chart_data_server'.$uniqid.' = new google.visualization.DataTable();
				live_chart_data_server'.$uniqid.'.addColumn("number", "Server Ram Status");
				
				live_chart_data_server'.$uniqid.'.addColumn("number", "Server Ram Status");
				live_chart_data_server'.$uniqid.'.addColumn({type: "string", role: "tooltip"});
				
				live_chart_data_server'.$uniqid.'.addRow([1, server_memory_response,"Used Memory : "+server_memory_response+"KB"]);
				
				live_chart_cpu_load'.$uniqid.' = new google.visualization.DataTable();
				live_chart_cpu_load'.$uniqid.'.addColumn("number", "Server Ram Status");
				
				live_chart_cpu_load'.$uniqid.'.addColumn("number", "Server Ram Status");
				live_chart_cpu_load'.$uniqid.'.addColumn({type: "string", role: "tooltip"});
				
				live_chart_cpu_load'.$uniqid.'.addRow([1, server_cpu_load,"CPU Load : "+server_cpu_load+"%"]);
				
				drawChart'.$uniqid.'();
				}
				function drawChart'.$uniqid.'()
				{
				options'.$uniqid.' = {
				title: "Memory Status of Server",
				
				hAxis: { textPosition: "none" },
				vAxis: {format: "#KB"},
				width: "100%",
        };      
		   option_cpu'.$uniqid.' = {
				title: "CPU Status of Server CPU Model '.$json->Hardware->CPU->CpuCore->attributes->Model.' CPU Speed  '.$json->Hardware->CPU->CpuCore->attributes->CpuSpeed.'",
				
				hAxis: { textPosition: "none" },
				width: "100%",
        };
			chart_server'.$uniqid.' = new google.visualization.LineChart(document.getElementById("widget_sever_2"));
			chart_cpu'.$uniqid.' = new google.visualization.LineChart(document.getElementById("widget_sever_3"));
			google.visualization.events.addListener(chart_server'.$uniqid.', "ready", function(){ 
			jQuery( "#tabs" ).tabs( "refresh" );
			
			setTimeout(function(){
					
					ram_info' . $uniqid . ' = ram_info' . $uniqid . '+1;
					
					 if(live_chart_data_server' . $uniqid . '.getNumberOfRows()>'.$data_range_limits.'){
			          live_chart_data_server' . $uniqid . '.removeRow(0); 
		               }
					  live_chart_data_server'.$uniqid.'.addRow([parseInt(ram_info' . $uniqid . '), infor_memory_response,"Used Memory : "+infor_memory_response+"KB"]);
					  		   
					
					chart_server'.$uniqid.'.draw(live_chart_data_server'.$uniqid.', options'.$uniqid.');
					
				}, 120000);
			});	   
			google.visualization.events.addListener(chart_cpu'.$uniqid.', "ready", function(){ 
			setTimeout(function(){
					
					
					cpu_info' . $uniqid . ' = cpu_info' . $uniqid . '+1;
					 
					  
					if(live_chart_cpu_load' . $uniqid . '.getNumberOfRows()>'.$data_range_limits.'){
			          live_chart_cpu_load' . $uniqid . '.removeRow(0); 
		               }
                   
                   live_chart_cpu_load'.$uniqid.'.addRow([parseInt(cpu_info' . $uniqid . '), infor_cpu_response,"CPU Load : "+infor_cpu_response+"%"]);				   
					option_cpu'.$uniqid.'.title = "CPU Status of Server CPU Model "+infor_cpu_name;
					chart_cpu'.$uniqid.'.draw(live_chart_cpu_load'.$uniqid.', option_cpu'.$uniqid.');
				}, 120000);
			
			});
	        chart_server'.$uniqid.'.draw(live_chart_data_server'.$uniqid.', options_server'.$uniqid.');
			 chart_cpu'.$uniqid.'.draw(live_chart_cpu_load'.$uniqid.', option_cpu'.$uniqid.');
            			
	        }';
			}
          return $scripts;
	
		
	}


}

?>
