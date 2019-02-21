<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');



JHTML::_('behavior.tooltip');
JHtml::_('behavior.modal');

$db = JFactory::getDbo();

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

/* $query = 'DELETE FROM table_name where created_by=804';
$db->setQuery($query);
$db->execute();
jexit(); */  


$query = 'SELECT count(*) from #__vbizz_employee where userid='.$userId;
$db->setQuery($query);
$isEmployee = $db->loadResult();

$query = 'SELECT count(*) from #__vbizz_users where ownerid='.$userId;
$db->setQuery($query);
$isOwner = $db->loadResult();

$query = 'SELECT group_id from #__user_usergroup_map where user_id = '.$userId;
$db->setQuery($query);
$group_id = $db->loadResult();

if(VaccountHelper::checkOwnerGroup())
{
	$ownerId = $userId;
} else {
	$query = 'SELECT ownerid from #__vbizz_users where userid = '.$userId;
	$db->setQuery($query);
	$ownerId = $db->loadResult();
}  
   
$input = JFactory::getApplication()->input;
$view = $input->get('view', '', '');

$query = 'SELECT * from #__vbizz_configuration limit 1';
$db->setQuery($query);
$main_config = $db->loadObject();

$query = 'SELECT * from #__vbizz_config WHERE created_by='.$db->quote($ownerId);
$db->setQuery($query);
$config = $db->loadObject();   
$layout = JRequest::getCmd('layout', '');
$tran_registry = new JRegistry;
$tran_registry->loadString($config->transaction_acl);
$config->transaction_acl = $tran_registry;

$income_registry = new JRegistry;    
$income_registry->loadString($config->income_acl);
$config->income_acl = $income_registry;

$expense_registry = new JRegistry;
$expense_registry->loadString($config->expense_acl);
$config->expense_acl = $expense_registry;

$type_registry = new JRegistry;
$type_registry->loadString($config->type_acl);
$config->type_acl = $type_registry;

$mode_registry = new JRegistry;
$mode_registry->loadString($config->mode_acl);
$config->mode_acl = $mode_registry;

$account_registry = new JRegistry;
$account_registry->loadString($config->account_acl);
$config->account_acl = $account_registry;

$tax_registry = new JRegistry;
$tax_registry->loadString($config->tax_acl);
$config->tax_acl = $tax_registry;

$discount_registry = new JRegistry;
$discount_registry->loadString($config->discount_acl);
$config->discount_acl = $discount_registry;

$import_registry = new JRegistry;
$import_registry->loadString($config->import_acl);
$config->import_acl = $import_registry;

$customer_registry = new JRegistry;
$customer_registry->loadString($config->customer_acl);
$config->customer_acl = $customer_registry;

$vendor_registry = new JRegistry;
$vendor_registry->loadString($config->vendor_acl);
$config->vendor_acl = $vendor_registry;

$employee_registry = new JRegistry;
$employee_registry->loadString($config->employee_acl);
$config->employee_acl = $employee_registry;

$empmanage_registry = new JRegistry;
$empmanage_registry->loadString($config->employee_manage_acl);
$config->employee_manage_acl = $empmanage_registry;

$imp_shd_task_acl = new JRegistry;
$imp_shd_task_acl->loadString($config->imp_shd_task_acl);
$config->imp_shd_task_acl = $imp_shd_task_acl;

$recur_registry = new JRegistry;
$recur_registry->loadString($config->recur_acl);
$config->recur_acl = $recur_registry;

$invoice_registry = new JRegistry;
$invoice_registry->loadString($config->etemp_acl);
$config->etemp_acl = $invoice_registry;

$project_registry = new JRegistry;
$project_registry->loadString($config->project_acl);
$config->project_acl = $project_registry;

$ptask_registry = new JRegistry;
$ptask_registry->loadString($config->project_task_acl);
$config->project_task_acl = $ptask_registry;

$inv_registry = new JRegistry;
$inv_registry->loadString($config->invoice_acl);
$config->invoice_acl = $inv_registry;

$quotes_registry = new JRegistry;
$quotes_registry->loadString($config->quotes_acl);
$config->quotes_acl = $quotes_registry;

$support_registry = new JRegistry;
$support_registry->loadString($config->support_acl);
$config->support_acl = $support_registry;

$bug_registry = new JRegistry;
$bug_registry->loadString($config->bug_acl);
$config->bug_acl = $bug_registry;

$attendance_registry = new JRegistry;
$attendance_registry->loadString($config->attendance_acl);
$config->attendance_acl = $attendance_registry;

$milestone_registry = new JRegistry;
$milestone_registry->loadString($config->milestone_acl);
$config->milestone_acl = $milestone_registry;    

$item_registry = new JRegistry;
$item_registry->loadString($config->item_acl);
$config->item_acl = $item_registry;  

