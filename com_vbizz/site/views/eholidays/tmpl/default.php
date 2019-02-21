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
JHtml::_('formbehavior.chosen', 'select');

$canDo = VaccountHelper::getActions();

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->employee_manage_acl->get('addaccess');
$edit_access = $this->config->employee_manage_acl->get('editaccess');
$delete_access = $this->config->employee_manage_acl->get('deleteaccess');

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
				<h1 class="page-title"><?php echo JText::_('EMPLOYEE_HOLIDAYS'); ?></h1>
		</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=eholidays'); ?>" method="post" name="adminForm" id="adminForm">

<div class="subhead">
	<div class="container-fluid">
        <div id="container-collapse" class="container-collapse"></div>
        <div class="row-fluid">
            <div class="span12">
                <div id="toolbar" class="btn-toolbar">
                    <?php if (($canDo->get('core.create'))) { ?>
						<?php if($addaccess) { ?>
                        <div class="btn-wrapper"  id="toolbar-new">
                            <span onclick="Joomla.submitbutton('add')" class="btn btn-small btn-success">
                            <span class="icon-new icon-white"></span> <?php echo JText::_('NEW'); ?></span>
                        </div>
                        <?php } ?>
                    <?php } ?>
                    
                    <?php if (($canDo->get('core.edit'))) { ?>
						<?php if($editaccess) { ?>
                        <div class="btn-wrapper"  id="toolbar-edit">
                            <span onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('edit')}" class="btn btn-small">
                            <span class="icon-edit"></span> <?php echo JText::_('EDIT'); ?></span>
                        </div>
                        <?php } ?>
                    <?php } ?>
                    
                    <?php if (($canDo->get('core.delete'))) { ?>
						<?php if($deleteaccess) { ?>
                        <div class="btn-wrapper"  id="toolbar-delete">
                        <span onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('remove')}" class="btn btn-small">
                        <span class="icon-delete"></span> <?php echo JText::_('DELETE'); ?></span>
                        </div>
                        <?php } ?>
                    <?php } ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<table class="adminlist table filter">
<tr>
<td align="left">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT');?>" onclick="this.form.submit();"><i class="icon-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();"><i class="icon-remove"></i></button>
</td>

</tr>
</table>

<div id="editcell">
	<table class="adminlist table">
        <thead>
            <tr>
                <th class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
                <th width="20"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
                <th><?php echo JHTML::_('grid.sort', 'HOLIDAY', 'holiday', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'FROM', 'from_date', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'TO', 'to_date', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				<th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'OPTIONAL', 'optional', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				<th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'ID', 'id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            </tr>
        </thead>
        <?php
        $k = 0;
        for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
            $row = &$this->items[$i];
            $canEdit = $user->authorise('core.edit', 'com_vbizz');
            $canChange = $user->authorise('core.edit.state', 'com_vbizz');
            $checked 	= JHTML::_('grid.id',   $i, $row->id );
            $link 		= JRoute::_( 'index.php?option=com_vbizz&view=eholidays&task=edit&cid[]='. $row->id );
			
			//get date format from configuration
			$format = $this->config->date_format;
			$saved_fdate = $row->from_date;
			$saved_edate = $row->to_date;
			
			//convert date into given format
			$fdatetime = strtotime($saved_fdate);
			$edatetime = strtotime($saved_edate);
			if($format)
			{
				if($row->from_date=="0000-00-00") {
					$from_date = "--";
				} else {
					$from_date = date($format, $fdatetime );
				}
				if($row->to_date=="0000-00-00") {
					$to_date = "--";
				} else {
					$to_date = date($format, $edatetime );
				}
			} else {
				if($row->from_date=="0000-00-00") {
					$from_date = "--";
				} else {
					$from_date = $saved_fdate;
				}
				if($row->to_date=="0000-00-00") {
					$to_date = "--";
				} else {
					$to_date = $saved_edate;
				}
			}
			
			if($row->optional) {
				$optional = JText::_('YS');
			} else {
				$optional = JText::_('NOS');
			}
		
            ?>
            <tr class="<?php echo "row$k"; ?>">
            
                <td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i); ?></td>
                
                <td><?php echo $checked; ?></td>
                
                
                <td align="center">
                    <?php if ($canEdit) : ?>
                        <a href="<?php echo $link; ?>"><?php echo $row->holiday; ?></a>
                    <?php else : ?>
                        <?php echo $row->holiday; ?>
                    <?php endif; ?>
                </td>
				
				<td align="center"><?php echo $from_date; ?></td>
				
				<td align="center"><?php echo $to_date; ?></td>
				
				<td align="center" class="hidden-phone"><?php echo $optional; ?></td>
                                
                <td align="center" class="hidden-phone"><?php echo $row->id; ?></td>
               
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

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="eholidays" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
<div class="copyright" align="center">
	<?php echo JText::_( 'WDM' );?>
</div>
