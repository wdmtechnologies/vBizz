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

//$canDo = VaccountHelper::getActions();

$user = JFactory::getUser();
$userId = $user->id;

//get authorised user group
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->account_acl->get('addaccess');
$edit_access = $this->config->account_acl->get('editaccess');
$delete_access = $this->config->account_acl->get('deleteaccess');

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

?>
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('ACCOUNTS'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=accounts'); ?>" method="post" name="adminForm" id="adminForm">


<div class="subhead">
	<div class="container-fluid">
        <div id="container-collapse" class="container-collapse"></div>
        <div class="row-fluid">
            <div class="span12">
                <div id="toolbar" class="btn-toolbar">
					<?php if($addaccess) { ?>
						<?php if($this->config->enable_yodlee==1) { ?>
							<div class="btn-wrapper"  id="toolbar-new">
								<a class="modal btn btn-small" id="modal1" title="Select" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=accounts&task=add&tmpl=component';?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
								<span class="fa fa-plus"></span> <?php echo JText::_('NEW'); ?></a>
							</div>
						<?php } else { ?>
							<div class="btn-wrapper"  id="toolbar-new">
								<span onclick="Joomla.submitbutton('add')" class="btn btn-small btn-success">
								<span class="fa fa-plus"></span> <?php echo JText::_('NEW'); ?></span>
							</div>
						<?php } ?>
					<?php } ?>
				
					<?php if($editaccess) { ?>
					<div class="btn-wrapper"  id="toolbar-edit">
						<span onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('edit')}" class="btn btn-small">
						<span class="fa fa-edit"></span> <?php echo JText::_('EDIT'); ?></span>
					</div>
					<?php } ?>
				
					<?php if($deleteaccess) { ?>
					<div class="btn-wrapper"  id="toolbar-delete">
					<span onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('remove')}" class="btn btn-small">
					<span class="fa fa-remove"></span> <?php echo JText::_('DELETE'); ?></span>
					</div>
					<?php } ?>
					
					<?php if(VaccountHelper::checkOwnerGroup() && $this->config->enable_yodlee==1) { ?>
					<div class="btn-wrapper"  id="toolbar-setting"style="float: right;">
						<span>
						<a class="modal" id="modal1" title="Select" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=config&task=loadYodlee&tmpl=component&ot=1';?>" rel="{handler: 'iframe', size: {x: 550, y: 400}}">
						<span><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/setting.png"   />' ?></span></span>
						</a>
                    </div>
					<?php } ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<div class="adminlist filter">
<div class="filet_left">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
</div>
</div>


<div id="editcell">
    <table class="adminlist table">
        <thead>
            <tr>
                <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
                <th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/></th>
                <th><?php echo JHTML::_('grid.sort', 'ACCOUNT_NAME', 'account_name', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'ACCOUNT_NUMBER', 'account_number', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th><?php echo JText::_( 'AVAILABLE_BALANCE' ); ?></th>
				<th class="hidden-phone"></th>
				<th class="hidden-phone"></th>
            </tr>
        </thead>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=accounts&task=edit&cid[]='. $row->id );
		$balance = ($row->initial_balance+$row->income) - $row->expense;
		
		$acstate 		= JRoute::_( 'index.php?option=com_vbizz&view=statement&filter_account='.$row->id );
		
		$acreport =JURI::root(). 'index.php?option=com_vbizz&view=statement&layout=modal&tmpl=component&accountid='.$row->id;
		
		//get currency format
		$currency_format = $this->config->currency_format;
		
		//convert amount into given format
		if($currency_format==1)
		{
			$available_balance = $row->available_balance;
		} else if($currency_format==2) {
			$available_balance = number_format($row->available_balance, 2, '.', ',');
		} else if($currency_format==3) {
			$available_balance = number_format($row->available_balance, 2, ',', ' ');
		} else if($currency_format==4) {
			$available_balance = number_format($row->available_balance, 2, ',', '.');
		} else {
			$available_balance = $row->available_balance;
		}
		
    ?>
        <tr class="<?php echo "row$k"; ?>">
            <td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i);?></td>
            
            <td><?php echo $checked; ?></td>
            
            
            <td>
            <?php if ($editaccess) : ?>
            	<a href="<?php echo $link; ?>"><?php echo $row->account_name; ?></a>
            <?php else : ?>
            	<?php echo $row->account_name; ?>
            <?php endif; ?>
            </td>
            
            <td class="hidden-phone"><?php echo $row->account_number; ?></td>
            
             <td><?php echo $available_balance; ?></td>
            
            <td class="hidden-phone"><a class="btn btn-small" href="<?php echo $acstate; ?>"><?php echo JText::_('VIEW_AC_STATEMENT'); ?></a></td>
			
			<td class="hidden-phone">
			<a class="modal btn btn-small" id="modal1" href="<?php echo $acreport; ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
				<span><?php echo JText::_('VIEW_REPORT');; ?></span>
			</a>
        	</td>
            
        
        </tr>
    <?php
    	$k = 1 - $k;
    }
    ?>
        <tfoot>
            <tr>
                <td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
            </tr>
        </tfoot>
    </table>
</div>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>