$tran_access = $config->transaction_acl->get('access_interface');  
if($tran_access) {
	$transaction_acl = false;
	foreach($groups as $group) { 
		if(in_array($group,$tran_access))
		{
			$transaction_acl=true;
			break;
		}
	}
} else {
	$transaction_acl=true;
}


$type_access = $config->type_acl->get('access_interface');
if($type_access) {
	$type_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$type_access))
		{
			$type_acl=true;
			break;
		}
	}
} else {
	$type_acl=true;
}


$mode_access = $config->mode_acl->get('access_interface');
if($mode_access) {
$mode_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$mode_access))
		{
			$mode_acl=true;
			break;
		}
	} 
}else {
	$mode_acl=true;
}


$account_access = $config->account_acl->get('access_interface');
if($account_access) {
	$account_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$account_access))
		{
			$account_acl=true;
			break;
		}
	}
}else {
	$account_acl=true;
}


$tax_access = $config->tax_acl->get('access_interface');
if($tax_access) {
	$tax_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$tax_access))
		{
			$tax_acl=true;
			break;
		}
	}
}else {
	$tax_acl=true;
}

$discount_access = $config->discount_acl->get('access_interface');
if($discount_access) {
	$discount_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$discount_access))
		{
			$discount_acl=true;
			break;
		}
	}
}
else {
	$discount_acl=true;
}

$import_access = $config->import_acl->get('access_interface');
if($import_access) {
	$import_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$import_access))
		{
			$import_acl=true;
			break;
		}
	}
} else {
	$import_acl=true;
}

$customer_access = $config->customer_acl->get('access_interface');
if($customer_access) {
	$customer_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$customer_access))
		{
			$customer_acl=true;
			break;
		}
	}
} else {
	$customer_acl=true;
}


$vendor_access = $config->vendor_acl->get('access_interface');
if($vendor_access) {
	$vendor_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$vendor_access))
		{
			$vendor_acl=true;
			break;
		}
	}
} else {
	$vendor_acl=true;
}

$employee_access = $config->employee_acl->get('access_interface');
if($employee_access) {
	$employee_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$employee_access))
		{
			$employee_acl=true;
			break;
		}
	}
} else {
	$employee_acl=true;
}


$imp_shd_task_access = $config->imp_shd_task_acl->get('access_interface');
if($imp_shd_task_access) {
	$imp_shd_task_acl_access = false;
	foreach($groups as $group) {
		if(in_array($group,$imp_shd_task_access))
		{
			$imp_shd_task_acl_access=true;
			break;
		}
	}
} else {
	$imp_shd_task_acl_access=true;
}

$recur_access = $config->recur_acl->get('access_interface');
if($recur_access) {
	$recur_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$recur_access))
		{
			$recur_acl=true;
			break;
		}
	}
} else {
	$recur_acl=true;
}

$invoice_access = $config->etemp_acl->get('access_interface');
if($invoice_access) {
	$etemp_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$invoice_access))
		{
			$etemp_acl=true;
			break;
		}
	}
}
else {
	$etemp_acl=true;
}

$project_access = $config->project_acl->get('access_interface');
if($project_access) {
	$project_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$project_access))
		{
			$project_acl=true;
			break;
		}
	}
} else {
	$project_acl=true;
}

$ptask_access = $config->project_task_acl->get('access_interface');
if($ptask_access) {
	$ptask_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$ptask_access))
		{
			$ptask_acl=true;
			break;
		}
	}
} else {
	$ptask_acl=true;
}

$milestone_access = $config->milestone_acl->get('access_interface');
if($milestone_access) {
	$milestone_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$milestone_access))
		{
			$milestone_acl=true;
			break;
		}
	}
} else {
	$milestone_acl=true;
}

$inv_access = $config->invoice_acl->get('access_interface');
if($inv_access) {
	$inv_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$inv_access))
		{
			$inv_acl=true;
			break;
		}
	}
} else {
	$inv_acl=true;
}

$quotes_access = $config->quotes_acl->get('access_interface');
if($quotes_access) {
	$quotes_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$quotes_access))
		{
			$quotes_acl=true;
			break;
		}
	}
} else {
	$quotes_acl=true;
}

$empmanage_access = $config->employee_manage_acl->get('access_interface');
if($empmanage_access) {
	$empmanage_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$empmanage_access))
		{
			$empmanage_acl=true;
			break;
		}
	}
} else {
	$empmanage_acl=true;
}

$support_access = $config->support_acl->get('access_interface');
if($support_access) {
	$support_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$support_access))
		{
			$support_acl=true;
			break;
		}
	}
} else {
	$support_acl=true;
}

