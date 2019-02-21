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
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controllerform');

class VbizzControllerVbizz extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'unpublish', 	'publish');
	}

	function edit($key = NULL, $urlVar = NULL)
	{
		JRequest::setVar( 'view', 'widget' );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}
	
	function apply()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		$id = $data['id'];
		$input_name = $data['input_name'];
		$li_id = $data['widget_id'];
		
		$model = $this->getModel('vbizz');
		
		if ($model->storeWidget($data)) {
						
			ob_start();
			$widget_id = $id;
			$config = $model->getConfig();
			$modes = $model->getModes();
			$types = $model->getTypes();
			$latest_expense = $model->getLatestExpense();
			$latest_income = $model->getLatestIncome();
			$debt = $model->getDebt();
			$oweus = $model->getOweus();
			
			
			$currency_format = $config->currency_format;
				
			if($currency_format==1)
			{
				$today_income = $model->getTodayIncome();
				$today_expense = $model->getTodayExpense();
				$month_income = $model->getMonthIncome();
				$month_expense = $model->getMonthExpense();
			} else if($currency_format==2) {
				$today_income = number_format($model->getTodayIncome(), 2, '.', ',');
				$today_expense = number_format($model->getTodayExpense(), 2, '.', ',');
				$month_income = number_format($model->getMonthIncome(), 2, '.', ',');
				$month_expense = number_format($model->getMonthExpense(), 2, '.', ',');
			} else if($currency_format==3) {
				$today_income = number_format($model->getTodayIncome(), 2, ',', ' ');
				$today_expense = number_format($model->getTodayExpense(), 2, ',', ' ');
				$month_income = number_format($model->getMonthIncome(), 2, ',', ' ');
				$month_expense = number_format($model->getMonthExpense(), 2, ',', ' ');
			} else if($currency_format==4) {
				$today_income = number_format($model->getTodayIncome(), 2, ',', '.');
				$today_expense = number_format($model->getTodayExpense(), 2, ',', '.');
				$month_income = number_format($model->getMonthIncome(), 2, ',', '.');
				$month_expense = number_format($model->getMonthExpense(), 2, ',', '.');
			} else {
				$today_income = $model->getTodayIncome();
				$today_expense = $model->getTodayExpense();
				$month_income = $model->getMonthIncome();
				$month_expense = $model->getMonthExpense();
			}
			
			require_once (JPATH_BASE . '/components/com_vbizz/views/vbizz/tmpl/'.$input_name.'.php');
			$chart = ob_get_contents();
			ob_end_clean();
			
			$obj->result = 'success';
			$obj->msg = JText::_('WIDGET_SAVED_SUCCESSFULLY');
			$obj->chart = $chart;
			
		} else {
			$obj->result = 'error';
			$obj->msg = $model->getError();;
		}
		
		jexit(json_encode($obj));
		
	}
	
	function getWidget()
	{
		$db = JFactory::getDbo();

		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		$user = JFactory::getUser();
		
		$id = $data['id'];
		$input_name = $data['input_name'];
		
		$model = $this->getModel('vbizz');
		
		$widget = $model->getWidget();
		
		$query = 'SELECT widget_ordering from #__vbizz_users where userid='.$user->id;
		$db->setQuery($query);
		$widget_ordering = $db->loadResult();
		
		//$ordering = explode(',',$widget_ordering);
		
						
		ob_start();
		$widget_id = $id;
		$config = $model->getConfig();
		$modes = $model->getModes();
		$types = $model->getTypes();
		$latest_expense = $model->getLatestExpense();
		$latest_income = $model->getLatestIncome();
		$debt = $model->getDebt();
		$oweus = $model->getOweus();
		
		
		$currency_format = $config->currency_format;
			
		if($currency_format==1)
		{
			$today_income = $model->getTodayIncome();
			$today_expense = $model->getTodayExpense();
			$month_income = $model->getMonthIncome();
			$month_expense = $model->getMonthExpense();
		} else if($currency_format==2) {
			$today_income = number_format($model->getTodayIncome(), 2, '.', ',');
			$today_expense = number_format($model->getTodayExpense(), 2, '.', ',');
			$month_income = number_format($model->getMonthIncome(), 2, '.', ',');
			$month_expense = number_format($model->getMonthExpense(), 2, '.', ',');
		} else if($currency_format==3) {
			$today_income = number_format($model->getTodayIncome(), 2, ',', ' ');
			$today_expense = number_format($model->getTodayExpense(), 2, ',', ' ');
			$month_income = number_format($model->getMonthIncome(), 2, ',', ' ');
			$month_expense = number_format($model->getMonthExpense(), 2, ',', ' ');
		} else if($currency_format==4) {
			$today_income = number_format($model->getTodayIncome(), 2, ',', '.');
			$today_expense = number_format($model->getTodayExpense(), 2, ',', '.');
			$month_income = number_format($model->getMonthIncome(), 2, ',', '.');
			$month_expense = number_format($model->getMonthExpense(), 2, ',', '.');
		} else {
			$today_income = $model->getTodayIncome();
			$today_expense = $model->getTodayExpense();
			$month_income = $model->getMonthIncome();
			$month_expense = $model->getMonthExpense();
		}
		
		require_once (JPATH_BASE . '/components/com_vbizz/views/vbizz/tmpl/'.$input_name.'.php');
		$chart = ob_get_contents();
		ob_end_clean();
		
		$obj->result = 'success';
		$obj->chart = $chart;
		
		
		
		jexit(json_encode($obj));
		
	}
	function drawChart()
	{
		$db = JFactory::getDbo();

		$obj = new stdClass();
		$obj->result='error';
		$model = $this->getModel('vbizz');
		$return_data = $model->drawChart();
		
		$obj->playedquiz = $return_data;
		$obj->result = 'success';
		jexit(json_encode($obj));
		
	}
	function remove_widget()
	{
		$db = JFactory::getDbo();
		
		$user = JFactory::getUser();		
		
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		$id = $data['id'];
		$widget_id = $data['widget_id'];
		$input_name = $data['input_name'];
		
		$query = 'SELECT widget_ordering from #__vbizz_users where userid='.$user->id;
		$db->setQuery($query);
		$widget_ordering = $db->loadResult();
		
		$ordering = explode(',',$widget_ordering);
		
		if (($key = array_search($widget_id, $ordering)) !== false) {
			unset($ordering[$key]);
		}
		
		$newOrder = implode(',',$ordering);
		
		$query = 'UPDATE #__vbizz_widget set '.$db->QuoteName($input_name).' =0 WHERE id='.$id;
		$db->setQuery( $query );
		if(!$db->query())	{
			$obj->result = $db->getErrorMsg();
			return false;
		}
		
		$query = 'UPDATE #__vbizz_users set '.$db->QuoteName('widget_ordering').' ='.$db->quote($newOrder).' WHERE userid='.$user->id;
		$db->setQuery( $query );
		if(!$db->query())	{
			$obj->result = $db->getErrorMsg();
			return false;
		}
		
		$obj->result = 'success';
			
		
		jexit(json_encode($obj));
		
	}
	
	//reorder widget
	function reorder()
	{
		$db = JFactory::getDbo();
		
		$user = JFactory::getUser();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		$widget_ordering = $data['order'];
		
		$wid_ordering = explode(',',$widget_ordering);
		
		$orders = array_filter($wid_ordering);
		
		$ordering = implode(',',$orders);
		
		$query = 'UPDATE #__vbizz_users set '.$db->QuoteName('widget_ordering').' ='.$db->quote($ordering).' WHERE userid='.$user->id;
		$db->setQuery( $query );
		if(!$db->query())	{
			$obj->result = $db->getErrorMsg();
			return false;
		} else {
			$obj->result = 'success';
		}
		
		jexit(json_encode($obj));
		
	}
	
	//delete dashboard widget
	function delete_widget()
	{
		$model = $this->getModel('vbizz');

		if($model->delete()) {
			$this->update_dashboard();	
		} 

	}
	
	//
	function live_chart_data_test()
	{  
		
		
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
 
		$this->model = $this->getModel('vbizz');
		$this->profiles = $this->model->getProfiles();
         $user = JFactory::getUser();
		 $groups = $user->getAuthorisedGroups();
		require_once JPATH_COMPONENT.'/operating/drawchart.php';
		$datas = array();
		$profile_outer = array();
		$profile_listing = array();
		$other_profile_listing = array();
		
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
			if(isset($profile->style_layout) && $profile->style_layout=='single_formate')
			{
				$single_data = vchart::widgetvalue($profile);
				if(isset($single_data->result)&&$single_data->result=='error'){
					array_push($profile_outer, $single_data->error);
				}
				else
				{
					$single_data = $single_data->data;

					$object_array = get_object_vars($single_data);
					$object_array = count($object_array)>0?array_keys($object_array):array();
					$regex		= '/{(.*?)}/i';
					preg_match_all($regex, $profile->style_layout_editor, $matches, PREG_SET_ORDER);
					foreach ($single_data as $key=>$value)
					{    $value = (float)$value>0?VaccountHelper::getNumberFormatValue($value):$value;
						
						if($value==null)	
							array_push($profile_outer, 0);
						else
							array_push($profile_outer, $value);
					}
				}
			}
			elseif(isset($profile->style_layout) && $profile->style_layout=='listing_formate')
			{      
				$profile_listing_detail = array(); 
				if($profile->existing_database_table=='vData Profiles'){

					for ($i=0, $n=count( $this->items ); $i < $n; $i++)
					{
						$row = $this->items[$i];
						$listing = '';
						if(in_array(JText::_("COM_VDATA_SELECT_ALL_PROFILES"),$profile->profiles) || in_array($row->id,$profile->profiles))
						{
							 $listing .= '<li class="common_profile profile_mid_data_inner num'.($i+1).' profile-section-listing" data-ordering-profile="'.$row->id.':'.$row->ordering.'" style="'.$sub_current_width.'">';
							
							 if($row->iotype)
							 {
							$listing .= '<a href="index.php?option=com_vdata&view=profiles&task=export&profileid='.$row->id.'"><img src="'.Juri::root().'media/com_vdata/images/export_profile.png" alt="'.JText::_('CONFIG').'" /><span class="dashboard_profile_name">'.JText::_($row->title).'</span></a>';
							 }
							 else
							 {
							$listing .= '<a href="index.php?option=com_vdata&view=import&profileid='.$row->id.'"><img src="'.Juri::root().'media/com_vdata/images/import_profile.png" alt="'.JText::_('CONFIG').'" /><span class="dashboard_profile_name">'.JText::_($row->title).'</span></a>';

							 }
							$listing .= '</li>';

						}
						array_push($profile_listing_detail, $listing);
					}	
				}
				elseif($profile->existing_database_table=='vData Plugins'){
					
					$profile_listing_detail = array(); 
					for ($i=0, $n=count( $this->plugins ); $i < $n; $i++)
					{
						$row = $this->plugins[$i];
						$listing = '';
						if(in_array(JText::_("COM_VDATA_SELECT_ALL_PLUGINS"),$profile->plugins) || in_array($row->extension_id,$profile->plugins))
						{

							$listing .= '<li class="common_profile_plugin profile_mid_data_inner num'.($i+1).'" data-ordering-profile="'. $row->extension_id.':'.$row->name.'" style="'.$sub_current_width.'"><a href="index.php?option=com_vdata&view=profiles&extension_id='.$row->extension_id.'&task=edit&cid[]=0"><img src="'.Juri::root().'media/com_vdata/images/export_profile.png" alt="'.JText::_('CONFIG').'" /><span class="dashboard_profile_name">'.JText::_($row->name).'</span></a></li>';

						}
						array_push($profile_listing_detail, $listing);
					}    
				}
				else
				{

					$single_data = vchart::widgetvalue($profile);
					if(isset($single_data->result)&&$single_data->result=='error'){
						array_push($profile_listing, $single_data->error);
					}
					else
					{
						$single_data = $single_data->data;
						$lsting = '<table class="common_class_listing_format_new adminlist table table-hover listing_info listing_info_'.$this->profiles[$j]->id.' profile_mid_data_inner" width="100%"><thead><tr>';
						//$object_array = isset($single_data[0])?get_object_vars($single_data[0]):array();
						$lsting = '';
						/* $object_array = count($object_array)>0?array_keys($object_array):array();
						for ($s = 0;$s<count($object_array);$s++)
						{
							$lsting .='<th>'.$object_array[$s].'</th></thead></tr>';
						}
						array_push($profile_listing_detail, $lsting); */					
						//array_push($profile_listing, $lsting);
						for ($l=0; $l < count( $single_data ); $l++)
						{
							$listing = $single_data[$l];
							$lsting .= '<tr>';
							foreach($listing as $listings){
							
								if(VaccountHelper::getValidateDate($listings))
								$lsting .= '<td>'.VaccountHelper::getDate($listings).'</td>';
                                else							
								$lsting .= '<td>'.((float)$listings>0?VaccountHelper::getNumberFormatValue((float)$listings):$listings).'</td>';	
							}
							$lsting .= '</tr>';	
						} 
						//$lsting .= '</table>'; 	
						array_push($profile_listing_detail, $lsting);	

					}				   
				}
				array_push($profile_listing, $profile_listing_detail);
			}

		}
		array_push($datas,$profile_outer);
		array_push($datas,$profile_listing);
		$datas = json_encode($datas);
		echo "data:".$datas."\n\n";
		echo "retry: 8000" . PHP_EOL;
		flush();

		jexit();
	}
	
	//update dashboard widget
	function update_dashboard_new()
	{
		$this->model = $this->getModel('vbizz');
		$this->profiles = $this->model->getProfiles();
		$this->configuration = $this->model->getConfig();
		$this->update_item = JFactory::getApplication()->input->get('status_action','');
		$status_index = JFactory::getApplication()->input->get('status_index','');
		$status_index = $status_index==0?count($this->profiles)+1:($status_index+1);
		$this->profile = $this->model->getProfile($this->update_item);
		$row_siz = $this->configuration->row_limit;;
		$one_column_size = 100/$this->configuration->column_limit;
		require_once JPATH_COMPONENT.'/operating/drawchart.php';
		$prev_current_width ='';
		$future_width ='';
		$column_widget_width_value = '';
		$row_widget_height_value = '';
		$row_widget_height_value_chart = '';
		
		
		$live_data_query = array('Server Response Monitoring','Server CPU Monitoring','Server Monitoring','Thread Status','Queries Status');
		
		$data = new stdClass();
		$data->result = 'error';
		
		$html ='';
		$script =''; 
		$li =''; 
		$k =1;
		$profile = json_decode($this->profile->detail);
		$current_width ='';
		$style = '';
		$sub_current_width =0;$box_class_name='';
		
		if(isset($profile->box_column) && $profile->box_column)
		{
			$column_widget_width_value = (($profile->box_column*$one_column_size)-2).'%';	
		}

		if(isset($profile->box_row) && $profile->box_row)
		{
			$row_widget_height_value =	(($profile->box_row*$row_siz)-20).'px';
			$row_widget_height_value_chart = (($profile->box_row*$row_siz)-20);
		}
		
		if($column_widget_width_value!='' && $row_widget_height_value!='') 
			$style = ' style="width:'.$column_widget_width_value.';height:'.$row_widget_height_value.';"';
		
		$style_update = $column_widget_width_value.':'.$row_widget_height_value;
		
		if(isset($profile->box_layout) && $profile->box_layout=="onebox"){
			$sub_current_width=1;$box_class_name=' onebox';
		} else if(isset($profile->box_layout) && $profile->box_layout=="twobox") {
			$sub_current_width=2;
			$box_class_name=' twobox';
		} else if(isset($profile->box_layout) && $profile->box_layout=="threebox"){
			$sub_current_width=3;
			$box_class_name=' threebox';
		} else if(isset($profile->box_layout) && $profile->box_layout=="fourbox"){
			$sub_current_width=4;
			$box_class_name=' fourbox';
		} else if(isset($profile->box_layout) && $profile->box_layout=="fivebox"){
			$sub_current_width=5;
			$box_class_name=' fivebox';
		} else if(isset($profile->box_layout) && $profile->box_layout=="sixbox"){
			$sub_current_width=5;
			$box_class_name=' sixbox';
		}
		
		if(empty($prev_current_width))
		{
			$prev_current_width = $current_width;	
		}
		
		$current_width ='';
		if(isset($profile->style_layout) && $profile->style_layout=='single_formate')
		{ 
			$li .= '<li class="common_profile_main single_formate num'.($status_index);
			if($this->profile->datatype_option=='profile')
			{
				$li .= ' profile_widget';
			} 
			$li .= $box_class_name.'" data-ordering-profile="'.$this->profile->id.':'.$this->profile->ordering.'"'.$style.'>'; 
			
			$html .= '<div class="panel_header">';
			if(isset($this->profile->name)&& $this->profile->name!='')
				$html .='<span class="profile_name">'.$this->profile->name.'</span>';
            if(VaccountHelper::WidgetAccess('widget_acl', 'deleteaccess')) {
			$html .='<span class="delete-widget" onclick="delete_widget(this);" data-widget-id="'.$this->profile->id.'"><i class="fa fa-remove"></i></span>';}
			if(VaccountHelper::WidgetAccess('widget_acl', 'editaccess')) { 
			$html .='<span class="edit-widget" onclick="edit_widget(this);" data-widget-id="'.$this->profile->id.'"><i class="fa fa-edit"></i></span>';}
			
			$html .='</div><div class="profile_mid_data">';
             

			$single_data = vchart::widgetvalue($profile);
			$test_v = $profile->style_layout_editor;

			$object_array = get_object_vars($single_data);
			$object_array = count($object_array)>0?array_keys($object_array):array();
			$regex		= '/{(.*?)}/i';
			preg_match_all($regex, $profile->style_layout_editor, $matches, PREG_SET_ORDER);

			foreach ($matches as $match)
			{

				foreach ($single_data->data as $key=>$value)
				{
					if($value=='') $value=0;
					if(isset($profile->style_layout_editor) && $profile->style_layout_editor!=''&&$value!=null&&$match[1]==$key)
					{	
						$test_v = preg_replace("|$match[0]|", '<span class="innser_single_trigger" data-profile-ids="profile_'.$this->profile->id.'" id="inner_single_'.trim(preg_replace('/\s*\([^)]*\)/', '', $key)).'">'.$value.'</span>', $test_v, 1);
						
						$test_v = str_replace('{cur}', $this->configuration->currency, $test_v);
				
					}
					elseif(isset($profile->style_layout_editor) && $profile->style_layout_editor!=''&&$value==null){
						$test_v = preg_replace("|$match[0]|", '<span class="innser_single_trigger" data-profile-ids="profile_'.$this->profile->id.'" id="inner_single_'.trim(preg_replace('/\s*\([^)]*\)/', '', $key)).'">'.$value.'</span>', $test_v, 1);

						$test_v = str_replace('{cur}', $this->configuration->currency, $test_v);
					}
				}	
			}			
			$html .= $test_v;

			$html .='</div>';
			//$html .='</li>';

		}
		else if(isset($profile->style_layout) && $profile->style_layout=='listing_formate')
		{ 
			$k =1;
			if($profile->existing_database_table=='vData Profiles'){

				

						
			}
			else if($profile->existing_database_table=='vData Plugins'){
				
					
			}
			else {

				$li .= '<li class="common_profile_main listing_profiles listing_formate num'.($status_index);
				if($this->profile->datatype_option=='profile'){
					$li  .= ' profile_widget';
				}
				
				$li .= $box_class_name.'" data-ordering-profile="'.$this->profile->id.':'.$this->profile->ordering.'"'.$style.'>';
				
				$html .= '<div class="panel_header">';
				
				if(isset($this->profile->name)&& $this->profile->name!='')
					$html .= '<span class="profile_name">'.$this->profile->name.'</span>';
				 if(VaccountHelper::WidgetAccess('widget_acl', 'deleteaccess')) {
				 $html .= '<span class="delete-widget" onclick="delete_widget(this);" data-widget-id="'.$this->profile->id.'"><i class="fa fa-remove"></i></span>';}
				 if(VaccountHelper::WidgetAccess('widget_acl', 'editaccess')) {
				 $html .= '<span class="edit-widget" onclick="edit_widget(this);" data-widget-id="'.$this->profile->id.'"><i class="fa fa-edit"></i></span>';}
				
				$html .= '</div><div class="profile_mid_data">';

				$single_data = vchart::widgetvalue($profile);

				//Changed by Waseem
				$single_data = $single_data->data;
				$object_array = isset($single_data[0])?get_object_vars($single_data[0]):array();
				//$object_array = get_object_vars($single_data);
				$object_array = count($object_array)>0?array_keys($object_array):array();
				$html .= '<div class="listing_layout_others" style="height:'.($row_widget_height_value_chart-60).'px;">';
				$html .= '<table class="adminlist table table-hover listing_info listing_info_'.$this->profile->id.' profile_mid_data_inner" width="100%"><thead><tr>';
				
				for ($s = 0;$s<count($object_array);$s++)
				{
					$html .= '<th>'.$object_array[$s].'</th>';
				}			
				$html .= '</thead>';
				
				$g = 0;
				for ($l=0; $l < count( $single_data ); $l++)
				{
					$listing = $single_data[$l];
					$html .= '<tr>';
					foreach($listing as $listings) {
						$html .= '<td>'.$listings.'</td>';	
					}
					$html .= '</tr>';
				}
				$html .= '</table></div>';
				$html .= '</div>';
			}	
		}
		else
		{ 

			$li .= '<li class="common_profile_main num'.($status_index);
			if($this->profile->datatype_option=='profile')
			{
				$li .= ' profile_widget';
			}
			$li .= $box_class_name.'" data-ordering-profile="'.$this->profile->id.':'.$this->profile->ordering.'"'.$style.'>';
			
			$html .= '<div class="panel_header">';
			if(isset($this->profile->name)&& $this->profile->name!='')
				$html .= '<span class="profile_name">'.$this->profile->name.'</span>';
             if(VaccountHelper::WidgetAccess('widget_acl', 'deleteaccess')) {
			 $html .='<span class="delete-widget" onclick="delete_widget(this);" data-widget-id="'.$this->profile->id.'"><i class="fa fa-remove"></i></span>';}
			  if(VaccountHelper::WidgetAccess('widget_acl', 'editaccess')) {
			  $html .='<span class="edit-widget" onclick="edit_widget(this);" data-widget-id="'.$this->profile->id.'"><i class="fa fa-edit"></i></span>';}
			  
			$html .='</div><div class="profile_mid_data"><ul>';

			if($this->profile->datatype_option=='profile'){
				
				/* for ($i=0, $n=count( $this->items ); $i < $n; $i++)
				{

					$row = $this->items[$i];
					if((isset($profile->show_all_profile) && $profile->show_all_profile==1) || in_array($row->id,$profile->existing_database_table)) {

						$html .='<li class="common_profile num'.($i+1).' profile-section-listing" data-ordering-profile="'.$row->id.':'.$row->ordering.'" style="'. $sub_current_width.'">';

						if($row->iotype) : 
							$html .='<a href="index.php?option=com_vdata&view=profiles&task=export&profileid='.$row->id.'"><img src="'. Juri::root().'media/com_vdata/images/export_profile.png" alt="'.JText::_('CONFIG').'" /><span class="dashboard_profile_name">'.JText::_($row->title).'</span></a>';
						else : 
							$html .= '<a href="index.php?option=com_vdata&view=import&profileid='.$row->id.'"><img src="'.Juri::root().'media/com_vdata/images/import_profile.png" alt="'.JText::_('CONFIG').'" /><span class="dashboard_profile_name">'.JText::_($row->title).'</span></a>';
						endif; 


						$html .= '</li>';

						$k = 1 - $k;
					}
				} */
			}
			elseif($this->profile->datatype_option=='custom_plugin')
			{
				/* for ($i=0, $n=count( $this->plugins ); $i < $n; $i++)
				{

					$row = $this->plugins[$i];
					
					if((isset($profile->show_all_plugin) && $profile->show_all_plugin==1) || in_array($row->extension_id,$profile->existing_database_table)){

						$html .= '<li class="common_profile_plugin num'.($i+1).'" data-ordering-profile="'.$row->extension_id.':'.$row->name.'" style="'.$sub_current_width.'">';

						$html .= '<a href="index.php?option=com_vdata&view=profiles&extension_id='.$row->extension_id.'&task=edit&cid[]=0"><img src="media/com_vdata/images/export_profile.png" alt="'.JText::_('CONFIG').'" /><span class="dashboard_profile_name">'. JText::_($row->name).'</span></a>';

						$html .= '</li>';

						$k = 1 - $k;
					}
				} */
			}	
			else if($this->profile->datatype_option=='predefined')
			{ 

				if(isset($this->profile->chart_type) && $this->profile->chart_type!=''&& !in_array($profile->existing_database_table,$live_data_query)){
					
					$style= $row_widget_height_value_chart!=''?' style="height:'.($row_widget_height_value_chart-68).'px"':'';
					$script_data = vchart::draw_view_chart($this->profile);

					if($script_data->result=='success'){ 
						$html .=' <li class="chart_profile num'.($status_index).'" data-ordering-profile="'.$this->profile->id.':'.$this->profile->ordering.'" data-widget-label="'.strtolower(str_replace(" ","_",$profile->existing_database_table)).$this->profile->id.'" style="height:300px;">';
						$html .=' <div id="widget_'.$this->profile->id.'" class="widget_chart" data-profile-id="drawchart'.$this->profile->id.'" '.$style.'></div></li>';

						$script .= '<script type="text/javascript"> ';
						$script .= $script_data->scripts;
						$script .= '</script>';
					} 
					else if($script_data->result=='error')
					{
						$script .= $script_data->error; 
					} 
				}
				else if(in_array($profile->existing_database_table,$live_data_query)) {
					$script_data = vchart::draw_live_chart($this->profile);
					
					if($profile->existing_database_table=='Server Monitoring'){

						$html .= '<div id="tabs" class="widget_chart" data-profile-id="drawchart'.$this->profile->id.'">';
						$html .= '<ul>';
						$html .= '<li data-chart-id="'.$this->profile->id.'" data-show-chart="no"><a href="#tabs-1">Processes</a></li>';
						$html .= '<li data-chart-id="'.$this->profile->id.'" data-show-chart="yes" data-chart-for="ram"><a href="#tabs-2">RAM Status</a></li>';
						$html .= '<li data-chart-id="'.$this->profile->id.'" data-show-chart="yes" data-chart-for="cpu"><a href="#tabs-3">CPU Status</a></li>';
						$html .= '</ul>';
						$html .= '<div id="tabs-1">';
						$html .= '<table class="adminlist table table-striped table-hover" width="100%"><tr><th>Process Name</th><th>Process Type</th><th>Time Taken</th></tr></table></div><div id="tabs-2"  style="width: 100%; height: 80%;" data-chart-id="'.$this->profile->id.'" data-show-chart="yes"><div id="widget_sever_2"  style="width: 100%; height: 80%;"></div></div><div id="tabs-3" data-chart-id="'.$this->profile->id.'" data-show-chart="yes"><div id="widget_sever_3"  style="width: 100%; height: 80%;"></div></div></div>'; 

						$script .= '<script type="text/javascript"> ';
						$script .=  $script_data;
						$script .= '</script>';
					} else {

						if($script_data!='no_chart')
						{ 
							$html .= '<li class="chart_profile num'.($status_index).'"data-widget-label="'.strtolower(str_replace(" ","_",$profile->existing_database_table)).$this->profile->id.'" data-ordering-profile="'.$this->profile->id.':'.$this->profile->ordering.'" style="height:300px;">';
							$html .= '<div id="widget_'.$this->profile->id.'" class="widget_chart" data-profile-id="drawchart'.$this->profile->id.'"  style="width: 100%; height: 80%;"></div></li>';

							$script .=  '<script type="text/javascript"> ';
							$script .=  $script_data;
							$script .=  '</script>';
						}

					}		
				} 
				else { 
					$html .= ' <li class="chart_profile num'.($status_index).'" data-widget-label="'.strtolower(str_replace(" ","_",$profile->existing_database_table)).$this->profile->id.'" data-ordering-profile="'.$this->profile->id.':'.$this->profile->ordering.'" style="height:300px;">'.$this->log_information($this->profile,$response_data).'</li>';
				}	  

			}
			elseif($this->profile->datatype_option=='writequery'){
				$script_data = vchart::draw_view_chart($this->profile);
				if($script_data->result=='success')
				{ 
					$html .= ' <li class="chart_profile num'.($status_index).'" data-widget-label="'.strtolower(str_replace(" ","_",$profile->existing_database_table)).$this->profile->id.'" data-ordering-profile="'.$this->profile->id.':'.$this->profile->ordering.'">';
					$html .= ' <div id="widget_'.$this->profile->id.'" class="widget_chart" data-profile-id="drawchart'.$this->profile->id.'" style="width: 100%; height: 80%;"></div></li>';

					$script .= '<script type="text/javascript"> ';
					$script .=  $script_data->scripts;
					$script .=  '</script>';  
				}  
				else if($script_data->result=='error'){
					$script .= $script_data->error; 
				} 
			}

			$html .= '</ul></div>'; 
		}
		$data->html = $html;
		$data->script = $script;
		$data->li = $li;
		$data->style = $style_update;
		$data->result = 'success';
		jexit(json_encode($data));
	}
	
	function update_dashboard(){
	    $this->model = $this->getModel('vbizz');
		//$this->items = $this->model->getItems();
		$this->profiles = $this->model->getProfiles();
		//$this->plugins = $this->model->getPlugins();
		
		$data = new stdClass();
		$data->result = 'error';
		ob_start();
		
		require(JPATH_SITE.'/components/com_vbizz/views/vbizz/tmpl/default.php');
		require_once JPATH_COMPONENT.'/operating/drawchart.php';
		$document =  JFactory::getDocument();
		$document->addScript("https://www.google.com/jsapi");
		$html = mb_convert_encoding(ob_get_contents(), 'UTF-8');
		
		ob_end_clean();
		
		$data->html = $html;
		$data->result = 'success';
		jexit(json_encode($data));	
		
	}
	
	//show live chart widget
	function live_chart_data()
	{
		$data = new stdClass();
		$data->result = 'error';
		$dbs = JFactory::getDbo();
		require_once JPATH_ADMINISTRATOR.'/components/com_vbizz/classes/oprating_lib.php';
		$output = new operating();
		$output->loading();

		//echo $rx[] = @file_get_contents("/sys/class/net/$int/statistics/rx_bytes");
		$host =  'www.okhlites.in';
		$port = 80;
		$timeout = 16;

		$start_time = microtime(TRUE);
		$status = fsockopen($host, $port, $errno, $errstr, $timeout); 


		$end_time = microtime(TRUE);
		$time_taken = $end_time - $start_time;
		$time_taken = round($time_taken,5);

		$output = new WebpageXML(false, null);
		if (defined('PSI_JSON_ISSUE') && (PSI_JSON_ISSUE)) {
			$json = json_encode(simplexml_load_string(str_replace(">", ">\n", $output->getXMLString()))); // solving json_encode issue
		} else {
			$json = json_encode(simplexml_load_string($output->getXMLString()));
		}

		$html = $time_taken;
		$data->html = $html*1000;
		$data->json = str_replace('@attributes','attributes',$json);
		$data->result = 'success';
		jexit(json_encode($data));

	}
	
	//log widget info
	function log_information($q,$response_data){
		$query = '';
		$opton_value = json_decode($q->detail);
		$db = JFactory::getDbo();
		for($v=0;$v<count($response_data);$v++){
			$response_datas = $response_data[$v];
			if($opton_value->existing_database_table==$response_datas->label)
				$query =$response_datas->value;
		}
		
		if(!empty($query)){
			preg_match_all('/{tablename\s(.*?)}/i', $query, $matches);
			preg_match_all('/{as\s(.*?)}/i', $query, $match);
			$matches_s = $matches[1]; 
			$text = $query;
			for($r=0;$r<count($matches_s);$r++){
				$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
			}
			$query = str_replace('{','',str_replace('}','',$text)); 

			$sql = $query;
			$db->setQuery($sql);
			$datas = $db->loadObjectList();
			$html = '<table class="adminlist table table-hover" width="100%"><th>No</th><th>Table Name</th><th>Message</th>';
			for($v=0;$v<count($datas)&&$v<6;$v++){
				$data = $datas[$v];
				$html .= '<tr><td>'.$data->id.'</td><td>'.$data->table.'</td><td>'.$data->message.'</td><tr>'; 
			}
			$html .= '</table>';

			return $html;
		}

	}
  
	function newbandwidth()
	{
		$int="eth0"; 
		echo is_file("/sys/class/net/eth0/statistics/rx_bytes");
		echo @file_get_contents("/sys/class/net/".$int."/statistics/rx_bytes");
		jexit();
	}
	
	//check user login status
	function check_login_status(){
		$obj = new stdClass();
		$obj->result='error';
		$state = JFactory::getUser()->id>0 ? true:false;
		$obj->result = 'success';
		$obj->state = $state;
		jexit(json_encode($obj));
	}
	
	//update search keyword in database
	function update_selected_keyword()
	{
		$keyword = JFactory::getApplication()->input->get('keyword', '','RAW');
		$type = JFactory::getApplication()->input->get('type', '');
		$url = 'http://www.joomlawings.com/index.php';
		$obj = new stdClass();
		$obj->result = 'error';
		$ch = curl_init(); 

		$postdata = array("keyword"=>$keyword, "type"=>$type, "option"=>"com_vreview", "task"=>"update_keyword", "token"=>JSession::getFormToken());
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$result = json_decode(curl_exec($ch));
		$obj->result = $result->result;
		jexit(json_encode($obj));
		
	}
	
	
	//update ordering of widget
	function update_profile_ordering()
	{
		$this->model = $this->getModel('vbizz');
		
		$this->model->updateordering();

	}
	
   function publish()
	{
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=vbizz&layout=widgetlisting') );

		// Initialize variables
		$db			= JFactory::getDBO();
		$user		= JFactory::getUser();
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= JRequest::getCmd( 'task' );
		$publish	= ($task == 'publish');
		$n			= count( $cid );

		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'NO_ITEM_SELECTED' ) );
		}

		JArrayHelper::toInteger( $cid );
		$cids = implode( ',', $cid );

		$query = 'UPDATE #__vbizz_widget'
		. ' SET published = ' . (int) $publish
		. ' WHERE id IN ( '. $cids .' )'
		; 
		$db->setQuery( $query );
		if (!$db->query()) {
			return JError::raiseWarning( 500, $row->getError() );
		}
		$this->setMessage( JText::sprintf( $publish ? JText::_('WIDGET_PUBLISHED') : JText::_('WIDGET_UNPUBLISHED'), $n ) );

	}
	function remove()
	{
		$model = $this->getModel('vbizz');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'WIDGET_REMOVED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=vbizz&layout=widgetlisting'), $msg );
	}
	public function checkin()
	{ 
		// Check for request forgeries
		//JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $this->model = $this->getModel('vbizz');
		
			$msg = JText::plural('COM_CHECKIN_N_ITEMS_CHECKED_IN', $this->model->checkin());  
		
		$this->setRedirect(JRoute::_("index.php?option=com_vbizz&view=vbizz"),$msg);
	}
}