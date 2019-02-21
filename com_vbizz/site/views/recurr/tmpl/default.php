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
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->recur_acl->get('addaccess');
$edit_access = $this->config->recur_acl->get('editaccess');
$delete_access = $this->config->recur_acl->get('deleteaccess');

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
		<h1 class="page-title"><?php echo JText::_('RECURRING_TRANSACTION'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=recurr'); ?>" method="post" name="adminForm" id="adminForm">


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
                    
                </div>
            </div>
        </div>
    </div>
</div>

<div class="adminlist filter filter_recurr">
<div class="filet_left">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT');?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit(); document.getElementById('filter_type').value='';this.form.submit(); document.getElementById('filter_year').value='';this.form.submit(); document.getElementById('filter_month').value='';this.form.submit(); document.getElementById('filter_day').value='';this.form.submit(); document.getElementById('filter_mode').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
</div>
<div class="filter_right">
<?php
echo $this->lists['years'];
echo $this->lists['months'];
echo $this->lists['days'];
echo $this->lists['modes'];
echo $this->lists['ttypes'];
?>
</div>
</div>

<div id="editcell">
	<table class="adminlist table">
	<thead>
		<tr>
        	<th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
			<th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>			
			<th><?php echo JHTML::_('grid.sort', 'TITLE', 'r.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <th class="hidden-phone"><?php echo JHTML::_('grid.sort', $this->config->type_view_single, 't.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <?php /*?><th><?php echo JHTML::_('grid.sort', 'QUANTITY', 'r.quantity', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th><?php */?>
            <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'MODE', 'm.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <th class="hidden-phone"><?php echo JText::_( 'TRANSACTION_ID' ); ?></th>
            <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'DATE', 'r.tdate', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'AMOUNT', 'r.actual_amount', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
		</tr>
	</thead>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=recurr&task=edit&cid[]='.$row->id );
		
		//get date format from configuration
		$format = $this->config->date_format;
		$saved_date = $row->tdate;
		
		//convert date into given format in configuration
		$datetime = strtotime($saved_date);
		if($format)
		{
			$date = date($format, $datetime );
		} else {
			$date = $saved_date;
		}
		
		//get currency format from configuration
		$currency_format = $this->config->currency_format;
		
		//convert amount into given format in configuration
		if($currency_format==1)
		{
			$actual_amount = $row->actual_amount;
		} else if($currency_format==2) {
			$actual_amount = number_format($row->actual_amount, 2, '.', ',');
		} else if($currency_format==3) {
			$actual_amount = number_format($row->actual_amount, 2, ',', ' ');
		} else if($currency_format==4) {
			$actual_amount = number_format($row->actual_amount, 2, ',', '.');
		} else {
			$actual_amount = $row->actual_amount;
		}
		
		?>
		<tr class="<?php echo "row$k"; ?>">
        	<td align="center" class="hidden-phone">
				<?php echo $this->pagination->getRowOffset($i); ?>
			</td>
			<?php /*?><td align="center">
				<?php echo $row->id; ?>
			</td><?php */?>
			<td>
				<?php echo $checked; ?>
			</td>
			<td align="center">
				<?php if ($editaccess) : ?>
					<a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
                <?php else : ?>
                	<?php echo $row->title; ?>
                <?php endif; ?>
			</td>
            <td align="center" class="hidden-phone">
				<?php echo '<span style="color:'.$row->color.';">'.$row->type.'</span>'; ?>
			</td>
            
            <?php /*?><td align="center"><?php echo $row->quantity; ?></td><?php */?>
            
            <td align="center" class="hidden-phone">
				<?php echo $row->mode; ?>
			</td>
            
            <td align="center" class="hidden-phone">
				<?php echo $row->tranid; ?>
			</td>
            
            <td align="center" class="hidden-phone">
				<?php echo $date; ?>
			</td>
            
            <td align="center">
				<?php if($row->types=='expense'){ echo'<span style="color:#FF0000;">'.$this->config->currency.' '.$actual_amount.'</span>'; } else{echo '<span style="color:#04B404;">'.$this->config->currency.' '.$actual_amount.'</span>';}?>
			</td>
            
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	
	<tr>
  		<td class="hidden-phone">&nbsp;</td><td class="hidden-phone">&nbsp;</td><td class="hidden-phone">&nbsp;</td><td class="hidden-phone">&nbsp;</td><td class="hidden-phone">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
        
        <td>
			<?php //if($final_income) { ?>
        	<strong class="hasTip" title="<?php echo JText::_('FINALAMOUNTTXT'); ?>"><?php echo JText::_('<span style="color:#0404B4;">'.JText::_('TOTAL').' : '.$this->config->currency.' '.$this->total.'</span>');?></strong>
			<?php //} ?>
   		</td>
	</tr>
	
    <tfoot>
    <tr>
      <td colspan="8"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
  </tfoot>
	</table>
 </div>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="recurr" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>