$bug_access = $config->bug_acl->get('access_interface');
if($bug_access) {
	$bug_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$bug_access))
		{
			$bug_acl=true;
			break;
		}
	}
} else {
	$bug_acl=true;
}

$attendance_access = $config->attendance_acl->get('access_interface');
if($attendance_access) {
	$attendance_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$attendance_access))
		{
			$attendance_acl=true;
			break;
		}
	}
}else {
	$attendance_acl=true;  
}
$check_view = JRequest::getCmd('view','vbizz');
$first_menu = array('income','invoices','quotes','customer');
$second_menu = array('expense','invoicesexpense','quotesexpense','vendor');
$third_menu = array('projects','ptask');
$fourth_menu = array('accounts','banking','statement','trial','deposit','withdrawl');
$fifth_menu = array('support','bug','mail');
$sixth_menu = array('employee','attendance','edept','edesg','leaves','payheads','commission');
$seventh_menu = array('items','stock','assets','category','itemqueue');
$eighth_menu = array('config','etemp','users','notes','tran','mode','import','imtask','exptask','recurr', 'tax', 'discount');
?>  

<script type="text/javascript">
jQuery(document).ready(function(e) {

	var window_height = jQuery(document).height();
	var header = jQuery('.header').height();
	var copyright = jQuery('.copyright').height();
	var new_height = window_height -(copyright+header-5);
	jQuery('.cpanel').css('min-height',new_height);
	//jQuery("a.menu-heading:first").addClass('active');
jQuery('.desktop_menu li.has-sub>a').on('click', function(){
jQuery(this).removeAttr('href');
var element = jQuery(this).parent('li');
if (element.hasClass('open')) {
element.removeClass('open');
element.find('li').removeClass('open');
element.find('li').removeClass('opendiv');
element.find('ul').slideUp();
}
else {
element.addClass('open');
element.children('ul').slideDown();
element.siblings('li').children('ul').slideUp();
element.siblings('li').removeClass('open');
element.siblings('li').find('li').removeClass('open');
element.find('li').removeClass('opendiv');
element.siblings('li').find('ul').slideUp();
}
});
});

</script>

<script>
jQuery(function() {
	jQuery(document).on('click','.toggle',function() {
		jQuery( ".responsive_lisy" ).slideToggle("slow");
	});
});
</script>    

<div class="front-page">
<?php if(isset($main_config->showheader) && $main_config->showheader==1) { ?>
<div class="vbizz-top">
<div class="vbizz-left"><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=vbizz'); ?>"><img src="components/com_vbizz/assets/images/logo.png" alt="vbizz"> <?php echo JText::_('COM_VBIZZ'); ?></a></div>
<div class="vbizz-right">
<div class="moduletable mod_vbizz_search"><?php
jimport('joomla.application.module.helper');
$module = &JModuleHelper::getModule('mod_vbizz_search');
echo JModuleHelper::renderModule($module);
?>
</div>
<div class="moduletable mod_vbizz_notify"><?php
jimport('joomla.application.module.helper');
$module = &JModuleHelper::getModule('mod_vbizz_notify');
echo JModuleHelper::renderModule($module);
?>
</div>
<div class="moduletable mod_vbizz_login"><?php
jimport('joomla.application.module.helper');
$module = &JModuleHelper::getModule('mod_vbizz_login');
echo JModuleHelper::renderModule($module);
?>
</div>
</div>
</div>
<?php } ?>
<div id="vbizz">
<div class="cpanel-left sidebar">
<div class="mobile_menu cpanel">
<div class="desktop_menu">
<a class="toggle" href="#"><i class="fa fa-navicon"></i></span></a>
<ul class="responsive_lisy" style="display:none;">
<li class="m_dashboard"><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=vbizz'); ?>" <?php if($view=="vbizz") { ?> class="active" <?php } ?>><span><i class="fa fa-th-large"></i> <?php echo JText::_('DASHBOARD'); ?></span></a></li>

<li class="has-sub<?php if(in_array($view, $first_menu)){ echo ' opendiv'; }?>"><a class="menu-heading" href="#"><span><i class="fa fa-plus"></i> <?php echo JText::_('INCOME'); ?></span></a>
	<ul class="childnav">
	
		<?php if(VaccountHelper::WidgetAccess('income_acl', 'access_interface')) { ?>
		<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=income'); ?>" <?php if($view=="income") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('SALESORDER'); ?></span></a></li>
		<?php } ?>
	
		<?php if($inv_acl) { ?> 
		<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=invoices'); ?>" <?php if($view=="invoices") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('INVOICES'); ?></span></a></li>
		<?php } ?>
	
		<?php if($quotes_acl) { ?>
		<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=quotes'); ?>" <?php if($view=="quotes") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('QUOTATION'); ?></span></a></li>
		<?php } ?>
	
		<?php if($customer_acl && !VaccountHelper::checkClientGroup() && $config->enable_cust==1 && $main_config->enable_cust==1) { ?>
		
		<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=customer'); ?>" <?php if($view=="customer") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo $config->customer_view; ?></span></a></li>
		<?php } ?>
		
		
	</ul>
