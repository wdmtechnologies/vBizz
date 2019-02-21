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
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

$db = JFactory::getDbo();
/* $query = 'CREATE TABLE IF NOT EXISTS `#__vbizz_lead_source` (
  `source_id` int(11) NOT NULL AUTO_INCREMENT,
  `source_name` varchar(150) NOT NULL,
  `ownerid` int(100) NOT NULL,
  PRIMARY KEY (`source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;';
$db->setQuery($query);
$db->execute();
jexit();  */

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->leads_acl->get('addaccess');
$edit_access = $this->config->leads_acl->get('editaccess');
$delete_access = $this->config->leads_acl->get('deleteaccess');

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



//get date format from configuration
$format = $this->config->date_format;

		//get currency format from configuration
$currency_format = $this->config->currency_format;

//var_dump($this->lists); exit;

?>
<style>
.shadow {
	left:5% !important;
	top:5% !important;
}
</style>
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('LEADS_LABEL'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=leads'); ?>" method="post" name="adminForm" id="adminForm">

<?php if($addaccess || $editaccess || $deleteaccess) { ?>
<div class="subhead">
	<div class="container-fluid">
        <div id="container-collapse" class="container-collapse"></div>
        <div class="row-fluid">
            <div class="span12">
                <div id="toolbar" class="btn-toolbar">
								
						<?php if($addaccess) { ?>
                        <div class="btn-wrapper"  id="toolbar-new">
                            <span onclick="Joomla.submitbutton('add')" class="btn btn-small btn-success">
                            <span class="fa fa-plus"></span> <?php echo JText::_('NEW'); ?></span>
                        </div>
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
						 
                        <?php if(VaccountHelper::checkOwnerGroup()) { ?>
						
					   <div class="btn-wrapper"  id="toolbar-setting"style="float: right;">
						<a class="modal btn faa-parent animated-hover faa-slow" id="modal1" title="Select" href="<?php echo JRoute::_('index.php?option=com_vbizz&view=leads&task=leadstatus&tmpl=component&ot=1');?>" rel="{handler: 'iframe', size: {x: '90%', y: '80%'}}">
						<span class="fa fa-cog faa-spin faa-slow"></span> 
						</a>
                    </div>
					<?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div class="adminlist filter">
<div class="filet_left filter_block-a">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('SEARCH'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();
document.getElementById('filter_status').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
</div>

<?php if(VaccountHelper::checkOwnerGroup()) { ?>  
<div class="filter_right filter_block-b">
    <div class="vbz_lead lead_so"><label><?php echo JText::_( 'COM_VBIZZ_LEAD_SOURCE' ); ?></label>
	<?php echo $this->lists['lead_source']; ?></div>
	<div class="vbz_lead lead_in"><label><?php echo JText::_( 'COM_VBIZZ_LEAD_INDUSTRY' ); ?></label>
	<?php echo $this->lists['lead_industry']; ?></div>
	<div class="vbz_lead lead_st"><label><?php echo JText::_( 'COM_VBIZZ_LEAD_STATUS' ); ?></label>
	<?php echo $this->lists['lead_status']; ?></div>
</div>
<?php } ?>  

</div>


<div id="editcell">
    <table class="adminlist table">
        <thead>
            <tr>
                <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
				<?php if(!VaccountHelper::checkClientGroup()) { ?>
                <th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/></th>
				<?php } ?>
                <th><?php echo JHTML::_('grid.sort', JText::_('COM_VBIZZ_LEADS_NAME'), 'title', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<?php if(VaccountHelper::checkOwnerGroup() && $this->config->enable_cust) { ?>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', JText::_('COM_VBIZZ_LEADS_USER_NAME'), 'userid', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<?php } ?>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', JText::_('COM_VBIZZ_LEADS_SOURCE'), 'lead_source', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>  
				<th class="hidden-phone"><?php echo JHTML::_('grid.sort', JText::_('COM_VBIZZ_LEADS_INDUSTRY'), 'lead_industry', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<th class="hidden-phone"><?php echo JHTML::_('grid.sort', JText::_('COM_VBIZZ_LEADS_STATUS'), 'lead_status', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th><?php echo JHTML::_('grid.sort', 'COM_VBIZZ_LEAD_DATE', 'lead_date', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<th><?php echo JHTML::_('grid.sort', 'COM_VBIZZ_LEAD_ACTION', 'move_to', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
            </tr>
        </thead>
    <?php
    $k = 0;
	 
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		
		if($userId==$row->created_by) {
			$link 		= JRoute::_( 'index.php?option=com_vbizz&view=leads&task=edit&cid[]='.$row->id );
		} else {    
			$link 		= JRoute::_( 'index.php?option=com_vbizz&view=leads&task=details&cid[]='.$row->id );
		}
		
		$saved_date = $row->lead_date;
		
		//convert date format into given date in configuration
		$datetime = strtotime($saved_date);
		if($format)
		{
			if($saved_date == "0000-00-00")
			{
				$lead_date = '';
			} else {
				$lead_date = date($format, $datetime );
			}
		} else {
			if($saved_date == "0000-00-00")
			{
				$lead_date = '';
			} else {
				$lead_date = $saved_date;
			}
		}
		
		    $query = 'select name from #__users where id = '.$row->userid;
			$db->setQuery( $query );
			$customers = $db->loadResult();
			
		//convert amount into given format in configuration
		
		
    ?>
        <tr class="<?php echo "row$k"; ?>">
            <td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i);?></td>
            
             <td><?php echo $checked; ?></td>
            
            <td>
			<a href="<?php echo $link; ?>"><?php echo $row->title;?></a>
            </td>
			<?php if(VaccountHelper::checkOwnerGroup() && $this->config->enable_cust) { ?>
			<td class="hidden-phone"><a href="<?php echo JRoute::_( 'index.php?option=com_vbizz&view=customer&task=edit&cid[]='.$row->userid );?>"><?php echo $customers; ?></a></td>
			<?php } ?>
			<td class="hidden-phone"><?php echo $row->lead_source; ?></td>
			<td class="hidden-phone"><?php echo $row->lead_industry; ?></td>
            <td class="hidden-phone"><?php echo $row->lead_status; ?></td>
          
            <td class="hidden-phone"><?php echo $lead_date; ?></td>
			
            <td class="hidden-phone"><?php echo !empty($row->moved_to)?sprintf(JText::_('COM_VBIZZ_ACTION_ON_LEAD'),$row->moved_to):JText::_('COM_VBIZZ_ACTION_NO_OPERATION'); ?></td>
            
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
<input type="hidden" name="view" value="leads" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