</li>   
					
<li class="has-sub<?php if(in_array($view, $second_menu)){ echo ' opendiv'; }?>"><a class="menu-heading" href="#"><span><i class="fa fa-minus"></i> <?php echo JText::_('EXPENSE'); ?></span></a>
	<ul class="childnav">
	<?php if(VaccountHelper::WidgetAccess('expense_acl', 'access_interface')) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=expense'); ?>" <?php if($view=="expense") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('PURCHASESORDER'); ?></span></a></li>
	<?php } ?>
	<?php if($inv_acl) { ?> 
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=invoicesexpense'); ?>" <?php if($view=="invoicesexpense") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('INVOICES'); ?></span></a></li>
	<?php } ?>
	
	<?php if($quotes_acl) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=quotesèxpense'); ?>" <?php if($view=="quotesèxpense") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('QUOTATION'); ?></span></a></li>
	<?php } ?>
	
	<?php if($vendor_acl && !VaccountHelper::checkEmployeeGroup() && $config->enable_vendor==1 && $main_config->enable_vendor==1) { ?>                    
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=vendor'); ?>" <?php if($view=="vendor") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo $config->vendor_view; ?></span></a></li>
	
	<?php } ?>
	
	</ul>
</li>
	<?php if($main_config->enable_project==1 && ($project_acl || $ptask_acl)) { ?>				
<li class="has-sub<?php if(in_array($view, $third_menu)){ echo ' opendiv'; }?>"><a class="menu-heading" href="#"><span><i class="fa fa-file-text-o"></i> <?php echo JText::_('PROJECTS'); ?></span></a>
	<ul class="childnav">
	
	<?php if($project_acl) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=projects'); ?>" <?php if($view=="projects") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('MANAGEPROJECTS'); ?></span></a></li>
	<?php } ?>
	
	<?php if( ($ptask_acl) && ($isEmployee || VaccountHelper::checkOwnerGroup()) ) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=ptask'); ?>" <?php if($view=="ptask") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('MANAGE_TASK'); ?></span></a></li>
	<?php } ?>
	
	</ul>
</li>
	<?php } ?>	
<?php if($config->enable_account==1 && $main_config->enable_account==1) { ?>	
<li class="has-sub<?php if(in_array($view, $fourth_menu)){ echo ' opendiv'; }?>"><a class="menu-heading" href="#"><span><i class="fa fa-bank"></i> <?php echo JText::_('BANKING'); ?></span></a>
	<ul class="childnav">
	
	<?php if($account_acl) { ?>
	<?php if($config->enable_account==1) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=accounts'); ?>" <?php if($view=="accounts") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('ACCOUNTS'); ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=banking'); ?>" <?php if($view=="banking") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('TRANSFER'); ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=statement'); ?>" <?php if($view=="statement") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('STATEMENT'); ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=trial'); ?>" <?php if($view=="trial") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('TRIAL_BALANCE'); ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=deposit'); ?>" <?php if($view=="deposit") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('DEPOSITS'); ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=withdrawl'); ?>" <?php if($view=="withdrawl") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('WITHDRAWL'); ?></span></a></li>
	
	<?php } ?>
	<?php } ?>
	
	</ul>
</li>
<?php } ?>	
<?php if($bug_acl || ($support_acl && $config->enable_cust && $main_config->enable_cust==1)) { ?>
<li class="has-sub<?php if(in_array($view, $fifth_menu)){ echo ' opendiv'; }?>"><a class="menu-heading" href="#"><span><i class="fa fa-envelope"></i> <?php echo JText::_('INTERACTIONS'); ?></span></a>
	<ul class="childnav">
	
	<?php if($support_acl) { ?>  
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=support'); ?>" <?php if($view=="support") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('SUPPORT_FORUM'); ?></span></a></li>
	<?php } ?>
	
	<?php if($bug_acl) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=bug'); ?>" <?php if($view=="bug") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('BUG_TRACKER'); ?></span></a></li>
	<?php } ?>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=mail'); ?>" <?php if($view=="mail") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('VACCOUNT_MAIL'); ?></span></a></li>
	
	</ul>      
</li>
<?php } ?>
<?php if($employee_acl && $config->enable_employee==1 && $main_config->enable_employee==1) { ?>
<li class="has-sub<?php if(in_array($view, $sixth_menu)){ echo ' opendiv'; }?>"><a class="menu-heading" href="#"><span><i class="fa fa-users"></i> <?php echo JText::_('EMPLOYEES'); ?></span></a>
	<ul class="childnav">
	<?php if($employee_acl && $config->enable_employee==1 && VaccountHelper::checkOwnerGroup()) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=employee'); ?>" <?php if($view=="employee") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('MANAGEEMPLOYEES'); ?></span></a></li>
	
	<?php }  else if($isEmployee) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=leavecard'); ?>" <?php if($view=="leavecard") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('MY_LEAVE_CARD'); ?></span></a></li>
	<?php } ?>
	<?php if((int)$config->employeecommission == 1) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=commission'); ?>" <?php if($view=="commission") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('EMPLOYEE_COMMISSION'); ?></span></a></li>
	<?php } ?>
	<?php if($attendance_acl) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=attendance'); ?>" <?php if($view=="attendance") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('ATTENDANCE'); ?></span></a></li>
	<?php } ?>
	
	
	<?php if($empmanage_acl) { ?> 
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=edept'); ?>" <?php if($view=="edept") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('EMPLOYEE_DEPT'); ?></span></a>
	</li>

	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=edesg'); ?>" <?php if($view=="edesg") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('EMPLOYEE_DESG'); ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=leaves'); ?>" <?php if($view=="leaves") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('LEAVE_TYPE'); ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=payheads'); ?>" <?php if($view=="payheads") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('PAYHEADS'); ?></span></a></li>
	
	
	<?php } ?>
	
	</ul>
</li>
<?php } ?>
<?php if($transaction_acl && $config->enable_items==1 && $main_config->enable_items==1) { ?>
<li class="has-sub<?php if(in_array($view, $seventh_menu)){ echo ' opendiv'; }?>"><a class="menu-heading" href="#"><span><i class="fa fa-file-text"></i> <?php echo JText::_('INVENTORY'); ?></span></a>
	<ul class="childnav">
	
	<?php if($config->enable_items==1) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=category'); ?>" <?php if($view=="category") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('COM_VBIZZ_CATEGORY'); ?></span></a></li>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=itemqueue'); ?>" <?php if($view=="itemqueue") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('COM_VBIZZ_ITEMS_IN_QUEUE'); ?></span></a></li>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=items'); ?>" <?php if($view=="items") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo $config->item_view; ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=stock'); ?>" <?php if($view=="stock") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('MANAGE_STOCK'); ?></span></a></li><?php } ?>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=assets'); ?>" <?php if($view=="assets") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('ASSETS'); ?></span></a></li>
	
	</ul>
</li>
<?php } ?>

<li><a class="menu-heading" href="<?php echo JRoute::_('index.php?option=com_vbizz&view=reports'); ?>" <?php if($view=="reports") { ?> class="active" <?php } ?>><span><i class="fa fa-clipboard"></i> <?php echo JText::_('REPORTS'); ?></span></a></li>

<li class="has-sub<?php if(in_array($view, $eighth_menu)){ echo ' opendiv'; }?>"><a class="menu-heading" href="#"><span><i class="fa fa-gears"></i> <?php echo JText::_('SETTINGS'); ?></span></a>
	<ul class="childnav">
	<?php if(VaccountHelper::checkOwnerGroup()) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=config'); ?>" <?php if($view=="config") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('CONFIGURATION'); ?></span></a></li>
	<?php } ?>
	
	<?php if(VaccountHelper::checkOwnerGroup()) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=etemp'); ?>" <?php if($view=="etemp") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('EMAIL_TEMPLATES'); ?></span></a></li>
	<?php } ?>
	
	<?php if($userId) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=users'); ?>" <?php if($view=="users") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('USER_PROFILE'); ?></span></a></li>

	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=notes'); ?>" <?php if($view=="notes") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('ACTIVITY_LOG'); ?></span></a></li>
	<?php } ?>
	<?php if($type_acl) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=tran'); ?>" <?php if($view=="tran") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo $config->type_view; ?></span></a></li>
	<?php } ?>

	<?php if($mode_acl) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=mode'); ?>" <?php if($view=="mode") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('TRANSACTION_MODE'); ?></span></a></li>
	<?php } ?>
	
	<?php if($import_acl) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=import'); ?>" <?php if($view=="import") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('IMPORT_EXPORT'); ?></span></a></li>
	<?php } ?>
	
	<?php if($imp_shd_task_acl_access) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=imtask'); ?>" <?php if($view=="imtask") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('IMPORT_TASK'); ?></span></a></li>
					
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=exptask'); ?>" <?php if($view=="exptask") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('EXPORT_TASK'); ?></span></a></li>
	<?php } ?>
	
	<?php if($recur_acl && $config->enable_recur==1 && $main_config->enable_recur==1) { ?>
	<?php if($config->enable_recur==1) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=recurr'); ?>" <?php if($view=="recurr") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('RECURRING_TRANSACTION'); ?></span></a></li>
	<?php } ?>
	<?php } ?>
	
	</ul>
<li>

<!--<?php if($config->enable_tax_discount==1 && $main_config->enable_tax_discount==1) { ?>
<?php if($tax_acl) { ?>
<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=tax'); ?>" <?php if($view=="tax") { ?> class="active" <?php } ?>><span class="menu-heading"><i class="fa"></i> <?php echo JText::_('TAX'); ?></span></a></li>
<?php } ?>

<?php if($discount_acl) { ?>
<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=discount'); ?>" <?php if($view=="discount") { ?> class="active" <?php } ?>><span class="menu-heading"><i class="fa"></i> <?php echo JText::_('DISCOUNT'); ?></span></a></li>
<?php } ?>
<?php } ?>-->
</ul>
</div>
</div>
	
	
<div class="desktop cpanel">
<div class="desktop_menu">
<ul class="side_menu_items">
<li class="m_dashboard"><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=vbizz'); ?>" <?php if($view=="vbizz") { ?> class="active" <?php } ?>><span><i class="fa fa-th-large"></i> <?php echo JText::_('DASHBOARD'); ?></span></a></li>
<?php if(!VaccountHelper::checkClientGroup()) { ?>
<li class="has-sub<?php if(in_array($view, $first_menu)){ echo ' opendiv'; }?>"><a class="menu-heading" href="#"><span><i class="fa fa-plus"></i> <?php echo JText::_('INCOME'); ?></span><?php $income_notification = VaccountHelper::getIncomeNotification();if($income_notification>0)echo '<span class="count-note counting_display">'.$income_notification.'</span>';?><span class="selected"></span></a>
	<ul class="childnav">
	
		<?php if(VaccountHelper::WidgetAccess('income_acl', 'access_interface')) { ?>
		<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=income'); ?>" <?php if($view=="income") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('SALESORDER'); ?></span></a></li>
		<?php } ?>
	
		<?php if($inv_acl) { ?>   
		<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=invoices'); ?>" <?php if($view=="invoices") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('INVOICES'); ?></span><?php $income_notification = VaccountHelper::getIncomeNotificationInvoice();if($income_notification>0)echo '<span class="count-note counting_display">'.$income_notification.'</span>';?></a></li> 
		<?php } ?>
	
		<?php if($quotes_acl) { ?>
		<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=quotes'); ?>" <?php if($view=="quotes") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('QUOTATION'); ?></span><?php $income_notification = VaccountHelper::getIncomeNotificationQuote();if($income_notification>0)echo '<span class="count-note counting_display">'.$income_notification.'</span>';?></a></li>  
		<?php } ?>
	
		<?php if($customer_acl && !VaccountHelper::checkClientGroup()) { ?>
		<?php if($config->enable_cust==1 && $main_config->enable_cust==1) { ?>
		<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=customer'); ?>" <?php if($view=="customer") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo $config->customer_view; ?></span></a></li>
		<?php } ?>
		<?php } ?>
		<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=leads'); ?>" <?php if($view=="leads") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('LEADS'); ?></span></a></li>  
	</ul>
</li>
<?php } 

?>   
<?php if(!VaccountHelper::checkVenderGroup()) {  ?>  					
<li class="has-sub<?php if(in_array($view, $second_menu)){ echo ' opendiv'; }?>"><a class="menu-heading" href="#"><span><i class="fa fa-minus"></i> <?php echo JText::_('EXPENSE'); ?></span><?php $income_notification = VaccountHelper::getExpenseNotification();if($income_notification>0)echo '<span class="count-note counting_display">'.$income_notification.'</span>';?><span class="selected"></span></a>
	<ul class="childnav"> 
	<?php if(VaccountHelper::WidgetAccess('expense_acl', 'access_interface')) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=expense'); ?>" <?php if($view=="expense") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('PURCHASESORDER'); ?></span></a></li>
	<?php } ?>
	<?php if($inv_acl) { ?>      
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=invoicesexpense'); ?>" <?php if($view=="invoicesexpense") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('INVOICES'); ?></span><?php $income_notification = VaccountHelper::getExpenseNotificationInvoiceexpense();if($income_notification>0)echo '<span class="count-note counting_display">'.$income_notification.'</span>';?></a></li>
	<?php } ?>
	
	<?php if($quotes_acl) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=quotesexpense'); ?>" <?php if($view=="quotesexpense") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('QUOTATION'); ?></span><?php $income_notification = VaccountHelper::getExpenseNotificationQuoteexpense();if($income_notification>0)echo '<span class="count-note counting_display">'.$income_notification.'</span>';?></a></li>
	<?php } ?>
	
	<?php if($vendor_acl && !VaccountHelper::checkVenderGroup() && $main_config->enable_vendor==1) {  ?>                    
	<?php if($config->enable_vendor==1) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=vendor'); ?>" <?php if($view=="vendor") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo $config->vendor_view; ?></span></a></li>
	<?php } ?>
	<?php } ?>

	</ul>
</li>  
<?php } ?>	
<?php if(!VaccountHelper::checkVenderGroup() && $main_config->enable_project==1 && ($project_acl || $ptask_acl)) {  ?>  				
<li class="has-sub<?php if(in_array($view, $third_menu)){ echo ' opendiv'; }?>"><a class="menu-heading" href="#"><span><i class="fa fa-file-text-o"></i> <?php echo JText::_('PROJECTS'); ?></span><span class="selected"></span></a>
	<ul class="childnav">
	
	<?php if($project_acl) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=projects'); ?>" <?php if($view=="projects") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('MANAGEPROJECTS'); ?></span></a></li>
	<?php } ?>
	
	<?php if($ptask_acl) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=ptask'); ?>" <?php if($view=="ptask") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('MANAGE_TASK'); ?></span></a></li>
	<?php } ?>
	
	</ul>
</li>
<?php } ?>	
	<?php if($account_acl && $config->enable_account==1 && $main_config->enable_account==1) { ?>				
<li class="has-sub<?php if(in_array($view, $fourth_menu)){ echo ' opendiv'; }?>"><a class="menu-heading" href="#"><span><i class="fa fa-bank"></i> <?php echo JText::_('BANKING'); ?></span><span class="selected"></span></a>
	<ul class="childnav">
	
	
	<?php if($config->enable_account==1) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=accounts'); ?>" <?php if($view=="accounts") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('ACCOUNTS'); ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=banking'); ?>" <?php if($view=="banking") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('TRANSFER'); ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=statement'); ?>" <?php if($view=="statement") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('STATEMENT'); ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=trial'); ?>" <?php if($view=="trial") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('TRIAL_BALANCE'); ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=deposit'); ?>" <?php if($view=="deposit") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('DEPOSITS'); ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=withdrawl'); ?>" <?php if($view=="withdrawl") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('WITHDRAWL'); ?></span></a></li>
	
	<?php } ?>
	
	
	</ul>
</li>
<?php } ?>
<?php if($bug_acl || ($support_acl && $config->enable_cust && $main_config->enable_cust==1)) { ?>  
<li class="has-sub<?php if(in_array($view, $fifth_menu)){ echo ' opendiv'; }?>"><a class="menu-heading" href="#"><span><i class="fa fa-envelope"></i> <?php echo JText::_('INTERACTIONS'); ?></span><span class="selected"></span></a>
	<ul class="childnav">
	
	<?php if($support_acl) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=support'); ?>" <?php if($view=="support") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('SUPPORT_FORUM'); ?></span></a></li>
	<?php } ?>
	
	<?php if($bug_acl) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=bug'); ?>" <?php if($view=="bug") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('BUG_TRACKER'); ?></span></a></li>
	<?php } ?>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=mail'); ?>" <?php if($view=="mail") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('VACCOUNT_MAIL'); ?></span></a></li>
	
	</ul>
</li>
<?php } ?>
<?php if($employee_acl && $config->enable_employee==1 && $main_config->enable_employee==1) { ?>
<li class="has-sub<?php if(in_array($view, $sixth_menu)){ echo ' opendiv'; }?>"><a class="menu-heading" href="#"><span><i class="fa fa-users"></i> <?php echo JText::_('EMPLOYEES'); ?></span><span class="selected"></span></a>
	<ul class="childnav">
	<?php if(VaccountHelper::checkOwnerGroup()) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=employee'); ?>" <?php if($view=="employee") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('MANAGEEMPLOYEES'); ?></span></a></li>
	
	<?php }  else if($isEmployee) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=leavecard'); ?>" <?php if($view=="leavecard") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('MY_LEAVE_CARD'); ?></span></a></li>	
	<?php } ?>
	<?php if((int)$config->employeecommission == 1) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=commission'); ?>" <?php if($view=="commission") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('EMPLOYEE_COMMISSION'); ?></span></a></li>
	<?php } ?>  
	<?php if($attendance_acl) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=attendance'); ?>" <?php if($view=="attendance") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('ATTENDANCE'); ?></span></a></li>
	<?php } ?>
	<?php if($empmanage_acl) { ?> 
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=edept'); ?>" <?php if($view=="edept") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('EMPLOYEE_DEPT'); ?></span></a>
	</li>

	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=edesg'); ?>" <?php if($view=="edesg") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('EMPLOYEE_DESG'); ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=leaves'); ?>" <?php if($view=="leaves") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('LEAVE_TYPE'); ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=payheads'); ?>" <?php if($view=="payheads") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('PAYHEADS'); ?></span></a></li>
	
	<?php } ?>
	
	
	</ul>
</li>
<?php } ?>
<?php if($transaction_acl && $config->enable_items==1 && $main_config->enable_items==1) { ?>
<li class="has-sub<?php if(in_array($view, $seventh_menu)){ echo ' opendiv'; }?>"><a class="menu-heading" href="#"><span><i class="fa fa-file-text"></i> <?php echo JText::_('INVENTORY'); ?></span><span class="selected"></span></a>
	<ul class="childnav">
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=category'); ?>" <?php if($view=="category") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('COM_VBIZZ_CATEGORY'); ?></span></a></li>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=itemqueue'); ?>" <?php if($view=="itemqueue") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('COM_VBIZZ_ITEMS_IN_QUEUE'); ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=items'); ?>" <?php if($view=="items") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo $config->item_view; ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=stock'); ?>" <?php if($view=="stock") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('MANAGE_STOCK'); ?></span></a></li>
	
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=assets'); ?>" <?php if($view=="assets") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('ASSETS'); ?></span></a></li>
	
	</ul>
</li>
<?php } ?>
<li class="has-sub<?php if($view=='reports'){ echo ' opendiv'; }?>"><a class="menu-heading" href="#"><span><i class="fa fa-clipboard"></i> <?php echo JText::_('REPORTS'); ?></span><span class="selected"></span></a>
	<ul class="childnav">
       <li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=reports'); ?>" <?php if($view=="reports") { ?> class="active" <?php } ?>><span><i class="fa fa-clipboard"></i> <?php echo JText::_('CALENDER_VIEW'); ?></span></a>
	   </li>
    </ul>
</li>
<li class="has-sub<?php if(in_array($view, $eighth_menu)||$layout=='widgetlisting'){ echo ' opendiv'; }?>"><a class="menu-heading" href="#"><span><i class="fa fa-gears"></i> <?php echo JText::_('SETTINGS'); ?></span><span class="selected"></span></a>
	<ul class="childnav">
	<?php if(VaccountHelper::checkOwnerGroup()) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=config'); ?>" <?php if($view=="config") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('CONFIGURATION'); ?></span></a></li>
	<?php } ?>
	
	<?php if(VaccountHelper::checkOwnerGroup()) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=etemp'); ?>" <?php if($view=="etemp") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('EMAIL_TEMPLATES'); ?></span></a></li>
	<?php } ?>
	
	<?php if($userId) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=users'); ?>" <?php if($view=="users") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('USER_PROFILE'); ?></span></a></li>

	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=notes'); ?>" <?php if($view=="notes") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('ACTIVITY_LOG'); ?></span></a></li>
	<?php } ?>
	<?php if($type_acl) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=tran'); ?>" <?php if($view=="tran") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo $config->type_view; ?></span></a></li>
	<?php } ?>

	<?php if($mode_acl) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=mode'); ?>" <?php if($view=="mode") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('TRANSACTION_MODE'); ?></span></a></li>
	<?php } ?>
	
	<?php if($import_acl) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=import'); ?>" <?php if($view=="import") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('IMPORT_EXPORT'); ?></span></a></li>
	<?php } ?>
	
	<?php if($imp_shd_task_acl_access) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=imtask'); ?>" <?php if($view=="imtask") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('IMPORT_TASK'); ?></span></a></li>
					
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=exptask'); ?>" <?php if($view=="exptask") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('EXPORT_TASK'); ?></span></a></li>
	<?php } ?>
	
	<?php if($config->enable_tax_discount==1 && $main_config->enable_tax_discount==1) { ?>     
		
		<?php if($tax_acl) { ?>
		<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=tax'); ?>" <?php if($view=="tax") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('TAX'); ?></span></a></li>
		<?php } ?>

		<?php if($discount_acl) { ?>
		<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=discount'); ?>" <?php if($view=="discount") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('DISCOUNT'); ?></span></a></li>
		<?php } ?>
	
	<?php } ?>  
	<?php if(VaccountHelper::checkOwnerGroup()) { ?>  
		<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=vbizz&layout=widgetlisting'); ?>" <?php if($view=="vbizz" && $layout=="widgetlisting") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('WIDGET_LISTING'); ?></span></a></li>
		<?php } ?>
	<?php if($recur_acl && $config->enable_recur==1 && $main_config->enable_recur==1) { ?>
	<?php if($config->enable_recur==1) { ?>
	<li><a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=recurr'); ?>" <?php if($view=="recurr") { ?> class="active" <?php } ?>><span><i class="fa fa-circle-o"></i> <?php echo JText::_('RECURRING_TRANSACTION'); ?></span></a></li>
	<?php } ?>
	<?php } ?>
	
	</ul>
</li>


</ul>
</div>
</div>
</div>
